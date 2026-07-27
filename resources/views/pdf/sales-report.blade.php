{{-- resources/views/pdf/sales-report.blade.php --}}
@extends('pdf.layouts.base')

@section('title', 'Sales Report')
@section('doc-title', 'SALES REPORT')

@section('content')

    {{-- Filter info --}}
    @if($dateFrom || $dateTo)
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;
            padding:8px 14px;margin-bottom:16px;font-size:10px;color:#6b7280">
            Period:
            {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d M Y') : 'All time' }}
            —
            {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d M Y') : 'Present' }}
        </div>
    @endif

    {{-- Summary --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Total revenue</div>
            <div class="info-value font-bold text-blue" style="font-size:14px">
                GHS {{ number_format($summary['total_revenue'], 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Transactions</div>
            <div class="info-value font-bold" style="font-size:14px">
                {{ number_format($summary['total_count']) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Total discounts</div>
            <div class="info-value font-bold">
                GHS {{ number_format($summary['total_discount'], 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Outstanding balance</div>
            <div class="info-value font-bold text-red">
                GHS {{ number_format($summary['balance_due'], 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Total refunds</div>
            <div class="info-value font-bold text-red">
                GHS {{ number_format($summary['total_refunds'], 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Net revenue</div>
            <div class="info-value font-bold" style="font-size:14px">
                GHS {{ number_format($summary['net_revenue'], 2) }}
            </div>
        </div>
    </div>

    {{-- Payment breakdown --}}
    @if($byPayment->count() > 0)
        <div style="margin-bottom:16px">
            <div style="font-size:10px;font-weight:bold;color:#374151;
                text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px">
                Revenue by payment method
            </div>
            <div style="display:flex;gap:10px">
                @foreach($byPayment as $method => $data)
                    <div class="info-box" style="flex:0 0 auto;min-width:120px">
                        <div class="info-label">{{ ucfirst($method) }}</div>
                        <div class="info-value font-bold">
                            GHS {{ number_format($data['total'], 2) }}
                        </div>
                        <div class="text-gray text-sm">{{ $data['count'] }} sales</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Sales table --}}
    <table>
        <thead>
        <tr>
            <th style="text-align:left">Invoice</th>
            @if($isSuperAdmin)
                <th style="text-align:left">Branch</th>
            @endif
            <th style="text-align:left">Customer</th>
            <th style="text-align:left">Cashier</th>
            <th style="text-align:right">Amount</th>
            <th style="text-align:right">Discount</th>
            <th style="text-align:left">Method</th>
            <th style="text-align:left">Status</th>
            <th style="text-align:left">Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($sales as $sale)
            @php
                $badgeClass = match($sale->status) {
                    'completed' => 'badge-green',
                    'partial'   => 'badge-amber',
                    'credit'    => 'badge-red',
                    'cancelled' => 'badge-gray',
                    default     => 'badge-gray',
                };
            @endphp
            <tr>
                <td class="monospace">{{ $sale->invoice_no }}</td>
                @if($isSuperAdmin)
                    <td>{{ $sale->branch?->name }}</td>
                @endif
                <td>{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                <td>{{ $sale->user?->name }}</td>
                <td class="text-right font-bold">
                    GHS {{ number_format($sale->total_amount, 2) }}
                </td>
                <td class="text-right">
                    {{ $sale->discount > 0 ? 'GHS ' . number_format($sale->discount, 2) : '—' }}
                </td>
                <td>{{ ucfirst($sale->payment_method) }}</td>
                <td>
                <span class="badge {{ $badgeClass }}">
                    {{ ucfirst($sale->status) }}
                </span>
                </td>
                <td>{{ $sale->created_at->format('d M Y') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="{{ $isSuperAdmin ? 4 : 3 }}" class="text-right">Total</td>
            <td class="text-right">GHS {{ number_format($summary['total_revenue'], 2) }}</td>
            <td class="text-right">GHS {{ number_format($summary['total_discount'], 2) }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="{{ $isSuperAdmin ? 4 : 3 }}" class="text-right">Net revenue (after refunds)</td>
            <td class="text-right" colspan="2">GHS {{ number_format($summary['net_revenue'], 2) }}</td>
            <td colspan="3"></td>
        </tr>
        </tfoot>
    </table>

@endsection
