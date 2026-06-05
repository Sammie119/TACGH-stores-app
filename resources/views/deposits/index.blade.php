{{-- resources/views/deposits/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Bank Deposits')
@section('header', 'Bank Deposits')
@section('subheader', 'Manage pay-in slips and deposits')

@section('content')

    {{-- Summary --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Pending verification
            </p>
            <p class="text-2xl font-semibold mt-1
                  {{ $pendingCount > 0 ? 'text-amber-600' : 'text-gray-800' }}">
                {{ $pendingCount }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Awaiting accountant review</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total verified
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                GHS {{ number_format($totalVerified, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">All confirmed deposits</p>
        </div>
    </div>

    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"
              method="GET" action="{{ route('deposits.index') }}">

            @if($isSuperAdmin)
                <select name="branch"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                       text-sm text-gray-700 focus:outline-none
                       focus:ring-2 focus:ring-blue-500">
                    <option value="">All branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}"
                            {{ request('branch') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            @endif

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

            @if(request()->hasAny(['branch','status','date_from','date_to']))
                <a href="{{ route('deposits.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500 transition-colors">
                    Clear
                </a>
            @endif
        </form>

        @can('create deposits')
            <a href="{{ route('deposits.create') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
              font-medium rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                Upload slip
            </a>
        @endcan
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('deposits.index')"
    />

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Branch</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
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
                           uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($deposits as $deposit)
                @php
                    $statusColors = [
                        'pending'  => 'background:#fef3c7;color:#92400e',
                        'verified' => 'background:#dcfce7;color:#166534',
                        'rejected' => 'background:#fee2e2;color:#991b1b',
                    ];
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $deposit->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3 text-gray-700">
                        {{ $deposit->branch?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3 font-semibold text-gray-800">
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
                        {{ $deposit->depositedBy?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;
                                 {{ $statusColors[$deposit->status] ?? '' }}">
                        {{ ucfirst($deposit->status) }}
                    </span>
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($deposit->trashed())
                                @can('create deposits')
                                    <form method="POST"
                                          action="{{ route('deposits.restore', $deposit->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-green-600 hover:underline font-medium">
                                            Restore
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('deposits.force-delete', $deposit->id) }}"
                                          onsubmit="return confirm('Permanently delete this deposit? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:underline font-medium">
                                            Delete permanently
                                        </button>
                                    </form>
                                @endcan
                            @else
                                <a href="{{ route('deposits.show', $deposit) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">
                                    View
                                </a>

                                @if($deposit->status === 'pending')
                                    @can('verify deposits')
                                        <form method="POST"
                                              action="{{ route('deposits.verify', $deposit) }}">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-green-600
                                                   hover:underline font-medium">
                                                Verify
                                            </button>
                                        </form>
                                        <form method="POST"
                                              action="{{ route('deposits.reject', $deposit) }}"
                                              onsubmit="return confirm('Reject this deposit?')">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-red-500
                                                   hover:underline font-medium">
                                                Reject
                                            </button>
                                        </form>
                                    @endcan
                                    @can('create deposits')
                                        <a href="{{ route('deposits.edit', $deposit) }}"
                                           class="text-xs text-gray-600 hover:underline font-medium">
                                            Edit
                                        </a>
                                        <form method="POST"
                                              action="{{ route('deposits.destroy', $deposit) }}"
                                              onsubmit="return confirm('Delete this deposit?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-500
                                                   hover:underline font-medium">
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
                    <td colspan="8"
                        class="px-5 py-12 text-center text-gray-400">
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
