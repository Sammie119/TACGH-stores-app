{{-- resources/views/pdf/profit-loss.blade.php --}}
@extends('pdf.layouts.base')

@section('title', 'Profit & Loss Report')
@section('doc-title', 'PROFIT & LOSS')

@section('content')

    {{-- Period info --}}
    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;
            padding:8px 14px;margin-bottom:16px;font-size:10px;color:#6b7280">
        Period:
        <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</strong>
        —
        <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong>
        @if($branchId)
            &nbsp;&middot;&nbsp; Branch filtered
        @else
            &nbsp;&middot;&nbsp; All branches
        @endif
    </div>

    {{-- P&L Statement --}}
    <div style="display:flex;gap:16px;margin-bottom:20px">

        {{-- Main statement --}}
        <div style="flex:1.2">
            <div style="background:#1e3a5f;color:#fff;padding:10px 14px;
                    border-radius:6px 6px 0 0">
                <p style="font-size:12px;font-weight:bold">Profit & Loss Statement</p>
                <p style="font-size:10px;color:#93c5fd;margin-top:2px">
                    {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
                    — {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                </p>
            </div>

            <table style="margin-bottom:0;border:1px solid #e5e7eb;
                      border-top:none;border-radius:0 0 6px 6px">
                <tbody>
                {{-- Revenue --}}
                <tr style="background:#f0fdf4">
                    <td colspan="2" style="padding:8px 12px;font-size:10px;
                                           font-weight:bold;color:#166534;
                                           text-transform:uppercase;
                                           letter-spacing:.05em">
                        Revenue
                    </td>
                </tr>
                <tr>
                    <td style="padding:6px 12px;color:#374151">
                        Gross sales ({{ $totalTransactions }} transactions)
                    </td>
                    <td class="text-right font-bold" style="padding:6px 12px">
                        GHS {{ number_format($totalRevenue, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:6px 12px;color:#374151">Discounts given</td>
                    <td class="text-right text-red" style="padding:6px 12px">
                        - GHS {{ number_format($totalDiscount, 2) }}
                    </td>
                </tr>
                <tr style="background:#f0fdf4;border-top:1px solid #bbf7d0">
                    <td style="padding:6px 12px;font-weight:bold;color:#166534">
                        Net revenue
                    </td>
                    <td class="text-right font-bold text-green" style="padding:6px 12px">
                        GHS {{ number_format($totalRevenue - $totalDiscount, 2) }}
                    </td>
                </tr>

                {{-- COGS --}}
                <tr style="background:#fef2f2">
                    <td colspan="2" style="padding:8px 12px;font-size:10px;
                                           font-weight:bold;color:#991b1b;
                                           text-transform:uppercase;
                                           letter-spacing:.05em">
                        Cost of goods sold
                    </td>
                </tr>
                <tr>
                    <td style="padding:6px 12px;color:#374151">
                        Product cost (at cost price)
                    </td>
                    <td class="text-right text-red font-bold" style="padding:6px 12px">
                        - GHS {{ number_format($totalCOGS, 2) }}
                    </td>
                </tr>

                {{-- Gross profit --}}
                <tr style="background:#eff6ff;border-top:2px solid #bfdbfe">
                    <td style="padding:10px 12px;font-weight:bold;color:#1e40af;
                               font-size:11px">
                        GROSS PROFIT
                        <span style="font-size:9px;font-weight:normal;color:#3b82f6;
                                     margin-left:6px">
                            Margin: {{ $grossProfitMargin }}%
                        </span>
                    </td>
                    <td style="padding:10px 12px;text-align:right;font-weight:bold;
                               font-size:13px;
                               color:{{ $grossProfit >= 0 ? '#1d4ed8' : '#dc2626' }}">
                        GHS {{ number_format($grossProfit, 2) }}
                    </td>
                </tr>

                {{-- Deductions --}}
                <tr style="background:#fffbeb">
                    <td colspan="2" style="padding:8px 12px;font-size:10px;
                                           font-weight:bold;color:#92400e;
                                           text-transform:uppercase;
                                           letter-spacing:.05em">
                        Deductions
                    </td>
                </tr>
                <tr>
                    <td style="padding:6px 12px;color:#374151">
                        Refunds ({{ $totalReturns }} returns)
                    </td>
                    <td class="text-right text-red" style="padding:6px 12px">
                        - GHS {{ number_format($totalRefunds, 2) }}
                    </td>
                </tr>

                {{-- Net profit --}}
                <tr style="background:{{ $netProfit >= 0 ? '#f0fdf4' : '#fef2f2' }};
                           border-top:2px solid {{ $netProfit >= 0 ? '#bbf7d0' : '#fecaca' }}">
                    <td style="padding:12px;font-weight:bold;font-size:13px;
                               color:{{ $netProfit >= 0 ? '#166534' : '#991b1b' }}">
                        NET PROFIT
                        <span style="font-size:9px;font-weight:normal;margin-left:6px;
                                     color:{{ $netProfit >= 0 ? '#16a34a' : '#dc2626' }}">
                            Margin: {{ $netProfitMargin }}%
                        </span>
                    </td>
                    <td style="padding:12px;text-align:right;font-weight:bold;
                               font-size:16px;
                               color:{{ $netProfit >= 0 ? '#166534' : '#991b1b' }}">
                        GHS {{ number_format($netProfit, 2) }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        {{-- Right: quick stats + payment breakdown --}}
        <div style="flex:1;display:flex;flex-direction:column;gap:12px">

            {{-- Quick stats --}}
            <div class="info-box">
                <div class="info-label" style="margin-bottom:10px">Quick stats</div>
                @foreach([
                    ['Transactions',   number_format($totalTransactions)],
                    ['Avg sale value', 'GHS ' . number_format($totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0, 2)],
                    ['Total discounts','GHS ' . number_format($totalDiscount, 2)],
                    ['Total refunds',  'GHS ' . number_format($totalRefunds, 2)],
                    ['Purchases made', 'GHS ' . number_format($totalPurchases, 2)],
                    ['Gross margin',   $grossProfitMargin . '%'],
                    ['Net margin',     $netProfitMargin . '%'],
                ] as [$label, $value])
                    <div style="display:flex;justify-content:space-between;
                        padding:4px 0;border-bottom:1px solid #f3f4f6;
                        font-size:10px">
                        <span style="color:#6b7280">{{ $label }}</span>
                        <span style="font-weight:600;color:#111827">{{ $value }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Payment method --}}
            @if($revenueByPayment->count() > 0)
                <div class="info-box">
                    <div class="info-label" style="margin-bottom:8px">
                        By payment method
                    </div>
                    @foreach($revenueByPayment as $pm)
                        <div style="display:flex;justify-content:space-between;
                        padding:4px 0;border-bottom:1px solid #f3f4f6;
                        font-size:10px">
                <span style="color:#374151">
                    {{ ucfirst($pm->payment_method) }}
                    <span style="color:#9ca3af">({{ $pm->count }})</span>
                </span>
                            <span style="font-weight:600">
                    GHS {{ number_format($pm->total, 2) }}
                </span>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

    </div>

    {{-- Branch breakdown --}}
    @if($isSuperAdmin && $branchBreakdown->count() > 0)
        <div style="font-size:10px;font-weight:bold;color:#374151;
            text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">
            Branch performance
        </div>
        <table style="margin-bottom:16px">
            <thead>
            <tr>
                <th style="text-align:left">Branch</th>
                <th style="text-align:right">Transactions</th>
                <th style="text-align:right">Revenue</th>
                <th style="text-align:right">Discounts</th>
                <th style="text-align:right">Net revenue</th>
                <th style="text-align:right">% of total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($branchBreakdown as $b)
                @php
                    $pct = $totalRevenue > 0
                        ? round(($b->revenue / $totalRevenue) * 100, 1) : 0;
                @endphp
                <tr>
                    <td class="font-bold">{{ $b->branch?->name }}</td>
                    <td class="text-right">{{ $b->transactions }}</td>
                    <td class="text-right font-bold">
                        GHS {{ number_format($b->revenue, 2) }}
                    </td>
                    <td class="text-right text-red">
                        GHS {{ number_format($b->discounts, 2) }}
                    </td>
                    <td class="text-right text-green font-bold">
                        GHS {{ number_format($b->revenue - $b->discounts, 2) }}
                    </td>
                    <td class="text-right">{{ $pct }}%</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    {{-- Daily breakdown --}}
    @if($dailyBreakdown->count() > 0)
        <div style="font-size:10px;font-weight:bold;color:#374151;
            text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">
            Daily breakdown
        </div>
        <table style="margin-bottom:16px">
            <thead>
            <tr>
                <th style="text-align:left">Date</th>
                <th style="text-align:right">Transactions</th>
                <th style="text-align:right">Revenue</th>
                <th style="text-align:right">Discounts</th>
                <th style="text-align:right">Net</th>
            </tr>
            </thead>
            <tbody>
            @foreach($dailyBreakdown as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('D, d M Y') }}</td>
                    <td class="text-right">{{ $day->transactions }}</td>
                    <td class="text-right font-bold">
                        GHS {{ number_format($day->revenue, 2) }}
                    </td>
                    <td class="text-right text-red">
                        GHS {{ number_format($day->discounts, 2) }}
                    </td>
                    <td class="text-right text-green font-bold">
                        GHS {{ number_format($day->revenue - $day->discounts, 2) }}
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td class="font-bold">Total</td>
                <td class="text-right font-bold">
                    {{ $dailyBreakdown->sum('transactions') }}
                </td>
                <td class="text-right font-bold">
                    GHS {{ number_format($dailyBreakdown->sum('revenue'), 2) }}
                </td>
                <td class="text-right font-bold text-red">
                    GHS {{ number_format($dailyBreakdown->sum('discounts'), 2) }}
                </td>
                <td class="text-right font-bold text-green">
                    GHS {{ number_format(
                    $dailyBreakdown->sum('revenue') - $dailyBreakdown->sum('discounts'), 2
                ) }}
                </td>
            </tr>
            </tfoot>
        </table>
    @endif

    {{-- Top products --}}
    @if($topProducts->count() > 0)
        <div style="font-size:10px;font-weight:bold;color:#374151;
            text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">
            Top 10 products by revenue
        </div>
        <table>
            <thead>
            <tr>
                <th style="text-align:left">#</th>
                <th style="text-align:left">Product</th>
                <th style="text-align:right">Units sold</th>
                <th style="text-align:right">Revenue</th>
                <th style="text-align:right">% of total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($topProducts as $i => $product)
                @php
                    $pct = $totalRevenue > 0
                        ? round(($product->total_revenue / $totalRevenue) * 100, 1) : 0;
                @endphp
                <tr>
                    <td class="font-bold text-blue">{{ $i + 1 }}</td>
                    <td class="font-bold">{{ $product->product?->name }}</td>
                    <td class="text-right">{{ number_format($product->total_qty, 0) }}</td>
                    <td class="text-right font-bold">
                        GHS {{ number_format($product->total_revenue, 2) }}
                    </td>
                    <td class="text-right">{{ $pct }}%</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

@endsection
