<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Customer;
use App\Models\FinancialYear;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockTransfer;
use App\Traits\HasBranchAccess;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use HasBranchAccess;

    public function index()
    {
        $user         = auth()->user();
        $branchId     = $user->branch_id;
        $isSuperAdmin = $this->canViewAllBranches();

        // ── Summary cards ─────────────────────────────────────
        $salesToday = Sale::query()
            ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $branchId))
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total_amount');

        $salesMonth = Sale::query()
            ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $branchId))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total_amount');

        $salesLastMonth = Sale::query()
            ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $branchId))
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('status', 'completed')
            ->sum('total_amount');

        $monthGrowth = $salesLastMonth > 0
            ? round((($salesMonth - $salesLastMonth) / $salesLastMonth) * 100, 1)
            : null;

        $totalProducts  = Product::where('is_active', true)->count();
        $totalCustomers = Customer::count();
        $activeYear     = FinancialYear::getActive();

        $lowStockCount = BranchStock::query()
            ->join('products', 'branch_stock.product_id', '=', 'products.id')
            ->whereColumn('branch_stock.quantity', '<=', 'products.reorder_level')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_stock.branch_id', $branchId))
            ->count();

        $pendingTransfers = StockTransfer::where('status', 'pending')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where(fn($q2) =>
            $q2->where('from_branch_id', $branchId)
                ->orWhere('to_branch_id', $branchId)
            )
            )->count();

        // ── Sales chart — last 30 days ─────────────────────────
        $salesLast30 = Sale::query()
            ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->subDays(29)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill all 30 days including zeros
        $salesChartLabels = [];
        $salesChartData   = [];
        $salesCountData   = [];
        for ($i = 29; $i >= 0; $i--) {
            $date               = now()->subDays($i)->format('Y-m-d');
            $salesChartLabels[] = now()->subDays($i)->format('d M');
            $salesChartData[]   = (float) ($salesLast30[$date]->total ?? 0);
            $salesCountData[]   = (int)   ($salesLast30[$date]->count ?? 0);
        }

        // ── Sales by payment method (this month) ──────────────
        $paymentBreakdown = Sale::query()
            ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('payment_method, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get();

        $paymentLabels = $paymentBreakdown->pluck('payment_method')
            ->map(fn($m) => ucfirst($m))->toArray();
        $paymentData   = $paymentBreakdown->pluck('total')
            ->map(fn($v) => (float) $v)->toArray();

        // ── Top 5 products this month ──────────────────────────
        $topProducts = DB::table('sale_items')
            ->join('sales',    'sale_items.sale_id',    '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('sales.branch_id', $branchId))
            ->whereMonth('sales.created_at', now()->month)
            ->where('sales.status', 'completed')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        // ── Branch performance (super admin only) ─────────────
        $branchPerformance = [];
        if ($isSuperAdmin) {
            $branchPerformance = Sale::query()
                ->with('branch')
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->selectRaw('branch_id, SUM(total_amount) as total, COUNT(*) as count')
                ->groupBy('branch_id')
                ->orderByDesc('total')
                ->get();
        }

        // ── Monthly trend — last 6 months ─────────────────────
        $monthlyTrend = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $total = Sale::query()
                ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $branchId))
                ->where('status', 'completed')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at',  $month->year)
                ->sum('total_amount');
            $monthlyTrend[]  = (float) $total;
            $monthlyLabels[] = $month->format('M Y');
        }

        // ── Stock value ────────────────────────────────────────
        $stockValue = BranchStock::join('products',
            'branch_stock.product_id', '=', 'products.id')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_stock.branch_id', $branchId))
            ->selectRaw('SUM(branch_stock.quantity * products.cost_price) as cost_value,
                         SUM(branch_stock.quantity * products.selling_price) as selling_value')
            ->first();

        // ── Low stock items ────────────────────────────────────
        $lowStockItems = BranchStock::with(['product', 'branch'])
            ->join('products', 'branch_stock.product_id', '=', 'products.id')
            ->whereColumn('branch_stock.quantity', '<=', 'products.reorder_level')
            ->where('products.is_active', true)
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_stock.branch_id', $branchId))
            ->select('branch_stock.*')
            ->orderBy('branch_stock.quantity')
            ->take(6)
            ->get();

        // ── Recent sales ───────────────────────────────────────
        $recentSales = Sale::with(['user', 'branch'])
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_id', $branchId))
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'salesToday', 'salesMonth', 'salesLastMonth', 'monthGrowth',
            'totalProducts', 'totalCustomers', 'activeYear',
            'lowStockCount', 'pendingTransfers',
            'salesChartLabels', 'salesChartData', 'salesCountData',
            'paymentLabels', 'paymentData',
            'topProducts', 'branchPerformance',
            'monthlyTrend', 'monthlyLabels',
            'stockValue', 'lowStockItems', 'recentSales',
            'isSuperAdmin'
        ));
    }
}
