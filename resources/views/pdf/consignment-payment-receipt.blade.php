<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt {{ $payment->reference }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #111;
            padding: 15mm 20mm;
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
            font-size: 22px;
            font-weight: bold;
            color: #111;
            margin-bottom: 2px;
        }

        .store-meta {
            font-size: 12px;
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
            font-size: 15px;
            font-weight: bold;
            letter-spacing: .08em;
        }

        .invoice-banner small {
            font-size: 11px;
            color: #93c5fd;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 3px;
            color: #555;
        }

        .total-section {
            border-top: 1px dashed #999;
            padding-top: 8px;
            margin-top: 4px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 4px;
            color: #555;
        }

        .total-row.this-payment {
            font-size: 18px;
            font-weight: bold;
            color: #16a34a;
            margin: 6px 0;
            padding-top: 6px;
            border-top: 1.5px solid #111;
        }

        .total-row.balance-due {
            font-size: 13px;
            font-weight: bold;
            color: #dc2626;
        }

        .total-row.fully-paid {
            font-size: 13px;
            font-weight: bold;
            color: #16a34a;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 14px;
            border-top: 1px dashed #999;
            padding-top: 10px;
        }

        .footer .thank-you {
            font-size: 15px;
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
             style="max-height:80px;max-width:200px;object-fit:contain;
                margin-bottom:8px;display:block;
                margin-left:auto;margin-right:auto">
    @endif

    <div class="store-name">
        {{ $payment->consignment->branch?->name ?? setting('app_name') }}
    </div>
    @if($payment->consignment->branch?->address)
        <div class="store-meta">{{ $payment->consignment->branch->address }}</div>
    @endif
    @if($payment->consignment->branch?->phone)
        <div class="store-meta">Tel: {{ $payment->consignment->branch->phone }}</div>
    @endif
    @if($payment->consignment->branch?->email)
        <div class="store-meta">{{ $payment->consignment->branch->email }}</div>
    @endif
</div>

{{-- Banner --}}
<div class="invoice-banner">
    <p>PAYMENT RECEIPT</p>
    <small>{{ $payment->reference }}</small>
</div>

{{-- Meta --}}
<div class="meta-row">
    <span>Date</span>
    <span>{{ $payment->payment_date->format('d M Y') }}</span>
</div>
<div class="meta-row">
    <span>Received by</span>
    <span>{{ $payment->paidBy?->name ?? '—' }}</span>
</div>
<div class="meta-row">
    <span>Recipient</span>
    <span>{{ $payment->consignment->recipient_name }}</span>
</div>
<div class="meta-row">
    <span>Consignment</span>
    <span>{{ $payment->consignment->reference_no }}</span>
</div>
<div class="meta-row">
    <span>Method</span>
    <span>{{ strtoupper($payment->payment_method) }}</span>
</div>

<div class="divider"></div>

{{-- Financial summary --}}
@php
    $previouslyPaid = (float) $payment->consignment->amount_paid - (float) $payment->amount;
@endphp

<div class="total-section">

    <div class="total-row">
        <span>Consignment total</span>
        <span>GHS {{ number_format($payment->consignment->total_value, 2) }}</span>
    </div>

    @if($previouslyPaid > 0)
        <div class="total-row">
            <span>Previously paid</span>
            <span>GHS {{ number_format($previouslyPaid, 2) }}</span>
        </div>
    @endif

    <div class="total-row this-payment">
        <span>This payment</span>
        <span>GHS {{ number_format($payment->amount, 2) }}</span>
    </div>

    @if((float) $payment->consignment->balance_due > 0)
        <div class="total-row balance-due">
            <span>Remaining balance</span>
            <span>GHS {{ number_format($payment->consignment->balance_due, 2) }}</span>
        </div>
    @else
        <div class="total-row fully-paid">
            <span>Remaining balance</span>
            <span>FULLY PAID</span>
        </div>
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
