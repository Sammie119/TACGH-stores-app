<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    use HasSoftDeleteActions;

    public function index(Request $request)
    {
        $query = Branch::with('manager');

        if ($request->get('trashed')) {
            $query->onlyTrashed();
        }

        $branches       = $query->latest()->paginate(15);
        $trashedCount   = Branch::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');

        return view('branches.index', compact('branches', 'trashedCount', 'showingTrashed'));
    }

    public function create()
    {
        $managers = User::where('is_active', true)->get();
        return view('branches.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:20|unique:branches,code',
            'address'    => 'nullable|string|max:500',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'is_active'  => 'boolean',
            'logo'       => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')
                ->store('branches/logos', 'public');
        }

        $branch = Branch::create($validated);

        activity()
            ->performedOn($branch)
            ->causedBy(auth()->user())
            ->log('Created branch: ' . $branch->name);

        return redirect()->route('branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function show(Branch $branch)
    {
        $branch->load('manager', 'users');

        $totalStockValue = $branch->stock()
            ->join('products', 'branch_stock.product_id', '=', 'products.id')
            ->selectRaw('SUM(branch_stock.quantity * products.cost_price) as total')
            ->value('total') ?? 0;

        $totalSales = \App\Models\Sale::where('branch_id', $branch->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        return view('branches.show', compact('branch', 'totalStockValue', 'totalSales'));
    }

    public function edit(Branch $branch)
    {
        $managers = User::where('is_active', true)->get();
        return view('branches.edit', compact('branch', 'managers'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:20|unique:branches,code,' . $branch->id,
            'address'    => 'nullable|string|max:500',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'is_active'  => 'boolean',
            'logo'       => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($branch->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')
                    ->delete($branch->logo);
            }
            $validated['logo'] = $request->file('logo')
                ->store('branches/logos', 'public');
        }

        $branch->update($validated);

        activity()
            ->performedOn($branch)
            ->causedBy(auth()->user())
            ->log('Updated branch: ' . $branch->name);

        return redirect()->route('branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        return $this->softDelete($branch, 'branches.index');
    }

    public function restore($id)
    {
        $branch = Branch::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($branch, 'branches.index');
    }

    public function forceDelete($id)
    {
        $branch = Branch::onlyTrashed()->findOrFail($id);
        return $this->forceDeleteModel($branch, 'branches.index');
    }
}
