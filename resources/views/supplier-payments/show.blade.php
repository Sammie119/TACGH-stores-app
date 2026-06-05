{{-- resources/views/supplier-payments/show.blade.php --}}
@extends('layouts.app')
@section('title', $supplierPayment->reference)
@section('header', 'Payment Details')
@section('subheader', $supplierPayment->reference)

@section('content')

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

        <div class="space-y-4">

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Payment information
                </p>
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Reference</dt>
                        <dd class="font-mono font-semibold text-gray-800">
                            {{ $supplierPayment->reference }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Supplier</dt>
                        <dd class="text-gray-700">
                            {{ $supplierPayment->supplier?->name }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Purchase order</dt>
                        <dd class="text-gray-700">
                            @if($supplierPayment->purchaseOrder)
                                <a href="{{ route('purchase-orders.show', $supplierPayment->purchaseOrder) }}"
                                   class="text-blue-600 hover:underline font-mono text-sm">
                                    {{ $supplierPayment->purchaseOrder->po_number }}
                                </a>
                            @else
                                <span class="text-gray-400">General payment</span>
                            @endif
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Amount</dt>
                        <dd class="font-bold text-green-600 text-base">
                            GHS {{ number_format($supplierPayment->amount, 2) }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Payment method</dt>
                        <dd class="text-gray-700">
                            {{ ucfirst($supplierPayment->payment_method) }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Bank reference</dt>
                        <dd class="font-mono text-xs text-gray-700">
                            {{ $supplierPayment->bank_reference ?? '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Payment date</dt>
                        <dd class="text-gray-700">
                            {{ $supplierPayment->payment_date->format('d M Y') }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Paid by</dt>
                        <dd class="text-gray-700">
                            {{ $supplierPayment->paidBy?->name }}
                        </dd>
                    </div>
                    @if($supplierPayment->notes)
                        <div>
                            <dt class="text-gray-500 mb-1">Notes</dt>
                            <dd class="text-xs text-gray-700 bg-gray-50 p-3 rounded-lg">
                                {{ $supplierPayment->notes }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            <a href="{{ route('supplier-payments.index') }}"
               class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
               style="display:flex;align-items:center;justify-content:center">
                ← Back to payments
            </a>

        </div>

        {{-- Supplier summary --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                Supplier account
            </p>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                <div style="width:44px;height:44px;border-radius:10px;background:#e0f2fe;
                        color:#0369a1;display:flex;align-items:center;
                        justify-content:center;font-size:16px;font-weight:700;
                        flex-shrink:0">
                    {{ strtoupper(substr($supplierPayment->supplier?->name ?? 'S', 0, 2)) }}
                </div>
                <div>
                    <p class="font-medium text-gray-800">
                        {{ $supplierPayment->supplier?->name }}
                    </p>
                    <p class="text-xs text-gray-400 font-mono">
                        {{ $supplierPayment->supplier?->code }}
                    </p>
                </div>
            </div>
            <dl class="space-y-3 text-sm">
                <div style="display:flex;justify-content:space-between;gap:8px">
                    <dt class="text-gray-500">Current balance</dt>
                    <dd class="font-semibold
                           {{ ($supplierPayment->supplier?->balance ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }}">
                        GHS {{ number_format($supplierPayment->supplier?->balance ?? 0, 2) }}
                    </dd>
                </div>
                <div style="display:flex;justify-content:space-between;gap:8px">
                    <dt class="text-gray-500">Phone</dt>
                    <dd class="text-gray-700">
                        {{ $supplierPayment->supplier?->phone ?? '—' }}
                    </dd>
                </div>
            </dl>
            <div style="margin-top:16px">
                <a href="{{ route('suppliers.show', $supplierPayment->supplier) }}"
                   class="text-sm text-blue-600 hover:underline">
                    View supplier profile →
                </a>
            </div>
        </div>

    </div>

@endsection
