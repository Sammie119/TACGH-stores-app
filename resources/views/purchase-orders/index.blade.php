{{-- resources/views/purchase-orders/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Purchase Orders')
@section('header', 'Purchase Orders')
@section('subheader', 'Manage stock purchases from suppliers')

@section('content')

    {{-- Summary --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Pending approval
            </p>
            <p class="text-2xl font-semibold mt-1
                  {{ $pendingCount > 0 ? 'text-amber-600' : 'text-gray-800' }}">
                {{ $pendingCount }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Awaiting approval</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Overdue deliveries
            </p>
            <p class="text-2xl font-semibold mt-1
                  {{ $overdueCount > 0 ? 'text-red-600' : 'text-gray-800' }}">
                {{ $overdueCount }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Past expected date</p>
        </div>
    </div>

    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"
              method="GET" action="{{ route('purchase-orders.index') }}">

            <select name="supplier_id"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                       text-sm text-gray-700 focus:outline-none
                       focus:ring-2 focus:ring-blue-500">
                <option value="">All suppliers</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"
                        {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>

            <select name="status"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                       text-sm text-gray-700 focus:outline-none
                       focus:ring-2 focus:ring-blue-500">
                <option value="">All statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}"
                        {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>

            @if($isSuperAdmin)
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
            @endif

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                      text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                      text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                       text-sm font-medium rounded-lg hover:bg-gray-50
                       transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['supplier_id','status','branch_id','date_from','date_to']))
                <a href="{{ route('purchase-orders.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500">Clear</a>
            @endif
        </form>

        @can('create purchase orders')
            <a href="{{ route('purchase-orders.create') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
              font-medium rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                New order
            </a>
        @endcan
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('purchase-orders.index')"
    />

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:700px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">PO Number</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Supplier</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Branch</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Amount</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Balance</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Order date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                @php
                    $sc = [
                        'draft'     => 'background:#f3f4f6;color:#374151',
                        'pending'   => 'background:#fef3c7;color:#92400e',
                        'approved'  => 'background:#dcfce7;color:#166534',
                        'ordered'   => 'background:#dbeafe;color:#1e40af',
                        'partial'   => 'background:#fef3c7;color:#92400e',
                        'received'  => 'background:#f3f4f6;color:#374151',
                        'cancelled' => 'background:#fee2e2;color:#991b1b',
                    ][$order->status] ?? 'background:#f3f4f6;color:#374151';

                    $isOverdue = in_array($order->status, ['ordered','partial'])
                        && $order->expected_date
                        && $order->expected_date->isPast();
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $isOverdue ? 'bg-red-50' : '' }}
                       {{ $order->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3">
                        <a href="{{ route('purchase-orders.show', $order) }}"
                           class="font-mono text-sm text-blue-600 hover:underline font-medium">
                            {{ $order->po_number }}
                        </a>
                        @if($isOverdue)
                            <p class="text-xs text-red-500">Overdue</p>
                        @endif
                    </td>

                    <td class="px-5 py-3 text-gray-700">
                        {{ $order->supplier?->name }}
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $order->branch?->name }}
                    </td>

                    <td class="px-5 py-3 text-right font-medium text-gray-800">
                        GHS {{ number_format($order->total_amount, 2) }}
                    </td>

                    <td class="px-5 py-3 text-right">
                        @if($order->balance_due > 0)
                            <span class="font-medium text-red-600">
                        GHS {{ number_format($order->balance_due, 2) }}
                    </span>
                        @else
                            <span class="text-gray-400 text-xs">Paid</span>
                        @endif
                    </td>

                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;{{ $sc }}">
                        {{ ucfirst($order->status) }}
                    </span>
                    </td>

                    <td class="px-5 py-3 text-xs text-gray-500">
                        {{ $order->order_date->format('d M Y') }}
                        @if($order->expected_date)
                            <br><span class="text-gray-400">
                        Expected: {{ $order->expected_date->format('d M Y') }}
                    </span>
                        @endif
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                            @if($order->trashed())
                                <form method="POST"
                                      action="{{ route('purchase-orders.restore', $order->id) }}">
                                    @csrf @method('PATCH')
                                    <button class="text-xs text-green-600
                                               hover:underline font-medium">
                                        Restore
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('purchase-orders.show', $order) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">
                                    View
                                </a>

                                @if($order->status === 'pending')
                                    @can('approve purchase orders')
                                        <form method="POST"
                                              action="{{ route('purchase-orders.approve', $order) }}">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-green-600
                                                   hover:underline font-medium">
                                                Approve
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                @if($order->status === 'approved')
                                    @can('create purchase orders')
                                        <form method="POST"
                                              action="{{ route('purchase-orders.mark-ordered', $order) }}">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-blue-600
                                                   hover:underline font-medium">
                                                Mark ordered
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                @if(in_array($order->status, ['draft','pending']))
                                    @can('edit purchase orders')
                                        <a href="{{ route('purchase-orders.edit', $order) }}"
                                           class="text-xs text-gray-600 hover:underline font-medium">
                                            Edit
                                        </a>
                                    @endcan
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8"
                        class="px-5 py-12 text-center text-gray-400">
                        No purchase orders found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        @if($orders->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

@endsection
