{{-- resources/views/reports/sales.blade.php --}}
@extends('layouts.app')
@section('title', 'Sales Report')
@section('header', 'Sales Report')
@section('subheader', 'Revenue and transaction analysis')

@section('content')

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <form method="GET" action="{{ route('reports.sales') }}"
              style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap">

            @if($isSuperAdmin)
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Branch</label>
                    <select name="branch_id"
                            class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                           text-sm text-gray-700 focus:outline-none
                           focus:ring-2 focus:ring-blue-500">
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
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                           text-sm text-gray-700 focus:outline-none
                           focus:ring-2 focus:ring-blue-500">
                    <option value="">All statuses</option>
                    @foreach(['completed','partial','credit','cancelled'] as $s)
                        <option value="{{ $s }}"
                            {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Payment method
                </label>
                <select name="payment_method"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                           text-sm text-gray-700 focus:outline-none
                           focus:ring-2 focus:ring-blue-500">
                    <option value="">All methods</option>
                    @foreach(['cash','momo','bank','pos','split'] as $m)
                        <option value="{{ $m }}"
                            {{ request('payment_method') === $m ? 'selected' : '' }}>
                            {{ ucfirst($m) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Cashier</label>
                <select name="user_id"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                           text-sm text-gray-700 focus:outline-none
                           focus:ring-2 focus:ring-blue-500">
                    <option value="">All cashiers</option>
                    @foreach($cashiers as $cashier)
                        <option value="{{ $cashier->id }}"
                            {{ request('user_id') == $cashier->id ? 'selected' : '' }}>
                            {{ $cashier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-700 focus:outline-none
                          focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-700 focus:outline-none
                          focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors">
                Apply
            </button>

            @if(request()->hasAny(['branch_id','status','payment_method','user_id','date_from','date_to']))
                <a href="{{ route('reports.sales') }}"
                   class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center">
                    Clear
                </a>
            @endif

        </form>
    </div>

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total revenue
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                GHS {{ number_format($summary['total_sales'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Transactions
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                {{ number_format($summary['total_count']) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total discounts
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                GHS {{ number_format($summary['total_discount'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Outstanding balance
            </p>
            <p class="text-2xl font-semibold
                  {{ $summary['balance_due'] > 0 ? 'text-red-600' : 'text-gray-800' }} mt-1">
                GHS {{ number_format($summary['balance_due'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total refunds
            </p>
            <p class="text-2xl font-semibold
                  {{ $summary['total_refunds'] > 0 ? 'text-red-600' : 'text-gray-800' }} mt-1">
                GHS {{ number_format($summary['total_refunds'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Net sales
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                GHS {{ number_format($summary['net_sales'], 2) }}
            </p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:20px;margin-bottom:24px">

        {{-- Payment method breakdown --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Revenue by payment method</p>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                               text-gray-500 uppercase">Method</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                               text-gray-500 uppercase">Count</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                               text-gray-500 uppercase">Total</th>
                </tr>
                </thead>
                <tbody>
                @forelse($byPayment as $row)
                    <tr class="border-b border-gray-50">
                        <td class="px-5 py-3">
                        <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#f3f4f6;color:#374151">
                            {{ ucfirst($row->payment_method) }}
                        </span>
                        </td>
                        <td class="px-5 py-3 text-right text-gray-600">
                            {{ number_format($row->count) }}
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-gray-800">
                            GHS {{ number_format($row->total, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-gray-400">
                            No data
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Top products --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Top 10 products</p>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topProducts as $i => $item)
                    <div class="px-5 py-3"
                         style="display:flex;align-items:center;gap:10px">
                <span style="width:20px;height:20px;border-radius:50%;
                             background:#eff6ff;color:#2563eb;font-size:10px;
                             font-weight:600;display:flex;align-items:center;
                             justify-content:center;flex-shrink:0">
                    {{ $i + 1 }}
                </span>
                        <div style="flex:1;min-width:0">
                            <p class="text-sm font-medium text-gray-800 truncate">
                                {{ $item->product?->name }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ number_format($item->total_qty, 0) }} units
                            </p>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 flex-shrink-0">
                            GHS {{ number_format($item->total_revenue, 2) }}
                        </p>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-gray-400 text-sm">No data</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Sales table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100"
             style="display:flex;align-items:center;justify-content:space-between">
            <p class="font-semibold text-gray-700">
                Transactions
                <span class="text-sm font-normal text-gray-400 ml-2">
                {{ $sales->total() }} records
            </span>
            </p>
            @can('export reports')
                <a href="{{ route('reports.export.sales') }}?{{ http_build_query(request()->query()) }}"
                   class="h-8 px-3 bg-green-50 hover:bg-green-100 text-green-700 text-xs
                  font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center;gap:4px">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>

                <a href="{{ route('pdf.sales-report') }}?{{ http_build_query(request()->query()) }}"
                   target="_blank"
                   class="h-8 px-3 bg-red-50 hover:bg-red-100 text-red-600 text-xs
                    font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center;gap:4px">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export PDF
                </a>
            @endcan
        </div>
        <table class="w-full text-sm" style="border-collapse:collapse">
            <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Invoice</th>
                @if($isSuperAdmin)
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Branch</th>
                @endif
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Customer</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Cashier</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Amount</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Method</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Date</th>
            </tr>
            </thead>
            <tbody>
            @forelse($sales as $sale)
                @php
                    $sc = [
                        'completed' => 'background:#dcfce7;color:#166534',
                        'partial'   => 'background:#fef3c7;color:#92400e',
                        'credit'    => 'background:#fee2e2;color:#991b1b',
                        'cancelled' => 'background:#f3f4f6;color:#374151',
                    ][$sale->status] ?? 'background:#f3f4f6;color:#374151';
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <a href="{{ route('sales.show', $sale) }}"
                           class="font-mono text-sm text-blue-600 hover:underline">
                            {{ $sale->invoice_no }}
                        </a>
                    </td>
                    @if($isSuperAdmin)
                        <td class="px-5 py-3 text-gray-600">{{ $sale->branch?->name }}</td>
                    @endif
                    <td class="px-5 py-3 text-gray-600">
                        {{ $sale->customer?->name ?? 'Walk-in' }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $sale->user?->name }}</td>
                    <td class="px-5 py-3 text-right font-medium text-gray-800">
                        GHS {{ number_format($sale->total_amount, 2) }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ ucfirst($sale->payment_method) }}
                    </td>
                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;{{ $sc }}">
                        {{ ucfirst($sale->status) }}
                    </span>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-500">
                        {{ $sale->created_at->format('d M Y H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                        No sales found for the selected filters.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        @if($sales->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

@endsection
