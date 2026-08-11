{{-- resources/views/pdf/purchases-report.blade.php --}}
@extends('pdf.layouts.base')

@section('title', 'Purchases Report')
@section('doc-title', 'PURCHASES REPORT')

@section('content')

    {{-- Period banner --}}
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
            <div class="info-label">Total ordered</div>
            <div class="info-value font-bold text-blue" style="font-size:14px">
                GHS {{ number_format($summary['total_amount'], 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Total paid</div>
            <div class="info-value font-bold text-green" style="font-size:14px">
                GHS {{ number_format($summary['amount_paid'], 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Outstanding balance</div>
            <div class="info-value font-bold {{ $summary['balance_due'] > 0 ? 'text-red' : '' }}" style="font-size:14px">
                GHS {{ number_format($summary['balance_due'], 2) }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-label">Purchase orders</div>
            <div class="info-value font-bold" style="font-size:14px">
                {{ number_format($summary['total_count']) }}
            </div>
        </div>
    </div>

    {{-- Status breakdown --}}
    @if($byStatus->count() > 0)
        <div style="margin-bottom:16px">
            <div style="font-size:10px;font-weight:bold;color:#374151;
                text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px">
                Breakdown by status
            </div>
            <table style="margin-bottom:0">
                <thead>
                <tr>
                    <th style="text-align:left">Status</th>
                    <th style="text-align:right">Count</th>
                    <th style="text-align:right">Total</th>
                    <th style="text-align:right">Outstanding</th>
                </tr>
                </thead>
                <tbody>
                @foreach($byStatus as $status => $row)
                    @php
                        $badgeClass = match($status) {
                            'received'  => 'badge-green',
                            'partial'   => 'badge-red',
                            'approved', 'ordered' => 'badge-blue',
                            'pending'   => 'badge-amber',
                            default     => 'badge-gray',
                        };
                    @endphp
                    <tr>
                        <td><span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span></td>
                        <td class="text-right">{{ number_format($row['count']) }}</td>
                        <td class="text-right font-bold">GHS {{ number_format($row['total_amount'], 2) }}</td>
                        <td class="text-right {{ $row['balance_due'] > 0 ? 'text-red' : '' }}">
                            GHS {{ number_format($row['balance_due'], 2) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Main purchase orders table --}}
    <table>
        <thead>
        <tr>
            <th style="text-align:left">PO Number</th>
            @if($isSuperAdmin)
                <th style="text-align:left">Branch</th>
            @endif
            <th style="text-align:left">Supplier</th>
            <th style="text-align:left">Ordered by</th>
            <th style="text-align:right">Total</th>
            <th style="text-align:right">Paid</th>
            <th style="text-align:right">Balance</th>
            <th style="text-align:left">Status</th>
            <th style="text-align:left">Order date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($purchaseOrders as $po)
            @php
                $badgeClass = match($po->status) {
                    'received'  => 'badge-green',
                    'partial'   => 'badge-red',
                    'approved', 'ordered' => 'badge-blue',
                    'pending'   => 'badge-amber',
                    default     => 'badge-gray',
                };
            @endphp
            <tr>
                <td class="monospace">{{ $po->po_number }}</td>
                @if($isSuperAdmin)
                    <td>{{ $po->branch?->name }}</td>
                @endif
                <td class="font-bold">{{ $po->supplier?->name }}</td>
                <td>{{ $po->createdBy?->name }}</td>
                <td class="text-right font-bold">
                    GHS {{ number_format($po->total_amount, 2) }}
                </td>
                <td class="text-right text-green">
                    GHS {{ number_format($po->amount_paid, 2) }}
                </td>
                <td class="text-right {{ $po->balance_due > 0 ? 'text-red' : '' }}">
                    GHS {{ number_format($po->balance_due, 2) }}
                </td>
                <td><span class="badge {{ $badgeClass }}">{{ ucfirst($po->status) }}</span></td>
                <td>{{ $po->order_date?->format('d M Y') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="{{ $isSuperAdmin ? 4 : 3 }}" class="text-right">Total</td>
            <td class="text-right">GHS {{ number_format($summary['total_amount'], 2) }}</td>
            <td class="text-right">GHS {{ number_format($summary['amount_paid'], 2) }}</td>
            <td class="text-right">GHS {{ number_format($summary['balance_due'], 2) }}</td>
            <td colspan="2"></td>
        </tr>
        </tfoot>
    </table>

@endsection
