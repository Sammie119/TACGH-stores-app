{{-- resources/views/cashier-accounts/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Cashier Cash Accounts')
@section('header', 'Cashier Cash Accounts')
@section('subheader', 'Cash received by cashiers vs amounts banked')

@section('content')

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <form method="GET" action="{{ route('cashier-accounts.index') }}"
              style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap">

            @if($isSuperAdmin)
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Branch</label>
                    <select name="branch_id"
                            class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                                   text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                              text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                              text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
                           font-medium rounded-lg transition-colors">
                Apply
            </button>

            @if(request()->hasAny(['branch_id', 'date_from', 'date_to']))
                <a href="{{ route('cashier-accounts.index', array_filter(['branch_id' => request('branch_id')]) + ['date_from' => '', 'date_to' => '']) }}"
                   class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50
                          text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total cash sales</p>
            <p class="text-2xl font-semibold text-blue-600 mt-1">
                GHS {{ number_format($totals['cash_sales'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Consignment cash</p>
            <p class="text-2xl font-semibold text-orange-500 mt-1">
                GHS {{ number_format($totals['cash_consignments'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total banked</p>
            <p class="text-2xl font-semibold text-green-600 mt-1">
                GHS {{ number_format($totals['verified_deposits'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total outstanding</p>
            <p class="text-2xl font-semibold {{ $totals['outstanding'] > 0 ? 'text-red-600' : 'text-gray-800' }} mt-1">
                GHS {{ number_format($totals['outstanding'], 2) }}
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="font-semibold text-gray-700">
                Cashiers
                <span class="text-sm font-normal text-gray-400 ml-2">
                    {{ $accounts->count() }} with cash activity
                </span>
            </p>
        </div>

        <table class="w-full text-sm" style="border-collapse:collapse">
            <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Cashier</th>
                @if($isSuperAdmin)
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Branch</th>
                @endif
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Cash Sales</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Consignment</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Received</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Banked</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Outstanding</th>
                <th class="px-5 py-3"></th>
            </tr>
            </thead>
            <tbody>
            @forelse($accounts as $account)
                @php $outstanding = $account['outstanding']; @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $account['cashier']->name }}</p>
                        <p class="text-xs text-gray-400">{{ $account['cashier']->email }}</p>
                    </td>
                    @if($isSuperAdmin)
                        <td class="px-5 py-3 text-gray-600 text-xs">{{ $account['cashier']->branch?->name }}</td>
                    @endif
                    <td class="px-5 py-3 text-right">
                        <span class="font-medium text-gray-800">GHS {{ number_format($account['cash_sales'], 2) }}</span>
                        <br><span class="text-xs text-gray-400">{{ $account['sales_count'] }} sale{{ $account['sales_count'] != 1 ? 's' : '' }}</span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <span class="font-medium text-orange-600">GHS {{ number_format($account['cash_consignments'], 2) }}</span>
                        <br><span class="text-xs text-gray-400">{{ $account['consig_count'] }} payment{{ $account['consig_count'] != 1 ? 's' : '' }}</span>
                    </td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-800">
                        GHS {{ number_format($account['cash_sales'] + $account['cash_consignments'], 2) }}
                    </td>
                    <td class="px-5 py-3 text-right">
                        <span class="font-medium text-green-600">GHS {{ number_format($account['verified_deposits'], 2) }}</span>
                        <br><span class="text-xs text-gray-400">{{ $account['deposits_count'] }} deposit{{ $account['deposits_count'] != 1 ? 's' : '' }}</span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        @if($outstanding > 0)
                            <span class="font-bold text-red-600">GHS {{ number_format($outstanding, 2) }}</span>
                        @elseif($outstanding < 0)
                            <span class="font-medium text-blue-600">GHS {{ number_format(abs($outstanding), 2) }} excess</span>
                        @else
                            <span style="padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;
                                         background:#dcfce7;color:#166534">Settled</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('cashier-accounts.show', ['user' => $account['cashier']->id, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                           class="text-xs font-medium text-blue-600 hover:underline">
                            View details
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $isSuperAdmin ? 8 : 7 }}"
                        class="px-5 py-12 text-center text-gray-400">
                        No cashiers with cash transactions found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

@endsection
