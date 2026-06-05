<?php

namespace App\Http\Controllers;

use App\Models\BranchStock;
use App\Models\ProductReturn;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReturnController extends Controller
{
    use HasSoftDeleteActions;

    public function index(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin');

        if ($request->get('trashed')) {
            $query = ProductReturn::onlyTrashed()
                ->with(['sale', 'product', 'branch', 'processedBy']);
        } else {
            $query = ProductReturn::with(['sale', 'product', 'branch', 'processedBy']);
        }

        if (!$isSuperAdmin) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->get('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->get('search')) {
            $query->whereHas('sale', fn($q) =>
            $q->where('invoice_no', 'like', '%' . $request->get('search') . '%')
            );
        }

        if ($request->get('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->get('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $returns        = $query->latest()->paginate(15)->withQueryString();
        $trashedCount   = ProductReturn::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');
        $statuses       = ['pending', 'approved', 'rejected'];
        $types          = ['refund', 'exchange', 'damaged'];

        $pendingCount = ProductReturn::where('status', 'pending')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_id', $user->branch_id)
            )->count();

        return view('returns.index', compact(
            'returns', 'trashedCount', 'showingTrashed',
            'statuses', 'types', 'pendingCount', 'isSuperAdmin'
        ));
    }

    public function create(Request $request)
    {
        $user     = auth()->user();
        $invoiceNo = $request->get('invoice');
        $sale     = null;
        $saleItems = collect();

        if ($invoiceNo) {
            $sale = Sale::with('items.product')
                ->where('invoice_no', $invoiceNo)
                ->when(!$user->hasRole('super_admin'), fn($q) =>
                $q->where('branch_id', $user->branch_id)
                )
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($sale) {
                $saleItems = $sale->items;
            }
        }

        return view('returns.create', compact('sale', 'saleItems', 'invoiceNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id'    => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:0.01',
            'type'       => 'required|in:refund,exchange,damaged',
            'reason'     => 'nullable|string|max:500',
        ]);

        $sale = Sale::findOrFail($request->sale_id);

        // Validate quantity doesn't exceed original sale quantity
        $saleItem = SaleItem::where('sale_id', $request->sale_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$saleItem) {
            return back()->with('error', 'Product not found in this sale.')
                ->withInput();
        }

        if ($request->quantity > $saleItem->quantity) {
            return back()->with('error',
                "Cannot return more than sold quantity ({$saleItem->quantity}).")
                ->withInput();
        }

        $refundAmount = ($saleItem->unit_price * $request->quantity);

        $return = ProductReturn::create([
            'sale_id'      => $request->sale_id,
            'product_id'   => $request->product_id,
            'branch_id'    => $sale->branch_id,
            'quantity'     => $request->quantity,
            'refund_amount'=> $refundAmount,
            'reason'       => $request->reason,
            'type'         => $request->type,
            'status'       => 'pending',
            'processed_by' => auth()->id(),
        ]);

        activity()
            ->performedOn($return)
            ->causedBy(auth()->user())
            ->log('Created return request for invoice: ' . $sale->invoice_no);

        return redirect()->route('returns.index')
            ->with('success', 'Return request submitted successfully.');
    }

    public function show(ProductReturn $return)
    {
        $return->load(['sale', 'product', 'branch', 'processedBy']);
        return view('returns.show', compact('return'));
    }

    // Add to ProductReturnController

    public function edit(ProductReturn $return)
    {
        if ($return->status !== 'pending') {
            return redirect()->route('returns.show', $return)
                ->with('error', 'Only pending returns can be edited.');
        }

        $return->load(['sale', 'product', 'branch']);
        return view('returns.edit', compact('return'));
    }

    public function update(Request $request, ProductReturn $return)
    {
        if ($return->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be edited.');
        }

        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'type'     => 'required|in:refund,exchange,damaged',
            'reason'   => 'nullable|string|max:500',
        ]);

        // Validate quantity doesn't exceed original sale quantity
        $saleItem = SaleItem::where('sale_id', $return->sale_id)
            ->where('product_id', $return->product_id)
            ->first();

        if ($request->quantity > $saleItem->quantity) {
            return back()->with('error',
                "Cannot return more than sold quantity ({$saleItem->quantity}).")
                ->withInput();
        }

        $return->update([
            'quantity'      => $request->quantity,
            'refund_amount' => $saleItem->unit_price * $request->quantity,
            'type'          => $request->type,
            'reason'        => $request->reason,
        ]);

        activity()
            ->performedOn($return)
            ->causedBy(auth()->user())
            ->log('Updated return #' . $return->id);

        return redirect()->route('returns.index')
            ->with('success', 'Return updated successfully.');
    }

    public function approve(ProductReturn $return)
    {
        if ($return->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be approved.');
        }

        DB::transaction(function () use ($return) {
            // Restore stock if refund or exchange
            if (in_array($return->type, ['refund', 'exchange'])) {
                $stock = BranchStock::firstOrCreate(
                    [
                        'branch_id'  => $return->branch_id,
                        'product_id' => $return->product_id,
                    ],
                    ['quantity' => 0, 'last_updated' => now()]
                );

                $stock->incrementStock($return->quantity);

                StockMovement::record(
                    branchId     : $return->branch_id,
                    productId    : $return->product_id,
                    userId       : auth()->id(),
                    type         : 'return',
                    quantity     : $return->quantity,
                    balanceAfter : $stock->quantity,
                    referenceType: 'return',
                    referenceId  : $return->id,
                    notes        : 'Return approved — ' . $return->sale?->invoice_no
                );
            }

            $return->update([
                'status'       => 'approved',
                'processed_by' => auth()->id(),
            ]);

            activity()
                ->performedOn($return)
                ->causedBy(auth()->user())
                ->log('Approved return #' . $return->id);
        });

        return back()->with('success', 'Return approved. Stock restored.');
    }

    public function reject(ProductReturn $return)
    {
        if ($return->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be rejected.');
        }

        $return->update([
            'status'       => 'rejected',
            'processed_by' => auth()->id(),
        ]);

        activity()
            ->performedOn($return)
            ->causedBy(auth()->user())
            ->log('Rejected return #' . $return->id);

        return back()->with('success', 'Return rejected.');
    }

    public function destroy(ProductReturn $return)
    {
        if ($return->status !== 'pending') {
            return back()->with('error', 'Only pending returns can be deleted.');
        }
        return $this->softDelete($return, 'returns.index');
    }

    public function restore($id)
    {
        $return = ProductReturn::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($return, 'returns.index');
    }
}
