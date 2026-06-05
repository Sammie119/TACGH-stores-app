{{-- resources/views/purchase-orders/show.blade.php --}}
@extends('layouts.app')
@section('title', $purchaseOrder->po_number)
@section('header', 'Purchase Order')
@section('subheader', $purchaseOrder->po_number)

@section('content')

    {{-- Status timeline --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        @php
            $steps        = ['pending','approved','ordered','received'];
            $currentIndex = array_search($purchaseOrder->status, $steps);
            $isCancelled  = $purchaseOrder->status === 'cancelled';
            $isPartial    = $purchaseOrder->status === 'partial';
        @endphp
        <div style="display:flex;align-items:center">
            @foreach($steps as $i => $step)
                @php
                    $done = !$isCancelled && $currentIndex !== false && $currentIndex >= $i;
                @endphp
                <div style="display:flex;align-items:center;flex:1">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:6px">
                        <div style="width:32px;height:32px;border-radius:50%;display:flex;
                            align-items:center;justify-content:center;font-size:12px;
                            font-weight:600;flex-shrink:0;
                            background:{{ $done ? '#2563eb' : '#f3f4f6' }};
                            color:{{ $done ? '#fff' : '#9ca3af' }}">
                            @if($done)
                                <svg width="14" height="14" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <p style="font-size:11px;font-weight:500;white-space:nowrap;
                          color:{{ $done ? '#2563eb' : '#9ca3af' }}">
                            {{ ucfirst($step) }}
                        </p>
                    </div>
                    @if(!$loop->last)
                        <div style="flex:1;height:2px;margin:0 8px;margin-bottom:18px;
                        background:{{ ($done && $currentIndex !== false && $currentIndex > $i) ? '#2563eb' : '#e5e7eb' }}">
                        </div>
                    @endif
                </div>
            @endforeach

            @if($isCancelled)
                <span style="margin-left:16px;margin-bottom:18px;padding:3px 10px;
                     border-radius:20px;background:#fee2e2;color:#991b1b;
                     font-size:11px;font-weight:500">
            Cancelled
        </span>
            @elseif($isPartial)
                <span style="margin-left:16px;margin-bottom:18px;padding:3px 10px;
                     border-radius:20px;background:#fef3c7;color:#92400e;
                     font-size:11px;font-weight:500">
            Partially received
        </span>
            @endif
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:20px;align-items:start">

        {{-- Left: info + actions --}}
        <div class="space-y-4">

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Order information
                </p>
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">PO Number</dt>
                        <dd class="font-mono font-semibold text-gray-800">
                            {{ $purchaseOrder->po_number }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Supplier</dt>
                        <dd class="text-gray-700">
                            {{ $purchaseOrder->supplier?->name }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Branch</dt>
                        <dd class="text-gray-700">{{ $purchaseOrder->branch?->name }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Created by</dt>
                        <dd class="text-gray-700">{{ $purchaseOrder->createdBy?->name }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Approved by</dt>
                        <dd class="text-gray-700">
                            {{ $purchaseOrder->approvedBy?->name ?? '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Order date</dt>
                        <dd class="text-gray-700">
                            {{ $purchaseOrder->order_date->format('d M Y') }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Expected</dt>
                        <dd class="text-gray-700">
                            {{ $purchaseOrder->expected_date?->format('d M Y') ?? '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Received</dt>
                        <dd class="text-gray-700">
                            {{ $purchaseOrder->received_date?->format('d M Y') ?? '—' }}
                        </dd>
                    </div>
                    <div style="border-top:1px solid #f3f4f6;padding-top:10px;margin-top:4px">
                        <div style="display:flex;justify-content:space-between;gap:8px;
                                margin-bottom:4px">
                            <dt class="text-gray-500">Total amount</dt>
                            <dd class="font-semibold text-gray-800">
                                GHS {{ number_format($purchaseOrder->total_amount, 2) }}
                            </dd>
                        </div>
                        <div style="display:flex;justify-content:space-between;gap:8px;
                                margin-bottom:4px">
                            <dt class="text-gray-500">Amount paid</dt>
                            <dd class="font-medium text-green-600">
                                GHS {{ number_format($purchaseOrder->amount_paid, 2) }}
                            </dd>
                        </div>
                        <div style="display:flex;justify-content:space-between;gap:8px">
                            <dt class="font-medium
                                   {{ $purchaseOrder->balance_due > 0 ? 'text-red-600' : 'text-gray-500' }}">
                                Balance due
                            </dt>
                            <dd class="font-semibold
                                   {{ $purchaseOrder->balance_due > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                GHS {{ number_format($purchaseOrder->balance_due, 2) }}
                            </dd>
                        </div>
                    </div>
                </dl>
            </div>

            {{-- Actions --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">
                    Actions
                </p>

                @if($purchaseOrder->status === 'pending')
                    @can('approve purchase orders')
                        <form method="POST"
                              action="{{ route('purchase-orders.approve', $purchaseOrder) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="w-full h-10 bg-green-600 hover:bg-green-700
                                   text-white text-sm font-medium rounded-lg
                                   transition-colors">
                                ✓ Approve order
                            </button>
                        </form>
                    @endcan
                @endif

                @if($purchaseOrder->status === 'approved')
                    @can('create purchase orders')
                        <form method="POST"
                              action="{{ route('purchase-orders.mark-ordered', $purchaseOrder) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="w-full h-10 bg-blue-600 hover:bg-blue-700
                                   text-white text-sm font-medium rounded-lg
                                   transition-colors">
                                Mark as ordered
                            </button>
                        </form>
                    @endcan
                @endif

                @if(in_array($purchaseOrder->status, ['draft','pending']))
                    @can('edit purchase orders')
                        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}"
                           class="w-full h-10 bg-gray-100 hover:bg-gray-200 text-gray-700
                          text-sm font-medium rounded-lg transition-colors"
                           style="display:flex;align-items:center;justify-content:center">
                            Edit order
                        </a>
                    @endcan
                @endif

                @if($purchaseOrder->balance_due > 0 &&
                    in_array($purchaseOrder->status, ['received','partial']))
                    @can('create supplier payments')
                        <a href="{{ route('supplier-payments.create') }}?supplier_id={{ $purchaseOrder->supplier_id }}&po_id={{ $purchaseOrder->id }}"
                           class="w-full h-10 bg-green-600 hover:bg-green-700 text-white
                          text-sm font-medium rounded-lg transition-colors"
                           style="display:flex;align-items:center;justify-content:center">
                            Record payment
                        </a>
                    @endcan
                @endif

                @if(!in_array($purchaseOrder->status, ['received','cancelled']))
                    <form method="POST"
                          action="{{ route('purchase-orders.cancel', $purchaseOrder) }}"
                          onsubmit="return confirm('Cancel this purchase order?')">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="w-full h-10 bg-white border border-red-300
                                   text-red-600 hover:bg-red-600 hover:text-white
                                   text-sm font-medium rounded-lg transition-colors">
                            Cancel order
                        </button>
                    </form>
                @endif

                {{-- Print PDF --}}
                <a href="{{ route('pdf.purchase-order', $purchaseOrder) }}"
                   target="_blank"
                   class="w-full h-10 bg-red-600 hover:bg-red-700 text-white text-sm
                            font-medium rounded-lg transition-colors"
                   style="display:flex;align-items:center;justify-content:center;gap:6px">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </a>

                <a href="{{ route('purchase-orders.index') }}"
                   class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                      text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:flex;align-items:center;justify-content:center">
                    ← Back to orders
                </a>
            </div>

        </div>

        {{-- Right: items + receive + payments --}}
        <div class="space-y-4">

            {{-- Items table --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <p class="font-semibold text-gray-700">Order items</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $purchaseOrder->items->count() }} products
                    </p>
                </div>
                <table class="w-full text-sm" style="border-collapse:collapse">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                                   text-gray-500 uppercase">Product</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                                   text-gray-500 uppercase">Ordered</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                                   text-gray-500 uppercase">Received</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                                   text-gray-500 uppercase">Unit cost</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                                   text-gray-500 uppercase">Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($purchaseOrder->items as $item)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">
                                    {{ $item->product?->name }}
                                </p>
                                <p class="text-xs text-gray-400 font-mono">
                                    {{ $item->product?->sku }}
                                </p>
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700">
                                {{ number_format($item->quantity_ordered, 2) }}
                                <span class="text-xs text-gray-400">
                                {{ $item->product?->unit }}
                            </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                @if($item->quantity_received >= $item->quantity_ordered)
                                    <span class="font-medium text-green-600">
                                    {{ number_format($item->quantity_received, 2) }}
                                </span>
                                @elseif($item->quantity_received > 0)
                                    <span class="font-medium text-amber-600">
                                    {{ number_format($item->quantity_received, 2) }}
                                </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700">
                                GHS {{ number_format($item->unit_cost, 2) }}
                            </td>
                            <td class="px-5 py-3 text-right font-medium text-gray-800">
                                GHS {{ number_format($item->subtotal, 2) }}
                            </td>
                        </tr>
                    @endforeach
                    {{-- Total row --}}
                    <tr class="bg-gray-50 border-t border-gray-200">
                        <td colspan="4" class="px-5 py-3 text-right text-sm
                                               font-semibold text-gray-700">
                            Total
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-blue-600">
                            GHS {{ number_format($purchaseOrder->total_amount, 2) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            {{-- Receive stock form --}}
            @if(in_array($purchaseOrder->status, ['approved','ordered','partial']))
                @can('receive purchase orders')
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <p class="font-semibold text-gray-700">Receive stock</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Enter the quantity actually received for each item
                            </p>
                        </div>
                        <form method="POST"
                              action="{{ route('purchase-orders.receive', $purchaseOrder) }}">
                            @csrf

                            <div class="p-5 space-y-3">

                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Received date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="received_date"
                                           value="{{ today()->format('Y-m-d') }}"
                                           class="h-10 px-3 rounded-lg border border-gray-300
                                      bg-white text-sm text-gray-800 focus:outline-none
                                      focus:ring-2 focus:ring-blue-500"
                                           style="width:200px">
                                </div>

                                <table class="w-full text-sm" style="border-collapse:collapse">
                                    <thead>
                                    <tr class="border-b border-gray-100 bg-gray-50">
                                        <th class="px-4 py-2.5 text-left text-xs font-semibold
                                           text-gray-500 uppercase">Product</th>
                                        <th class="px-4 py-2.5 text-right text-xs font-semibold
                                           text-gray-500 uppercase">Ordered</th>
                                        <th class="px-4 py-2.5 text-right text-xs font-semibold
                                           text-gray-500 uppercase">Qty received</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($purchaseOrder->items as $item)
                                        <tr class="border-b border-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-800">
                                                {{ $item->product?->name }}
                                            </td>
                                            <td class="px-4 py-3 text-right text-gray-600">
                                                {{ number_format($item->quantity_ordered, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <input type="number"
                                                       name="received_quantities[{{ $item->id }}]"
                                                       value="{{ $item->quantity_ordered }}"
                                                       min="0"
                                                       step="0.01"
                                                       max="{{ $item->quantity_ordered }}"
                                                       style="width:90px;height:32px;padding:0 8px;
                                                  border:1px solid #d1d5db;border-radius:6px;
                                                  font-size:13px;text-align:right;outline:none">
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>

                            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                                <button type="submit"
                                        class="h-10 px-6 bg-green-600 hover:bg-green-700 text-white
                                   text-sm font-medium rounded-lg transition-colors
                                   focus:outline-none focus:ring-2 focus:ring-green-500
                                   focus:ring-offset-2">
                                    ✓ Confirm receipt & update stock
                                </button>
                            </div>
                        </form>
                    </div>
                @endcan
            @endif

            {{-- Payment history --}}
            @if($purchaseOrder->payments->count() > 0)
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="font-semibold text-gray-700">Payments made</p>
                    </div>
                    <table class="w-full text-sm" style="border-collapse:collapse">
                        <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-2.5 text-left text-xs font-semibold
                                   text-gray-500 uppercase">Reference</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold
                                   text-gray-500 uppercase">Amount</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold
                                   text-gray-500 uppercase">Method</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold
                                   text-gray-500 uppercase">Date</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold
                                   text-gray-500 uppercase">Paid by</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($purchaseOrder->payments as $payment)
                            <tr class="border-b border-gray-50 hover:bg-gray-50">
                                <td class="px-5 py-3 font-mono text-xs text-gray-600">
                                    {{ $payment->reference }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-green-600">
                                    GHS {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    {{ ucfirst($payment->payment_method) }}
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-500">
                                    {{ $payment->payment_date->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">
                                    {{ $payment->paidBy?->name }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Notes --}}
            @if($purchaseOrder->notes)
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">
                        Notes
                    </p>
                    <p class="text-sm text-gray-700 leading-relaxed">
                        {{ $purchaseOrder->notes }}
                    </p>
                </div>
            @endif

        </div>
    </div>

@endsection
