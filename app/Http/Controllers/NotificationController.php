<?php

namespace App\Http\Controllers;

use App\Models\BranchStock;
use App\Models\StockTransfer;
use App\Models\ProductReturn;
use App\Models\BankDeposit;
use App\Traits\HasBranchAccess;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use HasBranchAccess;

    public function index()
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();
        $branchId     = $user->branch_id;

        // Low stock items
        $lowStock = BranchStock::with(['product', 'branch'])
            ->join('products', 'branch_stock.product_id', '=', 'products.id')
            ->whereColumn('branch_stock.quantity', '<=', 'products.reorder_level')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_stock.branch_id', $branchId))
            ->select('branch_stock.*')
            ->take(5)
            ->get();

        // Pending transfers
        $pendingTransfers = StockTransfer::with(['fromBranch', 'toBranch'])
            ->where('status', 'pending')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where(fn($q2) =>
            $q2->where('from_branch_id', $branchId)
                ->orWhere('to_branch_id', $branchId)
            ))
            ->latest()
            ->take(5)
            ->get();

        // Pending returns
        $pendingReturns = ProductReturn::with(['product', 'sale'])
            ->where('status', 'pending')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_id', $branchId))
            ->latest()
            ->take(5)
            ->get();

        // Pending deposits
        $pendingDeposits = BankDeposit::with('branch')
            ->where('status', 'pending')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_id', $branchId))
            ->latest()
            ->take(5)
            ->get();

        // Total count for badge
        $totalCount = $lowStock->count()
            + $pendingTransfers->count()
            + $pendingReturns->count()
            + $pendingDeposits->count();

        return response()->json([
            'total'            => $totalCount,
            'low_stock'        => $lowStock,
            'pending_transfers'=> $pendingTransfers,
            'pending_returns'  => $pendingReturns,
            'pending_deposits' => $pendingDeposits,
        ]);
    }
}
