<?php

namespace App\Http\Controllers;

use App\Models\BankDeposit;
use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\ProductCategory;
use App\Models\ProductReturn;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\StockTake;
use App\Models\StockTransfer;
use App\Traits\HasBranchAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;

class PdfController extends Controller
{
    use HasBranchAccess;
    // Add this private helper method to PdfController
    private function getLogoBase64(?string $logoPath): ?string
    {
        if (!$logoPath) return null;

        $fullPath = storage_path('app/public/' . $logoPath);

        if (!file_exists($fullPath)) {
            // Try public path as fallback
            $fullPath = public_path('storage/' . $logoPath);
        }

        if (!file_exists($fullPath)) return null;

        $mimeType = mime_content_type($fullPath);
        $data     = base64_encode(file_get_contents($fullPath));

        return "data:{$mimeType};base64,{$data}";
    }

    // ── Receipt ───────────────────────────────────────────────────
    public function receipt(Sale $sale)
    {
        $sale->load(['branch', 'user', 'customer', 'items.product']);

        $logoBase64 = $this->getLogoBase64($sale->branch?->logo);

        $pdf = Pdf::loadView('pdf.receipt', compact('sale', 'logoBase64'))
            ->setPaper([0, 0, 283.46, 800], 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
                'dpi'                  => 150,
            ]);

