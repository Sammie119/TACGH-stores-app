{{-- resources/views/pdf/stock-transfer.blade.php --}}
@extends('pdf.layouts.base')

@section('title', 'Stock Transfer ' . $transfer->reference_no)
@section('doc-title', 'STOCK TRANSFER')

@section('content')

    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Reference</div>
            <div class="info-value font-bold monospace" style="font-size:14px">
                {{ $transfer->reference_no }}
            </div>
            <div class="text-gray text-sm" style="margin-top:4px">
                Status: <strong>{{ ucfirst($transfer->status) }}</strong>
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">From branch</div>
            <div class="info-value font-bold">{{ $transfer->fromBranch?->name }}</div>
            <div class="text-gray text-sm">{{ $transfer->fromBranch?->address }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">To branch</div>
            <div class="info-value font-bold">{{ $transfer->toBranch?->name }}</div>
            <div class="text-gray text-sm">{{ $transfer->toBranch?->address }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Details</div>
            <div class="text-sm" style="margin-bottom:3px">
                <span class="text-gray">Requested by:</span>
                {{ $transfer->requestedBy?->name }}
            </div>
            <div class="text-sm">
                <span class="text-gray">Date:</span>
                {{ $transfer->created_at->format('d M Y') }}
            </div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th style="text-align:left">#</th>
            <th style="text-align:left">Product</th>
            <th style="text-align:left">SKU</th>
            <th style="text-align:right">Quantity</th>
            <th style="text-align:left">Unit</th>
            <th style="text-align:left">Notes</th>
        </tr>
        </thead>
        <tbody>
        @foreach($transfer->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="font-bold">{{ $item->product?->name }}</td>
                <td class="monospace text-sm">{{ $item->product?->sku }}</td>
                <td class="text-right font-bold">
                    {{ number_format($item->quantity, 2) }}
                </td>
                <td>{{ $item->product?->unit }}</td>
                <td class="text-gray">{{ $item->notes ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($transfer->notes)
        <div style="margin-bottom:20px">
            <div style="font-size:10px;font-weight:bold;color:#374151;margin-bottom:4px">
                Notes
            </div>
            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;
                padding:10px 12px;font-size:10px;color:#6b7280">
                {{ $transfer->notes }}
            </div>
        </div>
    @endif

    <div style="display:flex;gap:40px;margin-top:30px">
        <div style="flex:1;border-top:1px solid #9ca3af;padding-top:8px;
                font-size:10px;color:#6b7280">
            Dispatched by: ____________________<br>
            Signature: ____________________<br>
            Date: ____________________
        </div>
        <div style="flex:1;border-top:1px solid #9ca3af;padding-top:8px;
                font-size:10px;color:#6b7280">
            Received by: ____________________<br>
            Signature: ____________________<br>
            Date: ____________________
        </div>
    </div>

@endsection
