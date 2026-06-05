<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Branch;
use App\Models\BranchStock;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use HasSoftDeleteActions;

    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->get('trashed')) {
            $query->onlyTrashed();
        }

        if ($request->get('search')) {
            $query->where(fn($q) =>
            $q->where('name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('sku', 'like', '%' . $request->get('search') . '%')
                ->orWhere('barcode', 'like', '%' . $request->get('search') . '%')
            );
        }

        if ($request->get('category')) {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->get('stock') === 'low') {
            $query->whereHas('stock', fn($q) =>
            $q->whereColumn('quantity', '<=', 'products.reorder_level')
            );
        }

        $products       = $query->latest()->paginate(20)->withQueryString();
        $trashedCount   = Product::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');
        $categories     = ProductCategory::where('is_active', true)->get();

        return view('products.index', compact(
            'products', 'trashedCount', 'showingTrashed', 'categories'
        ));
    }

    public function create()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|max:100|unique:products,sku',
            'barcode'       => 'nullable|string|max:100|unique:products,barcode',
            'category_id'   => 'required|exists:product_categories,id',
            'unit'          => 'required|string|max:50',
            'cost_price'    => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'description'   => 'nullable|string|max:2000',
            'image'         => 'nullable|image|max:2048',
            'is_active'     => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('products', 'public');
        }

        $product = Product::create($validated);

        // Initialise zero stock for all active branches
        $branches = Branch::where('is_active', true)->get();
        foreach ($branches as $branch) {
            BranchStock::firstOrCreate(
                ['branch_id' => $branch->id, 'product_id' => $product->id],
                ['quantity' => 0, 'last_updated' => now()]
            );
        }

        activity()
            ->performedOn($product)
            ->causedBy(auth()->user())
            ->log('Created product: ' . $product->name);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load('category');

        $branchStock = BranchStock::with('branch')
            ->where('product_id', $product->id)
            ->get();

        $totalStock = $branchStock->sum('quantity');

        return view('products.show', compact('product', 'branchStock', 'totalStock'));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::where('is_active', true)->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|max:100|unique:products,sku,' . $product->id,
            'barcode'       => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'category_id'   => 'required|exists:product_categories,id',
            'unit'          => 'required|string|max:50',
            'cost_price'    => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'description'   => 'nullable|string|max:2000',
            'image'         => 'nullable|image|max:2048',
            'is_active'     => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('products', 'public');
        }

        $product->update($validated);

        activity()
            ->performedOn($product)
            ->causedBy(auth()->user())
            ->log('Updated product: ' . $product->name);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        return $this->softDelete($product, 'products.index');
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($product, 'products.index');
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        return $this->forceDeleteModel($product, 'products.index');
    }
}
