{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Products')
@section('header', 'Products')
@section('subheader', 'Central product catalogue')

@section('content')

    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"
              method="GET" action="{{ route('products.index') }}">

            <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search name, SKU, barcode…"
                       class="h-9 pl-9 pr-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none focus:ring-2
                          focus:ring-blue-500"
                       style="width:240px">
            </div>

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
                <option value="">All stock levels</option>
                <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>
                    Low stock only
                </option>
            </select>

            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                       text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search', 'category', 'stock']))
                <a href="{{ route('products.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500 transition-colors">Clear</a>
            @endif
        </form>

        @can('create products')
            <a href="{{ route('products.create') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium
              rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add product
            </a>
        @endcan

        @can('create products')
            <a href="{{ route('products.import') }}"
               class="h-9 px-4 bg-green-600 hover:bg-green-700 text-white text-sm
          font-medium rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 11l3-3m0 0l3 3m-3-3v8"/>
                </svg>
                Import
            </a>
        @endcan
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('products.index')"
    />

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Product
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    SKU
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Category
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Quantity
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Cost
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Price
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Reorder
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Status
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $product->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($product->image)
                                <img src="{{ branch_logo_url($product->image) }}"
                                     alt="{{ $product->name }}"
                                     style="width:34px;height:34px;border-radius:8px;
                                    object-fit:cover;border:1px solid #e5e7eb">
                            @else
                                <div style="width:34px;height:34px;border-radius:8px;background:#f3f4f6;
                                    color:#9ca3af;display:flex;align-items:center;
                                    justify-content:center;font-size:13px;font-weight:600;
                                    flex-shrink:0">
                                    {{ strtoupper(substr($product->name, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                <p class="text-xs text-gray-400">{{ $product->unit }}</p>
                            </div>
                        </div>
                    </td>

                    <td class="px-5 py-3">
                    <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded">
                        {{ $product->sku }}
                    </span>
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $product->category?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3 text-gray-700">
                        {{ number_format($product->total_quantity ?? 0, 0) }}
                    </td>

                    <td class="px-5 py-3 text-gray-700">
                        GHS {{ number_format($product->cost_price, 2) }}
                    </td>

                    <td class="px-5 py-3 font-medium text-gray-800">
                        GHS {{ number_format($product->selling_price, 2) }}
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $product->reorder_level }}
                    </td>

                    <td class="px-5 py-3">
                        @if($product->trashed())
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#fee2e2;color:#991b1b">
                            Deleted
                        </span>
                        @elseif($product->is_active)
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#dcfce7;color:#166534">
                            Active
                        </span>
                        @else
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#f3f4f6;color:#374151">
                            Inactive
                        </span>
                        @endif
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:12px">
                            @if($product->trashed())
                                @can('create products')
                                    <form method="POST"
                                          action="{{ route('products.restore', $product->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-green-600 hover:underline font-medium">
                                            Restore
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('products.force-delete', $product->id) }}"
                                          onsubmit="return confirm('Permanently delete this product?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:underline font-medium">
                                            Delete permanently
                                        </button>
                                    </form>
                                @endcan
                            @else
                                <a href="{{ route('products.show', $product) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">
                                    View
                                </a>
                                @can('edit products')
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="text-xs text-gray-600 hover:underline font-medium">
                                        Edit
                                    </a>
                                @endcan
                                @can('delete products')
                                    <form method="POST"
                                          action="{{ route('products.destroy', $product) }}"
                                          onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-500 hover:underline font-medium">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                        No products found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($products->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
