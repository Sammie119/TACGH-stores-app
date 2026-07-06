{{-- resources/views/pdf/consignment-invoice.blade.php --}}
@extends('pdf.layouts.base')

@section('title', 'Invoice ' . $consignment->reference_no)
@section('doc-title', 'CONSIGNMENT INVOICE')

@php $branch = $consignment->branch; @endphp

@section('content')

    {{-- Consignment info --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Invoice No.</div>
            <div class="info-value font-bold monospace" style="font-size:14px">
                {{ $consignment->reference_no }}
            </div>
            <div class="text-gray text-sm" style="margin-top:4px">
                Status: <strong>{{ ucfirst($consignment->status) }}</strong>
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Recipient</div>
            <div class="info-value font-bold">{{ $consignment->recipient_name }}</div>
            @if($consignment->customer?->phone)
                <div class="text-gray text-sm">{{ $consignment->customer->phone }}</div>
            @endif
        </div>
        <div class="info-box">
            <div class="info-label">Dispatched from</div>
            <div class="info-value font-bold">{{ $consignment->branch?->name }}</div>
            <div class="text-gray text-sm">{{ $consignment->branch?->address }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Date</div>
            <div class="text-sm">
                <span class="text-gray">Created:</span>
                {{ $consignment->created_at->format('d M Y') }}
            </div>
        </div>
    </div>

    {{-- Items --}}
    <table>
        <thead>
        <tr>
            <th style="text-align:left">#</th>
            <th style="text-align:left">Product</th>
            <th style="text-align:left">SKU</th>
            <th style="text-align:right">Qty</th>
            <th style="text-align:right">Unit price</th>
            <th style="text-align:right">Subtotal</th>
        </tr>
        </thead>
        <tbody>
        @foreach($consignment->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="font-bold">{{ $item->product?->name }}</td>
                <td class="monospace text-sm">{{ $item->product?->sku }}</td>
                <td class="text-right">{{ number_format($item->quantity, 0) }}</td>
                <td class="text-right">GHS {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right font-bold">GHS {{ number_format($item->subtotal, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="5" class="text-right">Total value</td>
            <td class="text-right text-blue">
                GHS {{ number_format($consignment->total_value, 2) }}
            </td>
        </tr>
        <tr>
            <td colspan="5" class="text-right">Amount paid</td>
            <td class="text-right text-green">
                GHS {{ number_format($consignment->amount_paid, 2) }}
            </td>
        </tr>
        <tr>
            <td colspan="5" class="text-right">Balance due</td>
            <td class="text-right {{ $consignment->balance_due > 0 ? 'text-red' : 'text-green' }}">
                GHS {{ number_format($consignment->balance_due, 2) }}
            </td>
        </tr>
        </tfoot>
    </table>

    {{-- Notes --}}
    @if($consignment->notes)
        <div style="margin-bottom:20px">
            <div style="font-size:10px;font-weight:bold;color:#374151;margin-bottom:4px">
                Notes
            </div>
            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;
                padding:10px 12px;font-size:10px;color:#6b7280">
                {{ $consignment->notes }}
            </div>
        </div>
    @endif

    {{-- Signature lines --}}
    <div style="display:flex;gap:40px;margin-top:30px">
        <div style="flex:1;border-top:1px solid #9ca3af;padding-top:8px;font-size:10px;color:#6b7280">
            Dispatched by: {{ $consignment->user?->name }}<br>
            Date: {{ $consignment->created_at->format('d M Y') }}
        </div>
        <div style="flex:1;border-top:1px solid #9ca3af;padding-top:8px;font-size:10px;color:#6b7280">
            Received by: ____________________<br>
            Date: ____________________
        </div>
    </div>

@endsection
