{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')
@section('title', $product->name)
@section('header', $product->name)
@section('subheader', 'SKU: ' . $product->sku)

@section('content')
    <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start">

        {{-- Left: product card --}}
        <div class="space-y-4">

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}"
                         alt="{{ $product->name }}"
                         style="width:100%;height:180px;object-fit:cover">
                @else
                    <div style="width:100%;height:120px;background:#f3f4f6;display:flex;
                        align-items:center;justify-content:center;
                        font-size:36px;font-weight:700;color:#d1d5db">
                        {{ strtoupper(substr($product->name, 0, 2)) }}
                    </div>
                @endif
                <div class="p-5">
                    <p class="font-semibold text-gray-800">{{ $product->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $product->category?->name }}</p>
                    @if($product->description)
                        <p class="text-sm text-gray-500 mt-3">{{ $product->description }}</p>
                    @endif
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Details
                </p>
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">SKU</dt>
                        <dd class="font-mono text-gray-700">{{ $product->sku }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Barcode</dt>
                        <dd class="font-mono text-gray-700">{{ $product->barcode ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Unit</dt>
                        <dd class="text-gray-700">{{ ucfirst($product->unit) }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Cost price</dt>
                        <dd class="text-gray-700">GHS {{ number_format($product->cost_price, 2) }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Selling price</dt>
                        <dd class="font-semibold text-gray-800">
                            GHS {{ number_format($product->selling_price, 2) }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Reorder level</dt>
                        <dd class="text-gray-700">{{ $product->reorder_level }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Total stock</dt>
                        <dd class="font-semibold text-gray-800">
                            {{ number_format($totalStock) }} {{ $product->unit }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            @if($product->is_active)
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
                        </dd>
                    </div>
                </dl>
            </div>

            @can('edit products')
                <a href="{{ route('products.edit', $product) }}"
                   class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white text-sm
                  font-medium rounded-lg transition-colors"
                   style="display:flex;align-items:center;justify-content:center;gap:6px">
                    Edit product
                </a>
            @endcan

        </div>

        {{-- Right: stock per branch --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"
                 style="display:flex;align-items:center;justify-content:space-between">
                <p class="font-semibold text-gray-700">Stock by branch</p>
                @can('adjust stock')
                    <a href="{{ route('inventory.adjust', $product) }}"
                       class="h-8 px-3 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs
                      font-medium rounded-lg transition-colors"
                       style="display:inline-flex;align-items:center;gap:4px">
                        Adjust stock
                    </a>
                @endcan
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($branchStock as $stock)
                    <div class="px-5 py-4"
                         style="display:flex;align-items:center;justify-content:space-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700">
                                {{ $stock->branch->name }}
                            </p>
                            <p class="text-xs text-gray-400">
                                Last updated:
                                {{ $stock->last_updated ? \Carbon\Carbon::parse($stock->last_updated)->diffForHumans() : 'Never' }}
                            </p>
                        </div>
                        <div style="text-align:right">
                            <p class="text-sm font-semibold
                              {{ $stock->quantity <= $product->reorder_level ? 'text-red-600' : 'text-gray-800' }}">
                                {{ number_format($stock->quantity) }}
                                <span class="text-xs font-normal text-gray-400">{{ $product->unit }}</span>
                            </p>
                            @if($stock->quantity <= $product->reorder_level)
                                <p class="text-xs text-red-500">Below reorder level</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-gray-400">
                        No stock records found.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
