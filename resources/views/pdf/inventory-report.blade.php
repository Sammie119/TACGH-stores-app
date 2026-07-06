{{-- resources/views/pdf/inventory-report.blade.php --}}
@extends('pdf.layouts.base')

@section('title', 'Inventory Report')
@section('doc-title', 'INVENTORY REPORT')

@section('content')

    {{-- Summary --}}
    <div class="info-grid" style="margin-bottom:16px">
        <div class="info-box">
            <div class="info-label">Total products</div>
            <div class="info-value font-bold" style="font-size:14px">
                {{ $stock->count() }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Cost value</div>
            <div class="info-value font-bold text-blue" style="font-size:14px">
                GHS {{ number_format($totalCostValue, 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Selling value</div>
            <div class="info-value font-bold" style="font-size:14px">
                GHS {{ number_format($totalSellingValue, 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Potential profit</div>
            <div class="info-value font-bold text-green" style="font-size:14px">
                GHS {{ number_format($totalSellingValue - $totalCostValue, 2) }}
            </div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th style="text-align:left">Product</th>
            <th style="text-align:left">SKU</th>
            <th style="text-align:left">Category</th>
            @if($isSuperAdmin)
                <th style="text-align:left">Branch</th>
            @endif
            <th style="text-align:right">Qty</th>
            <th style="text-align:left">Unit</th>
            <th style="text-align:right">Reorder</th>
            <th style="text-align:right">Cost price</th>
            <th style="text-align:right">Selling price</th>
            <th style="text-align:right">Cost value</th>
            <th style="text-align:left">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($stock as $item)
            @php
                $isOut = $item->quantity <= 0;
                $isLow = !$isOut && $item->quantity <= $item->product->reorder_level;
                $badgeClass = $isOut ? 'badge-red' : ($isLow ? 'badge-amber' : 'badge-green');
                $badgeText  = $isOut ? 'Out' : ($isLow ? 'Low' : 'OK');
            @endphp
            <tr>
                <td class="font-bold">{{ $item->product->name }}</td>
                <td class="monospace text-sm">{{ $item->product->sku }}</td>
                <td>{{ $item->product->category?->name ?? '—' }}</td>
                @if($isSuperAdmin)
                    <td>{{ $item->branch->name }}</td>
                @endif
                <td class="text-right font-bold
                        {{ $isOut ? 'text-red' : ($isLow ? '' : 'text-green') }}">
                    {{ number_format($item->quantity, 0) }}
                </td>
                <td>{{ $item->product->unit }}</td>
                <td class="text-right">{{ $item->product->reorder_level }}</td>
                <td class="text-right">GHS {{ number_format($item->product->cost_price, 2) }}</td>
                <td class="text-right">GHS {{ number_format($item->product->selling_price, 2) }}</td>
                <td class="text-right font-bold">
                    GHS {{ number_format($item->quantity * $item->product->cost_price, 2) }}
                </td>
                <td><span class="badge {{ $badgeClass }}">{{ $badgeText }}</span></td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="{{ $isSuperAdmin ? 9 : 8 }}"
                class="text-right">Total cost value</td>
            <td class="text-right">GHS {{ number_format($totalCostValue, 2) }}</td>
            <td></td>
        </tr>
        </tfoot>
    </table>

@endsection
