{{-- resources/views/pdf/return-receipt.blade.php --}}
    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Return Receipt #{{ $return->id }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #111;
            width: 80mm;
            padding: 8mm 6mm 12mm 6mm;
        }

        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: bold; }

        .divider {
            border-top: 1px dashed #999;
            margin: 8px 0;
        }

        .header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 1px dashed #999;
            margin-bottom: 10px;
        }

        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .return-stamp {
            border: 2px solid #dc2626;
            border-radius: 4px;
            padding: 4px 10px;
            text-align: center;
            margin: 8px 0;
        }

        .return-stamp p {
            font-size: 13px;
            font-weight: bold;
            color: #dc2626;
            letter-spacing: .08em;
        }

        .return-stamp small {
            font-size: 9px;
            color: #ef4444;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            margin-bottom: 3px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        th {
            font-size: 10px;
            font-weight: bold;
            padding: 3px 0;
            border-bottom: 1px solid #ddd;
        }

        td {
            font-size: 10px;
            padding: 3px 0;
            vertical-align: top;
        }

        .total-section {
            border-top: 1px dashed #999;
            padding-top: 8px;
            margin-top: 4px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            margin-bottom: 3px;
        }

        .total-row.refund {
            font-size: 13px;
            font-weight: bold;
            color: #dc2626;
            margin: 6px 0 4px;
        }

        .total-row.original {
            color: #9ca3af;
            text-decoration: line-through;
        }

        .note-box {
            background: #fff5f5;
            border: 1px dashed #fca5a5;
            border-radius: 4px;
            padding: 6px 8px;
            margin: 8px 0;
            font-size: 10px;
            color: #dc2626;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 12px;
            border-top: 1px dashed #999;
            padding-top: 10px;
        }

        .signature-line {
            border-top: 1px solid #bbb;
            margin-top: 20px;
            padding-top: 5px;
            font-size: 10px;
            color: #555;
        }
    </style>
</head>
<body>

{{-- Branch header --}}
<div class="header">
    @if($logoBase64)
        <img src="{{ $logoBase64 }}"
             style="max-height:60px;max-width:160px;object-fit:contain;
                margin-bottom:8px;display:block;
                margin-left:auto;margin-right:auto">
    @else
        <div style="font-size:11px;font-weight:bold;color:#2563eb;
                margin-bottom:6px">
            {{ $return->sale->branch?->name ?? setting('app_name') }}
        </div>
    @endif

    <div class="store-name">
        {{ $return->sale->branch?->name ?? setting('app_name') }}
    </div>
    @if($return->sale->branch?->address)
        <div style="font-size:10px;color:#555;margin-top:2px">
            {{ $return->sale->branch->address }}
        </div>
    @endif
    @if($return->sale->branch?->phone)
        <div style="font-size:10px;color:#555">
            Tel: {{ $return->sale->branch->phone }}
        </div>
    @endif
</div>

{{-- Return stamp --}}
<div class="return-stamp">
    <p>RETURN RECEIPT</p>
    <small>
        @switch($return->type)
            @case('refund')   Refund issued @break
            @case('exchange') Exchange processed @break
            @case('damaged')  Damaged item @break
            @endswitch
            &middot; Return #{{ str_pad($return->id, 6, '0', STR_PAD_LEFT) }}
    </small>
</div>

{{-- Meta info --}}
<div class="meta-row">
    <span class="bold">Original invoice</span>
    <span class="bold">{{ $return->sale->invoice_no }}</span>
</div>
<div class="meta-row">
    <span>Sale date</span>
    <span>{{ $return->sale->created_at->format('d M Y H:i') }}</span>
</div>
<div class="meta-row">
    <span>Return date</span>
    <span>{{ $return->created_at->format('d M Y H:i') }}</span>
</div>
<div class="meta-row">
    <span>Cashier</span>
    <span>{{ $return->sale->user?->name }}</span>
</div>
@if($return->sale->customer)
    <div class="meta-row">
        <span>Customer</span>
        <span>{{ $return->sale->customer->name }}</span>
    </div>
@endif
<div class="meta-row">
    <span>Processed by</span>
    <span>{{ $return->processedBy?->name }}</span>
</div>

<div class="divider"></div>

