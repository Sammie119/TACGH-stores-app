{{-- resources/views/transfers/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Stock Transfers')
@section('header', 'Stock Transfers')
@section('subheader', 'Manage stock movements between branches')

@section('content')

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Pending approval
            </p>
            <p class="text-2xl font-semibold mt-1 {{ $pendingCount > 0 ? 'text-amber-600' : 'text-gray-800' }}">
                {{ $pendingCount }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Awaiting manager approval</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                In transit
            </p>
            <p class="text-2xl font-semibold mt-1 {{ $dispatchedCount > 0 ? 'text-blue-600' : 'text-gray-800' }}">
                {{ $dispatchedCount }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Dispatched, awaiting receipt</p>
        </div>
    </div>

    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"
              method="GET" action="{{ route('transfers.index') }}">

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
                          text-sm text-gray-800 focus:outline-none focus:ring-2
                          focus:ring-blue-500"
                       style="width:200px">
            </div>

            <select name="status"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                       text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}"
                        {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>

            @if($isSuperAdmin)
                <select name="branch"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                       text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}"
                            {{ request('branch') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                       text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status', 'branch']))
                <a href="{{ route('transfers.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500 transition-colors">Clear</a>
            @endif
        </form>

        @can('create transfers')
            <a href="{{ route('transfers.create') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium
              rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New transfer
            </a>
        @endcan
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('transfers.index')"
    />

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Reference</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">From</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">To</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Items</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Requested by</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($transfers as $transfer)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $transfer->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3">
                    <span class="font-mono text-sm text-blue-600 font-medium">
                        {{ $transfer->reference_no }}
                    </span>
                    </td>

                    <td class="px-5 py-3 text-gray-700">
                        {{ $transfer->fromBranch?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3 text-gray-700">
                        {{ $transfer->toBranch?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#eff6ff;color:#1d4ed8">
                        {{ $transfer->items->count() }}
                        {{ Str::plural('item', $transfer->items->count()) }}
                    </span>
                    </td>

                    <td class="px-5 py-3">
                        @php
                            $statusColors = [
                                'pending'    => 'background:#fef3c7;color:#92400e',
                                'approved'   => 'background:#dcfce7;color:#166534',
                                'dispatched' => 'background:#dbeafe;color:#1e40af',
                                'received'   => 'background:#f3f4f6;color:#374151',
                                'rejected'   => 'background:#fee2e2;color:#991b1b',
                            ];
                            $style = $statusColors[$transfer->status] ?? 'background:#f3f4f6;color:#374151';
                        @endphp
                        <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;{{ $style }}">
                        {{ ucfirst($transfer->status) }}
                    </span>
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $transfer->requestedBy?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3 text-xs text-gray-400">
                        {{ $transfer->created_at->format('d M Y') }}
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">

                            @if($transfer->trashed())
                                @can('create transfers')
                                    <form method="POST"
                                          action="{{ route('transfers.restore', $transfer->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-green-600 hover:underline font-medium">
                                            Restore
                                        </button>
                                    </form>
                                @endcan
                            @else
                                <a href="{{ route('transfers.show', $transfer) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">
                                    View
                                </a>

                                @if($transfer->status === 'pending')
                                    @can('approve transfers')
                                        <form method="POST"
                                              action="{{ route('transfers.approve', $transfer) }}">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-green-600 hover:underline font-medium">
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST"
                                              action="{{ route('transfers.reject', $transfer) }}"
                                              onsubmit="return confirm('Reject this transfer?')">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-red-500 hover:underline font-medium">
                                                Reject
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                @if($transfer->status === 'approved')
                                    @can('dispatch transfers')
                                        <form method="POST"
                                              action="{{ route('transfers.dispatch', $transfer) }}"
                                              onsubmit="return confirm('Dispatch this transfer? Stock will be deducted from source branch.')">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-blue-600 hover:underline font-medium">
                                                Dispatch
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                @if($transfer->status === 'dispatched')
                                    @can('receive transfers')
                                        <a href="{{ route('transfers.show', $transfer) }}#receive"
                                           class="text-xs text-purple-600 hover:underline font-medium">
                                            Receive
                                        </a>
                                    @endcan
                                @endif

                                @if($transfer->status === 'pending')
                                    @can('create transfers')
                                        <form method="POST"
                                              action="{{ route('transfers.destroy', $transfer) }}"
                                              onsubmit="return confirm('Delete this transfer request?')">
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
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400">
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
