{{-- resources/views/stock-takes/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Stock Takes')
@section('header', 'Stock Takes')
@section('subheader', 'Physical inventory counts and reconciliation')

@section('content')

    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"
              method="GET" action="{{ route('stock-takes.index') }}">

            @if($isSuperAdmin)
                <select name="branch_id"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                       text-sm text-gray-700 focus:outline-none
                       focus:ring-2 focus:ring-blue-500">
                    <option value="">All branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}"
                            {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
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
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                       text-sm font-medium rounded-lg hover:bg-gray-50
                       transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['branch_id','status']))
                <a href="{{ route('stock-takes.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500">Clear</a>
            @endif
        </form>

        <a href="{{ route('stock-takes.create') }}"
           class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
              font-medium rounded-lg transition-colors"
           style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            New stock take
        </a>
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('stock-takes.index')"
    />

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Reference</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Branch</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Progress</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Started by</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($stockTakes as $st)
                @php
                    $total    = $st->items()->count();
                    $counted  = $st->items()->whereNotNull('counted_quantity')->count();
                    $progress = $total > 0 ? round(($counted / $total) * 100) : 0;
                    $sc = [
                        'draft'            => 'background:#f3f4f6;color:#374151',
                        'in_progress'      => 'background:#dbeafe;color:#1e40af',
                        'pending_approval' => 'background:#fef3c7;color:#92400e',
                        'approved'         => 'background:#dcfce7;color:#166534',
                        'cancelled'        => 'background:#fee2e2;color:#991b1b',
                    ][$st->status] ?? 'background:#f3f4f6;color:#374151';
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $st->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3">
                        <a href="{{ route('stock-takes.show', $st) }}"
                           class="font-mono text-sm text-blue-600 hover:underline font-medium">
                            {{ $st->reference }}
                        </a>
                    </td>

                    <td class="px-5 py-3 text-gray-700">
                        {{ $st->branch?->name }}
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="flex:1;height:6px;background:#e5e7eb;
                                    border-radius:3px;overflow:hidden;max-width:80px">
                                <div style="height:100%;width:{{ $progress }}%;
                                        background:{{ $progress === 100 ? '#16a34a' : '#2563eb' }};
                                        border-radius:3px;transition:width .3s">
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">
                            {{ $counted }}/{{ $total }}
                        </span>
                        </div>
                    </td>

                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;{{ $sc }}">
                        {{ ucfirst(str_replace('_', ' ', $st->status)) }}
                    </span>
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $st->createdBy?->name }}
                    </td>

                    <td class="px-5 py-3 text-xs text-gray-500">
                        {{ $st->created_at->format('d M Y') }}
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($st->trashed())
                                <form method="POST"
                                      action="{{ route('stock-takes.restore', $st->id) }}">
                                    @csrf @method('PATCH')
                                    <button class="text-xs text-green-600
                                               hover:underline font-medium">
                                        Restore
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('stock-takes.show', $st) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">
                                    {{ $st->status === 'in_progress' ? 'Count' : 'View' }}
                                </a>

                                @if($st->status === 'pending_approval')
                                    <form method="POST"
                                          action="{{ route('stock-takes.approve', $st) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-green-600
                                                   hover:underline font-medium">
                                            Approve
                                        </button>
                                    </form>
                                @endif

                                @if(!in_array($st->status, ['approved','cancelled']))
                                    <form method="POST"
                                          action="{{ route('stock-takes.cancel', $st) }}"
                                          onsubmit="return confirm('Cancel this stock take?')">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-red-500
                                                   hover:underline font-medium">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="7"
                        class="px-5 py-12 text-center text-gray-400">
                        No stock takes found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($stockTakes->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $stockTakes->links() }}
            </div>
        @endif
    </div>

@endsection
