<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductReturn;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\BankDeposit;
use App\Traits\HasBranchAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\SalesExport;
use App\Exports\InventoryExport;
use App\Exports\ConsignmentExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PurchaseOrderItem;
use App\Models\Consignment;

class ReportController extends Controller
{
    use HasBranchAccess;

    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();
        $canViewAll   = $user->can('view all branch sales');

        $query = Sale::with(['branch', 'user', 'customer'])
            ->when(!$isSuperAdmin && !$canViewAll, fn($q) =>
            $q->where('branch_id', $user->branch_id)
            );

        // Filters
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $sales = $query->latest()->paginate(25)->withQueryString();

        // Summary
        $summary = [
            'total_sales'    => (clone $query)->where('status', 'completed')->sum('total_amount'),
            'total_count'    => (clone $query)->where('status', 'completed')->count(),
            'total_discount' => (clone $query)->where('status', 'completed')->sum('discount'),
            'balance_due'    => (clone $query)->where('status', 'partial')->sum('balance_due'),
        ];

        // Approved returns/refunds, scoped by the same branch + date filters as the sales above
        $returnsQuery = ProductReturn::where('status', 'approved')
            ->when(!$isSuperAdmin && !$canViewAll, fn($q) =>
            $q->where('branch_id', $user->branch_id))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to));

        $summary['total_refunds'] = (clone $returnsQuery)->sum('refund_amount');
        $summary['net_sales']     = $summary['total_sales'] - $summary['total_refunds'];

        // Sales by payment method
        $byPayment = Sale::select('payment_method',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total'))
            ->where('status', 'completed')
            ->when(!$isSuperAdmin && !$canViewAll,
                fn($q) => $q->where('branch_id', $user->branch_id))
            ->when($request->date_from,
                fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,
                fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->groupBy('payment_method')
            ->get();

        // Top products
        $topProducts = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
            ->with('product')
            ->whereHas('sale', fn($q) =>
            $q->where('status', 'completed')
                ->when($request->date_from,
                    fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
                ->when($request->date_to,
                    fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            )
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        $branches = Branch::where('is_active', true)->get();

        $cashiers = User::orderBy('name')
            ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $user->branch_id))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->whereIn('id', Sale::distinct()->pluck('user_id'))
            ->get(['id', 'name']);

        return view('reports.sales', compact(
            'sales', 'summary', 'byPayment', 'topProducts',
            'branches', 'cashiers', 'isSuperAdmin'
        ));
    }

    public function inventory(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();

        $query = BranchStock::with(['product.category', 'branch'])
            ->join('products', 'branch_stock.product_id', '=', 'products.id')
            ->select('branch_stock.*')
            ->whereNull('products.deleted_at')
            ->where('products.is_active', true);

        if (!$isSuperAdmin) {
            $query->where('branch_stock.branch_id', $user->branch_id);
        }

        if ($request->branch_id) {
            $query->where('branch_stock.branch_id', $request->branch_id);
        }

        if ($request->category_id) {
            $query->where('products.category_id', $request->category_id);
        }

        if ($request->stock_status === 'low') {
            $query->whereColumn('branch_stock.quantity', '<=', 'products.reorder_level')
                ->where('branch_stock.quantity', '>', 0);
        } elseif ($request->stock_status === 'out') {
            $query->where('branch_stock.quantity', '<=', 0);
        }

        $stock = $query->orderBy('products.name')->paginate(25)->withQueryString();

        // Summary
        $totalValue = BranchStock::join('products', 'branch_stock.product_id', '=', 'products.id')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_stock.branch_id', $user->branch_id))
            ->selectRaw('SUM(branch_stock.quantity * products.cost_price) as cost_value,
                         SUM(branch_stock.quantity * products.selling_price) as selling_value')
            ->first();

        $lowCount = BranchStock::join('products', 'branch_stock.product_id', '=', 'products.id')
            ->whereColumn('branch_stock.quantity', '<=', 'products.reorder_level')
            ->where('branch_stock.quantity', '>', 0)
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_stock.branch_id', $user->branch_id))
            ->count();

        $outCount = BranchStock::where('quantity', '<=', 0)
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_id', $user->branch_id))
            ->count();

        $branches   = Branch::where('is_active', true)->get();
        $categories = ProductCategory::where('is_active', true)->get();

        return view('reports.inventory', compact(
            'stock', 'totalValue', 'lowCount', 'outCount',
            'branches', 'categories', 'isSuperAdmin'
        ));
    }

    public function transfers(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();

        $query = StockTransfer::with(['fromBranch', 'toBranch', 'requestedBy', 'items'])
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('from_branch_id', $user->branch_id)
                ->orWhere('to_branch_id', $user->branch_id)
            );

        if ($request->branch_id) {
            $query->where(fn($q) =>
            $q->where('from_branch_id', $request->branch_id)
                ->orWhere('to_branch_id', $request->branch_id)
            );
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transfers = $query->latest()->paginate(25)->withQueryString();
        $branches  = Branch::where('is_active', true)->get();

        return view('reports.transfers', compact(
            'transfers', 'branches', 'isSuperAdmin'
        ));
    }

    public function deposits(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();

        $query = BankDeposit::with(['branch', 'depositedBy', 'verifiedBy'])
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_id', $user->branch_id)
            );

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->date_from) {
            $query->whereDate('deposit_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('deposit_date', '<=', $request->date_to);
        }

        $deposits = $query->latest()->paginate(25)->withQueryString();

        $summary = [
            'total_verified' => (clone $query)->where('status', 'verified')->sum('amount'),
            'total_pending'  => (clone $query)->where('status', 'pending')->sum('amount'),
            'total_rejected' => (clone $query)->where('status', 'rejected')->sum('amount'),
        ];

        $branches = Branch::where('is_active', true)->get();

        return view('reports.deposits', compact(
            'deposits', 'summary', 'branches', 'isSuperAdmin'
        ));
    }

    public function exportSales(Request $request)
    {
        $this->authorize('export reports');

        $filename = 'sales-report-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new SalesExport($request->only([
                'branch_id', 'date_from', 'date_to', 'status', 'payment_method', 'user_id'
            ])),
            $filename
        );
    }

    public function exportInventory(Request $request)
    {
        $this->authorize('export reports');

        $filename = 'inventory-report-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new InventoryExport($request->only([
                'branch_id', 'category_id'
            ])),
            $filename
        );
    }

    // ── Product report ────────────────────────────────────────────
    public function product(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();

        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        $branches = Branch::where('is_active', true)->get();

        $selectedProduct = null;
        $stockLevel      = null;
        $salesData       = collect();
        $returnsData     = collect();
        $purchasesData   = collect();
        $movements       = collect();
        $summary         = [];

        if ($request->product_id) {
            $selectedProduct = Product::findOrFail($request->product_id);

            $branchId = $isSuperAdmin
                ? $request->branch_id
                : $user->branch_id;

            // ── Stock level ──────────────────────────────────────
            $stockQuery = BranchStock::with('branch')
                ->where('product_id', $request->product_id);

            if ($branchId) {
                $stockQuery->where('branch_id', $branchId);
            }

            $stockLevel = $stockQuery->get();

            // ── Sales of this product ────────────────────────────
            $salesQuery = \App\Models\SaleItem::with(['sale.branch', 'sale.user'])
                ->where('product_id', $request->product_id)
                ->whereHas('sale', function ($q) use ($request, $branchId) {
                    $q->where('status', 'completed');
                    if ($request->date_from) {
                        $q->whereDate('created_at', '>=', $request->date_from);
                    }
                    if ($request->date_to) {
                        $q->whereDate('created_at', '<=', $request->date_to);
                    }
                    if ($branchId) {
                        $q->where('branch_id', $branchId);
                    }
                });

            $salesData = $salesQuery->get();

            // ── Returns of this product ──────────────────────────
            $returnsQuery = \App\Models\ProductReturn::with(['sale', 'branch'])
                ->where('product_id', $request->product_id)
                ->where('status', 'approved');

            if ($request->date_from) {
                $returnsQuery->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->date_to) {
                $returnsQuery->whereDate('created_at', '<=', $request->date_to);
            }
            if ($branchId) {
                $returnsQuery->where('branch_id', $branchId);
            }

            $returnsData = $returnsQuery->get();

            // ── Purchases of this product ────────────────────────
            $purchasesData = PurchaseOrderItem::with([
                'purchaseOrder.supplier',
                'purchaseOrder.branch'
            ])
                ->where('product_id', $request->product_id)
                ->whereHas('purchaseOrder', function ($q) use ($request, $branchId) {
                    $q->whereIn('status', ['received', 'partial']);
                    if ($request->date_from) {
                        $q->whereDate('created_at', '>=', $request->date_from);
                    }
                    if ($request->date_to) {
                        $q->whereDate('created_at', '<=', $request->date_to);
                    }
                    if ($branchId) {
                        $q->where('branch_id', $branchId);
                    }
                })
                ->get();

            // ── Stock movements ──────────────────────────────────
            $movementsQuery = StockMovement::with(['branch', 'user'])
                ->where('product_id', $request->product_id);

            if ($request->date_from) {
                $movementsQuery->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->date_to) {
                $movementsQuery->whereDate('created_at', '<=', $request->date_to);
            }
            if ($branchId) {
                $movementsQuery->where('branch_id', $branchId);
            }

            $movements = $movementsQuery->latest()->get();

            // ── Summary ──────────────────────────────────────────
            $summary = [
                'total_qty_sold'     => $salesData->sum('quantity'),
                'total_revenue'      => $salesData->sum('subtotal'),
                'total_qty_returned' => $returnsData->sum('quantity'),
                'total_refunded'     => $returnsData->sum('refund_amount'),
                'total_qty_purchased'=> $purchasesData->sum('quantity_ordered'),
                'total_cost'         => $purchasesData->sum('subtotal'),
                'net_revenue'        => $salesData->sum('subtotal') - $returnsData->sum('refund_amount'),
                'gross_profit'       => $salesData->sum('subtotal') - $returnsData->sum('refund_amount')
                    - ($selectedProduct->cost_price * $salesData->sum('quantity')),
                'current_stock'      => $stockLevel->sum('quantity'),
                'stock_value'        => $stockLevel->sum('quantity') * $selectedProduct->cost_price,
            ];
        }

        return view('reports.product', compact(
            'products', 'branches', 'selectedProduct',
            'stockLevel', 'salesData', 'returnsData',
            'purchasesData', 'movements', 'summary',
            'isSuperAdmin'
        ));
    }

    // ── Profit & Loss report ──────────────────────────────────────
    public function profitLoss(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();

        $branchId  = $isSuperAdmin ? $request->branch_id : $user->branch_id;
        $dateFrom  = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo    = $request->date_to   ?? now()->format('Y-m-d');
        $branches  = Branch::where('is_active', true)->get();

        // ── Revenue ───────────────────────────────────────────────
        $salesQuery = Sale::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $totalRevenue    = (clone $salesQuery)->sum('total_amount');
        $totalDiscount   = (clone $salesQuery)->sum('discount');
        $totalTransactions = (clone $salesQuery)->count();

        // Revenue by payment method
        $revenueByPayment = (clone $salesQuery)
            ->selectRaw('payment_method, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        // ── Cost of goods sold ────────────────────────────────────
        $cogs = \App\Models\SaleItem::whereHas('sale', function ($q) use ($dateFrom, $dateTo, $branchId) {
            $q->where('status', 'completed')
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
        })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(sale_items.quantity * products.cost_price) as total_cost')
            ->first();

        $totalCOGS = $cogs->total_cost ?? 0;

        // ── Gross profit ──────────────────────────────────────────
        $grossProfit       = $totalRevenue - $totalCOGS;
        $grossProfitMargin = $totalRevenue > 0
            ? round(($grossProfit / $totalRevenue) * 100, 2)
            : 0;

        // ── Returns (refunds) ─────────────────────────────────────
        $returnsQuery = \App\Models\ProductReturn::where('status', 'approved')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $totalRefunds  = (clone $returnsQuery)->sum('refund_amount');
        $totalReturns  = (clone $returnsQuery)->count();

        // ── Purchases (expenses) ──────────────────────────────────
        $purchasesQuery = \App\Models\PurchaseOrder::whereIn('status', ['received', 'partial'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $totalPurchases = (clone $purchasesQuery)->sum('total_amount');
        $totalPOCount   = (clone $purchasesQuery)->count();

        // ── Net profit ────────────────────────────────────────────
        $netProfit       = $grossProfit - $totalRefunds;
        $netProfitMargin = $totalRevenue > 0
            ? round(($netProfit / $totalRevenue) * 100, 2)
            : 0;

        // ── Daily breakdown ───────────────────────────────────────
        $dailyBreakdown = Sale::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->selectRaw('DATE(created_at) as date,
                     COUNT(*) as transactions,
                     SUM(total_amount) as revenue,
                     SUM(discount) as discounts')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Top performing products ───────────────────────────────
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
            ->take(10)
            ->get();

        // ── Branch breakdown (super admin) ────────────────────────
        $branchBreakdown = [];
        if ($isSuperAdmin && !$branchId) {
            $branchBreakdown = Sale::where('status', 'completed')
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->with('branch')
                ->selectRaw('branch_id,
                         COUNT(*) as transactions,
                         SUM(total_amount) as revenue,
                         SUM(discount) as discounts')
                ->groupBy('branch_id')
                ->orderByDesc('revenue')
                ->get();
        }

        return view('reports.profit-loss', compact(
            'totalRevenue', 'totalDiscount', 'totalTransactions',
            'totalCOGS', 'grossProfit', 'grossProfitMargin',
            'totalRefunds', 'totalReturns',
            'totalPurchases', 'totalPOCount',
            'netProfit', 'netProfitMargin',
            'revenueByPayment', 'dailyBreakdown',
            'topProducts', 'branchBreakdown',
            'branches', 'isSuperAdmin',
            'dateFrom', 'dateTo', 'branchId'
        ));
    }

    // Add to app/Http/Controllers/ReportController.php

    public function stockBalance(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();
        $branchId     = $isSuperAdmin ? $request->branch_id : $user->branch_id;

        $query = BranchStock::with(['product.category', 'branch'])
            ->join('products', 'branch_stock.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select('branch_stock.*')
            ->whereNull('products.deleted_at')
            ->where('products.is_active', true)
            ->when($branchId, fn($q) =>
            $q->where('branch_stock.branch_id', $branchId))
            ->when($request->category_id, fn($q) =>
            $q->where('products.category_id', $request->category_id))
            ->when($request->stock_status === 'low', fn($q) =>
            $q->whereColumn('branch_stock.quantity', '<=', 'products.reorder_level')
                ->where('branch_stock.quantity', '>', 0))
            ->when($request->stock_status === 'out', fn($q) =>
            $q->where('branch_stock.quantity', '<=', 0))
            ->when($request->search, fn($q) =>
            $q->where(fn($q2) =>
            $q2->where('products.name', 'like', '%' . $request->search . '%')
                ->orWhere('products.sku', 'like', '%' . $request->search . '%')
            ))
            ->orderBy('product_categories.name')
            ->orderBy('products.name');

        $stock = $query->get();

        // Totals
        $totalCostValue    = $stock->sum(fn($s) =>
            $s->quantity * $s->product->cost_price);
        $totalSellingValue = $stock->sum(fn($s) =>
            $s->quantity * $s->product->selling_price);
        $totalItems        = $stock->count();
        $lowCount          = $stock->filter(fn($s) =>
            $s->quantity > 0 && $s->quantity <= $s->product->reorder_level)->count();
        $outCount          = $stock->filter(fn($s) =>
            $s->quantity <= 0)->count();

        $categories = ProductCategory::where('is_active', true)
            ->orderBy('name')->get();
        $branches   = Branch::where('is_active', true)->get();

        // Group by category for display
        $groupedStock = $stock->groupBy('product.category.name');

        return view('reports.stock-balance', compact(
            'stock', 'groupedStock', 'categories', 'branches',
            'totalCostValue', 'totalSellingValue',
            'totalItems', 'lowCount', 'outCount',
            'isSuperAdmin', 'branchId'
        ));
    }

    // ── Consignment report ────────────────────────────────────────
    public function consignments(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();
        $canViewAll   = $user->can('view all branch consignments');

        $query = Consignment::with(['branch', 'user', 'customer'])
            ->when(!$isSuperAdmin && !$canViewAll,
                fn($q) => $q->where('branch_id', $user->branch_id));

        if ($request->branch_id) { $query->where('branch_id', $request->branch_id); }
        if ($request->status)    { $query->where('status', $request->status); }
        if ($request->date_from) { $query->whereDate('created_at', '>=', $request->date_from); }
        if ($request->date_to)   { $query->whereDate('created_at', '<=', $request->date_to); }

        $consignments = $query->latest()->paginate(25)->withQueryString();

        $summary = [
            'total_value' => (clone $query)->sum('total_value'),
            'amount_paid' => (clone $query)->sum('amount_paid'),
            'balance_due' => (clone $query)->sum('balance_due'),
            'total_count' => (clone $query)->count(),
        ];

        $byStatus = Consignment::select('status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_value) as total_value'),
                DB::raw('SUM(balance_due) as balance_due'))
            ->when(!$isSuperAdmin && !$canViewAll,
                fn($q) => $q->where('branch_id', $user->branch_id))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->groupBy('status')
            ->get();

        $topDebtors = Consignment::select('customer_id', 'walkin_name',
                DB::raw('SUM(balance_due) as total_owed'),
                DB::raw('COUNT(*) as consignment_count'))
            ->with('customer')
            ->whereIn('status', ['dispatched', 'partial'])
            ->when(!$isSuperAdmin && !$canViewAll,
                fn($q) => $q->where('branch_id', $user->branch_id))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->groupBy('customer_id', 'walkin_name')
            ->orderByDesc('total_owed')
            ->take(10)
            ->get();

        $branches = Branch::where('is_active', true)->get();
        $statuses = ['pending', 'dispatched', 'partial', 'completed', 'cancelled'];

        return view('reports.consignments', compact(
            'consignments', 'summary', 'byStatus', 'topDebtors',
            'branches', 'statuses', 'isSuperAdmin'
        ));
    }

    public function exportConsignments(Request $request)
    {
        $this->authorize('export reports');
        return Excel::download(
            new ConsignmentExport($request->only(['branch_id', 'status', 'date_from', 'date_to'])),
            'consignment-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