{{-- Items table --}}
<table>
    <thead>
    <tr>
        <th style="text-align:left">Item</th>
        <th style="text-align:center">Qty</th>
        <th style="text-align:right">Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach($return->sale->items as $item)
        @php
            $isReturned = $item->product_id === $return->product_id;
        @endphp

        @if($isReturned)
            {{-- Original row struck through --}}
            <tr>
                <td style="color:#9ca3af;text-decoration:line-through">
                    {{ $item->product?->name }}
                    <br>
                    <span style="font-size:9px">
                    {{ number_format($item->unit_price, 2) }} x {{ number_format($item->quantity, 2) }}
                </span>
                </td>
                <td style="text-align:center;color:#9ca3af;
                       text-decoration:line-through">
                    {{ number_format($item->quantity, 2) }}
                </td>
                <td style="text-align:right;color:#9ca3af;
                       text-decoration:line-through">
                    {{ number_format($item->subtotal, 2) }}
                </td>
            </tr>
            {{-- Returned row --}}
            <tr style="background:#fff5f5">
                <td style="padding-left:8px;font-size:9px;color:#dc2626;
                       font-weight:bold">
                    (Returned)
                </td>
                <td style="text-align:center;font-weight:bold;color:#dc2626">
                    {{ number_format($return->quantity, 2) }}
                </td>
                <td style="text-align:right;font-weight:bold;color:#dc2626">
                    -{{ number_format($return->refund_amount, 2) }}
                </td>
            </tr>

        @else
            {{-- Normal items --}}
            <tr>
                <td>
                    {{ $item->product?->name }}
                    <br>
                    <span style="font-size:9px;color:#9ca3af">
                    {{ number_format($item->unit_price, 2) }} x {{ number_format($item->quantity, 2) }}
                </span>
                </td>
                <td style="text-align:center">
                    {{ number_format($item->quantity, 2) }}
                </td>
                <td style="text-align:right">
                    {{ number_format($item->subtotal, 2) }}
                </td>
            </tr>
        @endif

    @endforeach
    </tbody>
</table>

{{-- Return reason --}}
@if($return->reason)
    <div class="note-box">
        <span class="bold">Return reason:</span> {{ $return->reason }}
    </div>
@endif

{{-- Totals --}}
<div class="total-section">

    {{-- Original totals struck through --}}
    <div class="total-row original">
        <span>Original subtotal</span>
        <span>GHS {{ number_format($return->sale->items->sum('subtotal'), 2) }}</span>
    </div>

    @if($return->sale->discount > 0)
        <div class="total-row original">
            <span>Discount</span>
            <span>- GHS {{ number_format($return->sale->discount, 2) }}</span>
        </div>
    @endif

    <div class="total-row original">
        <span>Original total</span>
        <span>GHS {{ number_format($return->sale->total_amount, 2) }}</span>
    </div>

    <div class="divider"></div>

    {{-- Return deduction --}}
    <div class="total-row">
        <span>Return deduction</span>
        <span style="color:#dc2626">
            - GHS {{ number_format($return->refund_amount, 2) }}
        </span>
    </div>

    {{-- Adjusted total --}}
    @php
        $adjustedTotal = max(0, $return->sale->total_amount - $return->refund_amount);
    @endphp
    <div class="total-row" style="font-size:12px;font-weight:bold;
                                   margin-top:4px;padding-top:4px;
                                   border-top:1px solid #ddd">
        <span>Adjusted total</span>
        <span>GHS {{ number_format($adjustedTotal, 2) }}</span>
    </div>

    @if($return->type === 'refund')
        <div class="total-row refund">
            <span>REFUND TO CUSTOMER</span>
            <span>GHS {{ number_format($return->refund_amount, 2) }}</span>
        </div>
        <div style="font-size:9px;color:#16a34a;margin-top:2px">
            Stock returned to inventory
        </div>

    @elseif($return->type === 'exchange')
        <div class="total-row refund" style="color:#1d4ed8">
            <span>EXCHANGE VALUE</span>
            <span>GHS {{ number_format($return->refund_amount, 2) }}</span>
        </div>
        <div style="font-size:9px;color:#16a34a;margin-top:2px">
            Stock returned - Customer to select replacement
        </div>

    @elseif($return->type === 'damaged')
        <div class="total-row" style="font-size:11px;font-weight:bold;
                                   color:#dc2626;margin-top:4px">
            <span>WRITE-OFF (DAMAGED)</span>
            <span>GHS {{ number_format($return->refund_amount, 2) }}</span>
        </div>
        <div style="font-size:9px;color:#dc2626;margin-top:2px">
            Item written off - not returned to stock
        </div>
    @endif

    {{-- Payment info --}}
    <div class="divider"></div>
    <div class="meta-row">
        <span>Payment method</span>
        <span>{{ ucfirst($return->sale->payment_method) }}</span>
    </div>
    <div class="meta-row">
        <span>Originally paid</span>
        <span>GHS {{ number_format($return->sale->amount_paid, 2) }}</span>
    </div>

</div>

{{-- Signatures --}}
<div class="signature-line">
    Customer signature: ____________________
</div>
<div class="signature-line" style="margin-top:12px">
    Staff signature: ____________________
</div>

{{-- Footer --}}
<div class="footer">
    <p>{{ setting('receipt_footer', 'Thank you for your business!') }}</p>
    <p style="margin-top:4px;font-size:9px;color:#999">
        {{ setting('app_name', 'Stores Manager') }}
        &middot; {{ now()->format('d M Y') }}
    </p>
</div>

</body>
</html>
