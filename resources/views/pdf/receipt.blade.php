{{-- resources/views/pdf/receipt.blade.php --}}
    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $sale->invoice_no }}</title>
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
            padding-bottom: 12px;
            border-bottom: 1px dashed #999;
            margin-bottom: 10px;
        }

        .store-name {
            font-size: 18px;
            font-weight: bold;
            color: #111;
            margin-bottom: 2px;
        }

        .store-meta {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }

        .invoice-banner {
            background: #1e3a5f;
            color: #fff;
            text-align: center;
            padding: 6px 10px;
            border-radius: 4px;
            margin: 10px 0;
        }

        .invoice-banner p {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: .08em;
        }

        .invoice-banner small {
            font-size: 9px;
            color: #93c5fd;
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
            table-layout: fixed;
        }

        thead tr {
            border-bottom: 1.5px solid #111;
        }

        th {
            font-size: 10px;
            font-weight: bold;
            padding: 4px 0;
            color: #111;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }

        td {
            font-size: 10px;
            padding: 4px 0;
            vertical-align: top;
            word-wrap: break-word;
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
            margin-bottom: 4px;
            color: #555;
        }

        .total-row.grand {
            font-size: 14px;
            font-weight: bold;
            color: #111;
            margin: 6px 0;
            padding-top: 6px;
            border-top: 1.5px solid #111;
        }

        .total-row.paid {
            font-size: 11px;
            font-weight: bold;
            color: #16a34a;
        }

        .total-row.change {
            font-size: 11px;
            color: #2563eb;
        }

        .total-row.balance {
            font-size: 11px;
            font-weight: bold;
            color: #dc2626;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 14px;
            border-top: 1px dashed #999;
            padding-top: 10px;
        }

        .footer .thank-you {
            font-size: 12px;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    @if($logoBase64)
        <img src="{{ $logoBase64 }}"
             style="max-height:64px;max-width:160px;object-fit:contain;
                margin-bottom:8px;display:block;
                margin-left:auto;margin-right:auto">
    @endif

    <div class="store-name">
        {{ $sale->branch?->name ?? setting('app_name') }}
    </div>
    @if($sale->branch?->address)
        <div class="store-meta">{{ $sale->branch->address }}</div>
    @endif
    @if($sale->branch?->phone)
        <div class="store-meta">Tel: {{ $sale->branch->phone }}</div>
    @endif
    @if($sale->branch?->email)
        <div class="store-meta">{{ $sale->branch->email }}</div>
    @endif
</div>

{{-- Invoice banner --}}
<div class="invoice-banner">
    <p>SALES RECEIPT</p>
    <small>{{ $sale->invoice_no }}</small>
</div>

{{-- Meta info --}}
<div class="meta-row">
    <span>Date</span>
    <span>{{ $sale->created_at->format('d M Y H:i') }}</span>
</div>
<div class="meta-row">
    <span>Cashier</span>
    <span>{{ $sale->user?->name }}</span>
</div>
@if($sale->customer)
    <div class="meta-row">
        <span>Customer</span>
        <span>{{ $sale->customer->name }}</span>
    </div>
@endif
<div class="meta-row">
    <span>Payment</span>
    <span>{{ strtoupper($sale->payment_method) }}</span>
</div>

<div class="divider"></div>

{{-- Items --}}
<table>
    <thead>
    <tr>
        <th style="text-align:left;width:50%">Item</th>
        <th style="text-align:center;width:15%">Qty</th>
        <th style="text-align:right;width:17%">Price</th>
        <th style="text-align:right;width:18%">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sale->items as $item)
        <tr>
            <td>{{ $item->product?->name }}</td>
            <td style="text-align:center">
                {{ number_format($item->quantity, 2) }}
            </td>
            <td style="text-align:right">
                {{ number_format($item->unit_price, 2) }}
            </td>
            <td style="text-align:right;font-weight:bold">
                {{ number_format($item->subtotal, 2) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- Totals --}}
<div class="total-section">

    <div class="total-row">
        <span>Subtotal</span>
        <span>GHS {{ number_format($sale->items->sum('subtotal'), 2) }}</span>
    </div>

    @if($sale->discount > 0)
        <div class="total-row" style="color:#dc2626">
            <span>Discount</span>
            <span>- GHS {{ number_format($sale->discount, 2) }}</span>
        </div>
    @endif

    <div class="total-row grand">
        <span>TOTAL</span>
        <span>GHS {{ number_format($sale->total_amount, 2) }}</span>
    </div>

    <div class="total-row paid">
        <span>Amount paid ({{ strtoupper($sale->payment_method) }})</span>
        <span>GHS {{ number_format($sale->amount_paid, 2) }}</span>
    </div>

    @if($sale->balance_due > 0)
        <div class="total-row balance">
            <span>Balance due</span>
            <span>GHS {{ number_format($sale->balance_due, 2) }}</span>
        </div>
    @else
        @php $change = $sale->amount_paid - $sale->total_amount; @endphp
        @if($change > 0)
            <div class="total-row change">
                <span>Change</span>
                <span>GHS {{ number_format($change, 2) }}</span>
            </div>
        @endif
    @endif

</div>

{{-- Footer --}}
<div class="footer">
    <div class="thank-you">
        {{ setting('receipt_footer', 'Thank you for your business!') }}
    </div>
    <div style="margin-top:4px;font-size:9px;color:#999">
        {{ setting('app_name', 'Stores Manager') }}
        &middot; {{ now()->format('d M Y') }}
    </div>
</div>

</body>
</html>
