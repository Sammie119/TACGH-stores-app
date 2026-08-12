<?php

namespace App\Http\Controllers;

use App\Models\BankDeposit;
use App\Models\Branch;
use App\Models\ConsignmentPayment;
use App\Models\Sale;
use App\Models\User;
use App\Traits\HasBranchAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierAccountController extends Controller
{
    use HasBranchAccess;

    public function index(Request $request)
    {
        $this->authorize('view cashier accounts');

        $isSuperAdmin = $this->canViewAllBranches();
        $authUser     = auth()->user();

        // Default to this-financial-year-to-date when no filter was submitted;
        // an explicitly empty param (from the Clear link) leaves it unfiltered.
        $dateFrom = $request->has('date_from') ? $request->date_from : '2026-07-01';
        $dateTo   = $request->has('date_to')   ? $request->date_to   : now()->format('Y-m-d');

        $users = User::with('branch')
            ->where('is_active', true)
            ->when(!$isSuperAdmin, fn($q) => $q->where('branch_id', $authUser->branch_id))
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->orderBy('name')
            ->get();

        $accounts = $users->map(function ($cashier) use ($dateFrom, $dateTo) {
            $salesQ = Sale::where('user_id', $cashier->id)
                ->where('payment_method', 'cash')
                ->where('status', 'completed')
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo));

            $splitCash1Q = Sale::where('user_id', $cashier->id)
                ->where('payment_method', 'split')->where('split_method_1', 'cash')
                ->where('status', 'completed')
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo));

            $splitCash2Q = Sale::where('user_id', $cashier->id)
                ->where('payment_method', 'split')->where('split_method_2', 'cash')
                ->where('status', 'completed')
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo));

            $consignmentsQ = ConsignmentPayment::where('paid_by', $cashier->id)
                ->where('payment_method', 'cash')
                ->when($dateFrom, fn($q) => $q->whereDate('payment_date', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('payment_date', '<=', $dateTo));

            $depositsQ = BankDeposit::where('deposited_by', $cashier->id)
                ->where('status', 'verified')
                ->when($dateFrom, fn($q) => $q->whereDate('deposit_date', '>=', $dateFrom))
                ->when($dateTo,   fn($q) => $q->whereDate('deposit_date', '<=', $dateTo));

            // Cash actually retained, not cash tendered: amount_paid can exceed
            // total_amount when change was given back, which would otherwise
            // overstate what the cashier collected. (total_amount - balance_due)
            // equals total_amount when fully paid (change already excluded) and
            // equals amount_paid when a balance is still owed (no change given).
            $cashSales        = (clone $salesQ)->sum(DB::raw('total_amount - balance_due'))
                + (clone $splitCash1Q)->sum('split_amount_1')
                + (clone $splitCash2Q)->sum('split_amount_2');
            $salesCount       = (clone $salesQ)->count()
                + (clone $splitCash1Q)->count()
                + (clone $splitCash2Q)->count();
            $cashConsignments = (clone $consignmentsQ)->sum('amount');
            $consigCount      = (clone $consignmentsQ)->count();
            $verifiedDeposits = (clone $depositsQ)->sum('amount');
            $depositsCount    = (clone $depositsQ)->count();
            $outstanding      = $cashSales + $cashConsignments - $verifiedDeposits;

            return [
                'cashier'           => $cashier,
                'cash_sales'        => $cashSales,
                'sales_count'       => $salesCount,
                'cash_consignments' => $cashConsignments,
                'consig_count'      => $consigCount,
                'verified_deposits' => $verifiedDeposits,
                'deposits_count'    => $depositsCount,
                'outstanding'       => $outstanding,
            ];
        })
        ->filter(fn($a) => $a['cash_sales'] > 0 || $a['cash_consignments'] > 0 || $a['verified_deposits'] > 0)
        ->sortByDesc('outstanding')
        ->values();

        $branches = Branch::where('is_active', true)->get();

        $totals = [
            'cash_sales'        => $accounts->sum('cash_sales'),
            'cash_consignments' => $accounts->sum('cash_consignments'),
            'verified_deposits' => $accounts->sum('verified_deposits'),
            'outstanding'       => $accounts->sum('outstanding'),
        ];

        return view('cashier-accounts.index', compact(
            'accounts', 'totals', 'branches', 'isSuperAdmin', 'dateFrom', 'dateTo'
        ));
    }

    public function show(User $user, Request $request)
    {
        $this->authorize('view cashier accounts');

        $isSuperAdmin = $this->canViewAllBranches();
        $authUser     = auth()->user();

        if (!$isSuperAdmin && $user->branch_id !== $authUser->branch_id) {
            abort(403);
        }

        // Same default as the index page, so this page behaves consistently
        // whether reached via "View details" or visited/bookmarked directly.
        $dateFrom = $request->has('date_from') ? $request->date_from : '2026-07-01';
        $dateTo   = $request->has('date_to')   ? $request->date_to   : now()->format('Y-m-d');

        $sales = Sale::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where(fn($q) => $q
                ->where('payment_method', 'cash')
                ->orWhere(fn($q2) => $q2
                    ->where('payment_method', 'split')
                    ->where(fn($q3) => $q3
                        ->where('split_method_1', 'cash')
                        ->orWhere('split_method_2', 'cash')
                    )
                )
            )
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(20, ['*'], 'sales_page')
            ->withQueryString();

        $consignmentPayments = ConsignmentPayment::where('paid_by', $user->id)
            ->where('payment_method', 'cash')
            ->when($dateFrom, fn($q) => $q->whereDate('payment_date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('payment_date', '<=', $dateTo))
            ->with('consignment')
            ->latest('payment_date')
            ->paginate(20, ['*'], 'cpay_page')
            ->withQueryString();

        $deposits = BankDeposit::where('deposited_by', $user->id)
            ->when($dateFrom, fn($q) => $q->whereDate('deposit_date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('deposit_date', '<=', $dateTo))
            ->latest()
            ->paginate(20, ['*'], 'dep_page')
            ->withQueryString();

        // All-time totals (unaffected by date filter). Cash actually retained,
        // not cash tendered — see the equivalent computation in index() for why.
        $cashSales        = Sale::where('user_id', $user->id)->where('payment_method', 'cash')->where('status', 'completed')->sum(DB::raw('total_amount - balance_due'))
            + Sale::where('user_id', $user->id)->where('payment_method', 'split')->where('split_method_1', 'cash')->where('status', 'completed')->sum('split_amount_1')
            + Sale::where('user_id', $user->id)->where('payment_method', 'split')->where('split_method_2', 'cash')->where('status', 'completed')->sum('split_amount_2');
        $cashConsignments = ConsignmentPayment::where('paid_by', $user->id)->where('payment_method', 'cash')->sum('amount');
        $verifiedDeposits = BankDeposit::where('deposited_by', $user->id)->where('status', 'verified')->sum('amount');
        $outstanding      = $cashSales + $cashConsignments - $verifiedDeposits;

        return view('cashier-accounts.show', compact(
            'user', 'sales', 'consignmentPayments', 'deposits',
            'cashSales', 'cashConsignments', 'verifiedDeposits', 'outstanding', 'isSuperAdmin',
            'dateFrom', 'dateTo'
        ));
    }
}
