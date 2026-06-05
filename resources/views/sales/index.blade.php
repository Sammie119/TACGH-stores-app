{{-- resources/views/sales/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Sales')
@section('header', 'Sales History')
@section('subheader', 'All transactions')

@section('content')

    {{-- Today summary --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Today's revenue</p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                GHS {{ number_format($todayTotal, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ now()->format('d M Y') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Today's transactions</p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                {{ number_format($todayCount) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Completed sales</p>
        </div>
    </div>

    {{-- Filters --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"
              method="GET" action="{{ route('sales.index') }}">

            <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search invoice…"
                       class="h-9 pl-9 pr-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none focus:ring-2
                          focus:ring-blue-500"
                       style="width:180px">
            </div>

            @if($isSuperAdmin || $canViewAll)
                <select name="branch"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                       text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}"
                            {{ request('branch') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <select name="status"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                       text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}"
                        {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>

            <select name="payment_method"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                       text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All payment methods</option>
                @foreach($paymentMethods as $method)
                    <option value="{{ $method }}"
                        {{ request('payment_method') === $method ? 'selected' : '' }}>
                        {{ ucfirst($method) }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                      text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">

            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                      text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                       text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search','branch','status','payment_method','date_from','date_to']))
                <a href="{{ route('sales.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500 transition-colors">Clear</a>
            @endif
        </form>

        @can('create sales')
            <a href="{{ route('pos.index') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium
              rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New sale
            </a>
        @endcan
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('sales.index')"
    />

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Branch</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Cashier</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Payment</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($sales as $sale)
                @php
                    $statusColors = [
                        'completed' => 'background:#dcfce7;color:#166534',
                        'partial'   => 'background:#fef3c7;color:#92400e',
                        'credit'    => 'background:#fee2e2;color:#991b1b',
                        'cancelled' => 'background:#f3f4f6;color:#374151',
                    ];
                    $sc = $statusColors[$sale->status] ?? 'background:#f3f4f6;color:#374151';
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $sale->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3">
                    <span class="font-mono text-sm font-medium text-blue-600">
                        {{ $sale->invoice_no }}
                    </span>
                    </td>

                    <td class="px-5 py-3 text-gray-600">{{ $sale->branch?->name ?? '—' }}</td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $sale->customer?->name ?? 'Walk-in' }}
                    </td>

                    <td class="px-5 py-3 text-gray-600">{{ $sale->user?->name ?? '—' }}</td>

                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">
                            GHS {{ number_format($sale->total_amount, 2) }}
                        </p>
                        @if($sale->balance_due > 0)
                            <p class="text-xs text-red-500">
                                Balance: GHS {{ number_format($sale->balance_due, 2) }}
                            </p>
                        @endif
                    </td>

                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#f3f4f6;color:#374151">
                        {{ ucfirst($sale->payment_method) }}
                    </span>
                    </td>

                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;{{ $sc }}">
                        {{ ucfirst($sale->status) }}
                    </span>
                    </td>

                    <td class="px-5 py-3 text-xs text-gray-500">
                        {{ $sale->created_at->format('d M Y') }}<br>
                        <span class="text-gray-400">{{ $sale->created_at->format('H:i') }}</span>
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($sale->trashed())
                                <form method="POST" action="{{ route('sales.restore', $sale->id) }}">
                                    @csrf @method('PATCH')
                                    <button class="text-xs text-green-600 hover:underline font-medium">
                                        Restore
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('sales.show', $sale) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">
                                    View
                                </a>
                                @can('cancel sales')
                                    @if(!in_array($sale->status, ['cancelled']))
                                        <form method="POST" action="{{ route('sales.cancel', $sale) }}"
                                              onsubmit="return confirm('Cancel this sale? Stock will be restored.')">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-red-500 hover:underline font-medium">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            @endif
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-5 py-12 text-center text-gray-400">
                        No sales found.
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
