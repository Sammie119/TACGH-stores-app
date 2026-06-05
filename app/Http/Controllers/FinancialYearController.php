<?php

namespace App\Http\Controllers;

use App\Models\FinancialYear;
use App\Traits\HasSoftDeleteActions;
use Illuminate\Http\Request;

class FinancialYearController extends Controller
{
    use HasSoftDeleteActions;

    public function index(Request $request)
    {
        if ($request->get('trashed')) {
            $query = FinancialYear::onlyTrashed();
        } else {
            $query = FinancialYear::query();
        }

        $years          = $query->latest()->paginate(15)->withQueryString();
        $trashedCount   = FinancialYear::onlyTrashed()->count();
        $showingTrashed = (bool) $request->get('trashed');
        $activeYear     = FinancialYear::getActive();

        return view('financial-years.index', compact(
            'years', 'trashedCount', 'showingTrashed', 'activeYear'
        ));
    }

    public function create()
    {
        return view('financial-years.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:financial_years,name',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        FinancialYear::create([
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'is_active'  => false,
            'is_closed'  => false,
        ]);

        return redirect()->route('financial-years.index')
            ->with('success', 'Financial year created successfully.');
    }

    public function edit(FinancialYear $financialYear)
    {
        if ($financialYear->is_closed) {
            return redirect()->route('financial-years.index')
                ->with('error', 'Closed financial years cannot be edited.');
        }

        return view('financial-years.edit', compact('financialYear'));
    }

    public function update(Request $request, FinancialYear $financialYear)
    {
        if ($financialYear->is_closed) {
            return back()->with('error', 'Closed financial years cannot be edited.');
        }

        $request->validate([
            'name'       => 'required|string|max:255|unique:financial_years,name,' . $financialYear->id,
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $financialYear->update([
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ]);

        return redirect()->route('financial-years.index')
            ->with('success', 'Financial year updated.');
    }

    public function activate(FinancialYear $financialYear)
    {
        if ($financialYear->is_closed) {
            return back()->with('error', 'Cannot activate a closed financial year.');
        }

        // Deactivate all others first
        FinancialYear::where('id', '!=', $financialYear->id)
            ->update(['is_active' => false]);

        $financialYear->update(['is_active' => true]);

        activity()
            ->performedOn($financialYear)
            ->causedBy(auth()->user())
            ->log('Activated financial year: ' . $financialYear->name);

        return back()->with('success', $financialYear->name . ' is now the active financial year.');
    }

    public function close(FinancialYear $financialYear)
    {
        if ($financialYear->is_closed) {
            return back()->with('error', 'Financial year is already closed.');
        }

        $financialYear->update([
            'is_active' => false,
            'is_closed' => true,
        ]);

        activity()
            ->performedOn($financialYear)
            ->causedBy(auth()->user())
            ->log('Closed financial year: ' . $financialYear->name);

        return back()->with('success', $financialYear->name . ' has been closed. No further transactions are allowed.');
    }

    public function destroy(FinancialYear $financialYear)
    {
        if ($financialYear->is_active || $financialYear->is_closed) {
            return back()->with('error', 'Active or closed financial years cannot be deleted.');
        }

        return $this->softDelete($financialYear, 'financial-years.index');
    }

    public function restore($id)
    {
        $year = FinancialYear::onlyTrashed()->findOrFail($id);
        return $this->restoreModel($year, 'financial-years.index');
    }
}
