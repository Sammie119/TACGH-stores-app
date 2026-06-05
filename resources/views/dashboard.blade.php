{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subheader', 'Welcome back, ' . auth()->user()->name . ' — ' . now()->format('l, d M Y'))

@section('content')

    {{-- No active FY warning --}}
    @if(!$activeYear)
        <div style="display:flex;align-items:center;gap:12px;padding:14px 18px;
            background:#fef2f2;border:1px solid #fecaca;border-radius:10px;
            margin-bottom:24px">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-700">No active financial year</p>
                <p class="text-xs text-red-500 mt-0.5">
                    Sales cannot be processed.
                    <a href="{{ route('financial-years.index') }}"
                       class="underline font-medium">
                        Activate a financial year →
                    </a>
                </p>
            </div>
        </div>
    @endif

    {{-- ── Summary cards ─────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
            gap:16px;margin-bottom:24px">

        {{-- Today's sales --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div style="display:flex;align-items:flex-start;justify-content:space-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                        Sales today
                    </p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        GHS {{ number_format($salesToday, 2) }}
                    </p>
                </div>
                <div style="width:38px;height:38px;border-radius:10px;background:#eff6ff;
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0">
                    <svg width="18" height="18" fill="none" stroke="#2563eb"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ now()->format('d M Y') }}</p>
        </div>

        {{-- This month --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div style="display:flex;align-items:flex-start;justify-content:space-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                        This month
                    </p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        GHS {{ number_format($salesMonth, 2) }}
                    </p>
                </div>
                <div style="width:38px;height:38px;border-radius:10px;
                        background:{{ $monthGrowth >= 0 ? '#f0fdf4' : '#fef2f2' }};
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0">
                    <svg width="18" height="18" fill="none"
                         stroke="{{ $monthGrowth >= 0 ? '#16a34a' : '#dc2626' }}"
                         viewBox="0 0 24 24">
                        @if($monthGrowth >= 0)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/>
                        @endif
                    </svg>
                </div>
            </div>
            @if($monthGrowth !== null)
                <p class="text-xs mt-2 font-medium
                  {{ $monthGrowth >= 0 ? 'text-green-600' : 'text-red-500' }}">
                    {{ $monthGrowth >= 0 ? '+' : '' }}{{ $monthGrowth }}% vs last month
                </p>
            @else
                <p class="text-xs text-gray-400 mt-2">{{ now()->format('F Y') }}</p>
            @endif
        </div>

{{--        --}}{{-- Products --}}
{{--        <div class="bg-white border border-gray-200 rounded-xl p-5">--}}
{{--            <div style="display:flex;align-items:flex-start;justify-content:space-between">--}}
{{--                <div>--}}
{{--                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">--}}
{{--                        Products--}}
{{--                    </p>--}}
{{--                    <p class="text-2xl font-bold text-gray-800 mt-1">--}}
{{--                        {{ number_format($totalProducts) }}--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div style="width:38px;height:38px;border-radius:10px;background:#f0fdf4;--}}
{{--                        display:flex;align-items:center;justify-content:center;--}}
{{--                        flex-shrink:0">--}}
{{--                    <svg width="18" height="18" fill="none" stroke="#16a34a"--}}
{{--                         viewBox="0 0 24 24">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"--}}
{{--                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>--}}
{{--                    </svg>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <p class="text-xs text-gray-400 mt-2">Active products</p>--}}
{{--        </div>--}}

{{--        --}}{{-- Customers --}}
{{--        <div class="bg-white border border-gray-200 rounded-xl p-5">--}}
{{--            <div style="display:flex;align-items:flex-start;justify-content:space-between">--}}
{{--                <div>--}}
{{--                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">--}}
{{--                        Customers--}}
{{--                    </p>--}}
{{--                    <p class="text-2xl font-bold text-gray-800 mt-1">--}}
{{--                        {{ number_format($totalCustomers) }}--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div style="width:38px;height:38px;border-radius:10px;background:#faf5ff;--}}
{{--                        display:flex;align-items:center;justify-content:center;--}}
{{--                        flex-shrink:0">--}}
{{--                    <svg width="18" height="18" fill="none" stroke="#7c3aed"--}}
{{--                         viewBox="0 0 24 24">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"--}}
{{--                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>--}}
{{--                    </svg>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <p class="text-xs text-gray-400 mt-2">Registered</p>--}}
{{--        </div>--}}

        {{-- Low stock --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div style="display:flex;align-items:flex-start;justify-content:space-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                        Low stock
                    </p>
                    <p class="text-2xl font-bold mt-1
                          {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-gray-800' }}">
                        {{ $lowStockCount }}
                    </p>
                </div>
                <div style="width:38px;height:38px;border-radius:10px;
                        background:{{ $lowStockCount > 0 ? '#fef3c7' : '#f3f4f6' }};
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0">
                    <svg width="18" height="18" fill="none"
                         stroke="{{ $lowStockCount > 0 ? '#d97706' : '#9ca3af' }}"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Below reorder level</p>
        </div>

        {{-- Stock value --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div style="display:flex;align-items:flex-start;justify-content:space-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                        Stock value
                    </p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        GHS {{ number_format($stockValue->cost_value ?? 0, 2) }}
                    </p>
                </div>
                <div style="width:38px;height:38px;border-radius:10px;background:#fefce8;
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0">
                    <svg width="18" height="18" fill="none" stroke="#ca8a04"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">At cost price</p>
        </div>

    </div>

    {{-- ── Charts row ──────────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:1.8fr 1fr;gap:20px;margin-bottom:20px">

        {{-- Sales last 30 days --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"
                 style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p class="font-semibold text-gray-700">Sales — last 30 days</p>
                    <p class="text-xs text-gray-400 mt-0.5">Daily revenue trend</p>
                </div>
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="display:flex;align-items:center;gap:5px">
                        <div style="width:10px;height:3px;background:#2563eb;border-radius:2px"></div>
                        <span class="text-xs text-gray-400">Revenue</span>
                    </div>
                </div>
            </div>
            <div style="padding:16px 20px 20px">
                <canvas id="salesChart" height="110"></canvas>
            </div>
        </div>

        {{-- Payment method breakdown --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Payment methods</p>
                <p class="text-xs text-gray-400 mt-0.5">This month</p>
            </div>
            <div style="padding:16px 20px;display:flex;align-items:center;
                    justify-content:center">
                @if(count($paymentData) > 0)
                    <canvas id="paymentChart" width="200" height="200"
                            style="max-width:200px;max-height:200px"></canvas>
                @else
                    <div style="padding:40px;text-align:center">
                        <p class="text-sm text-gray-400">No sales this month</p>
                    </div>
                @endif
            </div>
            @if(count($paymentData) > 0)
                <div style="padding:0 20px 16px;display:flex;flex-wrap:wrap;gap:8px">
                    @foreach($paymentLabels as $i => $label)
                        <div style="display:flex;align-items:center;gap:5px">
                            <div style="width:8px;height:8px;border-radius:50%;
                            background:{{ ['#2563eb','#16a34a','#d97706','#7c3aed','#dc2626'][$i % 5] }}">
                            </div>
                            <span class="text-xs text-gray-600">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ── Second charts row ────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">

        {{-- 6-month trend --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Monthly revenue</p>
                <p class="text-xs text-gray-400 mt-0.5">Last 6 months</p>
            </div>
            <div style="padding:16px 20px 20px">
                <canvas id="monthlyChart" height="130"></canvas>
            </div>
        </div>

        {{-- Top products --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Top products</p>
                <p class="text-xs text-gray-400 mt-0.5">By revenue this month</p>
            </div>
            <div style="padding:16px 20px 20px">
                @if($topProducts->count() > 0)
                    <canvas id="topProductsChart" height="130"></canvas>
                @else
                    <div style="padding:40px;text-align:center">
                        <p class="text-sm text-gray-400">No sales data this month</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── Branch performance (super admin) ───────────────────────── --}}
    @if($isSuperAdmin && count($branchPerformance) > 0)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Branch performance</p>
                <p class="text-xs text-gray-400 mt-0.5">Revenue comparison this month</p>
            </div>
            <div style="padding:16px 20px 20px">
                <canvas id="branchChart" height="80"></canvas>
            </div>
        </div>
    @endif

    {{-- ── Bottom row ──────────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:20px">

        {{-- Recent sales --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"
                 style="display:flex;align-items:center;justify-content:space-between">
                <p class="font-semibold text-gray-700">Recent sales</p>
                @can('view sales')
                    <a href="{{ route('sales.index') }}"
                       class="text-xs text-blue-600 hover:underline">View all</a>
                @endcan
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse;min-width:400px">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                               text-gray-500 uppercase">Invoice</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                               text-gray-500 uppercase">Amount</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                               text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                               text-gray-500 uppercase">Time</th>
                </tr>
                </thead>
                <tbody>
                @forelse($recentSales as $sale)
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
                               class="font-mono text-xs text-blue-600 hover:underline">
                                {{ $sale->invoice_no }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">
                            GHS {{ number_format($sale->total_amount, 2) }}
                        </td>
                        <td class="px-5 py-3">
                        <span style="padding:2px 8px;border-radius:20px;
                                     font-size:11px;font-weight:500;{{ $sc }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-400">
                            {{ $sale->created_at->diffForHumans() }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4"
                            class="px-5 py-10 text-center text-gray-400 text-sm">
                            No sales yet
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Low stock alerts --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"
                 style="display:flex;align-items:center;justify-content:space-between">
                <p class="font-semibold text-gray-700">Low stock alerts</p>
                @can('view stock')
                    <a href="{{ route('inventory.index') }}"
                       class="text-xs text-blue-600 hover:underline">View all</a>
                @endcan
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($lowStockItems as $item)
                    @php
                        $isOut = $item->quantity <= 0;
                        $pct   = $item->product->reorder_level > 0
                            ? min(100, ($item->quantity / $item->product->reorder_level) * 100)
                            : 0;
                    @endphp
                    <div style="padding:12px 20px">
                        <div style="display:flex;align-items:center;
                            justify-content:space-between;gap:8px;margin-bottom:6px">
                            <div style="min-width:0;flex:1">
                                <p class="text-sm font-medium text-gray-800 truncate">
                                    {{ $item->product?->name }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $item->branch?->name }}
                                    · Reorder at {{ $item->product?->reorder_level }}
                                </p>
                            </div>
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:600;flex-shrink:0;
                                 background:{{ $isOut ? '#fee2e2' : '#fef3c7' }};
                                 color:{{ $isOut ? '#991b1b' : '#92400e' }}">
                        {{ $isOut ? 'Out' : number_format($item->quantity) . ' left' }}
                    </span>
                        </div>
                        {{-- Mini progress bar --}}
                        <div style="height:4px;background:#e5e7eb;border-radius:2px;overflow:hidden">
                            <div style="height:100%;width:{{ $pct }}%;border-radius:2px;
                                background:{{ $isOut ? '#ef4444' : '#f59e0b' }};
                                transition:width .3s">
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="padding:40px 20px;text-align:center">
                        <div style="width:40px;height:40px;border-radius:50%;background:#f0fdf4;
                            display:flex;align-items:center;justify-content:center;
                            margin:0 auto 10px">
                            <svg width="20" height="20" fill="none" stroke="#16a34a"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm text-green-600 font-medium">All stock levels healthy</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            // ── Shared defaults ─────────────────────────────────────────────
            Chart.defaults.font.family = "-apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
            Chart.defaults.font.size   = 11;
            Chart.defaults.color       = '#9ca3af';

            const gridColor    = 'rgba(0,0,0,.04)';
            const tooltipStyle = {
                backgroundColor : '#1f2937',
                titleColor      : '#f9fafb',
                bodyColor       : '#d1d5db',
                borderColor     : '#374151',
                borderWidth     : 1,
                padding         : 10,
                cornerRadius    : 8,
                displayColors   : false,
            };

            // ── Sales last 30 days ──────────────────────────────────────────
            const salesCtx = document.getElementById('salesChart');
            if (salesCtx) {
                new Chart(salesCtx, {
                    type: 'line',
                    data: {
                        labels  : @json($salesChartLabels),
                        datasets: [{
                            label          : 'Revenue (GHS)',
                            data           : @json($salesChartData),
                            borderColor    : '#2563eb',
                            backgroundColor: 'rgba(37,99,235,.08)',
                            borderWidth    : 2,
                            fill           : true,
                            tension        : 0.4,
                            pointRadius    : 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#2563eb',
                        }]
                    },
                    options: {
                        responsive       : true,
                        maintainAspectRatio: true,
                        interaction      : { mode: 'index', intersect: false },
                        plugins          : {
                            legend : { display: false },
                            tooltip: {
                                ...tooltipStyle,
                                callbacks: {
                                    label: ctx => `GHS ${ctx.parsed.y.toLocaleString('en-GH', { minimumFractionDigits: 2 })}`,
                                }
                            },
                        },
                        scales: {
                            x: {
                                grid       : { display: false },
                                ticks      : { maxTicksLimit: 8, maxRotation: 0 },
                            },
                            y: {
                                grid       : { color: gridColor },
                                ticks      : {
                                    callback: v => 'GHS ' + (v >= 1000 ? (v/1000).toFixed(1) + 'k' : v),
                                },
                                beginAtZero: true,
                            }
                        }
                    }
                });
            }

            // ── Payment method donut ────────────────────────────────────────
            const paymentCtx = document.getElementById('paymentChart');
            if (paymentCtx && @json(count($paymentData)) > 0) {
                new Chart(paymentCtx, {
                    type: 'doughnut',
                    data: {
                        labels  : @json($paymentLabels),
                        datasets: [{
                            data           : @json($paymentData),
                            backgroundColor: ['#2563eb','#16a34a','#d97706','#7c3aed','#dc2626'],
                            borderWidth    : 0,
                            hoverOffset    : 6,
                        }]
                    },
                    options: {
                        responsive       : true,
                        maintainAspectRatio: true,
                        cutout           : '65%',
                        plugins          : {
                            legend : { display: false },
                            tooltip: {
                                ...tooltipStyle,
                                callbacks: {
                                    label: ctx => `GHS ${ctx.parsed.toLocaleString('en-GH', { minimumFractionDigits: 2 })}`,
                                }
                            }
                        }
                    }
                });
            }

            // ── Monthly trend bar chart ─────────────────────────────────────
            const monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx) {
                new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels  : @json($monthlyLabels),
                        datasets: [{
                            label          : 'Revenue',
                            data           : @json($monthlyTrend),
                            backgroundColor: ctx => {
                                const last = ctx.dataIndex === ctx.dataset.data.length - 1;
                                return last ? '#2563eb' : 'rgba(37,99,235,.2)';
                            },
                            borderRadius   : 6,
                            borderSkipped  : false,
                        }]
                    },
                    options: {
                        responsive       : true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend : { display: false },
                            tooltip: {
                                ...tooltipStyle,
                                callbacks: {
                                    label: ctx => `GHS ${ctx.parsed.y.toLocaleString('en-GH', { minimumFractionDigits: 2 })}`,
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                grid  : { color: gridColor },
                                ticks : {
                                    callback: v => 'GHS ' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v),
                                },
                                beginAtZero: true,
                            }
                        }
                    }
                });
            }

            // ── Top products horizontal bar ─────────────────────────────────
            const topProductsCtx = document.getElementById('topProductsChart');
            if (topProductsCtx && @json($topProducts->count()) > 0) {
                new Chart(topProductsCtx, {
                    type: 'bar',
                    data: {
                        labels  : @json($topProducts->pluck('name')->map(fn($n) => strlen($n) > 20 ? substr($n, 0, 20) . '…' : $n)),
                        datasets: [{
                            label          : 'Revenue',
                            data           : @json($topProducts->pluck('total_revenue')->map(fn($v) => (float) $v)),
                            backgroundColor: [
                                '#2563eb','rgba(37,99,235,.7)','rgba(37,99,235,.5)',
                                'rgba(37,99,235,.35)','rgba(37,99,235,.2)',
                            ],
                            borderRadius   : 6,
                            borderSkipped  : false,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend : { display: false },
                            tooltip: {
                                ...tooltipStyle,
                                callbacks: {
                                    label: ctx => `GHS ${ctx.parsed.x.toLocaleString('en-GH', { minimumFractionDigits: 2 })}`,
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid  : { color: gridColor },
                                ticks : {
                                    callback: v => 'GHS ' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v),
                                },
                                beginAtZero: true,
                            },
                            y: { grid: { display: false } }
                        }
                    }
                });
            }

            // ── Branch performance (super admin) ───────────────────────────
            @if($isSuperAdmin && count($branchPerformance) > 0)
            const branchCtx = document.getElementById('branchChart');
            if (branchCtx) {
                new Chart(branchCtx, {
                    type: 'bar',
                    data: {
                        labels  : @json($branchPerformance->map(fn($b) => $b->branch?->name ?? 'Unknown')),
                        datasets: [
                            {
                                label          : 'Revenue',
                                data           : @json($branchPerformance->pluck('total')->map(fn($v) => (float) $v)),
                                backgroundColor: '#2563eb',
                                borderRadius   : 6,
                                borderSkipped  : false,
                                yAxisID        : 'y',
                            },
                            {
                                label          : 'Transactions',
                                data           : @json($branchPerformance->pluck('count')),
                                backgroundColor: 'rgba(124,58,237,.3)',
                                borderRadius   : 6,
                                borderSkipped  : false,
                                yAxisID        : 'y1',
                                type           : 'bar',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { boxWidth: 10, usePointStyle: true, padding: 16 }
                            },
                            tooltip: {
                                ...tooltipStyle,
                                displayColors: true,
                            }
                        },
                        scales: {
                            x  : { grid: { display: false } },
                            y  : {
                                grid  : { color: gridColor },
                                ticks : {
                                    callback: v => 'GHS ' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v),
                                },
                                beginAtZero: true,
                                position: 'left',
                            },
                            y1 : {
                                grid       : { display: false },
                                ticks      : { stepSize: 1 },
                                beginAtZero: true,
                                position   : 'right',
                            }
                        }
                    }
                });
            }
            @endif
        </script>
    @endpush

@endsection
