<?php

namespace App\Http\Controllers;

use App\Models\BranchStock;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\ConsignmentPayment;
use App\Models\Customer;
use App\Models\FinancialYear;
use App\Models\Product;
use App\Models\StockMovement;
use App\Traits\HasBranchAccess;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsignmentController extends Controller
{
    use HasSoftDeleteActions, HasBranchAccess;

    public function index(Request $request)
    {
        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();
        $canViewAll   = $user->can('view all branch consignments');

        if ($request->get('trashed')) {
            $query = Consignment::onlyTrashed()->with(['branch', 'user', 'customer', 'items', 'payments']);
        } else {
            $query = Consignment::with(['branch', 'user', 'customer', 'items', 'payments']);
        }

        if (!$isSuperAdmin && !$canViewAll) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->get('branch') && ($isSuperAdmin || $canViewAll)) {
            $query->where('branch_id', $request->get('branch'));
        }

        if ($request->get('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->get('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        if ($request->get('search')) {
            $query->where('reference_no', 'like', '%' . $request->get('search') . '%');
        }

        $consignments   = $query->latest()->paginate(15)->withQueryString();
        $trashedCount   = Consignment::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');

        $branchScope = fn($q) => $q->when(!$isSuperAdmin && !$canViewAll, fn($q2) => $q2->where('branch_id', $user->branch_id));

        $pendingCount       = Consignment::where('status', 'pending')->tap($branchScope)->count();
        $dispatchedCount    = Consignment::where('status', 'dispatched')->tap($branchScope)->count();
        $outstandingBalance = Consignment::whereIn('status', ['dispatched', 'partial'])->tap($branchScope)->sum('balance_due');

        $branches = \App\Models\Branch::where('is_active', true)->get();
        $statuses = ['pending', 'dispatched', 'partial', 'completed', 'cancelled'];

        return view('consignments.index', compact(
            'consignments', 'branches', 'statuses', 'trashedCount', 'showingTrashed',
            'pendingCount', 'dispatchedCount', 'outstandingBalance', 'isSuperAdmin', 'canViewAll'
        ));
    }

    public function create()
    {
        $financialYear = FinancialYear::getActive();
        if (!$financialYear) {
            return redirect()->route('consignments.index')
                ->with('error', 'No active financial year. Please activate one before creating a consignment.');
        }

        $user = auth()->user();
        if (!$user->branch_id) {
            return redirect()->route('consignments.index')
                ->with('error', 'Your account is not assigned to a branch.');
        }

        $customers = Customer::orderBy('name')->get();
        $products  = Product::where('is_active', true)->orderBy('name')->get();

        return view('consignments.create', compact('customers', 'products', 'user', 'financialYear'));
    }

    public function store(Request $request)
    {
        // Strip rows where no product was selected (user added a blank row and didn't fill it)
        $items = collect($request->input('items', []))
            ->filter(fn($item) => !empty($item['product_id']))
            ->values()
            ->toArray();
        $request->merge(['items' => $items]);

        $request->validate([
            'customer_id'          => 'nullable|exists:customers,id',
            'walkin_name'          => 'nullable|string|max:100',
            'notes'                => 'nullable|string|max:500',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ]);

        $productIds = array_column($request->items, 'product_id');
        if (count($productIds) !== count(array_unique($productIds))) {
            return back()->withErrors(['items' => 'Duplicate products found. Each product can only appear once.'])->withInput();
        }

        $financialYear = FinancialYear::getActive();
        if (!$financialYear) {
            return back()->with('error', 'No active financial year found.')->withInput();
        }

        $consignment = null;

        DB::transaction(function () use ($request, $financialYear, &$consignment) {
            $totalValue = collect($request->items)
                ->sum(fn($item) => $item['quantity'] * $item['unit_price']);

            $consignment = Consignment::create([
                'reference_no'     => Consignment::generateReferenceNo(),
                'branch_id'        => auth()->user()->branch_id,
                'user_id'          => auth()->id(),
                'customer_id'      => $request->customer_id,
                'walkin_name'      => $request->customer_id ? null : $request->walkin_name,
                'financial_year_id'=> $financialYear->id,
                'total_value'      => $totalValue,
                'amount_paid'      => 0,
                'balance_due'      => $totalValue,
                'status'           => 'pending',
                'notes'            => $request->notes,
            ]);

            foreach ($request->items as $item) {
                ConsignmentItem::create([
                    'consignment_id' => $consignment->id,
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'subtotal'       => $item['quantity'] * $item['unit_price'],
                ]);
            }

            activity()
                ->performedOn($consignment)
                ->causedBy(auth()->user())
                ->log('Created consignment: ' . $consignment->reference_no);
        });

        return redirect()->route('consignments.show', $consignment)
            ->with('success', 'Consignment ' . $consignment->reference_no . ' created successfully.');
    }

    public function show(Consignment $consignment)
    {
        $consignment->load(['branch', 'user', 'customer', 'financialYear', 'items.product', 'payments.paidBy']);
        $products = $consignment->status === 'pending'
            ? Product::where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('consignments.show', compact('consignment', 'products'));
    }

    public function addItem(Request $request, Consignment $consignment)
    {
        if ($consignment->status !== 'pending') {
            return back()->with('error', 'Products can only be added to pending consignments.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $alreadyExists = $consignment->items()->where('product_id', $request->product_id)->exists();
        if ($alreadyExists) {
            return back()->withErrors(['product_id' => 'This product is already in the consignment.']);
        }

        DB::transaction(function () use ($request, $consignment) {
            ConsignmentItem::create([
                'consignment_id' => $consignment->id,
                'product_id'     => $request->product_id,
                'quantity'       => $request->quantity,
                'unit_price'     => $request->unit_price,
                'subtotal'       => $request->quantity * $request->unit_price,
            ]);

            $total = $consignment->items()->sum('subtotal');
            $consignment->update([
                'total_value' => $total,
                'balance_due' => max(0, $total - (float) $consignment->amount_paid),
            ]);

            activity()
                ->performedOn($consignment)
                ->causedBy(auth()->user())
                ->log('Added item to consignment: ' . $consignment->reference_no);
        });

        return back()->with('success', 'Product added successfully.');
    }

    public function removeItem(Consignment $consignment, ConsignmentItem $item)
    {
        if ($consignment->status !== 'pending') {
            return back()->with('error', 'Products can only be removed from pending consignments.');
        }

        if ($item->consignment_id !== $consignment->id) {
            abort(403);
        }

        if ($consignment->items()->count() <= 1) {
            return back()->with('error', 'At least one product is required. Delete the consignment instead.');
        }

        DB::transaction(function () use ($consignment, $item) {
            $item->delete();

            $total = $consignment->items()->sum('subtotal');
            $consignment->update([
                'total_value' => $total,
                'balance_due' => max(0, $total - (float) $consignment->amount_paid),
            ]);

            activity()
                ->performedOn($consignment)
                ->causedBy(auth()->user())
                ->log('Removed item from consignment: ' . $consignment->reference_no);
        });

        return back()->with('success', 'Product removed.');
    }

    public function dispatch(Consignment $consignment)
    {
        $this->authorize('dispatch consignments');

        if ($consignment->status !== 'pending') {
            return back()->with('error', 'Only pending consignments can be dispatched.');
        }

        $consignment->load('items.product');

        $errors = [];
        foreach ($consignment->items as $item) {
            $stock = BranchStock::where('branch_id', $consignment->branch_id)
                ->where('product_id', $item->product_id)
                ->first();

            if (!$stock || $stock->quantity < $item->quantity) {
                $available = $stock?->quantity ?? 0;
                $errors[]  = "{$item->product->name}: need {$item->quantity}, only {$available} available.";
            }
        }

        if (!empty($errors)) {
            return back()->with('error', 'Insufficient stock: ' . implode(' | ', $errors));
        }

        DB::transaction(function () use ($consignment) {
            foreach ($consignment->items as $item) {
                $stock = BranchStock::where('branch_id', $consignment->branch_id)
                    ->where('product_id', $item->product_id)
                    ->first();

                $stock->decrementStock((float) $item->quantity);

                StockMovement::record(
                    branchId     : $consignment->branch_id,
                    productId    : $item->product_id,
                    userId       : auth()->id(),
                    type         : 'consignment_out',
                    quantity     : -(float) $item->quantity,
                    balanceAfter : (float) $stock->quantity,
                    referenceType: 'consignment',
                    referenceId  : $consignment->id,
                    notes        : 'Dispatched — ' . $consignment->reference_no
                );
            }

            $consignment->update(['status' => 'dispatched']);

            activity()
                ->performedOn($consignment)
                ->causedBy(auth()->user())
                ->log('Dispatched consignment: ' . $consignment->reference_no);
        });

        return back()->with('success', 'Consignment dispatched. Stock deducted from branch.');
    }

    public function recordPayment(Request $request, Consignment $consignment)
    {
        if (!in_array($consignment->status, ['dispatched', 'partial'])) {
            return back()->with('error', 'Payments can only be recorded on dispatched or partially paid consignments.');
        }

        $request->validate([
            'amount'         => 'required|numeric|min:0.01|max:' . $consignment->balance_due,
            'payment_method' => 'required|in:cash,momo,bank,cheque',
            'payment_date'   => 'required|date',
            'notes'          => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $consignment) {
            $payment = ConsignmentPayment::create([
                'reference'      => ConsignmentPayment::generateReference(),
                'consignment_id' => $consignment->id,
                'paid_by'        => auth()->id(),
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date'   => $request->payment_date,
                'notes'          => $request->notes,
            ]);

            $newAmountPaid = (float) $consignment->amount_paid + (float) $request->amount;
            $newBalance    = max(0, (float) $consignment->balance_due - (float) $request->amount);
            $newStatus     = $newBalance <= 0 ? 'completed' : 'partial';

            $consignment->update([
                'amount_paid' => $newAmountPaid,
                'balance_due' => $newBalance,
                'status'      => $newStatus,
            ]);

            activity()
                ->performedOn($consignment)
                ->causedBy(auth()->user())
                ->log('Payment ' . $payment->reference . ' recorded on ' . $consignment->reference_no);
        });

        return redirect()->route('consignments.show', $consignment)
            ->with('success', 'Payment recorded successfully.');
    }

    public function cancel(Consignment $consignment)
    {
        if (in_array($consignment->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'This consignment cannot be cancelled.');
        }

        $consignment->load('items.product');

        DB::transaction(function () use ($consignment) {
            if (in_array($consignment->status, ['dispatched', 'partial'])) {
                foreach ($consignment->items as $item) {
                    $stock = BranchStock::firstOrCreate(
                        ['branch_id' => $consignment->branch_id, 'product_id' => $item->product_id],
                        ['quantity' => 0, 'last_updated' => now()]
                    );

                    $stock->incrementStock((float) $item->quantity);

                    StockMovement::record(
                        branchId     : $consignment->branch_id,
                        productId    : $item->product_id,
                        userId       : auth()->id(),
                        type         : 'consignment_return',
                        quantity     : (float) $item->quantity,
                        balanceAfter : (float) $stock->quantity,
                        referenceType: 'consignment',
                        referenceId  : $consignment->id,
                        notes        : 'Cancelled — ' . $consignment->reference_no
                    );
                }
            }

            $consignment->update(['status' => 'cancelled']);

            activity()
                ->performedOn($consignment)
                ->causedBy(auth()->user())
                ->log('Cancelled consignment: ' . $consignment->reference_no);
        });

        return back()->with('success', 'Consignment cancelled.' . (in_array($consignment->getOriginal('status'), ['dispatched', 'partial']) ? ' Stock has been restored.' : ''));
    }

    public function destroy(Consignment $consignment)
    {
        if ($consignment->status !== 'pending') {
            return back()->with('error', 'Only pending consignments can be deleted.');
        }
        return $this->softDelete($consignment, 'consignments.index');
    }

    public function restore($id)
    {
        $consignment = Consignment::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($consignment, 'consignments.index');
    }
}
