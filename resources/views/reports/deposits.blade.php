{{-- resources/views/reports/deposits.blade.php --}}
@extends('layouts.app')
@section('title', 'Deposits Report')
@section('header', 'Deposits Report')
@section('subheader', 'Bank deposit analysis')

@section('content')

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <form method="GET" action="{{ route('reports.deposits') }}"
              style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap">

            @if($isSuperAdmin)
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Branch</label>
                    <select name="branch_id"
                            class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                           text-sm text-gray-700 focus:outline-none
                           focus:ring-2 focus:ring-blue-500">
                        <option value="">All branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}"
                                {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                           text-sm text-gray-700 focus:outline-none
                           focus:ring-2 focus:ring-blue-500">
                    <option value="">All statuses</option>
                    @foreach(['pending','verified','rejected'] as $s)
                        <option value="{{ $s }}"
                            {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors">
                Apply
            </button>

            @if(request()->hasAny(['branch_id','status','date_from','date_to']))
                <a href="{{ route('reports.deposits') }}"
                   class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Summary --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Verified deposits
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                GHS {{ number_format($summary['total_verified'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Pending verification
            </p>
            <p class="text-2xl font-semibold
                  {{ $summary['total_pending'] > 0 ? 'text-amber-600' : 'text-gray-800' }} mt-1">
                GHS {{ number_format($summary['total_pending'], 2) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Rejected
            </p>
            <p class="text-2xl font-semibold
                  {{ $summary['total_rejected'] > 0 ? 'text-red-600' : 'text-gray-800' }} mt-1">
                GHS {{ number_format($summary['total_rejected'], 2) }}
            </p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="font-semibold text-gray-700">
                Deposit records
                <span class="text-sm font-normal text-gray-400 ml-2">
                {{ $deposits->total() }} records
            </span>
            </p>
        </div>
        <table class="w-full text-sm" style="border-collapse:collapse">
            <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Branch</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Amount</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Bank</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Reference</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Deposit date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Submitted by</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Verified by</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Status</th>
            </tr>
            </thead>
            <tbody>
            @forelse($deposits as $deposit)
                @php
                    $sc = [
                        'pending'  => 'background:#fef3c7;color:#92400e',
                        'verified' => 'background:#dcfce7;color:#166534',
                        'rejected' => 'background:#fee2e2;color:#991b1b',
                    ][$deposit->status] ?? 'background:#f3f4f6;color:#374151';
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-700">
                        {{ $deposit->branch?->name }}
                    </td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-800">
                        GHS {{ number_format($deposit->amount, 2) }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ $deposit->bank_name ?? '—' }}
                    </td>
                    <td class="px-5 py-3 font-mono text-xs text-gray-600">
                        {{ $deposit->reference_no ?? '—' }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ \Carbon\Carbon::parse($deposit->deposit_date)->format('d M Y') }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ $deposit->depositedBy?->name }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ $deposit->verifiedBy?->name ?? '—' }}
                    </td>
                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;{{ $sc }}">
                        {{ ucfirst($deposit->status) }}
                    </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                        No deposits found.
                    </td>
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

@endsection
