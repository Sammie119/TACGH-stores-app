{{-- resources/views/cashier-accounts/show.blade.php --}}
@extends('layouts.app')
@section('title', $user->name . ' — Cash Account')
@section('header', $user->name)
@section('subheader', 'Cash account — ' . ($user->branch?->name ?? 'No branch'))

@section('content')

    {{-- Back link --}}
    <div class="mb-5">
        <a href="{{ route('cashier-accounts.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700"
           style="display:inline-flex;align-items:center;gap:6px">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            All cashier accounts
        </a>
    </div>

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Cash sales</p>
            <p class="text-2xl font-semibold text-blue-600 mt-1">
                GHS {{ number_format($cashSales, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">All time</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Consignment payments</p>
            <p class="text-2xl font-semibold text-orange-500 mt-1">
                GHS {{ number_format($cashConsignments, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">All time</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Verified deposits</p>
            <p class="text-2xl font-semibold text-green-600 mt-1">
                GHS {{ number_format($verifiedDeposits, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">All time</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Outstanding balance</p>
            <p class="text-2xl font-semibold {{ $outstanding > 0 ? 'text-red-600' : ($outstanding < 0 ? 'text-blue-600' : 'text-gray-800') }} mt-1">
                @if($outstanding < 0)
                    GHS {{ number_format(abs($outstanding), 2) }} excess
                @else
                    GHS {{ number_format($outstanding, 2) }}
                @endif
            </p>
            @if($outstanding == 0)
                <p class="text-xs text-green-600 font-medium mt-1">Fully settled</p>
            @elseif($outstanding > 0)
                <p class="text-xs text-red-500 mt-1">Still to be banked</p>
            @endif
        </div>
    </div>

    {{-- Three detail sections --}}
    <div class="space-y-6">

        {{-- Cash Sales --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">
                    Cash Sales
                    <span class="text-sm font-normal text-gray-400 ml-2">
                        {{ $sales->total() }} records
                    </span>
                </p>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                </tr>
                </thead>
                <tbody>
                @forelse($sales as $sale)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('sales.show', $sale) }}"
                               class="font-mono text-xs text-blue-600 hover:underline">
                                {{ $sale->invoice_no }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $sale->customer?->name ?? $sale->walkin_name ?? 'Walk-in' }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-blue-600">
                            @if($sale->payment_method === 'split')
                                GHS {{ number_format(($sale->split_method_1 === 'cash' ? $sale->split_amount_1 : 0) + ($sale->split_method_2 === 'cash' ? $sale->split_amount_2 : 0), 2) }}
                                <span class="text-xs text-gray-400 font-normal ml-1">(split)</span>
                            @else
                                GHS {{ number_format($sale->amount_paid, 2) }}
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">
                            {{ $sale->created_at->format('d M Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-400">No cash sales found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            @if($sales->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>

        {{-- Consignment Payments --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">
                    Consignment Cash Payments
                    <span class="text-sm font-normal text-gray-400 ml-2">
                        {{ $consignmentPayments->total() }} records
                    </span>
                </p>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Payment Ref</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Consignment</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Recipient</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                </tr>
                </thead>
                <tbody>
                @forelse($consignmentPayments as $cpay)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <span class="font-mono text-xs text-gray-700">{{ $cpay->reference }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('consignments.show', $cpay->consignment) }}"
                               class="font-mono text-xs text-blue-600 hover:underline">
                                {{ $cpay->consignment?->reference_no }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $cpay->consignment?->recipient_name }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-orange-600">
                            GHS {{ number_format($cpay->amount, 2) }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">
                            {{ $cpay->payment_date->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-400">No consignment cash payments found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            @if($consignmentPayments->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $consignmentPayments->links() }}
                </div>
            @endif
        </div>

        {{-- Deposits --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">
                    Bank Deposits
                    <span class="text-sm font-normal text-gray-400 ml-2">
                        {{ $deposits->total() }} records
                    </span>
                </p>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Reference</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Bank</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $depositColors = [
                        'pending'  => 'background:#fef3c7;color:#92400e',
                        'verified' => 'background:#dcfce7;color:#166534',
                        'rejected' => 'background:#fee2e2;color:#991b1b',
                    ];
                @endphp
                @forelse($deposits as $deposit)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('deposits.show', $deposit) }}"
                               class="font-mono text-xs text-blue-600 hover:underline">
                                {{ $deposit->reference_no ?? '—' }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $deposit->bank_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-green-600">
                            GHS {{ number_format($deposit->amount, 2) }}
                        </td>
                        <td class="px-5 py-3">
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:500;
                                         {{ $depositColors[$deposit->status] ?? 'background:#f3f4f6;color:#374151' }}">
                                {{ ucfirst($deposit->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">
                            {{ $deposit->deposit_date->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-400">No deposits found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            @if($deposits->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $deposits->links() }}
                </div>
            @endif
        </div>

    </div>

@endsection
