{{-- resources/views/pdf/stock-balance.blade.php --}}
@extends('pdf.layouts.base')

@section('title', 'Stock Balance Report')
@section('doc-title', 'STOCK BALANCE REPORT')

@section('content')

    {{-- Summary --}}
    <div class="info-grid" style="margin-bottom:16px">
        <div class="info-box">
            <div class="info-label">Total products</div>
            <div class="info-value font-bold" style="font-size:14px">
                {{ number_format($totalItems) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Total cost value</div>
            <div class="info-value font-bold text-blue" style="font-size:14px">
                GHS {{ number_format($totalCostValue, 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Total selling value</div>
            <div class="info-value font-bold text-green" style="font-size:14px">
                GHS {{ number_format($totalSellingValue, 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Potential profit</div>
            <div class="info-value font-bold" style="font-size:14px;color:#16a34a">
                GHS {{ number_format($totalSellingValue - $totalCostValue, 2) }}
            </div>
        </div>
    </div>

    {{-- Products grouped by category --}}
    @foreach($groupedStock as $categoryName => $items)

        <div style="font-size:10px;font-weight:bold;color:#fff;
            background:#1e3a5f;padding:6px 10px;border-radius:4px 4px 0 0;
            display:flex;justify-content:space-between;
            margin-top:{{ $loop->first ? '0' : '12px' }}">
    <span>{{ $categoryName ?? 'Uncategorised' }}
          ({{ $items->count() }} products)</span>
            <span>
        Cost: GHS {{ number_format($items->sum(fn($s) => $s->quantity * $s->product->cost_price), 2) }}
        &nbsp;&nbsp;
        Sell: GHS {{ number_format($items->sum(fn($s) => $s->quantity * $s->product->selling_price), 2) }}
    </span>
        </div>

        <table style="margin-bottom:0;border:1px solid #e5e7eb;
              border-top:none;border-radius:0 0 4px 4px">
            <thead>
            <tr>
                <th style="text-align:left">Product</th>
                <th style="text-align:left">SKU</th>
                @if($isSuperAdmin)
                    <th style="text-align:left">Branch</th>
                @endif
                <th style="text-align:right">Qty</th>
                <th style="text-align:left">Unit</th>
                <th style="text-align:right">Cost price</th>
                <th style="text-align:right">Cost value</th>
                <th style="text-align:right">Sell price</th>
                <th style="text-align:right">Sell value</th>
                <th style="text-align:left">Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                @php
                    $isOut = $item->quantity <= 0;
                    $isLow = !$isOut && $item->quantity <= $item->product->reorder_level;
                @endphp
                <tr>
                    <td class="font-bold">{{ $item->product->name }}</td>
                    <td class="monospace text-sm">{{ $item->product->sku }}</td>
                    @if($isSuperAdmin)
                        <td>{{ $item->branch?->name }}</td>
                    @endif
                    <td class="text-right font-bold
                       {{ $isOut ? 'text-red' : ($isLow ? '' : 'text-green') }}">
                        {{ number_format($item->quantity, 2) }}
                    </td>
                    <td>{{ $item->product->unit }}</td>
                    <td class="text-right">
                        GHS {{ number_format($item->product->cost_price, 2) }}
                    </td>
                    <td class="text-right font-bold">
                        GHS {{ number_format($item->quantity * $item->product->cost_price, 2) }}
                    </td>
                    <td class="text-right">
                        GHS {{ number_format($item->product->selling_price, 2) }}
                    </td>
                    <td class="text-right font-bold text-green">
                        GHS {{ number_format($item->quantity * $item->product->selling_price, 2) }}
                    </td>
                    <td>
                <span class="badge {{ $isOut ? 'badge-red' : ($isLow ? 'badge-amber' : 'badge-green') }}">
                    {{ $isOut ? 'Out' : ($isLow ? 'Low' : 'OK') }}
                </span>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="{{ $isSuperAdmin ? 6 : 5 }}"
                    class="text-right text-sm">Category total</td>
                <td class="text-right font-bold">
                    GHS {{ number_format($items->sum(fn($s) => $s->quantity * $s->product->cost_price), 2) }}
                </td>
                <td></td>
                <td class="text-right font-bold text-green">
                    GHS {{ number_format($items->sum(fn($s) => $s->quantity * $s->product->selling_price), 2) }}
                </td>
                <td></td>
            </tr>
            </tfoot>
        </table>

    @endforeach

    {{-- Grand total --}}
    <div style="margin-top:16px;background:#111827;color:#fff;
            padding:12px 14px;border-radius:6px;
            display:flex;justify-content:space-between;align-items:center">
    <span style="font-size:12px;font-weight:bold">
        GRAND TOTAL — ALL CATEGORIES
    </span>
        <div style="display:flex;gap:24px">
            <div style="text-align:right">
                <p style="font-size:9px;color:#9ca3af">Total products</p>
                <p style="font-size:12px;font-weight:bold;color:#fff">
                    {{ number_format($totalItems) }}
                </p>
            </div>
            <div style="text-align:right">
                <p style="font-size:9px;color:#9ca3af">Cost value</p>
                <p style="font-size:12px;font-weight:bold;color:#fff">
                    GHS {{ number_format($totalCostValue, 2) }}
                </p>
            </div>
            <div style="text-align:right">
                <p style="font-size:9px;color:#9ca3af">Selling value</p>
                <p style="font-size:12px;font-weight:bold;color:#86efac">
                    GHS {{ number_format($totalSellingValue, 2) }}
                </p>
            </div>
            <div style="text-align:right">
                <p style="font-size:9px;color:#9ca3af">Potential profit</p>
                <p style="font-size:12px;font-weight:bold;color:#34d399">
                    GHS {{ number_format($totalSellingValue - $totalCostValue, 2) }}
                </p>
            </div>
        </div>
    </div>

@endsection
