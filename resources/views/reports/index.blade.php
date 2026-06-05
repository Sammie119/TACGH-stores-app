{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Reports')
@section('header', 'Reports')
@section('subheader', 'Analytics and operational reports')

@section('content')

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px">

        @can('view reports')

            <a href="{{ route('reports.sales') }}"
               style="display:block;text-decoration:none"
               class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-300
              hover:shadow-sm transition-all">
                <div style="display:flex;align-items:flex-start;gap:14px">
                    <div style="width:44px;height:44px;border-radius:10px;background:#eff6ff;
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-1">Sales report</p>
                        <p class="text-sm text-gray-500">
                            Revenue, transactions, top products, payment methods
                        </p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reports.inventory') }}"
               style="display:block;text-decoration:none"
               class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-300
              hover:shadow-sm transition-all">
                <div style="display:flex;align-items:flex-start;gap:14px">
                    <div style="width:44px;height:44px;border-radius:10px;background:#f0fdf4;
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-1">Inventory report</p>
                        <p class="text-sm text-gray-500">
                            Stock levels, valuations, low stock alerts
                        </p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reports.transfers') }}"
               style="display:block;text-decoration:none"
               class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-300
              hover:shadow-sm transition-all">
                <div style="display:flex;align-items:flex-start;gap:14px">
                    <div style="width:44px;height:44px;border-radius:10px;background:#faf5ff;
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-1">Transfer report</p>
                        <p class="text-sm text-gray-500">
                            Stock movements between branches
                        </p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reports.deposits') }}"
               style="display:block;text-decoration:none"
               class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-300
              hover:shadow-sm transition-all">
                <div style="display:flex;align-items:flex-start;gap:14px">
                    <div style="width:44px;height:44px;border-radius:10px;background:#fefce8;
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-1">Deposits report</p>
                        <p class="text-sm text-gray-500">
                            Bank deposits, verification status, totals
                        </p>
                    </div>
                </div>
            </a>

            {{-- Product Report --}}
            <a href="{{ route('reports.product') }}"
               style="display:block;text-decoration:none"
               class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-300
          hover:shadow-sm transition-all">
                <div style="display:flex;align-items:flex-start;gap:14px">
                    <div style="width:44px;height:44px;border-radius:10px;background:#fdf4ff;
                    display:flex;align-items:center;justify-content:center;
                    flex-shrink:0">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-1">Product report</p>
                        <p class="text-sm text-gray-500">
                            Per-product sales, stock levels, purchases and movements
                        </p>
                    </div>
                </div>
            </a>

            {{-- Profit & Loss --}}
            <a href="{{ route('reports.profit-loss') }}"
               style="display:block;text-decoration:none"
               class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-300
          hover:shadow-sm transition-all">
                <div style="display:flex;align-items:flex-start;gap:14px">
                    <div style="width:44px;height:44px;border-radius:10px;background:#f0fdf4;
                    display:flex;align-items:center;justify-content:center;
                    flex-shrink:0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-1">Profit & Loss</p>
                        <p class="text-sm text-gray-500">
                            Revenue, COGS, gross profit, net profit with daily breakdown
                        </p>
                    </div>
                </div>
            </a>

            {{--Stock Balance--}}
            <a href="{{ route('reports.stock-balance') }}"
               style="display:block;text-decoration:none"
               class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-300
          hover:shadow-sm transition-all">
                <div style="display:flex;align-items:flex-start;gap:14px">
                    <div style="width:44px;height:44px;border-radius:10px;background:#f0fdf4;
                    display:flex;align-items:center;justify-content:center;
                    flex-shrink:0">
                        <svg class="w-6 h-6 text-green-600" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.5"
                                  d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 mb-1">Stock balance report</p>
                        <p class="text-sm text-gray-500">
                            Products, quantities, cost values and selling values by category
                        </p>
                    </div>
                </div>
            </a>
        @endcan

    </div>

@endsection
