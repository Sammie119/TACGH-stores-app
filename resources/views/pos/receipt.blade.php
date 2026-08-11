{{-- resources/views/pos/receipt.blade.php --}}
@extends('layouts.app')
@section('title', 'Receipt — ' . $sale->invoice_no)
@section('header', 'Sale Receipt')
@section('subheader', $sale->invoice_no)

@section('content')

    <div style="display:grid;grid-template-columns:420px 1fr;gap:20px;align-items:start">

        {{-- Receipt card --}}
        <div id="receipt-card"
             class="bg-white border border-gray-200 rounded-xl overflow-hidden">

            {{-- Header --}}
            <div style="padding:24px 24px 16px;text-align:center;
            border-bottom:1px dashed #e5e7eb">

                {{-- Branch logo --}}
                @if($sale->branch?->logo)
                    <div style="margin-bottom:12px">
                        <img src="{{ branch_logo_url($sale->branch->logo) }}"
                             alt="{{ $sale->branch->name }}"
                             style="height:60px;max-width:160px;object-fit:contain;
                    margin:0 auto;display:block">
                    </div>
                @else
                    {{-- Fallback initials avatar --}}
                    <div style="width:52px;height:52px;border-radius:10px;background:#2563eb;
                color:#fff;display:flex;align-items:center;justify-content:center;
                font-size:16px;font-weight:700;margin:0 auto 12px">
                        {{ strtoupper(substr($sale->branch?->name ?? 'S', 0, 2)) }}
                    </div>
                @endif

                <p style="font-size:18px;font-weight:700;color:#111827;margin:0">
                    {{ $sale->branch?->name ?? config('app.name') }}
                </p>
                @if($sale->branch?->address)
                    <p style="font-size:11px;color:#9ca3af;margin-top:3px">
                        {{ $sale->branch->address }}
                    </p>
                @endif
                @if($sale->branch?->phone)
                    <p style="font-size:11px;color:#9ca3af;margin-top:1px">
                        {{ $sale->branch->phone }}
                    </p>
                @endif
            </div>

            {{-- Invoice info --}}
            <div style="padding:16px 24px;border-bottom:1px dashed #e5e7eb">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                    <span style="font-size:12px;color:#6b7280">Receipt</span>
                    <span style="font-size:12px;font-weight:600;font-family:monospace;
                             color:#111827">{{ $sale->invoice_no }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                    <span style="font-size:12px;color:#6b7280">Date</span>
                    <span style="font-size:12px;color:#374151">
                    {{ $sale->created_at->format('d M Y H:i') }}
                </span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                    <span style="font-size:12px;color:#6b7280">Cashier</span>
                    <span style="font-size:12px;color:#374151">{{ $sale->user?->name }}</span>
                </div>
                {{-- Customer --}}
                @if($sale->customer)
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                        <span style="font-size:12px;color:#6b7280">Customer</span>
                        <span style="font-size:12px;color:#374151">{{ $sale->customer->name }}</span>
                    </div>
                @elseif($sale->walkin_name)
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                        <span style="font-size:12px;color:#6b7280">Customer</span>
                        <span style="font-size:12px;color:#374151">{{ $sale->walkin_name }}</span>
                    </div>
                @else
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                        <span style="font-size:12px;color:#6b7280">Customer</span>
                        <span style="font-size:12px;color:#374151">Walk-in</span>
                    </div>
                @endif
            </div>

            {{-- Items --}}
            <div style="padding:16px 24px;border-bottom:1px dashed #e5e7eb">
                <table style="width:100%;border-collapse:collapse">
                    <thead>
                    <tr style="border-bottom:1px solid #f3f4f6">
                        <th style="font-size:11px;color:#9ca3af;text-align:left;
                                   padding-bottom:6px;font-weight:500">Item</th>
                        <th style="font-size:11px;color:#9ca3af;text-align:center;
                                   padding-bottom:6px;font-weight:500">Qty</th>
                        <th style="font-size:11px;color:#9ca3af;text-align:right;
                                   padding-bottom:6px;font-weight:500">Price</th>
                        <th style="font-size:11px;color:#9ca3af;text-align:right;
                                   padding-bottom:6px;font-weight:500">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sale->items as $item)
                        <tr>
                            <td style="font-size:12px;color:#111827;padding:6px 0">
                                {{ $item->product?->name }}
                                @if($item->discount > 0)
                                    <br><span style="font-size:10px;color:#ef4444">
                                - disc GHS {{ number_format($item->discount, 2) }}
                            </span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:#374151;text-align:center;
                                   padding:6px 0">
                                {{ number_format($item->quantity, 0) }}
                            </td>
                            <td style="font-size:12px;color:#374151;text-align:right;
                                   padding:6px 0">
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td style="font-size:12px;font-weight:500;color:#111827;
                                   text-align:right;padding:6px 0">
                                {{ number_format($item->subtotal, 2) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div style="padding:16px 24px;border-bottom:1px dashed #e5e7eb">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                    <span style="font-size:12px;color:#6b7280">Subtotal</span>
                    <span style="font-size:12px;color:#374151">
                    GHS {{ number_format($sale->total_amount + $sale->discount, 2) }}
                </span>
                </div>
                @if($sale->discount > 0)
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                        <span style="font-size:12px;color:#6b7280">Discount</span>
                        <span style="font-size:12px;color:#dc2626">
                    - GHS {{ number_format($sale->discount, 2) }}
                </span>
                    </div>
                @endif
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;
                        border-top:1px solid #f3f4f6;padding-top:8px;margin-top:4px">
                    <span style="font-size:14px;font-weight:700;color:#111827">Total</span>
                    <span style="font-size:14px;font-weight:700;color:#111827">
                    GHS {{ number_format($sale->total_amount, 2) }}
                </span>
                </div>
                @if($sale->payment_method === 'split')
                    <div style="display:flex;justify-content:space-between;margin-bottom:2px">
                        <span style="font-size:12px;color:#6b7280">
                            Paid ({{ ucfirst($sale->split_method_1) }}{{ $sale->split_reference_1 ? ' — '.$sale->split_reference_1 : '' }})
                        </span>
                        <span style="font-size:12px;color:#16a34a;font-weight:500">GHS {{ number_format($sale->split_amount_1, 2) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                        <span style="font-size:12px;color:#6b7280">
                            Paid ({{ ucfirst($sale->split_method_2) }}{{ $sale->split_reference_2 ? ' — '.$sale->split_reference_2 : '' }})
                        </span>
                        <span style="font-size:12px;color:#16a34a;font-weight:500">GHS {{ number_format($sale->split_amount_2, 2) }}</span>
                    </div>
                @else
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                        <span style="font-size:12px;color:#6b7280">
                            Paid ({{ ucfirst($sale->payment_method) }}{{ $sale->transaction_reference ? ' — '.$sale->transaction_reference : '' }})
                        </span>
                        <span style="font-size:12px;color:#16a34a;font-weight:500">GHS {{ number_format($sale->amount_paid, 2) }}</span>
                    </div>
                @endif
                @if($sale->balance_due > 0)
                    <div style="display:flex;justify-content:space-between">
                        <span style="font-size:12px;color:#dc2626;font-weight:500">Balance due</span>
                        <span style="font-size:12px;color:#dc2626;font-weight:600">
                    GHS {{ number_format($sale->balance_due, 2) }}
                </span>
                    </div>
                @else
                    <div style="display:flex;justify-content:space-between">
                        <span style="font-size:12px;color:#6b7280">Change</span>
                        <span style="font-size:12px;color:#16a34a;font-weight:500">
                    GHS {{ number_format($sale->amount_paid - $sale->total_amount, 2) }}
                </span>
                    </div>
                @endif
            </div>

            {{-- Status + footer --}}
            <div style="padding:16px 24px;text-align:center">
                @php
                    $statusColors = [
                        'completed' => ['background:#dcfce7', 'color:#166534'],
                        'partial'   => ['background:#fef3c7', 'color:#92400e'],
                        'credit'    => ['background:#fee2e2', 'color:#991b1b'],
                        'cancelled' => ['background:#f3f4f6', 'color:#374151'],
                    ];
                    $sc = $statusColors[$sale->status] ?? ['background:#f3f4f6', 'color:#374151'];
                @endphp
                <span style="padding:4px 16px;border-radius:20px;font-size:12px;
                         font-weight:600;{{ $sc[0] }};{{ $sc[1] }}">
                {{ strtoupper($sale->status) }}
            </span>
                <p style="font-size:11px;color:#9ca3af;text-align:center;margin-top:8px">
                    {{ setting('receipt_footer', 'Thank you for your business!') }}
                </p>
            </div>

        </div>

        {{-- Actions --}}
        <div class="space-y-4">

            <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Actions
                </p>

                <button onclick="window.print()"
                        class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white text-sm
                           font-medium rounded-lg transition-colors"
                        style="border:none;cursor:pointer">
                    Print receipt
                </button>

                @can('create sales')
                    <a href="{{ route('pos.index') }}"
                       class="w-full h-10 bg-green-600 hover:bg-green-700 text-white text-sm
                      font-medium rounded-lg transition-colors"
                       style="display:flex;align-items:center;justify-content:center">
                        New sale
                    </a>
                @endcan

                <a href="{{ route('sales.show', $sale) }}"
                   class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                      text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:flex;align-items:center;justify-content:center">
                    View full details
                </a>

                <a href="{{ route('sales.index') }}"
                   class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                      text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:flex;align-items:center;justify-content:center">
                    Sales history
                </a>
            </div>

            {{-- Sale summary --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Summary
                </p>
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Items sold</dt>
                        <dd class="font-medium text-gray-800">{{ $sale->items->count() }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Total units</dt>
                        <dd class="font-medium text-gray-800">
                            {{ number_format($sale->items->sum('quantity'), 0) }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Financial year</dt>
                        <dd class="font-medium text-gray-800">
                            {{ $sale->financialYear?->name ?? '—' }}
                        </dd>
                    </div>
                </dl>
            </div>

        </div>

    </div>

    @push('scripts')
        <style>
            @media print {
                @page { size: A5 portrait; margin: 15mm 18mm; }

                body * { visibility: hidden; }
                #receipt-card, #receipt-card * { visibility: visible; }
                #receipt-card {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    border: none !important;
                    border-radius: 0 !important;
                    box-shadow: none !important;
                }
                #receipt-card img {
                    max-height: 80px !important;
                    max-width: 200px !important;
                    object-fit: contain !important;
                }
            }
        </style>
    @endpush

@endsection
