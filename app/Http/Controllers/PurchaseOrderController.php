<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Traits\HasBranchAccess;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    use HasSoftDeleteActions, HasBranchAccess;

    public function index(Request $request)
    {
        $this->authorize('view purchase orders');

        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();

        if ($request->get('trashed')) {
            $query = PurchaseOrder::onlyTrashed()
                ->with(['supplier', 'branch', 'createdBy']);
        } else {
            $query = PurchaseOrder::with(['supplier', 'branch', 'createdBy']);
        }

        if (!$isSuperAdmin) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->get('supplier_id')) {
            $query->where('supplier_id', $request->get('supplier_id'));
        }
        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->get('branch_id')) {
            $query->where('branch_id', $request->get('branch_id'));
        }
        if ($request->get('date_from')) {
            $query->whereDate('order_date', '>=', $request->get('date_from'));
        }
        if ($request->get('date_to')) {
            $query->whereDate('order_date', '<=', $request->get('date_to'));
        }

        $orders         = $query->latest()->paginate(15)->withQueryString();
        $trashedCount   = PurchaseOrder::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');
        $suppliers      = Supplier::where('is_active', true)->get();
        $branches       = Branch::where('is_active', true)->get();
        $statuses       = ['draft','pending','approved','ordered','partial','received','cancelled'];

        $pendingCount   = PurchaseOrder::whereIn('status', ['pending'])->count();
        $overdueCount   = PurchaseOrder::whereIn('status', ['ordered', 'partial'])
            ->where('expected_date', '<', today())
            ->count();

        return view('purchase-orders.index', compact(
            'orders', 'trashedCount', 'showingTrashed',
            'suppliers', 'branches', 'statuses',
            'pendingCount', 'overdueCount', 'isSuperAdmin'
        ));
    }

    public function create()
    {
        $this->authorize('create purchase orders');

        $user      = auth()->user();
        $suppliers = Supplier::where('is_active', true)->get();
        $products  = Product::where('is_active', true)->get();
        $branches  = $this->canViewAllBranches()
            ? Branch::where('is_active', true)->get()
            : Branch::where('id', $user->branch_id)->get();

        return view('purchase-orders.create', compact(
            'suppliers', 'products', 'branches', 'user'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create purchase orders');

        $request->validate([
            'supplier_id'           => 'required|exists:suppliers,id',
            'branch_id'             => 'required|exists:branches,id',
            'order_date'            => 'required|date',
            'expected_date'         => 'nullable|date|after_or_equal:order_date',
            'notes'                 => 'nullable|string|max:500',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => 'required|numeric|min:0.01',
            'items.*.unit_cost'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $totalAmount = 0;

            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $po = PurchaseOrder::create([
                'po_number'     => PurchaseOrder::generatePoNumber(),
                'supplier_id'   => $request->supplier_id,
                'branch_id'     => $request->branch_id,
                'created_by'    => auth()->id(),
                'order_date'    => $request->order_date,
                'expected_date' => $request->expected_date,
                'total_amount'  => $totalAmount,
                'amount_paid'   => 0,
                'balance_due'   => $totalAmount,
                'status'        => 'pending',
                'notes'         => $request->notes,
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id'        => $item['product_id'],
                    'quantity_ordered'  => $item['quantity'],
                    'quantity_received' => 0,
                    'unit_cost'         => $item['unit_cost'],
                    'subtotal'          => $item['quantity'] * $item['unit_cost'],
                ]);
            }

            activity()
                ->performedOn($po)
                ->causedBy(auth()->user())
                ->log('Created purchase order: ' . $po->po_number);
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('view purchase orders');

        $purchaseOrder->load([
            'supplier', 'branch', 'createdBy',
            'approvedBy', 'items.product', 'payments.paidBy'
        ]);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('edit purchase orders');

        if (!in_array($purchaseOrder->status, ['draft', 'pending'])) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only draft or pending orders can be edited.');
        }

        $user      = auth()->user();
        $suppliers = Supplier::where('is_active', true)->get();
        $products  = Product::where('is_active', true)->get();
        $branches  = $this->canViewAllBranches()
            ? Branch::where('is_active', true)->get()
            : Branch::where('id', $user->branch_id)->get();

        return view('purchase-orders.edit', compact(
            'purchaseOrder', 'suppliers', 'products', 'branches'
        ));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorize('edit purchase orders');

        if (!in_array($purchaseOrder->status, ['draft', 'pending'])) {
            return back()->with('error', 'Only draft or pending orders can be edited.');
        }

        $request->validate([
            'supplier_id'        => 'required|exists:suppliers,id',
            'branch_id'          => 'required|exists:branches,id',
            'order_date'         => 'required|date',
            'expected_date'      => 'nullable|date|after_or_equal:order_date',
            'notes'              => 'nullable|string|max:500',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.unit_cost'  => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $purchaseOrder->update([
                'supplier_id'   => $request->supplier_id,
                'branch_id'     => $request->branch_id,
                'order_date'    => $request->order_date,
                'expected_date' => $request->expected_date,
                'total_amount'  => $totalAmount,
                'balance_due'   => $totalAmount - $purchaseOrder->amount_paid,
                'notes'         => $request->notes,
            ]);

            $purchaseOrder->items()->delete();

            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id'        => $item['product_id'],
                    'quantity_ordered'  => $item['quantity'],
                    'quantity_received' => 0,
                    'unit_cost'         => $item['unit_cost'],
                    'subtotal'          => $item['quantity'] * $item['unit_cost'],
                ]);
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase order updated.');
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('approve purchase orders');

        if ($purchaseOrder->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be approved.');
        }

        $purchaseOrder->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        activity()
            ->performedOn($purchaseOrder)
            ->causedBy(auth()->user())
            ->log('Approved purchase order: ' . $purchaseOrder->po_number);

        return back()->with('success', 'Purchase order approved.');
    }

    public function markOrdered(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('create purchase orders');

        if ($purchaseOrder->status !== 'approved') {
            return back()->with('error', 'Only approved orders can be marked as ordered.');
        }

        $purchaseOrder->update(['status' => 'ordered']);

        return back()->with('success', 'Purchase order marked as ordered.');
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorize('receive purchase orders');

        if (!in_array($purchaseOrder->status, ['approved', 'ordered', 'partial'])) {
            return back()->with('error', 'This order cannot be received.');
        }

        $request->validate([
            'received_quantities'    => 'required|array',
            'received_quantities.*'  => 'required|numeric|min:0',
            'received_unit_costs'    => 'nullable|array',
            'received_unit_costs.*'  => 'nullable|numeric|min:0',
            'received_date'          => 'required|date',
        ]);

        $purchaseOrder->load('items.product');

        DB::transaction(function () use ($request, $purchaseOrder) {
            $originalStatus  = $purchaseOrder->status;
            $oldBalanceDue   = $purchaseOrder->balance_due;

            $allReceived = true;
            $anyReceived = false;

            foreach ($purchaseOrder->items as $item) {
                // Quantity newly delivered in *this* receiving event (not a running total).
                $deltaReceived = (float) ($request->received_quantities[$item->id] ?? 0);

                if ($deltaReceived > 0) {
                    $anyReceived = true;

                    $unitCost = $request->received_unit_costs[$item->id] ?? null;
                    $unitCostChanged = $unitCost !== null && (float) $unitCost !== (float) $item->unit_cost;

                    $item->quantity_received += $deltaReceived;
                    if ($unitCostChanged) {
                        $item->unit_cost = $unitCost;
                        $item->subtotal  = $unitCost * $item->quantity_ordered;
                    }
                    $item->save();

                    // Add stock to branch
                    $stock = BranchStock::firstOrCreate(
                        [
                            'branch_id'  => $purchaseOrder->branch_id,
                            'product_id' => $item->product_id,
                        ],
                        ['quantity' => 0, 'last_updated' => now()]
                    );

                    $stock->incrementStock($deltaReceived);

                    // Update product cost price to reflect what was actually paid
                    $item->product->update(['cost_price' => $item->unit_cost]);

                    StockMovement::record(
                        branchId     : $purchaseOrder->branch_id,
                        productId    : $item->product_id,
                        userId       : auth()->id(),
                        type         : 'restock',
                        quantity     : $deltaReceived,
                        balanceAfter : $stock->quantity,
                        referenceType: 'purchase_order',
                        referenceId  : $purchaseOrder->id,
                        notes        : 'Received from PO: ' . $purchaseOrder->po_number
                    );
                }

                if ($item->quantity_received < $item->quantity_ordered) {
                    $allReceived = false;
                }
                if ($item->quantity_received > 0) {
                    $anyReceived = true;
                }
            }

            $status = $allReceived ? 'received' : ($anyReceived ? 'partial' : $purchaseOrder->status);

            $totalAmount = $purchaseOrder->items->sum('subtotal');
            $balanceDue  = $totalAmount - $purchaseOrder->amount_paid;

            $purchaseOrder->update([
                'status'        => $status,
                'received_date' => $request->received_date,
                'total_amount'  => $totalAmount,
                'balance_due'   => $balanceDue,
            ]);

            // Update supplier's outstanding balance. It's only added the first time
            // this order is (partially or fully) received; later receiving events on
            // the same order just adjust for any unit cost changes since then, so the
            // debt isn't double-counted across multiple partial receipts.
            if (in_array($originalStatus, ['approved', 'ordered'])) {
                $purchaseOrder->supplier->increment('balance', $balanceDue);
            } elseif ($balanceDue != $oldBalanceDue) {
                $purchaseOrder->supplier->increment('balance', $balanceDue - $oldBalanceDue);
            }

            activity()
                ->performedOn($purchaseOrder)
                ->causedBy(auth()->user())
                ->log('Received purchase order: ' . $purchaseOrder->po_number);
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Stock received and updated successfully.');
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('edit purchase orders');

        if (in_array($purchaseOrder->status, ['received', 'cancelled'])) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        $purchaseOrder->update(['status' => 'cancelled']);

        return back()->with('success', 'Purchase order cancelled.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorize('edit purchase orders');

        if (!in_array($purchaseOrder->status, ['draft', 'cancelled'])) {
            return back()->with('error', 'Only draft or cancelled orders can be deleted.');
        }
        return $this->softDelete($purchaseOrder, 'purchase-orders.index');
    }

    public function restore($id)
    {
        $this->authorize('edit purchase orders');

        $po = PurchaseOrder::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($po, 'purchase-orders.index');
    }
}
