<?php

namespace App\Http\Controllers;

use App\Models\BankDeposit;
use App\Models\Branch;
use App\Traits\HasBranchAccess;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankDepositController extends Controller
{
    use HasSoftDeleteActions, HasBranchAccess;

    public function index(Request $request)
    {
        $this->authorize('view deposits');

        $user         = auth()->user();
        $isSuperAdmin = $this->canViewAllBranches();

        if ($request->get('trashed')) {
            $query = BankDeposit::onlyTrashed()
                ->with(['branch', 'depositedBy', 'verifiedBy']);
        } else {
            $query = BankDeposit::with(['branch', 'depositedBy', 'verifiedBy']);
        }

        if (!$isSuperAdmin) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->get('branch')) {
            $query->where('branch_id', $request->get('branch'));
        }

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->get('date_from')) {
            $query->whereDate('deposit_date', '>=', $request->get('date_from'));
        }

        if ($request->get('date_to')) {
            $query->whereDate('deposit_date', '<=', $request->get('date_to'));
        }

        $deposits       = $query->latest()->paginate(15)->withQueryString();
        $trashedCount   = BankDeposit::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');
        $branches       = Branch::where('is_active', true)->get();
        $statuses       = ['pending', 'verified', 'rejected'];

        $pendingCount = BankDeposit::where('status', 'pending')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_id', $user->branch_id)
            )->count();

        $totalVerified = BankDeposit::where('status', 'verified')
            ->when(!$isSuperAdmin, fn($q) =>
            $q->where('branch_id', $user->branch_id)
            )->sum('amount');

        return view('deposits.index', compact(
            'deposits', 'trashedCount', 'showingTrashed',
            'branches', 'statuses', 'pendingCount',
            'totalVerified', 'isSuperAdmin'
        ));
    }

    public function create()
    {
        $this->authorize('create deposits');

        $user     = auth()->user();
        $branches = $this->canViewAllBranches()
            ? Branch::where('is_active', true)->get()
            : Branch::where('id', $user->branch_id)->get();

        return view('deposits.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $this->authorize('create deposits');

        $request->validate([
            'branch_id'    => 'required|exists:branches,id',
            'amount'       => 'required|numeric|min:0.01',
            'deposit_date' => 'required|date',
            'bank_name'    => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:255',
            'slip_image'   => 'required|image|max:4096',
            'notes'        => 'nullable|string|max:500',
        ]);

        $path = $request->file('slip_image')
            ->store('deposits', 'public');

        $deposit = BankDeposit::create([
            'branch_id'    => $request->branch_id,
            'deposited_by' => auth()->id(),
            'amount'       => $request->amount,
            'deposit_date' => $request->deposit_date,
            'bank_name'    => $request->bank_name,
            'reference_no' => $request->reference_no,
            'slip_image'   => $path,
            'status'       => 'pending',
            'notes'        => $request->notes,
        ]);

        activity()
            ->performedOn($deposit)
            ->causedBy(auth()->user())
            ->log('Submitted bank deposit of GHS ' . number_format($request->amount, 2));

        return redirect()->route('deposits.index')
            ->with('success', 'Deposit slip submitted successfully.');
    }

    public function show(BankDeposit $deposit)
    {
        $this->authorize('view deposits');

        $deposit->load(['branch', 'depositedBy', 'verifiedBy']);
        return view('deposits.show', compact('deposit'));
    }

    public function edit(BankDeposit $deposit)
    {
        $this->authorize('create deposits');

        if ($deposit->status !== 'pending') {
            return redirect()->route('deposits.show', $deposit)
                ->with('error', 'Only pending deposits can be edited.');
        }

        $user     = auth()->user();
        $branches = $this->canViewAllBranches()
            ? Branch::where('is_active', true)->get()
            : Branch::where('id', $user->branch_id)->get();

        return view('deposits.edit', compact('deposit', 'branches'));
    }

    public function update(Request $request, BankDeposit $deposit)
    {
        $this->authorize('create deposits');

        if ($deposit->status !== 'pending') {
            return back()->with('error', 'Only pending deposits can be edited.');
        }

        $request->validate([
            'branch_id'    => 'required|exists:branches,id',
            'amount'       => 'required|numeric|min:0.01',
            'deposit_date' => 'required|date',
            'bank_name'    => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:255',
            'slip_image'   => 'nullable|image|max:4096',
            'notes'        => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'branch_id', 'amount', 'deposit_date',
            'bank_name', 'reference_no', 'notes'
        ]);

        if ($request->hasFile('slip_image')) {
            // Delete old image
            if ($deposit->slip_image) {
                Storage::disk('public')->delete($deposit->slip_image);
            }
            $data['slip_image'] = $request->file('slip_image')
                ->store('deposits', 'public');
        }

        $deposit->update($data);

        return redirect()->route('deposits.index')
            ->with('success', 'Deposit updated successfully.');
    }

    public function verify(BankDeposit $deposit)
    {
        $this->authorize('verify deposits');

        if ($deposit->status !== 'pending') {
            return back()->with('error', 'Only pending deposits can be verified.');
        }

        $deposit->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
        ]);

        activity()
            ->performedOn($deposit)
            ->causedBy(auth()->user())
            ->log('Verified deposit of GHS ' . number_format($deposit->amount, 2));

        return back()->with('success', 'Deposit verified successfully.');
    }

    public function reject(BankDeposit $deposit)
    {
        $this->authorize('verify deposits');

        if ($deposit->status !== 'pending') {
            return back()->with('error', 'Only pending deposits can be rejected.');
        }

        $deposit->update([
            'status'      => 'rejected',
            'verified_by' => auth()->id(),
        ]);

        activity()
            ->performedOn($deposit)
            ->causedBy(auth()->user())
            ->log('Rejected deposit of GHS ' . number_format($deposit->amount, 2));

        return back()->with('success', 'Deposit rejected.');
    }

    public function destroy(BankDeposit $deposit)
    {
        $this->authorize('create deposits');

        if ($deposit->status !== 'pending') {
            return back()->with('error', 'Only pending deposits can be deleted.');
        }
        return $this->softDelete($deposit, 'deposits.index');
    }

    public function restore($id)
    {
        $this->authorize('create deposits');

        $deposit = BankDeposit::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($deposit, 'deposits.index');
    }

    public function forceDelete($id)
    {
        $this->authorize('create deposits');

        $deposit = BankDeposit::onlyTrashed()->findOrFail($id);
        if ($deposit->slip_image) {
            Storage::disk('public')->delete($deposit->slip_image);
        }
        return $this->forceDeleteModel($deposit, 'deposits.index');
    }
}
