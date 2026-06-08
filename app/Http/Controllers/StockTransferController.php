<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\TransferItem;
use App\Traits\HasBranchAccess;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    use HasSoftDeleteActions, HasBranchAccess;

    public function index(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();

        // Start with trashed or active
        if ($request->get('trashed')) {
            $query = StockTransfer::onlyTrashed()->with([
                'fromBranch', 'toBranch', 'requestedBy', 'approvedBy', 'items'
            ]);
        } else {
            $query = StockTransfer::with([
                'fromBranch', 'toBranch', 'requestedBy', 'approvedBy', 'items'
            ]);
        }

        // Non-admins only see transfers involving their branch
        if (!$isSuperAdmin) {
            $query->where(fn($q) =>
            $q->where('from_branch_id', $user->branch_id)
                ->orWhere('to_branch_id', $user->branch_id)
            );
        }

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->get('branch')) {
            $query->where(fn($q) =>
            $q->where('from_branch_id', $request->get('branch'))
                ->orWhere('to_branch_id', $request->get('branch'))
            );
        }

        if ($request->get('search')) {
            $query->where('reference_no', 'like', '%' . $request->get('search') . '%');
        }

        $transfers      = $query->latest()->paginate(15)->withQueryString();
        $trashedCount   = StockTransfer::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');
        $branches       = Branch::where('is_active', true)->get();
        $statuses       = ['pending', 'approved', 'dispatched', 'received', 'rejected'];

        // Summary counts
        $pendingCount = StockTransfer::where('status', 'pending')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where(fn($q2) =>
            $q2->where('from_branch_id', $user->branch_id)
                ->orWhere('to_branch_id', $user->branch_id)
            )
            )->count();

        $dispatchedCount = StockTransfer::where('status', 'dispatched')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('to_branch_id', $user->branch_id)
            )->count();

        return view('transfers.index', compact(
            'transfers', 'branches', 'statuses', 'trashedCount',
            'showingTrashed', 'pendingCount', 'dispatchedCount', 'isSuperAdmin'
        ));
    }

    public function create()
    {
        $user     = auth()->user();
        $branches = Branch::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('transfers.create', compact('branches', 'products', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'to_branch_id'      => 'required|exists:branches,id|different:from_branch_id',
            'from_branch_id'    => 'required|exists:branches,id',
            'notes'             => 'nullable|string|max:500',
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|exists:products,id',
            'items.*.quantity'  => 'required|numeric|min:0.01',
        ]);

        // Ensure no duplicate products
        $productIds = array_column($request->items, 'product_id');
        if (count($productIds) !== count(array_unique($productIds))) {
            return back()->withErrors(['items' => 'Duplicate products found.'])->withInput();
        }

        DB::transaction(function () use ($request) {
            $transfer = StockTransfer::create([
                'reference_no'   => StockTransfer::generateReferenceNo(),
                'from_branch_id' => $request->from_branch_id,
                'to_branch_id'   => $request->to_branch_id,
                'requested_by'   => auth()->id(),
                'status'         => 'pending',
                'notes'          => $request->notes,
            ]);

            foreach ($request->items as $item) {
                TransferItem::create([
                    'transfer_id'        => $transfer->id,
                    'product_id'         => $item['product_id'],
                    'quantity_requested' => $item['quantity'],
                    'quantity_received'  => 0,
                ]);
            }

            activity()
                ->performedOn($transfer)
                ->causedBy(auth()->user())
                ->log('Created transfer request: ' . $transfer->reference_no);
        });

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer request created successfully.');
    }

    public function show(StockTransfer $transfer)
    {
        $transfer->load([
            'fromBranch', 'toBranch',
            'requestedBy', 'approvedBy',
            'items.product'
        ]);

        return view('transfers.show', compact('transfer'));
    }

    public function edit(StockTransfer $transfer)
    {
        if (!in_array($transfer->status, ['pending'])) {
            return redirect()->route('transfers.show', $transfer)
                ->with('error', 'Only pending transfers can be edited.');
        }

        $branches = Branch::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();

        return view('transfers.edit', compact('transfer', 'branches', 'products'));
    }

    public function update(Request $request, StockTransfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be edited.');
        }

        $request->validate([
            'notes'             => 'nullable|string|max:500',
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|exists:products,id',
            'items.*.quantity'  => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request, $transfer) {
            $transfer->update(['notes' => $request->notes]);

            // Remove old items and replace
            $transfer->items()->delete();

            foreach ($request->items as $item) {
                TransferItem::create([
                    'transfer_id'        => $transfer->id,
                    'product_id'         => $item['product_id'],
                    'quantity_requested' => $item['quantity'],
                    'quantity_received'  => 0,
                ]);
            }
        });

        return redirect()->route('transfers.show', $transfer)
            ->with('success', 'Transfer updated successfully.');
    }

    public function approve(StockTransfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be approved.');
        }

        $transfer->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        activity()
            ->performedOn($transfer)
            ->causedBy(auth()->user())
            ->log('Approved transfer: ' . $transfer->reference_no);

        return back()->with('success', 'Transfer approved successfully.');
    }

    public function dispatch(StockTransfer $transfer)
    {
        if ($transfer->status !== 'approved') {
            return back()->with('error', 'Only approved transfers can be dispatched.');
        }

        $transfer->load('items.product');

        // Check source branch has enough stock
        $errors = [];
        foreach ($transfer->items as $item) {
            $stock = BranchStock::where('branch_id', $transfer->from_branch_id)
                ->where('product_id', $item->product_id)
                ->first();

            if (!$stock || $stock->quantity < $item->quantity_requested) {
                $available = $stock?->quantity ?? 0;
                $errors[]  = "{$item->product->name}: need {$item->quantity_requested}, only {$available} available.";
            }
        }

        if (!empty($errors)) {
            return back()->with('error', 'Insufficient stock: ' . implode(' | ', $errors));
        }

        DB::transaction(function () use ($transfer) {
            foreach ($transfer->items as $item) {
                $stock = BranchStock::where('branch_id', $transfer->from_branch_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                $stock->decrementStock($item->quantity_requested);

                StockMovement::record(
                    branchId      : $transfer->from_branch_id,
                    productId     : $item->product_id,
                    userId        : auth()->id(),
                    type          : 'transfer_out',
                    quantity      : -$item->quantity_requested,
                    balanceAfter  : $stock->quantity,
                    referenceType : 'transfer',
                    referenceId   : $transfer->id,
                    notes         : 'Dispatched — ' . $transfer->reference_no
                );
            }

            $transfer->update(['status' => 'dispatched']);

            activity()
                ->performedOn($transfer)
                ->causedBy(auth()->user())
                ->log('Dispatched transfer: ' . $transfer->reference_no);
        });

        return back()->with('success', 'Transfer dispatched. Stock deducted from source branch.');
    }

    public function receive(Request $request, StockTransfer $transfer)
    {
        if ($transfer->status !== 'dispatched') {
            return back()->with('error', 'Only dispatched transfers can be received.');
        }

        $request->validate([
            'received_quantities'   => 'required|array',
            'received_quantities.*' => 'required|numeric|min:0',
        ]);

        $transfer->load('items.product');

        DB::transaction(function () use ($request, $transfer) {
            foreach ($transfer->items as $item) {
                $received = $request->received_quantities[$item->id] ?? 0;
                $item->update(['quantity_received' => $received]);

                if ($received > 0) {
                    $stock = BranchStock::firstOrCreate(
                        [
                            'branch_id'  => $transfer->to_branch_id,
                            'product_id' => $item->product_id,
                        ],
                        ['quantity' => 0, 'last_updated' => now()]
                    );

                    $stock->incrementStock($received);

                    StockMovement::record(
                        branchId      : $transfer->to_branch_id,
                        productId     : $item->product_id,
                        userId        : auth()->id(),
                        type          : 'transfer_in',
                        quantity      : $received,
                        balanceAfter  : $stock->quantity,
                        referenceType : 'transfer',
                        referenceId   : $transfer->id,
                        notes         : 'Received — ' . $transfer->reference_no
                    );
                }
            }

            $transfer->update(['status' => 'received']);

            activity()
                ->performedOn($transfer)
                ->causedBy(auth()->user())
                ->log('Received transfer: ' . $transfer->reference_no);
        });

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer received. Stock added to branch.');
    }

    public function reject(Request $request, StockTransfer $transfer)
    {
        if (!in_array($transfer->status, ['pending', 'approved'])) {
            return back()->with('error', 'This transfer cannot be rejected.');
        }

        $transfer->update(['status' => 'rejected']);

        activity()
            ->performedOn($transfer)
            ->causedBy(auth()->user())
            ->log('Rejected transfer: ' . $transfer->reference_no);

        return back()->with('success', 'Transfer rejected.');
    }

    public function destroy(StockTransfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be deleted.');
        }
        return $this->softDelete($transfer, 'transfers.index');
    }

    public function restore($id)
    {
        $transfer = StockTransfer::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($transfer, 'transfers.index');
    }
}
