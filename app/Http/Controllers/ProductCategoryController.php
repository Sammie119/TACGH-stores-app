<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    use HasSoftDeleteActions;

    public function index(Request $request)
    {
        if ($request->get('trashed')) {
            $query = ProductCategory::onlyTrashed()->with(['parent', 'children']);
        } else {
            $query = ProductCategory::with(['parent', 'children'])
                ->withCount('products');
        }

        if ($request->get('search')) {
            $query->where('name', 'like', '%' . $request->get('search') . '%');
        }

        $categories     = $query->orderByRaw('ISNULL(parent_id) DESC, parent_id, name')
            ->paginate(20)->withQueryString();
        $trashedCount   = ProductCategory::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');

        return view('categories.index', compact(
            'categories', 'trashedCount', 'showingTrashed'
        ));
    }

    public function create()
    {
        // Only root categories can be parents
        $parentCategories = ProductCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        // Prevent making a child a parent of another child
        if ($request->parent_id) {
            $parent = ProductCategory::find($request->parent_id);
            if ($parent && !is_null($parent->parent_id)) {
                return back()->with('error',
                    'Only top-level categories can be parent categories.')
                    ->withInput();
            }
        }

        $category = ProductCategory::create([
            'name'        => $request->name,
            'parent_id'   => $request->parent_id ?: null,
            'description' => $request->description,
            'is_active'   => $request->has('is_active'),
        ]);

        activity()
            ->performedOn($category)
            ->causedBy(auth()->user())
            ->log('Created category: ' . $category->name);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(ProductCategory $category)
    {
        // Only root categories as parent options, excluding self
        $parentCategories = ProductCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, ProductCategory $category)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        // Prevent circular reference
        if ($request->parent_id == $category->id) {
            return back()->with('error',
                'A category cannot be its own parent.')
                ->withInput();
        }

        // Prevent making a child a parent
        if ($request->parent_id) {
            $parent = ProductCategory::find($request->parent_id);
            if ($parent && !is_null($parent->parent_id)) {
                return back()->with('error',
                    'Only top-level categories can be parent categories.')
                    ->withInput();
            }
        }

        // If changing to a parent, remove its own parent_id
        $category->update([
            'name'        => $request->name,
            'parent_id'   => $request->parent_id ?: null,
            'description' => $request->description,
            'is_active'   => $request->has('is_active'),
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(ProductCategory $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error',
                'Cannot delete a category that has products assigned to it.');
        }

        return $this->softDelete($category, 'categories.index');
    }

    public function restore($id)
    {
        $category = ProductCategory::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($category, 'categories.index');
    }

    public function forceDelete($id)
    {
        $category = ProductCategory::onlyTrashed()->findOrFail($id);
        return $this->forceDeleteModel($category, 'categories.index');
    }
}
