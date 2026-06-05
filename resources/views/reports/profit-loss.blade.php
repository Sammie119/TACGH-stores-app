{{-- resources/views/reports/profit-loss.blade.php --}}
@extends('layouts.app')
@section('title', 'Profit & Loss')
@section('header', 'Profit & Loss Report')
@section('subheader', \Carbon\Carbon::parse($dateFrom)->format('d M Y') . ' — ' . \Carbon\Carbon::parse($dateTo)->format('d M Y'))

@section('content')

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <form method="GET" action="{{ route('reports.profit-loss') }}"
              style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap">

            @if($isSuperAdmin)
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">
                        Branch
                    </label>
                    <div class="relative">
                        <select name="branch_id"
                                class="h-9 px-3 pr-8 rounded-lg border border-gray-300
                               bg-white text-sm text-gray-700 focus:outline-none
                               focus:ring-2 focus:ring-blue-500 appearance-none">
                            <option value="">All branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center
                             pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-700 focus:outline-none
                          focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-700 focus:outline-none
                          focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors">
                Apply
            </button>

                <a href="{{ route('pdf.profit-loss') }}?{{ http_build_query(request()->query()) }}"
                   target="_blank"
                   class="h-9 px-4 bg-red-600 hover:bg-red-700 text-white text-sm
          font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print PDF
                </a>
        </form>
    </div>

    {{-- P&L statement --}}
    <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:20px;margin-bottom:24px">

        {{-- Main P&L card --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"
                 style="background:linear-gradient(135deg,#1e3a5f,#2563eb)">
                <p style="font-size:14px;font-weight:700;color:#fff">
                    Profit & Loss Statement
                </p>
                <p style="font-size:11px;color:#93c5fd;margin-top:2px">
                    {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
                    —
                    {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                </p>
            </div>

            <div class="divide-y divide-gray-50">

                {{-- Revenue section --}}
                <div style="padding:16px 20px;background:#f0fdf4">
                    <p class="text-xs font-bold text-green-700 uppercase
                           tracking-wide mb-2">
                        Revenue
                    </p>
                    <div style="display:flex;justify-content:space-between;
                            margin-bottom:6px">
                    <span class="text-sm text-gray-600">
                        Gross sales ({{ $totalTransactions }} transactions)
                    </span>
                        <span class="text-sm font-semibold text-gray-800">
                        GHS {{ number_format($totalRevenue, 2) }}
                    </span>
                    </div>
                    <div style="display:flex;justify-content:space-between;
                            margin-bottom:6px">
                        <span class="text-sm text-gray-600">Discounts given</span>
                        <span class="text-sm text-red-500">
                        - GHS {{ number_format($totalDiscount, 2) }}
                    </span>
                    </div>
                    <div style="display:flex;justify-content:space-between;
                            padding-top:8px;border-top:1px solid #bbf7d0">
                    <span class="text-sm font-semibold text-green-700">
                        Net revenue
                    </span>
                        <span class="text-sm font-bold text-green-700">
                        GHS {{ number_format($totalRevenue - $totalDiscount, 2) }}
                    </span>
                    </div>
                </div>

                {{-- COGS section --}}
                <div style="padding:16px 20px;background:#fef2f2">
                    <p class="text-xs font-bold text-red-700 uppercase
                           tracking-wide mb-2">
                        Cost of goods sold
                    </p>
                    <div style="display:flex;justify-content:space-between;
                            margin-bottom:6px">
                    <span class="text-sm text-gray-600">
                        Product cost (at cost price)
                    </span>
                        <span class="text-sm font-semibold text-red-600">
                        - GHS {{ number_format($totalCOGS, 2) }}
                    </span>
                    </div>
                </div>

                {{-- Gross profit --}}
                <div style="padding:16px 20px;background:#eff6ff">
                    <div style="display:flex;justify-content:space-between;
                            align-items:center">
                        <div>
                            <p class="text-sm font-bold text-blue-800">
                                Gross profit
                            </p>
                            <p class="text-xs text-blue-500 mt-0.5">
                                Margin: {{ $grossProfitMargin }}%
                            </p>
                        </div>
                        <span style="font-size:18px;font-weight:700;
                                 color:{{ $grossProfit >= 0 ? '#1d4ed8' : '#dc2626' }}">
                        GHS {{ number_format($grossProfit, 2) }}
                    </span>
                    </div>
                </div>

                {{-- Deductions --}}
                <div style="padding:16px 20px">
                    <p class="text-xs font-bold text-gray-500 uppercase
                           tracking-wide mb-2">
                        Deductions
                    </p>
                    <div style="display:flex;justify-content:space-between;
                            margin-bottom:6px">
                    <span class="text-sm text-gray-600">
                        Refunds ({{ $totalReturns }} returns)
                    </span>
                        <span class="text-sm text-red-500">
                        - GHS {{ number_format($totalRefunds, 2) }}
                    </span>
                    </div>
                </div>

                {{-- Net profit --}}
                <div style="padding:20px;background:{{ $netProfit >= 0 ? '#f0fdf4' : '#fef2f2' }}">
                    <div style="display:flex;justify-content:space-between;
                            align-items:center">
                        <div>
                            <p style="font-size:15px;font-weight:700;
                                  color:{{ $netProfit >= 0 ? '#166534' : '#991b1b' }}">
                                NET PROFIT
                            </p>
                            <p style="font-size:11px;margin-top:2px;
                                  color:{{ $netProfit >= 0 ? '#16a34a' : '#dc2626' }}">
                                Margin: {{ $netProfitMargin }}%
                            </p>
                        </div>
                        <span style="font-size:24px;font-weight:800;
                                 color:{{ $netProfit >= 0 ? '#166534' : '#991b1b' }}">
                        GHS {{ number_format($netProfit, 2) }}
                    </span>
                    </div>
                </div>

            </div>
        </div>

        {{-- Right column --}}
        <div style="display:flex;flex-direction:column;gap:16px">

            {{-- Quick stats --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase
                       tracking-widest mb-4">
                    Quick stats
                </p>
                <div class="space-y-3">
                    @foreach([
                        ['Transactions',       number_format($totalTransactions),              '#111827'],
                        ['Avg sale value',     'GHS ' . number_format($totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0, 2), '#111827'],
                        ['Total discounts',    'GHS ' . number_format($totalDiscount, 2),     '#dc2626'],
                        ['Total refunds',      'GHS ' . number_format($totalRefunds, 2),      '#dc2626'],
                        ['Purchases made',     'GHS ' . number_format($totalPurchases, 2),    '#6b7280'],
                        ['Gross margin',       $grossProfitMargin . '%',                      $grossProfitMargin >= 0 ? '#16a34a' : '#dc2626'],
                        ['Net margin',         $netProfitMargin . '%',                        $netProfitMargin >= 0 ? '#16a34a' : '#dc2626'],
                    ] as [$label, $value, $color])
                        <div style="display:flex;justify-content:space-between;
                            align-items:center;padding-bottom:8px;
                            border-bottom:1px solid #f9fafb">
                            <span class="text-sm text-gray-500">{{ $label }}</span>
                            <span style="font-size:13px;font-weight:600;color:{{ $color }}">
                        {{ $value }}
                    </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Revenue by payment --}}
            @if($revenueByPayment->count() > 0)
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="font-semibold text-gray-700">By payment method</p>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($revenueByPayment as $pm)
                            <div style="padding:10px 20px;display:flex;
                            justify-content:space-between;align-items:center">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">
                                        {{ ucfirst($pm->payment_method) }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $pm->count }} transactions
                                    </p>
                                </div>
                                <p class="text-sm font-semibold text-gray-800">
                                    GHS {{ number_format($pm->total, 2) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

    {{-- Branch breakdown (super admin) --}}
    @if($isSuperAdmin && count($branchBreakdown) > 0)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Branch performance</p>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Branch</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Transactions</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Revenue</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Discounts</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Net revenue</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">% of total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($branchBreakdown as $b)
                    @php
                        $pct = $totalRevenue > 0
                            ? round(($b->revenue / $totalRevenue) * 100, 1)
                            : 0;
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $b->branch?->name }}
                        </td>
                        <td class="px-5 py-3 text-right text-gray-600">
                            {{ $b->transactions }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">
                            GHS {{ number_format($b->revenue, 2) }}
                        </td>
                        <td class="px-5 py-3 text-right text-red-500">
                            GHS {{ number_format($b->discounts, 2) }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-green-600">
                            GHS {{ number_format($b->revenue - $b->discounts, 2) }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div style="display:flex;align-items:center;
                                justify-content:flex-end;gap:8px">
                                <div style="width:60px;height:6px;background:#e5e7eb;
                                    border-radius:3px;overflow:hidden">
                                    <div style="height:100%;width:{{ $pct }}%;
                                        background:#2563eb;border-radius:3px">
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Daily breakdown --}}
    @if($dailyBreakdown->count() > 0)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Daily breakdown</p>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Date</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Transactions</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Revenue</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Discounts</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Net</th>
                </tr>
                </thead>
                <tbody>
                @foreach($dailyBreakdown as $day)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-700">
                            {{ \Carbon\Carbon::parse($day->date)->format('D, d M Y') }}
                        </td>
                        <td class="px-5 py-3 text-right text-gray-600">
                            {{ $day->transactions }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">
                            GHS {{ number_format($day->revenue, 2) }}
                        </td>
                        <td class="px-5 py-3 text-right text-red-500">
                            GHS {{ number_format($day->discounts, 2) }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-green-600">
                            GHS {{ number_format($day->revenue - $day->discounts, 2) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="bg-gray-50 border-t border-gray-200">
                    <td class="px-5 py-3 font-semibold text-gray-700">Total</td>
                    <td class="px-5 py-3 text-right font-bold text-gray-800">
                        {{ $dailyBreakdown->sum('transactions') }}
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-gray-800">
                        GHS {{ number_format($dailyBreakdown->sum('revenue'), 2) }}
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-red-500">
                        GHS {{ number_format($dailyBreakdown->sum('discounts'), 2) }}
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-green-600">
                        GHS {{ number_format($dailyBreakdown->sum('revenue') - $dailyBreakdown->sum('discounts'), 2) }}
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    @endif

    {{-- Top products --}}
    @if($topProducts->count() > 0)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Top 10 products by revenue</p>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">#</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Product</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Units sold</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Revenue</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">% of revenue</th>
                </tr>
                </thead>
                <tbody>
                @foreach($topProducts as $i => $product)
                    @php
                        $pct = $totalRevenue > 0
                            ? round(($product->total_revenue / $totalRevenue) * 100, 1)
                            : 0;
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3">
                    <span style="width:22px;height:22px;border-radius:50%;
                                 background:#eff6ff;color:#2563eb;font-size:10px;
                                 font-weight:700;display:inline-flex;
                                 align-items:center;justify-content:center">
                        {{ $i + 1 }}
                    </span>
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $product->product?->name }}
                        </td>
                        <td class="px-5 py-3 text-right text-gray-600">
                            {{ number_format($product->total_qty, 2) }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">
                            GHS {{ number_format($product->total_revenue, 2) }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div style="display:flex;align-items:center;
                                justify-content:flex-end;gap:8px">
                                <div style="width:60px;height:6px;background:#e5e7eb;
                                    border-radius:3px;overflow:hidden">
                                    <div style="height:100%;width:{{ $pct }}%;
                                        background:#2563eb;border-radius:3px">
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

@endsection
