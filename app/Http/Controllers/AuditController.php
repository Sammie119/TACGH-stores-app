<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use App\Models\Branch;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);

        if ($request->get('user_id')) {
            $query->where('causer_id', $request->get('user_id'))
                ->where('causer_type', 'App\\Models\\User');
        }

        if ($request->get('log_name')) {
            $query->where('log_name', $request->get('log_name'));
        }

        if ($request->get('subject_type')) {
            $query->where('subject_type', 'like',
                '%' . $request->get('subject_type') . '%');
        }

        if ($request->get('search')) {
            $query->where('description', 'like',
                '%' . $request->get('search') . '%');
        }

        if ($request->get('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->get('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $logs     = $query->latest()->paginate(25)->withQueryString();
        $users    = User::orderBy('name')->get();
        $logNames = Activity::distinct()->pluck('log_name')->filter()->sort()->values();

        $subjectTypes = [
            'Sale'           => 'Sales',
            'Product'        => 'Products',
            'StockTransfer'  => 'Transfers',
            'ProductReturn'  => 'Returns',
            'BankDeposit'    => 'Deposits',
            'Branch'         => 'Branches',
            'User'           => 'Users',
            'FinancialYear'  => 'Financial Years',
        ];

        // Summary counts for today
        $todayCount  = Activity::whereDate('created_at', today())->count();
        $totalCount  = Activity::count();

        return view('audit.index', compact(
            'logs', 'users', 'logNames',
            'subjectTypes', 'todayCount', 'totalCount'
        ));
    }

    public function show($id)
    {
        $log = Activity::with(['causer', 'subject'])->findOrFail($id);
        return view('audit.show', compact('log'));
    }
}
