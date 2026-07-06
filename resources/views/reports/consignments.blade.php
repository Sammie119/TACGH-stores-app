{{-- resources/views/reports/consignments.blade.php --}}
@extends('layouts.app')
@section('title', 'Consignment Report')
@section('header', 'Consignment Report')
@section('subheader', 'Dispatched products, payment collections and outstanding balances')

@section('content')

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <form method="GET" action="{{ route('reports.consignments') }}"
              style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap">

            @if($isSuperAdmin)
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Branch</label>
                    <select name="branch_id"
                            class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                                   text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                               text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All statuses</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                              text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                              text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
                           font-medium rounded-lg transition-colors">
                Apply
            </button>

            @if(request()->hasAny(['branch_id', 'status', 'date_from', 'date_to']))
                <a href="{{ route('reports.consignments') }}"
                   class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50
                          text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center">
                    Clear
                </a>
            @endif

            @can('export reports')
                <a href="{{ route('reports.export.consignments', request()->query()) }}"
                   class="h-9 px-4 bg-green-600 hover:bg-green-700 text-white text-sm font-medium
                          rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center;gap:6px;text-decoration:none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('pdf.consignment-report', request()->query()) }}"
                   target="_blank"
                   class="h-9 px-4 bg-red-600 hover:bg-red-700 text-white text-sm font-medium
                          rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center;gap:6px;text-decoration:none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    PDF
                </a>
            @endcan

        </form>
    </div>

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total value</p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                GHS {{ number_format($summary['total_value'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total collected</p>
            <p class="text-2xl font-semibold text-green-600 mt-1">
                GHS {{ number_format($summary['amount_paid'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Outstanding balance</p>
            <p class="text-2xl font-semibold {{ $summary['balance_due'] > 0 ? 'text-red-600' : 'text-gray-800' }} mt-1">
                GHS {{ number_format($summary['balance_due'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Consignments</p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                {{ number_format($summary['total_count']) }}
            </p>
        </div>
    </div>

    {{-- Analytics --}}
    <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:20px;margin-bottom:24px">

        {{-- Status breakdown --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Breakdown by status</p>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Count</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Total Value</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Outstanding</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $statusColors = [
                        'pending'    => 'background:#fef3c7;color:#92400e',
                        'dispatched' => 'background:#dbeafe;color:#1e40af',
                        'partial'    => 'background:#ffedd5;color:#9a3412',
                        'completed'  => 'background:#dcfce7;color:#166534',
                        'cancelled'  => 'background:#f3f4f6;color:#374151',
                    ];
                @endphp
                @forelse($byStatus as $row)
                    <tr class="border-b border-gray-50">
                        <td class="px-5 py-3">
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:500;
                                         {{ $statusColors[$row->status] ?? 'background:#f3f4f6;color:#374151' }}">
                                {{ ucfirst($row->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right text-gray-600">
                            {{ number_format($row->count) }}
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-gray-800">
                            GHS {{ number_format($row->total_value, 2) }}
                        </td>
                        <td class="px-5 py-3 text-right {{ $row->balance_due > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                            GHS {{ number_format($row->balance_due, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-400">No data</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Top debtors --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Top outstanding recipients</p>
                <p class="text-xs text-gray-400 mt-0.5">Dispatched & partial only</p>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topDebtors as $i => $debtor)
                    <div class="px-5 py-3" style="display:flex;align-items:center;gap:10px">
                        <span style="width:20px;height:20px;border-radius:50%;
                                     background:#fff7ed;color:#ea580c;font-size:10px;
                                     font-weight:600;display:flex;align-items:center;
                                     justify-content:center;flex-shrink:0">
                            {{ $i + 1 }}
                        </span>
                        <div style="flex:1;min-width:0">
                            <p class="text-sm font-medium text-gray-800 truncate">
                                {{ $debtor->customer?->name ?? $debtor->walkin_name ?? 'Walk-in' }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $debtor->consignment_count }} {{ Str::plural('consignment', $debtor->consignment_count) }}
                            </p>
                        </div>
                        <p class="text-sm font-semibold text-red-600 flex-shrink-0">
                            GHS {{ number_format($debtor->total_owed, 2) }}
                        </p>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-gray-400 text-sm">
                        No outstanding balances
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Consignments table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="font-semibold text-gray-700">
                Consignments
                <span class="text-sm font-normal text-gray-400 ml-2">
                    {{ $consignments->total() }} records
                </span>
            </p>
        </div>

        <table class="w-full text-sm" style="border-collapse:collapse">
            <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                @if($isSuperAdmin)
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Branch</th>
                @endif
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Recipient</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Created by</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Value</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Paid</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Balance</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
            </tr>
            </thead>
            <tbody>
            @forelse($consignments as $consignment)
                @php
                    $sc = $statusColors[$consignment->status] ?? 'background:#f3f4f6;color:#374151';
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <a href="{{ route('consignments.show', $consignment) }}"
                           class="font-mono text-sm text-blue-600 hover:underline">
                            {{ $consignment->reference_no }}
                        </a>
                    </td>
                    @if($isSuperAdmin)
                        <td class="px-5 py-3 text-gray-600 text-xs">{{ $consignment->branch?->name }}</td>
                    @endif
                    <td class="px-5 py-3 text-gray-700 font-medium">
                        {{ $consignment->recipient_name }}
                    </td>
                    <td class="px-5 py-3 text-gray-600 text-xs">{{ $consignment->user?->name }}</td>
                    <td class="px-5 py-3 text-right font-medium text-gray-800">
                        GHS {{ number_format($consignment->total_value, 2) }}
                    </td>
                    <td class="px-5 py-3 text-right text-green-600 font-medium">
                        GHS {{ number_format($consignment->amount_paid, 2) }}
                    </td>
                    <td class="px-5 py-3 text-right {{ $consignment->balance_due > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                        GHS {{ number_format($consignment->balance_due, 2) }}
                    </td>
                    <td class="px-5 py-3">
                        <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:500;{{ $sc }}">
                            {{ ucfirst($consignment->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-500">
                        {{ $consignment->created_at->format('d M Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $isSuperAdmin ? 9 : 8 }}" class="px-5 py-12 text-center text-gray-400">
                        No consignments found for the selected filters.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($consignments->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $consignments->links() }}
            </div>
        @endif
    </div>

@endsection
