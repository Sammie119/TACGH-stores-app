{{-- resources/views/consignments/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Consignments')
@section('header', 'Consignments')
@section('subheader', 'Track products dispatched for consignment and installment payments')

@section('content')

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Pending dispatch</p>
            <p class="text-2xl font-semibold mt-1 {{ $pendingCount > 0 ? 'text-amber-600' : 'text-gray-800' }}">
                {{ $pendingCount }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Created but not yet dispatched</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Out in field</p>
            <p class="text-2xl font-semibold mt-1 {{ $dispatchedCount > 0 ? 'text-blue-600' : 'text-gray-800' }}">
                {{ $dispatchedCount }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Dispatched, awaiting full payment</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Outstanding balance</p>
            <p class="text-2xl font-semibold mt-1 {{ $outstandingBalance > 0 ? 'text-red-600' : 'text-gray-800' }}">
                GHS {{ number_format($outstandingBalance, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Total unpaid across active consignments</p>
        </div>
    </div>

    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"
              method="GET" action="{{ route('consignments.index') }}">

            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search reference no…"
                       class="h-9 pl-9 pr-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       style="width:200px">
            </div>

            <select name="status"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                       text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>

            @if($isSuperAdmin || $canViewAll)
                <select name="branch"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                           text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                      text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                      text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                       text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status', 'branch', 'date_from', 'date_to']))
                <a href="{{ route('consignments.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500 transition-colors">Clear</a>
            @endif
        </form>

        @can('create consignments')
            <a href="{{ route('consignments.create') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium
              rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New consignment
            </a>
        @endcan
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('consignments.index')"
    />

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:700px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Recipient</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Branch</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Value</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Paid</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Balance</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($consignments as $consignment)
                @php
                    $statusColors = [
                        'pending'    => 'background:#fef3c7;color:#92400e',
                        'dispatched' => 'background:#dbeafe;color:#1e40af',
                        'partial'    => 'background:#ffedd5;color:#9a3412',
                        'completed'  => 'background:#dcfce7;color:#166534',
                        'cancelled'  => 'background:#f3f4f6;color:#374151',
                    ];
                    $style = $statusColors[$consignment->status] ?? 'background:#f3f4f6;color:#374151';
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                           {{ $consignment->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3">
                        <span class="font-mono text-sm text-blue-600 font-medium">
                            {{ $consignment->reference_no }}
                        </span>
                    </td>

                    <td class="px-5 py-3 text-gray-700">
                        {{ $consignment->recipient_name }}
                    </td>

                    <td class="px-5 py-3 text-gray-600 text-xs">
                        {{ $consignment->branch?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3 text-right text-gray-700 font-medium">
                        {{ number_format($consignment->total_value, 2) }}
                    </td>

                    <td class="px-5 py-3 text-right text-green-600 font-medium">
                        {{ number_format($consignment->amount_paid, 2) }}
                    </td>

                    <td class="px-5 py-3 text-right {{ $consignment->balance_due > 0 ? 'text-red-600' : 'text-gray-400' }} font-medium">
                        {{ number_format($consignment->balance_due, 2) }}
                    </td>

                    <td class="px-5 py-3">
                        <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;{{ $style }}">
                            {{ ucfirst($consignment->status) }}
                        </span>
                    </td>

                    <td class="px-5 py-3 text-xs text-gray-400">
                        {{ $consignment->created_at->format('d M Y') }}
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">

                            @if($consignment->trashed())
                                @can('create consignments')
                                    <form method="POST"
                                          action="{{ route('consignments.restore', $consignment->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-green-600 hover:underline font-medium">
                                            Restore
                                        </button>
                                    </form>
                                @endcan
                            @else
                                <a href="{{ route('consignments.show', $consignment) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">
                                    View
                                </a>

                                @if($consignment->status === 'pending')
                                    @can('dispatch consignments')
                                        <form method="POST"
                                              action="{{ route('consignments.dispatch', $consignment) }}"
                                              onsubmit="return confirm('Dispatch this consignment? Stock will be deducted from the branch.')">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-indigo-600 hover:underline font-medium">
                                                Dispatch
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                @if(in_array($consignment->status, ['dispatched', 'partial']))
                                    @can('record consignment payment')
                                        <a href="{{ route('consignments.show', $consignment) }}#payment"
                                           class="text-xs text-green-600 hover:underline font-medium">
                                            Add payment
                                        </a>
                                    @endcan
                                @endif

                                @if(!in_array($consignment->status, ['completed', 'cancelled']))
                                    @can('cancel consignments')
                                        <form method="POST"
                                              action="{{ route('consignments.cancel', $consignment) }}"
                                              onsubmit="return confirm('Cancel this consignment?{{ in_array($consignment->status, ["dispatched","partial"]) ? " Stock will be restored to the branch." : "" }}')">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-red-500 hover:underline font-medium">
                                                Cancel
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                @if($consignment->status === 'pending')
                                    @can('create consignments')
                                        <form method="POST"
                                              action="{{ route('consignments.destroy', $consignment) }}"
                                              onsubmit="return confirm('Delete this consignment?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-500 hover:underline font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            @endif

                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-5 py-12 text-center text-gray-400">
                        No consignments found.
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
