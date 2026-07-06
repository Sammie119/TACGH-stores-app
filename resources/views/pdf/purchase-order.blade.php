{{-- resources/views/pdf/purchase-order.blade.php --}}
@extends('pdf.layouts.base')

@section('title', 'Purchase Order ' . $purchaseOrder->po_number)
@section('doc-title', 'PURCHASE ORDER')

@php $branch = $purchaseOrder->branch; @endphp

@section('content')

    {{-- PO info --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">PO Number</div>
            <div class="info-value font-bold monospace" style="font-size:14px">
                {{ $purchaseOrder->po_number }}
            </div>
            <div class="text-gray text-sm" style="margin-top:4px">
                Status:
                <strong>{{ ucfirst($purchaseOrder->status) }}</strong>
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Supplier</div>
            <div class="info-value font-bold">{{ $purchaseOrder->supplier?->name }}</div>
            <div class="text-gray text-sm">{{ $purchaseOrder->supplier?->phone }}</div>
            <div class="text-gray text-sm">{{ $purchaseOrder->supplier?->email }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Deliver to</div>
            <div class="info-value font-bold">{{ $purchaseOrder->branch?->name }}</div>
            <div class="text-gray text-sm">{{ $purchaseOrder->branch?->address }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Dates</div>
            <div class="text-sm" style="margin-bottom:3px">
                <span class="text-gray">Order date:</span>
                {{ $purchaseOrder->order_date->format('d M Y') }}
            </div>
            @if($purchaseOrder->expected_date)
                <div class="text-sm" style="margin-bottom:3px">
                    <span class="text-gray">Expected:</span>
                    {{ $purchaseOrder->expected_date->format('d M Y') }}
                </div>
            @endif
            @if($purchaseOrder->received_date)
                <div class="text-sm">
                    <span class="text-gray">Received:</span>
                    {{ $purchaseOrder->received_date->format('d M Y') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Items --}}
    <table>
        <thead>
        <tr>
            <th style="text-align:left">#</th>
            <th style="text-align:left">Product</th>
            <th style="text-align:left">SKU</th>
            <th style="text-align:right">Qty ordered</th>
            <th style="text-align:right">Qty received</th>
            <th style="text-align:right">Unit cost</th>
            <th style="text-align:right">Subtotal</th>
        </tr>
        </thead>
        <tbody>
        @foreach($purchaseOrder->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="font-bold">{{ $item->product?->name }}</td>
                <td class="monospace text-sm">{{ $item->product?->sku }}</td>
                <td class="text-right">{{ number_format($item->quantity_ordered, 0) }}</td>
                <td class="text-right">
                    @if($item->quantity_received > 0)
                        {{ number_format($item->quantity_received, 0) }}
                    @else
                        <span class="text-gray">—</span>
                    @endif
                </td>
                <td class="text-right">GHS {{ number_format($item->unit_cost, 2) }}</td>
                <td class="text-right font-bold">
                    GHS {{ number_format($item->subtotal, 2) }}
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="6" class="text-right">Total amount</td>
            <td class="text-right text-blue">
                GHS {{ number_format($purchaseOrder->total_amount, 2) }}
            </td>
        </tr>
        @if($purchaseOrder->amount_paid > 0)
            <tr>
                <td colspan="6" class="text-right">Amount paid</td>
                <td class="text-right text-green">
                    GHS {{ number_format($purchaseOrder->amount_paid, 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">Balance due</td>
                <td class="text-right {{ $purchaseOrder->balance_due > 0 ? 'text-red' : 'text-green' }}">
                    GHS {{ number_format($purchaseOrder->balance_due, 2) }}
                </td>
            </tr>
        @endif
        </tfoot>
    </table>

    {{-- Notes + Signatures --}}
    @if($purchaseOrder->notes)
        <div style="margin-bottom:20px">
            <div style="font-size:10px;font-weight:bold;color:#374151;margin-bottom:4px">
                Notes
            </div>
            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;
                padding:10px 12px;font-size:10px;color:#6b7280">
                {{ $purchaseOrder->notes }}
            </div>
        </div>
    @endif

    {{-- Signature lines --}}
    <div style="display:flex;gap:40px;margin-top:30px">
        <div style="flex:1;border-top:1px solid #9ca3af;padding-top:8px;font-size:10px;color:#6b7280">
            Prepared by: {{ $purchaseOrder->createdBy?->name }}<br>
            Date: {{ $purchaseOrder->created_at->format('d M Y') }}
        </div>
        <div style="flex:1;border-top:1px solid #9ca3af;padding-top:8px;font-size:10px;color:#6b7280">
            Approved by: {{ $purchaseOrder->approvedBy?->name ?? '____________________' }}<br>
            Date: {{ $purchaseOrder->approved_by ? $purchaseOrder->updated_at->format('d M Y') : '' }}
        </div>
        <div style="flex:1;border-top:1px solid #9ca3af;padding-top:8px;font-size:10px;color:#6b7280">
            Received by: ____________________<br>
            Date: ____________________
        </div>
    </div>

@endsection
