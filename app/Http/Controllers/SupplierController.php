<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use HasSoftDeleteActions;

    public function index(Request $request)
    {
        if ($request->get('trashed')) {
            $query = Supplier::onlyTrashed();
        } else {
            $query = Supplier::query();
        }

        if ($request->get('search')) {
            $query->where(fn($q) =>
            $q->where('name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('code', 'like', '%' . $request->get('search') . '%')
                ->orWhere('phone', 'like', '%' . $request->get('search') . '%')
            );
        }

        $suppliers      = $query->withCount('purchaseOrders')->latest()->paginate(15)->withQueryString();
        $trashedCount   = Supplier::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');

        $totalOwed = Supplier::where('balance', '>', 0)->sum('balance');

        return view('suppliers.index', compact(
            'suppliers', 'trashedCount', 'showingTrashed', 'totalOwed'
        ));
    }

    public function create()
    {
        $code = Supplier::generateCode();
        return view('suppliers.create', compact('code'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:20|unique:suppliers,code',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'is_active'      => 'boolean',
        ]);

        $supplier = Supplier::create([
            'name'           => $request->name,
            'code'           => $request->code,
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'is_active'      => $request->has('is_active'),
        ]);

        activity()
            ->performedOn($supplier)
            ->causedBy(auth()->user())
            ->log('Created supplier: ' . $supplier->name);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['purchaseOrders.branch', 'payments.paidBy']);

        $totalOrders   = $supplier->purchaseOrders()->count();
        $totalPurchased = $supplier->purchaseOrders()
            ->whereIn('status', ['received', 'partial'])
            ->sum('total_amount');
        $totalPaid     = $supplier->payments()->sum('amount');

        return view('suppliers.show', compact(
            'supplier', 'totalOrders', 'totalPurchased', 'totalPaid'
        ));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:20|unique:suppliers,code,' . $supplier->id,
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'is_active'      => 'boolean',
        ]);

        $supplier->update([
            'name'           => $request->name,
            'code'           => $request->code,
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'is_active'      => $request->has('is_active'),
        ]);

        activity()
            ->performedOn($supplier)
            ->causedBy(auth()->user())
            ->log('Updated supplier: ' . $supplier->name);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        return $this->softDelete($supplier, 'suppliers.index');
    }

    public function restore($id)
    {
        $supplier = Supplier::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($supplier, 'suppliers.index');
    }

    public function forceDelete($id)
    {
        $supplier = Supplier::onlyTrashed()->findOrFail($id);
        return $this->forceDeleteModel($supplier, 'suppliers.index');
    }
}
