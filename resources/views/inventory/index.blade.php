{{-- resources/views/inventory/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Inventory')
@section('header', 'Inventory')
@section('subheader', 'Stock levels across all branches')

@section('content')

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px">

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total items</p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                {{ number_format($totalItems) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Active stock records</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Low stock</p>
            <p class="text-2xl font-semibold mt-1 {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-gray-800' }}">
                {{ number_format($lowStockCount) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Below reorder level</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Out of stock</p>
            <p class="text-2xl font-semibold mt-1 {{ $outOfStock > 0 ? 'text-red-600' : 'text-gray-800' }}">
                {{ number_format($outOfStock) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Zero quantity items</p>
        </div>

    </div>

    {{-- Filters --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"
              method="GET" action="{{ route('inventory.index') }}">

            <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search product or SKU…"
                       class="h-9 pl-9 pr-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none focus:ring-2
                          focus:ring-blue-500"
                       style="width:220px">
            </div>

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

            <select name="category"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                       text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="stock"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                       text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All levels</option>
                <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>
                    Low stock
                </option>
                <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>
                    Out of stock
                </option>
            </select>

            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                       text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search', 'branch', 'category', 'stock']))
                <a href="{{ route('inventory.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500 transition-colors">Clear</a>
            @endif
        </form>

        <a href="{{ route('inventory.movements') }}"
           class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50 text-gray-600
              text-sm font-medium rounded-lg transition-colors"
           style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Movement log
        </a>
    </div>

    {{-- Stock table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Product
                </th>
                @if($isSuperAdmin)
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Branch
                    </th>
                @endif
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Category
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Quantity
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Reorder level
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Status
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Last updated
                </th>
                @can('adjust stock')
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Actions
                    </th>
                @endcan
            </tr>
            </thead>
            <tbody>
            @forelse($stock as $item)
                @php
                    $isLow  = $item->quantity <= $item->product->reorder_level && $item->quantity > 0;
                    $isOut  = $item->quantity <= 0;
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:32px;height:32px;border-radius:8px;background:#f3f4f6;
                                    color:#6b7280;display:flex;align-items:center;
                                    justify-content:center;font-size:11px;font-weight:600;
                                    flex-shrink:0">
                                {{ strtoupper(substr($item->product->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $item->product->sku }}</p>
                            </div>
                        </div>
                    </td>

                    @if($isSuperAdmin)
                        <td class="px-5 py-3 text-gray-600">{{ $item->branch->name }}</td>
                    @endif

                    <td class="px-5 py-3 text-gray-600">
                        {{ $item->product->category?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3">
                    <span class="font-semibold {{ $isOut ? 'text-red-600' : ($isLow ? 'text-amber-600' : 'text-gray-800') }}">
                        {{ number_format($item->quantity, 0) }}
                    </span>
                        <span class="text-xs text-gray-400 ml-1">{{ $item->product->unit }}</span>
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $item->product->reorder_level }}
                    </td>

                    <td class="px-5 py-3">
                        @if($isOut)
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#fee2e2;color:#991b1b">
                            Out of stock
                        </span>
                        @elseif($isLow)
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#fef3c7;color:#92400e">
                            Low stock
                        </span>
                        @else
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#dcfce7;color:#166534">
                            In stock
                        </span>
                        @endif
                    </td>

                    <td class="px-5 py-3 text-xs text-gray-400">
                        {{ $item->last_updated
                            ? \Carbon\Carbon::parse($item->last_updated)->diffForHumans()
                            : 'Never' }}
                    </td>

                    @can('adjust stock')
                        <td class="px-5 py-3">
                            <a href="{{ route('inventory.adjust', $item->product) }}"
                               class="text-xs text-blue-600 hover:underline font-medium">
                                Adjust
                            </a>
                        </td>
                    @endcan

                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                        No stock records found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($stock->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $stock->links() }}
            </div>
        @endif
    </div>

@endsection
