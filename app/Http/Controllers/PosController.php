<?php

namespace App\Http\Controllers;

use App\Models\BranchStock;
use App\Models\Customer;
use App\Models\FinancialYear;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Must have an active financial year
        $financialYear = FinancialYear::getActive();
        if (!$financialYear) {
            return redirect()->route('financial-years.index')
                ->with('error', 'No active financial year. Please create or activate one first.');
        }

        // Must be assigned to a branch
        if (!$user->branch_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to a branch. Contact your administrator.');
        }

        $products  = Product::where('is_active', true)
            ->with(['category', 'stock' => fn($q) =>
            $q->where('branch_id', $user->branch_id)
            ])
            ->get()
            ->map(fn($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'sku'           => $p->sku,
                'selling_price' => $p->selling_price,
                'unit'          => $p->unit,
                'category'      => $p->category?->name,
                'stock'         => $p->stock->first()?->quantity ?? 0,
                'image'         => $p->image,
            ]);

        $customers = Customer::orderBy('name')->get();
        $categories = \App\Models\ProductCategory::where('is_active', true)->get();

        return view('pos.index', compact(
            'products', 'customers', 'categories', 'financialYear', 'user'
        ));
    }

    public function process(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.subtotal'   => 'required|numeric|min:0',
            'payment_method'     => 'required|in:cash,momo,bank,credit,split',
            'amount_paid'        => 'required|numeric|min:0',
            'customer_id'        => 'nullable|exists:customers,id',
            'walkin_name'        => 'nullable|string|max:100',
            'discount'           => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string|max:500',
        ]);

        $user          = auth()->user();
        $financialYear = FinancialYear::getActive();

        if (!$financialYear) {
            return back()->with('error', 'No active financial year.');
        }

        if ($financialYear->is_closed) {
            return back()->with('error',
                'Financial year is closed. No transactions allowed.');
        }

        $sale = DB::transaction(function () use ($request, $user, $financialYear) {

            // ── Validate stock & calculate total from subtotals ───────────
            // We trust the subtotal from JS because it already has item
            // discounts deducted: subtotal = (unit_price * qty) - item_discount
            $totalAmount = 0;

            foreach ($request->items as $item) {
                $stock = BranchStock::where('branch_id', $user->branch_id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if (!$stock || $stock->quantity < $item['quantity']) {
                    $product   = Product::find($item['product_id']);
                    $available = $stock?->quantity ?? 0;
                    throw new \Exception(
                        "Insufficient stock for {$product->name}. "
                        . "Available: {$available}"
                    );
                }

                // Use the subtotal sent from JS — already has item discount applied
                $totalAmount += (float) $item['subtotal'];
            }

            // ── Apply overall order discount ──────────────────────────────
            $overallDiscount = (float) ($request->discount ?? 0);
            $finalTotal      = max(0, $totalAmount - $overallDiscount);
            $amountPaid      = (float) $request->amount_paid;
            $balanceDue      = max(0, $finalTotal - $amountPaid);

            // ── Determine status ──────────────────────────────────────────
            $status = 'completed';
            if ($request->payment_method === 'credit' && $amountPaid <= 0) {
                $status = 'credit';
            } elseif ($balanceDue > 0.009) {
                // Use 0.009 threshold to avoid floating point rounding issues
                $status = 'partial';
            }

            // ── Create sale ───────────────────────────────────────────────
            $sale = Sale::create([
                'invoice_no'        => Sale::generateInvoiceNo(),
                'branch_id'         => $user->branch_id,
                'user_id'           => $user->id,
                'customer_id'       => $request->customer_id ?: null,
                'walkin_name'       => !$request->customer_id
                    ? $request->walkin_name
                    : null,
                'financial_year_id' => $financialYear->id,
                'total_amount'      => $finalTotal,
                'discount'          => $overallDiscount,
                'amount_paid'       => $amountPaid,
                'balance_due'       => $balanceDue,
                'payment_method'    => $request->payment_method,
                'status'            => $status,
                'notes'             => $request->notes,
            ]);

            // ── Create items and deduct stock ─────────────────────────────
            foreach ($request->items as $item) {
                $qty        = (float) $item['quantity'];
                $unitPrice  = (float) $item['unit_price'];
                $subtotal   = (float) $item['subtotal'];

                // Derive item discount from the difference
                // (unit_price * qty) - subtotal = item discount
                $itemDiscount = max(0, ($unitPrice * $qty) - $subtotal);

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $qty,
                    'unit_price' => $unitPrice,
                    'discount'   => $itemDiscount,
                    'subtotal'   => $subtotal,
                ]);

                // Deduct stock
                $stock = BranchStock::where('branch_id', $user->branch_id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                $stock->decrementStock($qty);

                StockMovement::record(
                    branchId     : $user->branch_id,
                    productId    : $item['product_id'],
                    userId       : $user->id,
                    type         : 'sale',
                    quantity     : -$qty,
                    balanceAfter : $stock->quantity,
                    referenceType: 'sale',
                    referenceId  : $sale->id,
                    notes        : 'Sale — ' . $sale->invoice_no
                );
            }

            // ── Update customer credit balance ────────────────────────────
            if ($request->customer_id && $balanceDue > 0) {
                $customer = Customer::find($request->customer_id);
                $customer->increment('balance', $balanceDue);
            }

            activity()
                ->performedOn($sale)
                ->causedBy($user)
                ->log('Processed sale: ' . $sale->invoice_no);

            return $sale;
        });

        return redirect()->route('pos.receipt', $sale)
            ->with('success', 'Sale completed. Invoice: ' . $sale->invoice_no);
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'user', 'branch']);
        return view('pos.receipt', compact('sale'));
    }
}
