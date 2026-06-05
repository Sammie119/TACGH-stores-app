{{-- resources/views/supplier-payments/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Supplier Payments')
@section('header', 'Supplier Payments')
@section('subheader', 'Payments made to suppliers')

@section('content')

    {{-- Summary --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total paid out
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                GHS {{ number_format($totalPaid, 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total payments
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                {{ $payments->total() }}
            </p>
        </div>
    </div>

    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"
              method="GET" action="{{ route('supplier-payments.index') }}">

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

            @if(request()->hasAny(['supplier_id','date_from','date_to']))
                <a href="{{ route('supplier-payments.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500">Clear</a>
            @endif
        </form>

        @can('create supplier payments')
            <a href="{{ route('supplier-payments.create') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
              font-medium rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                Record payment
            </a>
        @endcan
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:650px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Reference</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Supplier</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Purchase order</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Amount</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Method</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Paid by</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($payments as $payment)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">

                    <td class="px-5 py-3 font-mono text-xs text-gray-600">
                        {{ $payment->reference }}
                    </td>

                    <td class="px-5 py-3 text-gray-700">
                        {{ $payment->supplier?->name }}
                    </td>

                    <td class="px-5 py-3">
                        @if($payment->purchaseOrder)
                            <a href="{{ route('purchase-orders.show', $payment->purchaseOrder) }}"
                               class="font-mono text-xs text-blue-600 hover:underline">
                                {{ $payment->purchaseOrder->po_number }}
                            </a>
                        @else
                            <span class="text-gray-400 text-xs">General payment</span>
                        @endif
                    </td>

                    <td class="px-5 py-3 text-right font-semibold text-green-600">
                        GHS {{ number_format($payment->amount, 2) }}
                    </td>

                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#f3f4f6;color:#374151">
                        {{ ucfirst($payment->payment_method) }}
                    </span>
                    </td>

                    <td class="px-5 py-3 text-xs text-gray-500">
                        {{ $payment->payment_date->format('d M Y') }}
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $payment->paidBy?->name }}
                    </td>

                    <td class="px-5 py-3">
                        <a href="{{ route('supplier-payments.show', $payment) }}"
                           class="text-xs text-blue-600 hover:underline font-medium">
                            View
                        </a>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="8"
                        class="px-5 py-12 text-center text-gray-400">
                        No payments recorded yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        @if($payments->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

@endsection
