<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Branch;
use App\Models\FinancialYear;
use App\Traits\HasBranchAccess;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    use HasBranchAccess;

    public function index(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();
        $canViewAll   = $user->can('view all branch sales');

        if ($request->get('trashed')) {
            $query = Sale::onlyTrashed()->with(['user', 'branch', 'customer']);
        } else {
            $query = Sale::with(['user', 'branch', 'customer']);
        }

        // Restrict to own branch unless super admin or auditor
        if (!$isSuperAdmin && !$canViewAll) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->get('branch')) {
            $query->where('branch_id', $request->get('branch'));
        }

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->get('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        if ($request->get('search')) {
            $query->where(fn($q) =>
            $q->where('invoice_no', 'like', '%' . $request->get('search') . '%')
            );
        }

        if ($request->get('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->get('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $sales    = $query->latest()->paginate(20)->withQueryString();
        $branches = Branch::where('is_active', true)->get();
        $statuses = ['completed', 'partial', 'credit', 'cancelled'];
        $paymentMethods = ['cash', 'momo', 'bank', 'pos', 'split'];

        // Summary for today
        $todayQuery = Sale::where('status', 'completed')
            ->whereDate('created_at', today())
            ->when(!$isSuperAdmin && !$canViewAll, fn($q) =>
            $q->where('branch_id', $user->branch_id)
            );

        $todayTotal = (clone $todayQuery)->sum('total_amount');
        $todayCount = (clone $todayQuery)->count();

        $trashedCount   = Sale::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');

        return view('sales.index', compact(
            'sales', 'branches', 'statuses', 'paymentMethods',
            'todayTotal', 'todayCount', 'isSuperAdmin', 'canViewAll',
            'trashedCount', 'showingTrashed'
        ));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'user', 'branch', 'financialYear']);
        return view('sales.show', compact('sale'));
    }

    public function cancel(Sale $sale)
    {
        if ($sale->status === 'cancelled') {
            return back()->with('error', 'Sale is already cancelled.');
        }

        // Restore stock
        foreach ($sale->items as $item) {
            $stock = \App\Models\BranchStock::where('branch_id', $sale->branch_id)
                ->where('product_id', $item->product_id)
                ->first();

            if ($stock) {
                $stock->incrementStock($item->quantity);

                \App\Models\StockMovement::record(
                    branchId     : $sale->branch_id,
                    productId    : $item->product_id,
                    userId       : auth()->id(),
                    type         : 'adjustment',
                    quantity     : $item->quantity,
                    balanceAfter : $stock->quantity,
                    referenceType: 'sale_cancel',
                    referenceId  : $sale->id,
                    notes        : 'Sale cancelled — ' . $sale->invoice_no
                );
            }
        }

        $sale->update(['status' => 'cancelled']);

        activity()
            ->performedOn($sale)
            ->causedBy(auth()->user())
            ->log('Cancelled sale: ' . $sale->invoice_no);

        return back()->with('success', 'Sale cancelled and stock restored.');
    }

    public function restore($id)
    {
        $sale = Sale::onlyTrashed()->findOrFail($id);
        $sale->restore();
        return redirect()->route('sales.index')
            ->with('success', 'Sale restored.');
    }
}
