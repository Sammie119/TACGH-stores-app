<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use HasSoftDeleteActions;

    public function index(Request $request)
    {
        if ($request->get('trashed')) {
            $query = Customer::onlyTrashed();
        } else {
            $query = Customer::query();
        }

        if ($request->get('search')) {
            $query->where(fn($q) =>
            $q->where('name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('phone', 'like', '%' . $request->get('search') . '%')
                ->orWhere('email', 'like', '%' . $request->get('search') . '%')
            );
        }

        $customers      = $query->latest()->paginate(20)->withQueryString();
        $trashedCount   = Customer::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');

        return view('customers.index', compact(
            'customers', 'trashedCount', 'showingTrashed'
        ));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        Customer::create($request->only(['name', 'phone', 'email', 'address']));

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load('sales');

        $totalSales    = $customer->sales()->where('status', 'completed')->sum('total_amount');
        $totalOrders   = $customer->sales()->where('status', 'completed')->count();
        $recentSales   = $customer->sales()->with('branch')->latest()->take(5)->get();

        return view('customers.show', compact(
            'customer', 'totalSales', 'totalOrders', 'recentSales'
        ));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $customer->update($request->only(['name', 'phone', 'email', 'address']));

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        return $this->softDelete($customer, 'customers.index');
    }

    public function restore($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($customer, 'customers.index');
    }

    public function forceDelete($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        return $this->forceDeleteModel($customer, 'customers.index');
    }
}
