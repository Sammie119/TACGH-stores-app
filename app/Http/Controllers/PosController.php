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
            'items.*.discount'   => 'nullable|numeric|min:0',
            'payment_method'     => 'required|in:cash,momo,bank,credit,split',
            'amount_paid'        => 'required|numeric|min:0',
            'customer_id'        => 'nullable|exists:customers,id',
            'discount'           => 'nullable|numeric|min:0',
        ]);

        $user          = auth()->user();
        $financialYear = FinancialYear::getActive();

        if (!$financialYear) {
            return back()->with('error', 'No active financial year.');
        }

        // Check financial year is not closed
        if ($financialYear->is_closed) {
            return back()->with('error', 'Financial year is closed. No transactions allowed.');
        }

        $sale = DB::transaction(function () use ($request, $user, $financialYear) {

            $totalAmount = 0;

            // Calculate totals and validate stock
            foreach ($request->items as $item) {
                $stock = BranchStock::where('branch_id', $user->branch_id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if (!$stock || $stock->quantity < $item['quantity']) {
                    $product  = Product::find($item['product_id']);
                    $available = $stock?->quantity ?? 0;
                    throw new \Exception(
                        "Insufficient stock for {$product->name}. Available: {$available}"
                    );
                }

                $itemDiscount = $item['discount'] ?? 0;
                $subtotal     = ($item['unit_price'] * $item['quantity']) - $itemDiscount;
                $totalAmount += $subtotal;
            }

            $overallDiscount = $request->discount ?? 0;
            $finalTotal      = max(0, $totalAmount - $overallDiscount);
            $amountPaid      = $request->amount_paid;
            $balanceDue      = max(0, $finalTotal - $amountPaid);

            // Determine status
            $status = 'completed';
            if ($amountPaid <= 0) {
                $status = 'credit';
            } elseif ($balanceDue > 0) {
                $status = 'partial';
            }

            // Create sale
            $sale = Sale::create([
                'invoice_no'       => Sale::generateInvoiceNo(),
                'branch_id'        => $user->branch_id,
                'user_id'          => $user->id,
                'customer_id'    => $request->customer_id ?: null,
                'walkin_name'    => !$request->customer_id
                    ? $request->walkin_name
                    : null, // ← add this
                'financial_year_id'=> $financialYear->id,
                'total_amount'     => $finalTotal,
                'discount'         => $overallDiscount,
                'amount_paid'      => $amountPaid,
                'balance_due'      => $balanceDue,
                'payment_method'   => $request->payment_method,
                'status'           => $status,
                'notes'            => $request->notes,
            ]);

            // Create items and deduct stock
            foreach ($request->items as $item) {
                $itemDiscount = $item['discount'] ?? 0;
                $subtotal     = ($item['unit_price'] * $item['quantity']) - $itemDiscount;

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount'   => $itemDiscount,
                    'subtotal'   => $subtotal,
                ]);

                // Deduct stock
                $stock = BranchStock::where('branch_id', $user->branch_id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                $stock->decrementStock($item['quantity']);

                StockMovement::record(
                    branchId     : $user->branch_id,
                    productId    : $item['product_id'],
                    userId       : $user->id,
                    type         : 'sale',
                    quantity     : -$item['quantity'],
                    balanceAfter : $stock->quantity,
                    referenceType: 'sale',
                    referenceId  : $sale->id,
                    notes        : 'Sale — ' . $sale->invoice_no
                );
            }

            // Update customer balance if credit
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
