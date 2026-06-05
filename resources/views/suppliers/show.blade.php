{{-- resources/views/suppliers/show.blade.php --}}
@extends('layouts.app')
@section('title', $supplier->name)
@section('header', $supplier->name)
@section('subheader', 'Supplier profile — ' . $supplier->code)

@section('content')

    <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start">

        {{-- Left: profile --}}
        <div class="space-y-4">

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div style="width:52px;height:52px;border-radius:10px;background:#e0f2fe;
                        color:#0369a1;display:flex;align-items:center;
                        justify-content:center;font-size:18px;font-weight:700;
                        margin-bottom:14px">
                    {{ strtoupper(substr($supplier->name, 0, 2)) }}
                </div>
                <p class="font-semibold text-gray-800">{{ $supplier->name }}</p>
                <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $supplier->code }}</p>

                @if($supplier->balance > 0)
                    <div style="margin-top:12px;padding:8px 12px;background:#fee2e2;
                        border-radius:8px">
                        <p style="font-size:12px;color:#dc2626;font-weight:500">
                            Owed: GHS {{ number_format($supplier->balance, 2) }}
                        </p>
                    </div>
                @else
                    <div style="margin-top:12px;padding:8px 12px;background:#f0fdf4;
                        border-radius:8px">
                        <p style="font-size:12px;color:#16a34a;font-weight:500">
                            Account settled ✓
                        </p>
                    </div>
                @endif
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase
                       tracking-widest mb-4">Details</p>
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Contact</dt>
                        <dd class="text-gray-700">
                            {{ $supplier->contact_person ?? '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="text-gray-700">{{ $supplier->phone ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-700">{{ $supplier->email ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Total orders</dt>
                        <dd class="font-semibold text-gray-800">{{ $totalOrders }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Total purchased</dt>
                        <dd class="font-semibold text-gray-800">
                            GHS {{ number_format($totalPurchased, 2) }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Total paid</dt>
                        <dd class="font-semibold text-green-600">
                            GHS {{ number_format($totalPaid, 2) }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div style="display:flex;flex-direction:column;gap:8px">
                @can('create purchase orders')
                    <a href="{{ route('purchase-orders.create') }}?supplier_id={{ $supplier->id }}"
                       class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white text-sm
                      font-medium rounded-lg transition-colors"
                       style="display:flex;align-items:center;justify-content:center;gap:6px">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New purchase order
                    </a>
                @endcan

                @if($supplier->balance > 0)
                    @can('create supplier payments')
                        <a href="{{ route('supplier-payments.create') }}?supplier_id={{ $supplier->id }}"
                           class="w-full h-10 bg-green-600 hover:bg-green-700 text-white text-sm
                      font-medium rounded-lg transition-colors"
                           style="display:flex;align-items:center;justify-content:center;gap:6px">
                            Record payment
                        </a>
                    @endcan
                @endif

                @can('edit suppliers')
                    <a href="{{ route('suppliers.edit', $supplier) }}"
                       class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                      text-gray-600 text-sm font-medium rounded-lg transition-colors"
                       style="display:flex;align-items:center;justify-content:center">
                        Edit supplier
                    </a>
                @endcan
            </div>

        </div>

        {{-- Right: orders + payments --}}
        <div class="space-y-6">

            {{-- Recent purchase orders --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100"
                     style="display:flex;align-items:center;justify-content:space-between">
                    <p class="font-semibold text-gray-700">Purchase orders</p>
                    @can('create purchase orders')
                        <a href="{{ route('purchase-orders.index') }}?supplier_id={{ $supplier->id }}"
                           class="text-xs text-blue-600 hover:underline">
                            View all
                        </a>
                    @endcan
                </div>
                <table class="w-full text-sm" style="border-collapse:collapse">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                                   text-gray-500 uppercase">PO Number</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                                   text-gray-500 uppercase">Branch</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                                   text-gray-500 uppercase">Amount</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                                   text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                                   text-gray-500 uppercase">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($supplier->purchaseOrders->take(5) as $order)
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
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <a href="{{ route('purchase-orders.show', $order) }}"
                                   class="font-mono text-sm text-blue-600 hover:underline">
                                    {{ $order->po_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $order->branch?->name }}
                            </td>
                            <td class="px-5 py-3 text-right font-medium text-gray-800">
                                GHS {{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-5 py-3">
                            <span style="padding:2px 8px;border-radius:20px;
                                         font-size:11px;font-weight:500;{{ $sc }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-400">
                                {{ $order->order_date->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="px-5 py-8 text-center text-gray-400 text-sm">
                                No orders yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Recent payments --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <p class="font-semibold text-gray-700">Payment history</p>
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
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($supplier->payments->take(5) as $payment)
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
                            <td class="px-5 py-3 text-xs text-gray-400">
                                {{ $payment->payment_date->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                                class="px-5 py-8 text-center text-gray-400 text-sm">
                                No payments yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
