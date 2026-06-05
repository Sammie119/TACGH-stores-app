<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierPayment::with(['supplier', 'purchaseOrder', 'paidBy']);

        if ($request->get('supplier_id')) {
            $query->where('supplier_id', $request->get('supplier_id'));
        }
        if ($request->get('date_from')) {
            $query->whereDate('payment_date', '>=', $request->get('date_from'));
        }
        if ($request->get('date_to')) {
            $query->whereDate('payment_date', '<=', $request->get('date_to'));
        }

        $payments   = $query->latest()->paginate(20)->withQueryString();
        $suppliers  = Supplier::where('is_active', true)->get();
        $totalPaid  = SupplierPayment::sum('amount');

        return view('supplier-payments.index', compact(
            'payments', 'suppliers', 'totalPaid'
        ));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::where('is_active', true)
            ->where('balance', '>', 0)
            ->get();

        $selectedSupplier = null;
        $openOrders       = collect();

        if ($request->get('supplier_id')) {
            $selectedSupplier = Supplier::find($request->get('supplier_id'));
            if ($selectedSupplier) {
                $openOrders = PurchaseOrder::where('supplier_id', $selectedSupplier->id)
                    ->whereIn('status', ['received', 'partial'])
                    ->where('balance_due', '>', 0)
                    ->get();
            }
        }

        return view('supplier-payments.create', compact(
            'suppliers', 'selectedSupplier', 'openOrders'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'       => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'amount'            => 'required|numeric|min:0.01',
            'payment_method'    => 'required|in:cash,momo,bank,cheque',
            'payment_date'      => 'required|date',
            'bank_reference'    => 'nullable|string|max:255',
            'notes'             => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            $payment = SupplierPayment::create([
                'reference'         => SupplierPayment::generateReference(),
                'supplier_id'       => $request->supplier_id,
                'purchase_order_id' => $request->purchase_order_id,
                'paid_by'           => auth()->id(),
                'amount'            => $request->amount,
                'payment_method'    => $request->payment_method,
                'payment_date'      => $request->payment_date,
                'bank_reference'    => $request->bank_reference,
                'notes'             => $request->notes,
            ]);

            // Reduce supplier balance
            $supplier = Supplier::find($request->supplier_id);
            $supplier->decrement('balance', $request->amount);

            // Update purchase order balance if linked
            if ($request->purchase_order_id) {
                $po = PurchaseOrder::find($request->purchase_order_id);
                $po->increment('amount_paid', $request->amount);
                $newBalance = max(0, $po->balance_due - $request->amount);
                $po->update([
                    'balance_due' => $newBalance,
                    'status'      => $newBalance <= 0 ? 'received' : $po->status,
                ]);
            }

            activity()
                ->performedOn($payment)
                ->causedBy(auth()->user())
                ->log('Supplier payment: GHS ' . number_format($request->amount, 2)
                    . ' to ' . $supplier->name);
        });

        return redirect()->route('supplier-payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(SupplierPayment $supplierPayment)
    {
        $supplierPayment->load(['supplier', 'purchaseOrder', 'paidBy']);
        return view('supplier-payments.show', compact('supplierPayment'));
    }
}