        return $pdf->stream('receipt-' . $sale->invoice_no . '.pdf',
            ['Attachment' => false]);
    }

    // ── Sales report ──────────────────────────────────────────────
    public function salesReport(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();

        $query = Sale::with(['branch', 'user', 'customer'])
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_id', $user->branch_id))
            ->when($request->branch_id,
                fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->status,
                fn($q) => $q->where('status', $request->status))
            ->when($request->date_from,
                fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,
                fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest();

        $sales = $query->get();

        $summary = [
            'total_revenue'  => $sales->where('status', 'completed')->sum('total_amount'),
            'total_count'    => $sales->where('status', 'completed')->count(),
            'total_discount' => $sales->where('status', 'completed')->sum('discount'),
            'balance_due'    => $sales->where('status', 'partial')->sum('balance_due'),
        ];

        $byPayment = $sales->where('status', 'completed')
            ->groupBy('payment_method')
            ->map(fn($group) => [
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
            ]);

        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        $pdf = Pdf::loadView('pdf.sales-report', compact(
            'sales', 'summary', 'byPayment', 'dateFrom', 'dateTo', 'isSuperAdmin'
        ))->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'defaultFont'          => 'sans-serif',
            ]);

        return $pdf->stream('sales-report-' . now()->format('Y-m-d') . '.pdf');
    }

    // ── Inventory report ──────────────────────────────────────────
    public function inventoryReport(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();

        $stock = BranchStock::with(['product.category', 'branch'])
            ->join('products', 'branch_stock.product_id', '=', 'products.id')
            ->select('branch_stock.*')
            ->whereNull('products.deleted_at')
            ->where('products.is_active', true)
            ->when(!$isSuperAdmin,
                fn($q) => $q->where('branch_stock.branch_id', $user->branch_id))
            ->when($request->branch_id,
                fn($q) => $q->where('branch_stock.branch_id', $request->branch_id))
            ->when($request->category_id,
                fn($q) => $q->where('products.category_id', $request->category_id))
            ->orderBy('products.name')
            ->get();

        $totalCostValue    = $stock->sum(fn($s) => $s->quantity * $s->product->cost_price);
        $totalSellingValue = $stock->sum(fn($s) => $s->quantity * $s->product->selling_price);
        $categories        = ProductCategory::where('is_active', true)->get();

        $pdf = Pdf::loadView('pdf.inventory-report', compact(
            'stock', 'totalCostValue', 'totalSellingValue', 'isSuperAdmin'
        ))->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'defaultFont'          => 'sans-serif',
            ]);

        return $pdf->stream('inventory-report-' . now()->format('Y-m-d') . '.pdf');
    }

    // ── Purchase order ────────────────────────────────────────────
    public function purchaseOrder(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'supplier', 'branch', 'createdBy', 'approvedBy', 'items.product'
        ]);

        $pdf = Pdf::loadView('pdf.purchase-order', compact('purchaseOrder'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
                'dpi'                  => 150,
            ]);

        return $pdf->stream('PO-' . $purchaseOrder->po_number . '.pdf',
            ['Attachment' => false]);
    }

    // ── Stock transfer ────────────────────────────────────────────
    public function stockTransfer(StockTransfer $transfer)
    {
        $transfer->load([
            'fromBranch', 'toBranch', 'requestedBy', 'items.product'
        ]);

        $pdf = Pdf::loadView('pdf.stock-transfer', compact('transfer'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'defaultFont'          => 'sans-serif',
            ]);

        return $pdf->stream('Transfer-' . $transfer->reference_no . '.pdf');
    }

    // ── Stock take report ─────────────────────────────────────────
    public function stockTake(StockTake $stockTake)
    {
        $stockTake->load([
            'branch', 'createdBy', 'approvedBy', 'items.product.category'
        ]);

        $pdf = Pdf::loadView('pdf.stock-take', compact('stockTake'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'defaultFont'          => 'sans-serif',
            ]);

        return $pdf->stream('StockTake-' . $stockTake->reference . '.pdf');
    }

    // ── Return receipt ────────────────────────────────────────────
    public function returnReceipt(ProductReturn $return, Request $request)
    {
        $return->load([
            'sale.items.product',
            'sale.branch',
            'sale.user',
            'sale.customer',
            'product',
            'branch',
            'processedBy',
        ]);

        $logoBase64 = $this->getLogoBase64($return->sale->branch?->logo);

        $pdf = Pdf::loadView('pdf.return-receipt', compact('return', 'logoBase64'))
            ->setPaper([0, 0, 283.46, 800], 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
                'dpi'                  => 150,
            ]);

        $filename = 'return-receipt-' . $return->id . '.pdf';

        if ($request->get('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename, ['Attachment' => false]);
    }

    // ── Product report PDF ────────────────────────────────────────
    public function productReport(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();
        $branchId     = $isSuperAdmin ? $request->branch_id : $user->branch_id;

        $selectedProduct = Product::findOrFail($request->product_id);

        // Stock levels
        $stockLevel = BranchStock::with('branch')
            ->where('product_id', $selectedProduct->id)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        // Sales
        $salesData = \App\Models\SaleItem::with(['sale.branch', 'sale.user'])
            ->where('product_id', $selectedProduct->id)
            ->whereHas('sale', function ($q) use ($request, $branchId) {
                $q->where('status', 'completed');
                if ($request->date_from) $q->whereDate('created_at', '>=', $request->date_from);
                if ($request->date_to)   $q->whereDate('created_at', '<=', $request->date_to);
                if ($branchId)           $q->where('branch_id', $branchId);
            })->get();

        // Returns
        $returnsData = \App\Models\ProductReturn::with(['sale', 'branch'])
            ->where('product_id', $selectedProduct->id)
            ->where('status', 'approved')
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($branchId,           fn($q) => $q->where('branch_id', $branchId))
            ->get();

        // Purchases
        $purchasesData = PurchaseOrderItem::with(['purchaseOrder.supplier', 'purchaseOrder.branch'])
            ->where('product_id', $selectedProduct->id)
            ->whereHas('purchaseOrder', function ($q) use ($request, $branchId) {
                $q->whereIn('status', ['received', 'partial']);
                if ($request->date_from) $q->whereDate('created_at', '>=', $request->date_from);
                if ($request->date_to)   $q->whereDate('created_at', '<=', $request->date_to);
                if ($branchId)           $q->where('branch_id', $branchId);
            })->get();

        // Movements
        $movements = StockMovement::with(['branch', 'user'])
            ->where('product_id', $selectedProduct->id)
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($branchId,           fn($q) => $q->where('branch_id', $branchId))
            ->latest()->get();

        $summary = [
            'total_qty_sold'      => $salesData->sum('quantity'),
            'total_revenue'       => $salesData->sum('subtotal'),
            'total_qty_returned'  => $returnsData->sum('quantity'),
            'total_refunded'      => $returnsData->sum('refund_amount'),
            'total_qty_purchased' => $purchasesData->sum('quantity_ordered'),
            'total_cost'          => $purchasesData->sum('subtotal'),
            'net_revenue'         => $salesData->sum('subtotal') - $returnsData->sum('refund_amount'),
            'gross_profit'        => $salesData->sum('subtotal') - $returnsData->sum('refund_amount')
                - ($selectedProduct->cost_price * $salesData->sum('quantity')),
            'current_stock'       => $stockLevel->sum('quantity'),
            'stock_value'         => $stockLevel->sum('quantity') * $selectedProduct->cost_price,
        ];

        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        $pdf = Pdf::loadView('pdf.product-report', compact(
            'selectedProduct', 'stockLevel', 'salesData',
            'returnsData', 'purchasesData', 'movements',
            'summary', 'isSuperAdmin', 'dateFrom', 'dateTo'
        ))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'defaultFont'          => 'sans-serif',
                'dpi'                  => 150,
                'margin_top'           => 15,
                'margin_right'         => 15,
                'margin_bottom'        => 20,
                'margin_left'          => 15,
            ]);

        return $pdf->stream(
            'product-report-' . $selectedProduct->sku . '-' . now()->format('Y-m-d') . '.pdf',
            ['Attachment' => false]
        );
    }

    // ── Profit & Loss PDF ─────────────────────────────────────────
    public function profitLossReport(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();
        $branchId     = $isSuperAdmin ? $request->branch_id : $user->branch_id;
        $dateFrom     = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo       = $request->date_to   ?? now()->format('Y-m-d');

        // Revenue
        $salesQuery = Sale::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $totalRevenue      = (clone $salesQuery)->sum('total_amount');
        $totalDiscount     = (clone $salesQuery)->sum('discount');
        $totalTransactions = (clone $salesQuery)->count();

        $revenueByPayment  = (clone $salesQuery)
            ->selectRaw('payment_method, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')->get();

        // COGS
        $cogs = \App\Models\SaleItem::whereHas('sale', function ($q) use ($dateFrom, $dateTo, $branchId) {
            $q->where('status', 'completed')
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
        })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(sale_items.quantity * products.cost_price) as total_cost')
            ->first();

        $totalCOGS         = $cogs->total_cost ?? 0;
        $grossProfit       = $totalRevenue - $totalCOGS;
        $grossProfitMargin = $totalRevenue > 0
            ? round(($grossProfit / $totalRevenue) * 100, 2) : 0;

        // Returns
        $returnsQuery  = \App\Models\ProductReturn::where('status', 'approved')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $totalRefunds  = (clone $returnsQuery)->sum('refund_amount');
        $totalReturns  = (clone $returnsQuery)->count();

        // Purchases
        $totalPurchases = \App\Models\PurchaseOrder::whereIn('status', ['received', 'partial'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('total_amount');

        $netProfit       = $grossProfit - $totalRefunds;
        $netProfitMargin = $totalRevenue > 0
            ? round(($netProfit / $totalRevenue) * 100, 2) : 0;

        // Daily breakdown
        $dailyBreakdown = Sale::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->selectRaw('DATE(created_at) as date,
                     COUNT(*) as transactions,
                     SUM(total_amount) as revenue,
                     SUM(discount) as discounts')
            ->groupBy('date')->orderBy('date')->get();

        // Top products
        $topProducts = \App\Models\SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
            ->with('product')
            ->whereHas('sale', fn($q) =>
            $q->where('status', 'completed')
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            )
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->take(10)->get();

        // Branch breakdown
        $branchBreakdown = collect();
        if ($isSuperAdmin && !$branchId) {
            $branchBreakdown = Sale::where('status', 'completed')
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->with('branch')
                ->selectRaw('branch_id, COUNT(*) as transactions,
                         SUM(total_amount) as revenue,
                         SUM(discount) as discounts')
                ->groupBy('branch_id')
                ->orderByDesc('revenue')->get();
        }

        $branches = Branch::where('is_active', true)->get();

        $pdf = Pdf::loadView('pdf.profit-loss', compact(
            'totalRevenue', 'totalDiscount', 'totalTransactions',
            'totalCOGS', 'grossProfit', 'grossProfitMargin',
            'totalRefunds', 'totalReturns',
            'totalPurchases', 'netProfit', 'netProfitMargin',
            'revenueByPayment', 'dailyBreakdown', 'topProducts',
            'branchBreakdown', 'isSuperAdmin',
            'dateFrom', 'dateTo', 'branchId'
        ))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'defaultFont'          => 'sans-serif',
                'dpi'                  => 150,
                'margin_top'           => 15,
                'margin_right'         => 15,
                'margin_bottom'        => 20,
                'margin_left'          => 15,
            ]);

        return $pdf->stream(
            'profit-loss-' . $dateFrom . '-to-' . $dateTo . '.pdf',
            ['Attachment' => false]
        );
    }

    // ── Stock balance report PDF ──────────────────────────────────
    public function stockBalance(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();
        $branchId     = $isSuperAdmin ? $request->branch_id : $user->branch_id;

        $stock = BranchStock::with(['product.category', 'branch'])
            ->join('products', 'branch_stock.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select('branch_stock.*')
            ->whereNull('products.deleted_at')
            ->where('products.is_active', true)
            ->when($branchId,           fn($q) => $q->where('branch_stock.branch_id', $branchId))
            ->when($request->category_id, fn($q) => $q->where('products.category_id', $request->category_id))
            ->when($request->stock_status === 'low', fn($q) =>
            $q->whereColumn('branch_stock.quantity', '<=', 'products.reorder_level')
                ->where('branch_stock.quantity', '>', 0))
            ->when($request->stock_status === 'out', fn($q) =>
            $q->where('branch_stock.quantity', '<=', 0))
            ->orderBy('product_categories.name')
            ->orderBy('products.name')
            ->get();

        $groupedStock      = $stock->groupBy('product.category.name');
        $totalCostValue    = $stock->sum(fn($s) => $s->quantity * $s->product->cost_price);
        $totalSellingValue = $stock->sum(fn($s) => $s->quantity * $s->product->selling_price);
        $totalItems        = $stock->count();
        $isSuperAdmin      = $isSuperAdmin && !$branchId;

        $pdf = Pdf::loadView('pdf.stock-balance', compact(
            'stock', 'groupedStock', 'totalCostValue',
            'totalSellingValue', 'totalItems', 'isSuperAdmin'
        ))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'defaultFont'          => 'sans-serif',
                'dpi'                  => 150,
                'margin_top'           => 15,
                'margin_right'         => 15,
                'margin_bottom'        => 20,
                'margin_left'          => 15,
            ]);

        return $pdf->stream(
            'stock-balance-' . now()->format('Y-m-d') . '.pdf',
            ['Attachment' => false]
        );
    }
}
