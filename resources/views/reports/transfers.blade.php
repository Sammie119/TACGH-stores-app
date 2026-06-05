{{-- resources/views/reports/transfers.blade.php --}}
@extends('layouts.app')
@section('title', 'Transfers Report')
@section('header', 'Transfers Report')
@section('subheader', 'Stock movements between branches')

@section('content')

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <form method="GET" action="{{ route('reports.transfers') }}"
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
                    @foreach(['pending','approved','dispatched','received','rejected'] as $s)
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
                <a href="{{ route('reports.transfers') }}"
                   class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="font-semibold text-gray-700">
                Transfer records
                <span class="text-sm font-normal text-gray-400 ml-2">
                {{ $transfers->total() }} records
            </span>
            </p>
        </div>
        <table class="w-full text-sm" style="border-collapse:collapse">
            <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Reference</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">From</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">To</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Items</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Requested by</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Date</th>
            </tr>
            </thead>
            <tbody>
            @forelse($transfers as $transfer)
                @php
                    $sc = [
                        'pending'    => 'background:#fef3c7;color:#92400e',
                        'approved'   => 'background:#dcfce7;color:#166534',
                        'dispatched' => 'background:#dbeafe;color:#1e40af',
                        'received'   => 'background:#f3f4f6;color:#374151',
                        'rejected'   => 'background:#fee2e2;color:#991b1b',
                    ][$transfer->status] ?? 'background:#f3f4f6;color:#374151';
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <a href="{{ route('transfers.show', $transfer) }}"
                           class="font-mono text-sm text-blue-600 hover:underline">
                            {{ $transfer->reference_no }}
                        </a>
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ $transfer->fromBranch?->name }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ $transfer->toBranch?->name }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ $transfer->items->count() }}
                    </td>
                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;{{ $sc }}">
                        {{ ucfirst($transfer->status) }}
                    </span>
                    </td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ $transfer->requestedBy?->name }}
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-500">
                        {{ $transfer->created_at->format('d M Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                        No transfers found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        @if($transfers->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>

@endsection
