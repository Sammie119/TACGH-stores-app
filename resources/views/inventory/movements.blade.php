{{-- resources/views/inventory/movements.blade.php --}}
@extends('layouts.app')
@section('title', 'Stock Movements')
@section('header', 'Stock Movement Log')
@section('subheader', 'Full history of all stock changes')

@section('content')

    {{-- Filters --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"
              method="GET" action="{{ route('inventory.movements') }}">

            <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search product…"
                       class="h-9 pl-9 pr-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none focus:ring-2
                          focus:ring-blue-500"
                       style="width:200px">
            </div>

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

            <select name="type"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                       text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All types</option>
                @foreach($types as $type)
                    <option value="{{ $type }}"
                        {{ request('type') === $type ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                    </option>
                @endforeach
            </select>

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

            @if(request()->hasAny(['search', 'branch', 'type', 'date_from', 'date_to']))
                <a href="{{ route('inventory.movements') }}"
                   class="text-sm text-gray-400 hover:text-red-500 transition-colors">Clear</a>
            @endif
        </form>

        <a href="{{ route('inventory.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
            ← Back to inventory
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Date
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Product
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Branch
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Type
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Quantity
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Balance after
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    By
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Notes
                </th>
            </tr>
            </thead>
            <tbody>
            @forelse($movements as $movement)
                @php
                    $isIn  = $movement->quantity > 0;
                    $isOut = $movement->quantity < 0;
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">

                    <td class="px-5 py-3 text-xs text-gray-500 whitespace-nowrap">
                        {{ $movement->created_at->format('d M Y') }}<br>
                        <span class="text-gray-400">{{ $movement->created_at->format('H:i') }}</span>
                    </td>

                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $movement->product?->name }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $movement->product?->sku }}</p>
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $movement->branch?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3">
                        @php
                            $typeColors = [
                                'sale'          => ['bg:#fef3c7', 'color:#92400e'],
                                'transfer_in'   => ['bg:#dcfce7', 'color:#166534'],
                                'transfer_out'  => ['bg:#fee2e2', 'color:#991b1b'],
                                'adjustment'    => ['bg:#eff6ff', 'color:#1d4ed8'],
                                'restock'       => ['bg:#e0f2fe', 'color:#0369a1'], // ← new
                                'return'        => ['bg:#f3e8ff', 'color:#6b21a8'],
                                'opening'       => ['bg:#f3f4f6', 'color:#374151'],
                            ];
                            $colors = $typeColors[$movement->type] ?? ['bg:#f3f4f6', 'color:#374151'];
                        @endphp
                        <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;
                                 background:{{ str_replace('bg:', '', $colors[0]) }};
                                 color:{{ str_replace('color:', '', $colors[1]) }}">
                        {{ ucfirst(str_replace('_', ' ', $movement->type)) }}
                    </span>
                    </td>

                    <td class="px-5 py-3 font-semibold
                           {{ $isIn ? 'text-green-600' : ($isOut ? 'text-red-600' : 'text-gray-700') }}">
                        {{ $isIn ? '+' : '' }}{{ number_format($movement->quantity, 2) }}
                    </td>

                    <td class="px-5 py-3 text-gray-700">
                        {{ number_format($movement->balance_after, 2) }}
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $movement->user?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3 text-xs text-gray-400">
                        {{ $movement->notes ? Str::limit($movement->notes, 40) : '—' }}
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                        No stock movements recorded yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($movements->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $movements->links() }}
            </div>
        @endif
    </div>

@endsection
