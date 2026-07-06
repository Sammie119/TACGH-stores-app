{{-- resources/views/pdf/stock-take.blade.php --}}
@extends('pdf.layouts.base')

@section('title', 'Stock Take ' . $stockTake->reference)
@section('doc-title', 'STOCK TAKE REPORT')

@php $branch = $stockTake->branch; @endphp

@section('content')

    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Reference</div>
            <div class="info-value font-bold monospace" style="font-size:14px">
                {{ $stockTake->reference }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Branch</div>
            <div class="info-value font-bold">{{ $stockTake->branch?->name }}</div>
        </div>
        <div class="info-box">
            <div class="info-label">Summary</div>
            <div class="text-sm" style="margin-bottom:3px">
                <span class="text-gray">Total items:</span>
                <strong>{{ $stockTake->items->count() }}</strong>
            </div>
            <div class="text-sm" style="margin-bottom:3px">
                <span class="text-gray">With variance:</span>
                <strong>{{ $stockTake->items->where('variance', '!=', 0)->whereNotNull('variance')->count() }}</strong>
            </div>
            <div class="text-sm">
                <span class="text-gray">Net variance:</span>
                @php $netVariance = $stockTake->items->whereNotNull('variance')->sum('variance'); @endphp
                <strong class="{{ $netVariance >= 0 ? 'text-green' : 'text-red' }}">
                    {{ $netVariance >= 0 ? '+' : '' }}{{ number_format($netVariance, 0) }}
                </strong>
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Dates</div>
            <div class="text-sm" style="margin-bottom:3px">
                <span class="text-gray">Started:</span>
                {{ $stockTake->started_at?->format('d M Y H:i') ?? '—' }}
            </div>
            <div class="text-sm" style="margin-bottom:3px">
                <span class="text-gray">Completed:</span>
                {{ $stockTake->completed_at?->format('d M Y H:i') ?? '—' }}
            </div>
            <div class="text-sm">
                <span class="text-gray">Approved by:</span>
                {{ $stockTake->approvedBy?->name ?? '—' }}
            </div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th style="text-align:left">Product</th>
            <th style="text-align:left">SKU</th>
            <th style="text-align:left">Category</th>
            <th style="text-align:right">System qty</th>
            <th style="text-align:right">Counted qty</th>
            <th style="text-align:right">Variance</th>
            <th style="text-align:left">Notes</th>
        </tr>
        </thead>
        <tbody>
        @foreach($stockTake->items->sortBy('product.name') as $item)
            @php
                $v = $item->variance;
            @endphp
            <tr>
                <td class="font-bold">{{ $item->product?->name }}</td>
                <td class="monospace text-sm">{{ $item->product?->sku }}</td>
                <td>{{ $item->product?->category?->name ?? '—' }}</td>
                <td class="text-right">{{ number_format($item->system_quantity, 0) }}</td>
                <td class="text-right font-bold">
                    {{ $item->counted_quantity !== null
                        ? number_format($item->counted_quantity, 0)
                        : '—' }}
                </td>
                <td class="text-right font-bold
                        {{ $v > 0 ? 'text-green' : ($v < 0 ? 'text-red' : 'text-gray') }}">
                    @if($v === null)—
                    @elseif($v > 0)+{{ number_format($v, 0) }}
                    @elseif($v < 0){{ number_format($v, 0) }}
                    @else 0
                    @endif
                </td>
                <td class="text-gray">{{ $item->notes ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div style="display:flex;gap:40px;margin-top:30px">
        <div style="flex:1;border-top:1px solid #9ca3af;padding-top:8px;
                font-size:10px;color:#6b7280">
            Counted by: {{ $stockTake->createdBy?->name }}<br>
            Date: {{ $stockTake->started_at?->format('d M Y') }}
        </div>
        <div style="flex:1;border-top:1px solid #9ca3af;padding-top:8px;
                font-size:10px;color:#6b7280">
            Approved by: {{ $stockTake->approvedBy?->name ?? '____________________' }}<br>
            Date: {{ $stockTake->completed_at?->format('d M Y') ?? '' }}
        </div>
    </div>

@endsection
