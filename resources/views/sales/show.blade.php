{{-- resources/views/sales/show.blade.php --}}
@extends('layouts.app')
@section('title', $sale->invoice_no)
@section('header', 'Sale Details')
@section('subheader', $sale->invoice_no)

@section('content')

    <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:20px;align-items:start">

        {{-- Left: sale info --}}
        <div class="space-y-4">

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Sale information
                </p>
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Invoice</dt>
                        <dd class="font-mono font-semibold text-gray-800">{{ $sale->invoice_no }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Branch</dt>
                        <dd class="text-gray-700">{{ $sale->branch?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Cashier</dt>
                        <dd class="text-gray-700">{{ $sale->user?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Customer</dt>
                        <dd class="text-gray-700">{{ $sale->customer?->name ?? $sale->walkin_name ?? 'Walk-in' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Payment</dt>
                        <dd class="text-gray-700">{{ ucfirst($sale->payment_method) }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Financial year</dt>
                        <dd class="text-gray-700">{{ $sale->financialYear?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Date</dt>
                        <dd class="text-gray-700">{{ $sale->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            @php
                                $statusColors = [
                                    'completed' => 'background:#dcfce7;color:#166534',
                                    'partial'   => 'background:#fef3c7;color:#92400e',
                                    'credit'    => 'background:#fee2e2;color:#991b1b',
                                    'cancelled' => 'background:#f3f4f6;color:#374151',
                                ];
                                $sc = $statusColors[$sale->status] ?? 'background:#f3f4f6;color:#374151';
                            @endphp
                            <span style="padding:2px 10px;border-radius:20px;font-size:11px;
                                     font-weight:500;{{ $sc }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Totals --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Payment summary
                </p>
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Subtotal</dt>
                        <dd class="text-gray-700">
                            GHS {{ number_format($sale->total_amount + $sale->discount, 2) }}
                        </dd>
                    </div>
                    @if($sale->discount > 0)
                        <div style="display:flex;justify-content:space-between">
                            <dt class="text-gray-500">Discount</dt>
                            <dd class="text-red-600">
                                - GHS {{ number_format($sale->discount, 2) }}
                            </dd>
                        </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;
                            border-top:1px solid #f3f4f6;padding-top:10px">
                        <dt class="font-semibold text-gray-800">Total</dt>
                        <dd class="font-semibold text-gray-800">
                            GHS {{ number_format($sale->total_amount, 2) }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Amount paid</dt>
                        <dd class="text-green-600 font-medium">
                            GHS {{ number_format($sale->amount_paid, 2) }}
                        </dd>
                    </div>
                    @if($sale->balance_due > 0)
                        <div style="display:flex;justify-content:space-between">
                            <dt class="text-red-600 font-medium">Balance due</dt>
                            <dd class="text-red-600 font-semibold">
                                GHS {{ number_format($sale->balance_due, 2) }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Actions --}}
            <div class="space-y-3">
                <a href="{{ route('pos.receipt', $sale) }}"
                   class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white text-sm
                      font-medium rounded-lg transition-colors"
                   style="display:flex;align-items:center;justify-content:center;gap:6px">
                    View receipt
                </a>
                <a href="{{ route('pdf.receipt', $sale) }}"
                   target="_blank"
                   class="w-full h-10 bg-red-600 hover:bg-red-700 text-white text-sm
                    font-medium rounded-lg transition-colors"
                   style="display:flex;align-items:center;justify-content:center;gap:6px">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    PDF receipt
                </a>
                @can('cancel sales')
                    @if(!in_array($sale->status, ['cancelled']))
                        <form method="POST" action="{{ route('sales.cancel', $sale) }}"
                              onsubmit="return confirm('Cancel this sale? Stock will be restored.')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="w-full h-10 bg-white border border-red-300 text-red-600
                               hover:bg-red-600 hover:text-white text-sm font-medium
                               rounded-lg transition-colors">
                                Cancel sale
                            </button>
                        </form>
                    @endif
                @endcan
                <a href="{{ route('sales.index') }}"
                   class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                      text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:flex;align-items:center;justify-content:center">
                    ← Back to sales
                </a>
            </div>

        </div>

        {{-- Right: items --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Items sold</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $sale->items->count() }} {{ Str::plural('item', $sale->items->count()) }}
                    · {{ number_format($sale->items->sum('quantity'), 2) }} total units
                </p>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">
                        Product
                    </th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">
                        Qty
                    </th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">
                        Unit price
                    </th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">
                        Discount
                    </th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">
                        Subtotal
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($sale->items as $item)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ $item->product?->name }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $item->product?->sku }}</p>
                        </td>
                        <td class="px-5 py-3 text-right text-gray-700">
                            {{ number_format($item->quantity, 2) }}
                            <span class="text-xs text-gray-400">{{ $item->product?->unit }}</span>
                        </td>
                        <td class="px-5 py-3 text-right text-gray-700">
                            GHS {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="px-5 py-3 text-right text-red-500">
                            @if($item->discount > 0)
                                - GHS {{ number_format($item->discount, 2) }}
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">
                            GHS {{ number_format($item->subtotal, 2) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="bg-gray-50 border-t border-gray-200">
                    <td colspan="4" class="px-5 py-3 text-right font-semibold text-gray-700">
                        Total
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-gray-800">
                        GHS {{ number_format($sale->total_amount, 2) }}
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>

    </div>

@endsection
