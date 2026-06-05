{{-- resources/views/returns/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Returns')
@section('header', 'Returns')
@section('subheader', 'Manage product returns and refunds')

@section('content')

    {{-- Pending alert --}}
    @if($pendingCount > 0)
        <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;
            background:#fef3c7;border:1px solid #fde68a;border-radius:10px;
            margin-bottom:16px">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p style="font-size:13px;color:#92400e;font-weight:500">
                {{ $pendingCount }} return {{ Str::plural('request', $pendingCount) }}
                awaiting approval
            </p>
        </div>
    @endif

    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"
              method="GET" action="{{ route('returns.index') }}">

            <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center
                         pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search invoice no…"
                       class="h-9 pl-9 pr-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none
                          focus:ring-2 focus:ring-blue-500"
                       style="width:200px">
            </div>

            <select name="status"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                       text-sm text-gray-700 focus:outline-none
                       focus:ring-2 focus:ring-blue-500">
                <option value="">All statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}"
                        {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>

            <select name="type"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                       text-sm text-gray-700 focus:outline-none
                       focus:ring-2 focus:ring-blue-500">
                <option value="">All types</option>
                @foreach($types as $type)
                    <option value="{{ $type }}"
                        {{ request('type') === $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                      text-sm text-gray-700 focus:outline-none
                      focus:ring-2 focus:ring-blue-500">

            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                      text-sm text-gray-700 focus:outline-none
                      focus:ring-2 focus:ring-blue-500">

            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                       text-sm font-medium rounded-lg hover:bg-gray-50
                       transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search','status','type','date_from','date_to']))
                <a href="{{ route('returns.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500 transition-colors">
                    Clear
                </a>
            @endif
        </form>

        @can('create returns')
            <a href="{{ route('returns.create') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
              font-medium rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                New return
            </a>
        @endcan
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('returns.index')"
    />

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Invoice</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Product</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Qty</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Refund</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Type</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($returns as $return)
                @php
                    $typeColors = [
                        'refund'   => 'background:#dbeafe;color:#1e40af',
                        'exchange' => 'background:#f3e8ff;color:#6b21a8',
                        'damaged'  => 'background:#fee2e2;color:#991b1b',
                    ];
                    $statusColors = [
                        'pending'  => 'background:#fef3c7;color:#92400e',
                        'approved' => 'background:#dcfce7;color:#166534',
                        'rejected' => 'background:#fee2e2;color:#991b1b',
                    ];
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $return->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3">
                    <span class="font-mono text-sm font-medium text-blue-600">
                        {{ $return->sale?->invoice_no ?? '—' }}
                    </span>
                    </td>

                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">
                            {{ $return->product?->name ?? '—' }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $return->branch?->name }}
                        </p>
                    </td>

                    <td class="px-5 py-3 text-gray-700">
                        {{ number_format($return->quantity, 2) }}
                        <span class="text-xs text-gray-400">
                        {{ $return->product?->unit }}
                    </span>
                    </td>

                    <td class="px-5 py-3 font-medium text-gray-800">
                        GHS {{ number_format($return->refund_amount, 2) }}
                    </td>

                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;
                                 {{ $typeColors[$return->type] ?? '' }}">
                        {{ ucfirst($return->type) }}
                    </span>
                    </td>

                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;
                                 {{ $statusColors[$return->status] ?? '' }}">
                        {{ ucfirst($return->status) }}
                    </span>
                    </td>

                    <td class="px-5 py-3 text-xs text-gray-400">
                        {{ $return->created_at->format('d M Y') }}
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($return->trashed())
                                @can('approve returns')
                                    <form method="POST"
                                          action="{{ route('returns.restore', $return->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-green-600
                                               hover:underline font-medium">
                                            Restore
                                        </button>
                                    </form>
                                @endcan
                            @else
                                <a href="{{ route('returns.show', $return) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">
                                    View
                                </a>
                                @if($return->status === 'pending')
                                    @can('create returns')
                                        <a href="{{ route('returns.edit', $return) }}"
                                           class="text-xs text-gray-600 hover:underline font-medium">
                                            Edit
                                        </a>
                                    @endcan
                                @endif

                                @if($return->status === 'pending')
                                    @can('approve returns')
                                        <form method="POST"
                                              action="{{ route('returns.approve', $return) }}">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-green-600
                                                   hover:underline font-medium">
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST"
                                              action="{{ route('returns.reject', $return) }}"
                                              onsubmit="return confirm('Reject this return?')">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-red-500
                                                   hover:underline font-medium">
                                                Reject
                                            </button>
                                        </form>
                                    @endcan
                                    @can('create returns')
                                        <form method="POST"
                                              action="{{ route('returns.destroy', $return) }}"
                                              onsubmit="return confirm('Delete this return?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-gray-400
                                                   hover:underline font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                @if($return->status === 'approved')
                                    <a href="{{ route('pdf.return-receipt', $return) }}"
                                       target="_blank"
                                       class="text-xs text-red-500 hover:underline font-medium">
                                        Receipt
                                    </a>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8"
                        class="px-5 py-12 text-center text-gray-400">
                        No returns found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($returns->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $returns->links() }}
            </div>
        @endif
    </div>

@endsection
