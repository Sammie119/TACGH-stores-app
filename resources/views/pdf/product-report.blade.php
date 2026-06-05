{{-- resources/views/pdf/product-report.blade.php --}}
@extends('pdf.layouts.base')

@section('title', 'Product Report — ' . $selectedProduct->name)
@section('doc-title', 'PRODUCT REPORT')

@section('content')

    {{-- Product info --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Product</div>
            <div class="info-value font-bold" style="font-size:13px">
                {{ $selectedProduct->name }}
            </div>
            <div style="font-family:monospace;font-size:10px;color:#6b7280;margin-top:2px">
                {{ $selectedProduct->sku }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Category</div>
            <div class="info-value">
                {{ $selectedProduct->category?->name ?? '—' }}
            </div>
            <div style="font-size:10px;color:#6b7280;margin-top:2px">
                Unit: {{ $selectedProduct->unit }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Pricing</div>
            <div class="info-value">
                Cost: GHS {{ number_format($selectedProduct->cost_price, 2) }}<br>
                Sell: GHS {{ number_format($selectedProduct->selling_price, 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Period</div>
            <div class="info-value">
                @if($dateFrom || $dateTo)
                    {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d M Y') : 'All time' }}
                    —
                    {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d M Y') : 'Present' }}
                @else
                    All time
                @endif
            </div>
        </div>
    </div>

    {{-- Summary boxes --}}
    <div class="info-grid" style="margin-bottom:16px">
        <div class="info-box">
            <div class="info-label">Current stock</div>
            <div class="info-value font-bold text-blue" style="font-size:14px">
                {{ number_format($summary['current_stock'], 2) }} {{ $selectedProduct->unit }}
            </div>
            <div style="font-size:10px;color:#6b7280">
                Value: GHS {{ number_format($summary['stock_value'], 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Units sold</div>
            <div class="info-value font-bold" style="font-size:14px">
                {{ number_format($summary['total_qty_sold'], 2) }}
            </div>
            <div style="font-size:10px;color:#6b7280">
                Revenue: GHS {{ number_format($summary['total_revenue'], 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Units returned</div>
            <div class="info-value font-bold" style="font-size:14px;color:#dc2626">
                {{ number_format($summary['total_qty_returned'], 2) }}
            </div>
            <div style="font-size:10px;color:#6b7280">
                Refunded: GHS {{ number_format($summary['total_refunded'], 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Gross profit</div>
            <div class="info-value font-bold" style="font-size:14px;
             color:{{ $summary['gross_profit'] >= 0 ? '#16a34a' : '#dc2626' }}">
                GHS {{ number_format($summary['gross_profit'], 2) }}
            </div>
            <div style="font-size:10px;color:#6b7280">
                Net revenue: GHS {{ number_format($summary['net_revenue'], 2) }}
            </div>
        </div>
    </div>

    {{-- Stock levels --}}
    @if($stockLevel->count() > 0)
        <div style="font-size:10px;font-weight:bold;color:#374151;
            text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">
            Stock levels by branch
        </div>
        <table style="margin-bottom:16px">
            <thead>
            <tr>
                <th style="text-align:left">Branch</th>
                <th style="text-align:right">Quantity</th>
                <th style="text-align:right">Cost value</th>
                <th style="text-align:right">Selling value</th>
                <th style="text-align:left">Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($stockLevel as $stock)
                @php
                    $isOut = $stock->quantity <= 0;
                    $isLow = !$isOut && $stock->quantity <= $selectedProduct->reorder_level;
                @endphp
                <tr>
                    <td class="font-bold">{{ $stock->branch?->name }}</td>
                    <td class="text-right font-bold
                       {{ $isOut ? 'text-red' : ($isLow ? '' : 'text-green') }}">
                        {{ number_format($stock->quantity, 2) }}
                    </td>
                    <td class="text-right">
                        GHS {{ number_format($stock->quantity * $selectedProduct->cost_price, 2) }}
                    </td>
                    <td class="text-right">
                        GHS {{ number_format($stock->quantity * $selectedProduct->selling_price, 2) }}
                    </td>
                    <td>
                <span class="badge {{ $isOut ? 'badge-red' : ($isLow ? 'badge-amber' : 'badge-green') }}">
                    {{ $isOut ? 'Out of stock' : ($isLow ? 'Low stock' : 'In stock') }}
                </span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    {{-- Sales history --}}
    @if($salesData->count() > 0)
        <div style="font-size:10px;font-weight:bold;color:#374151;
            text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">
            Sales history ({{ $salesData->count() }} transactions)
        </div>
        <table style="margin-bottom:16px">
            <thead>
            <tr>
                <th style="text-align:left">Invoice</th>
                <th style="text-align:left">Branch</th>
                <th style="text-align:left">Cashier</th>
                <th style="text-align:right">Qty</th>
                <th style="text-align:right">Unit price</th>
                <th style="text-align:right">Subtotal</th>
                <th style="text-align:left">Date</th>
            </tr>
            </thead>
            <tbody>
            @foreach($salesData as $item)
                <tr>
                    <td class="monospace text-sm">{{ $item->sale->invoice_no }}</td>
                    <td>{{ $item->sale->branch?->name }}</td>
                    <td>{{ $item->sale->user?->name }}</td>
                    <td class="text-right font-bold">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">GHS {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right font-bold text-green">
                        GHS {{ number_format($item->subtotal, 2) }}
                    </td>
                    <td>{{ $item->sale->created_at->format('d M Y') }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">{{ number_format($salesData->sum('quantity'), 2) }}</td>
                <td></td>
                <td class="text-right text-green">
                    GHS {{ number_format($salesData->sum('subtotal'), 2) }}
                </td>
                <td></td>
            </tr>
            </tfoot>
        </table>
    @endif

    {{-- Returns --}}
    @if($returnsData->count() > 0)
        <div style="font-size:10px;font-weight:bold;color:#374151;
            text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">
            Returns ({{ $returnsData->count() }})
        </div>
        <table style="margin-bottom:16px">
            <thead>
            <tr>
                <th style="text-align:left">Invoice</th>
                <th style="text-align:left">Type</th>
                <th style="text-align:right">Qty</th>
                <th style="text-align:right">Refund</th>
                <th style="text-align:left">Reason</th>
                <th style="text-align:left">Date</th>
            </tr>
            </thead>
            <tbody>
            @foreach($returnsData as $ret)
                @php
                    $bc = ['refund'=>'badge-green','exchange'=>'badge-blue','damaged'=>'badge-red'][$ret->type] ?? 'badge-gray';
                @endphp
                <tr>
                    <td class="monospace text-sm">{{ $ret->sale?->invoice_no }}</td>
                    <td><span class="badge {{ $bc }}">{{ ucfirst($ret->type) }}</span></td>
                    <td class="text-right">{{ number_format($ret->quantity, 2) }}</td>
                    <td class="text-right text-red">
                        GHS {{ number_format($ret->refund_amount, 2) }}
                    </td>
                    <td class="text-gray">{{ Str::limit($ret->reason ?? '—', 30) }}</td>
                    <td>{{ $ret->created_at->format('d M Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    {{-- Purchases --}}
    @if($purchasesData->count() > 0)
        <div style="font-size:10px;font-weight:bold;color:#374151;
            text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">
            Purchase history ({{ $purchasesData->count() }})
        </div>
        <table style="margin-bottom:16px">
            <thead>
            <tr>
                <th style="text-align:left">PO Number</th>
                <th style="text-align:left">Supplier</th>
                <th style="text-align:left">Branch</th>
                <th style="text-align:right">Qty ordered</th>
                <th style="text-align:right">Qty received</th>
                <th style="text-align:right">Unit cost</th>
                <th style="text-align:right">Total</th>
                <th style="text-align:left">Date</th>
            </tr>
            </thead>
            <tbody>
            @foreach($purchasesData as $p)
                <tr>
                    <td class="monospace text-sm">{{ $p->purchaseOrder->po_number }}</td>
                    <td>{{ $p->purchaseOrder->supplier?->name }}</td>
                    <td>{{ $p->purchaseOrder->branch?->name }}</td>
                    <td class="text-right">{{ number_format($p->quantity_ordered, 2) }}</td>
                    <td class="text-right">{{ number_format($p->quantity_received, 2) }}</td>
                    <td class="text-right">GHS {{ number_format($p->unit_cost, 2) }}</td>
                    <td class="text-right font-bold">GHS {{ number_format($p->subtotal, 2) }}</td>
                    <td>{{ $p->purchaseOrder->created_at->format('d M Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    {{-- Movements --}}
    @if($movements->count() > 0)
        <div style="font-size:10px;font-weight:bold;color:#374151;
            text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">
            Stock movements ({{ $movements->count() }})
        </div>
        <table>
            <thead>
            <tr>
                <th style="text-align:left">Type</th>
                <th style="text-align:left">Branch</th>
                <th style="text-align:left">User</th>
                <th style="text-align:right">Quantity</th>
                <th style="text-align:right">Balance after</th>
                <th style="text-align:left">Notes</th>
                <th style="text-align:left">Date</th>
            </tr>
            </thead>
            <tbody>
            @foreach($movements as $mov)
                @php
                    $isIn = in_array($mov->type, ['restock','return','adjustment_in','transfer_in']);
                @endphp
                <tr>
                    <td>
                <span class="badge {{ $isIn ? 'badge-green' : 'badge-red' }}">
                    {{ ucfirst(str_replace('_', ' ', $mov->type)) }}
                </span>
                    </td>
                    <td>{{ $mov->branch?->name }}</td>
                    <td>{{ $mov->user?->name }}</td>
                    <td class="text-right font-bold {{ $isIn ? 'text-green' : 'text-red' }}">
                        {{ $isIn ? '+' : '-' }}{{ number_format($mov->quantity, 2) }}
                    </td>
                    <td class="text-right">{{ number_format($mov->balance_after, 2) }}</td>
                    <td class="text-gray text-sm">
                        {{ Str::limit($mov->notes ?? '—', 30) }}
                    </td>
                    <td>{{ $mov->created_at->format('d M Y H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

@endsection
