<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockMovement;
use App\Models\StockTake;
use App\Models\StockTakeItem;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTakeController extends Controller
{
    use HasSoftDeleteActions;

    public function index(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin');

        if ($request->get('trashed')) {
            $query = StockTake::onlyTrashed()->with(['branch', 'createdBy']);
        } else {
            $query = StockTake::with(['branch', 'createdBy']);
        }

        if (!$isSuperAdmin) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->get('branch_id')) {
            $query->where('branch_id', $request->get('branch_id'));
        }

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        $stockTakes     = $query->latest()->paginate(15)->withQueryString();
        $trashedCount   = StockTake::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');
        $branches       = Branch::where('is_active', true)->get();
        $statuses       = ['draft','in_progress','pending_approval','approved','cancelled'];

        return view('stock-takes.index', compact(
            'stockTakes', 'trashedCount', 'showingTrashed',
            'branches', 'statuses', 'isSuperAdmin'
        ));
    }

    public function create()
    {
        $user       = auth()->user();
        $branches   = $user->hasRole('super_admin')
            ? Branch::where('is_active', true)->get()
            : Branch::where('id', $user->branch_id)->get();
        $categories = ProductCategory::where('is_active', true)->get();

        return view('stock-takes.create', compact('branches', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id'   => 'required|exists:branches,id',
            'notes'       => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:product_categories,id',
        ]);

        DB::transaction(function () use ($request) {

            $stockTake = StockTake::create([
                'reference'  => StockTake::generateReference(),
                'branch_id'  => $request->branch_id,
                'created_by' => auth()->id(),
                'status'     => 'in_progress',
                'notes'      => $request->notes,
                'started_at' => now(),
            ]);

            // Load all products for this branch
            $stockQuery = BranchStock::where('branch_id', $request->branch_id)
                ->with('product')
                ->whereHas('product', fn($q) =>
                $q->where('is_active', true)->whereNull('deleted_at')
                );

            // Filter by category if specified
            if ($request->category_id) {
                $stockQuery->whereHas('product', fn($q) =>
                $q->where('category_id', $request->category_id)
                );
            }

            $stocks = $stockQuery->get();

            foreach ($stocks as $stock) {
                StockTakeItem::create([
                    'stock_take_id'    => $stockTake->id,
                    'product_id'       => $stock->product_id,
                    'system_quantity'  => $stock->quantity,
                    'counted_quantity' => null,
                    'variance'         => null,
                ]);
            }

            activity()
                ->performedOn($stockTake)
                ->causedBy(auth()->user())
                ->log('Started stock take: ' . $stockTake->reference);
        });

        return redirect()->route('stock-takes.index')
            ->with('success', 'Stock take started. Begin counting products.');
    }

    public function show(StockTake $stockTake)
    {
        $stockTake->load(['branch', 'createdBy', 'approvedBy', 'items.product.category']);

        $items     = $stockTake->items->sortBy('product.name');
        $categories = $items->groupBy('product.category.name');

        $counted  = $items->whereNotNull('counted_quantity')->count();
        $total    = $items->count();
        $progress = $total > 0 ? round(($counted / $total) * 100) : 0;

        return view('stock-takes.show', compact(
            'stockTake', 'items', 'categories', 'counted', 'total', 'progress'
        ));
    }

    public function count(Request $request, StockTake $stockTake)
    {
        if (!in_array($stockTake->status, ['in_progress'])) {
            return response()->json(['error' => 'Stock take is not in progress.'], 422);
        }

        $request->validate([
            'item_id'          => 'required|exists:stock_take_items,id',
            'counted_quantity' => 'required|numeric|min:0',
            'notes'            => 'nullable|string|max:255',
        ]);

        $item = StockTakeItem::where('id', $request->item_id)
            ->where('stock_take_id', $stockTake->id)
            ->firstOrFail();

        $variance = $request->counted_quantity - $item->system_quantity;

        $item->update([
            'counted_quantity' => $request->counted_quantity,
            'variance'         => $variance,
            'notes'            => $request->notes,
        ]);

        // Check if all items counted
        $allCounted = $stockTake->items()
            ->whereNull('counted_quantity')
            ->doesntExist();

        if ($allCounted) {
            $stockTake->update([
                'status'       => 'pending_approval',
                'completed_at' => now(),
            ]);
        }

        $counted  = $stockTake->items()->whereNotNull('counted_quantity')->count();
        $total    = $stockTake->items()->count();
        $progress = $total > 0 ? round(($counted / $total) * 100) : 0;

        return response()->json([
            'success'          => true,
            'variance'         => $variance,
            'counted_quantity' => $request->counted_quantity,
            'system_quantity'  => $item->system_quantity,
            'all_counted'      => $allCounted,
            'progress'         => $progress,
            'counted'          => $counted,
            'total'            => $total,
            'status'           => $stockTake->fresh()->status,
        ]);
    }

    public function submit(StockTake $stockTake)
    {
        if ($stockTake->status !== 'in_progress') {
            return back()->with('error', 'Only in-progress stock takes can be submitted.');
        }

        $uncounted = $stockTake->items()->whereNull('counted_quantity')->count();

        if ($uncounted > 0) {
            return back()->with('error',
                "{$uncounted} product(s) still need to be counted before submitting.");
        }

        $stockTake->update([
            'status'       => 'pending_approval',
            'completed_at' => now(),
        ]);

        activity()
            ->performedOn($stockTake)
            ->causedBy(auth()->user())
            ->log('Submitted stock take for approval: ' . $stockTake->reference);

        return back()->with('success', 'Stock take submitted for approval.');
    }

    public function approve(StockTake $stockTake)
    {
        if ($stockTake->status !== 'pending_approval') {
            return back()->with('error', 'Only submitted stock takes can be approved.');
        }

        $stockTake->load('items.product');

        DB::transaction(function () use ($stockTake) {

            foreach ($stockTake->items as $item) {
                if ($item->variance === null || $item->variance == 0) {
                    continue;
                }

                // Adjust stock to match counted quantity
                $stock = BranchStock::firstOrCreate(
                    [
                        'branch_id'  => $stockTake->branch_id,
                        'product_id' => $item->product_id,
                    ],
                    ['quantity' => 0, 'last_updated' => now()]
                );

                $newQty = $item->counted_quantity;
                $stock->update([
                    'quantity'     => $newQty,
                    'last_updated' => now(),
                ]);

                // Record stock movement
                StockMovement::record(
                    branchId     : $stockTake->branch_id,
                    productId    : $item->product_id,
                    userId       : auth()->id(),
                    type         : $item->variance > 0 ? 'adjustment_in' : 'adjustment_out',
                    quantity     : abs($item->variance),
                    balanceAfter : $newQty,
                    referenceType: 'stock_take',
                    referenceId  : $stockTake->id,
                    notes        : 'Stock take adjustment: ' . $stockTake->reference
                );
            }

            $stockTake->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
            ]);

            activity()
                ->performedOn($stockTake)
                ->causedBy(auth()->user())
                ->log('Approved stock take: ' . $stockTake->reference);
        });

        return back()->with('success',
            'Stock take approved. Inventory has been adjusted to match counted quantities.');
    }

    public function cancel(StockTake $stockTake)
    {
        if (in_array($stockTake->status, ['approved', 'cancelled'])) {
            return back()->with('error', 'This stock take cannot be cancelled.');
        }

        $stockTake->update(['status' => 'cancelled']);

        return back()->with('success', 'Stock take cancelled.');
    }

    public function destroy(StockTake $stockTake)
    {
        if (!in_array($stockTake->status, ['draft', 'cancelled'])) {
            return back()->with('error', 'Only draft or cancelled stock takes can be deleted.');
        }
        return $this->softDelete($stockTake, 'stock-takes.index');
    }

    public function restore($id)
    {
        $st = StockTake::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($st, 'stock-takes.index');
    }
}
