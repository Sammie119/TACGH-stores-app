<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\FinancialYear;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $user     = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin');

        $query = BranchStock::with(['product.category', 'branch'])
            ->join('products', 'branch_stock.product_id', '=', 'products.id')
            ->select('branch_stock.*')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at');

        // Non-admins only see their branch
        if (!$isSuperAdmin) {
            $query->where('branch_stock.branch_id', $user->branch_id);
        }

        if ($request->get('branch')) {
            $query->where('branch_stock.branch_id', $request->get('branch'));
        }

        if ($request->get('category')) {
            $query->where('products.category_id', $request->get('category'));
        }

        if ($request->get('search')) {
            $query->where(fn($q) =>
            $q->where('products.name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('products.sku', 'like', '%' . $request->get('search') . '%')
            );
        }

        if ($request->get('stock') === 'low') {
            $query->whereColumn('branch_stock.quantity', '<=', 'products.reorder_level');
        }

        if ($request->get('stock') === 'out') {
            $query->where('branch_stock.quantity', '<=', 0);
        }

        $stock      = $query->latest('branch_stock.last_updated')->paginate(20)->withQueryString();
        $branches   = Branch::where('is_active', true)->get();
        $categories = \App\Models\ProductCategory::where('is_active', true)->get();

        // Summary stats
        $totalItems     = $query->count();
        $lowStockCount  = (clone $query)->whereColumn('branch_stock.quantity', '<=', 'products.reorder_level')->count();
        $outOfStock     = (clone $query)->where('branch_stock.quantity', '<=', 0)->count();

        return view('inventory.index', compact(
            'stock', 'branches', 'categories',
            'totalItems', 'lowStockCount', 'outOfStock', 'isSuperAdmin'
        ));
    }

    public function adjust(Product $product)
    {
        $user     = auth()->user();
        $branches = $user->hasRole('super_admin')
            ? Branch::where('is_active', true)->get()
            : Branch::where('id', $user->branch_id)->get();

        $currentStock = BranchStock::with('branch')
            ->where('product_id', $product->id)
            ->when(!$user->hasRole('super_admin'), fn($q) =>
            $q->where('branch_id', $user->branch_id)
            )
            ->get();

        return view('inventory.adjust', compact('product', 'branches', 'currentStock'));
    }

    public function storeAdjustment(Request $request, Product $product)
    {
        $request->validate([
            'branch_id'          => 'required|exists:branches,id',
            'type'               => 'required|in:add,subtract,set,restock',
            'quantity'           => 'required|numeric|min:0',
            'notes'              => 'nullable|string|max:500',
            'supplier_name'      => 'nullable|string|max:255',
            'supplier_reference' => 'nullable|string|max:255',
        ]);

        $stock = BranchStock::firstOrCreate(
            [
                'branch_id'  => $request->branch_id,
                'product_id' => $product->id,
            ],
            ['quantity' => 0, 'last_updated' => now()]
        );

        $before = $stock->quantity;

        switch ($request->type) {
            case 'add':
            case 'restock':
                $stock->quantity += $request->quantity;
                break;
            case 'subtract':
                $stock->quantity = max(0, $stock->quantity - $request->quantity);
                break;
            case 'set':
                $stock->quantity = $request->quantity;
                break;
        }

        $stock->last_updated = now();
        $stock->save();

        // Build note
        $note = $request->notes;
        if ($request->type === 'restock') {
            $parts = array_filter([
                'Restock from supplier',
                $request->supplier_name,
                $request->supplier_reference
                    ? '(Ref: ' . $request->supplier_reference . ')'
                    : null,
            ]);
            $note = implode(' — ', $parts) . ($request->notes ? '. ' . $request->notes : '');
        }

        StockMovement::record(
            branchId      : $request->branch_id,
            productId     : $product->id,
            userId        : auth()->id(),
            type          : $request->type === 'restock' ? 'restock' : 'adjustment',
            quantity      : $stock->quantity - $before,
            balanceAfter  : $stock->quantity,
            referenceType : 'adjustment',
            referenceId   : null,
            notes         : $note ?? "Manual adjustment ({$request->type})"
        );

        activity()
            ->performedOn($product)
            ->causedBy(auth()->user())
            ->withProperties([
                'branch_id'          => $request->branch_id,
                'type'               => $request->type,
                'quantity'           => $request->quantity,
                'before'             => $before,
                'after'              => $stock->quantity,
                'supplier_name'      => $request->supplier_name,
                'supplier_reference' => $request->supplier_reference,
            ])
            ->log('Stock ' . $request->type . ': ' . $product->name);

        return redirect()->route('inventory.index')
            ->with('success',
                ucfirst($request->type) . " successful. {$product->name}: " .
                number_format($before, 2) . " → " . number_format($stock->quantity, 2) .
                " {$product->unit}"
            );
    }

    public function movements(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin');

        $query = StockMovement::with(['product', 'branch', 'user'])
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_id', $user->branch_id)
            );

        if ($request->get('branch')) {
            $query->where('branch_id', $request->get('branch'));
        }

        if ($request->get('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->get('search')) {
            $query->whereHas('product', fn($q) =>
            $q->where('name', 'like', '%' . $request->get('search') . '%')
            );
        }

        if ($request->get('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->get('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $movements  = $query->latest()->paginate(25)->withQueryString();
        $branches   = Branch::where('is_active', true)->get();
        $types      = ['sale', 'transfer_in', 'transfer_out', 'adjustment', 'return', 'opening'];

        return view('inventory.movements', compact('movements', 'branches', 'types'));
    }
}
