{{-- resources/views/reports/stock-balance.blade.php --}}
@extends('layouts.app')
@section('title', 'Stock Balance Report')
@section('header', 'Stock Balance Report')
@section('subheader', 'Current stock levels, cost and selling values')

@section('content')

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <form method="GET" action="{{ route('reports.stock-balance') }}"
              style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap">

            @if($isSuperAdmin)
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">
                        Branch
                    </label>
                    <div class="relative">
                        <select name="branch_id"
                                class="h-9 px-3 pr-8 rounded-lg border border-gray-300
                               bg-white text-sm text-gray-700 focus:outline-none
                               focus:ring-2 focus:ring-blue-500 appearance-none"
                                style="min-width:160px">
                            <option value="">All branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center
                             pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
                    </div>
                </div>
            @endif

            {{-- Category --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Category
                </label>
                <div class="relative">
                    <select name="category_id"
                            class="h-9 px-3 pr-8 rounded-lg border border-gray-300
                               bg-white text-sm text-gray-700 focus:outline-none
                               focus:ring-2 focus:ring-blue-500 appearance-none"
                            style="min-width:160px">
                        <option value="">All categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center
                             pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
                </div>
            </div>

            {{-- Stock status --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Stock status
                </label>
                <div class="relative">
                    <select name="stock_status"
                            class="h-9 px-3 pr-8 rounded-lg border border-gray-300
                               bg-white text-sm text-gray-700 focus:outline-none
                               focus:ring-2 focus:ring-blue-500 appearance-none">
                        <option value="">All levels</option>
                        <option value="low"
                            {{ request('stock_status') === 'low' ? 'selected' : '' }}>
                            Low stock
                        </option>
                        <option value="out"
                            {{ request('stock_status') === 'out' ? 'selected' : '' }}>
                            Out of stock
                        </option>
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center
                             pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
                </div>
            </div>

            {{-- Search --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Search
                </label>
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
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="Product or SKU…"
                           class="h-9 pl-9 pr-3 rounded-lg border border-gray-300
                              bg-white text-sm text-gray-800 focus:outline-none
                              focus:ring-2 focus:ring-blue-500"
                           style="width:180px">
                </div>
            </div>

            <button type="submit"
                    class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors">
                Apply
            </button>

            @if(request()->hasAny(['branch_id','category_id','stock_status','search']))
                <a href="{{ route('reports.stock-balance') }}"
                   class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center">
                    Clear
                </a>
            @endif

            {{-- Print button --}}
            <a href="{{ route('pdf.stock-balance') }}?{{ http_build_query(request()->query()) }}"
               target="_blank"
               class="h-9 px-4 bg-red-600 hover:bg-red-700 text-white text-sm
                  font-medium rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;
                  margin-left:auto;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print PDF
            </a>

        </form>
    </div>

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
            gap:16px;margin-bottom:24px">

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total products
            </p>
            <p class="text-2xl font-bold text-gray-800 mt-1">
                {{ number_format($totalItems) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Active SKUs</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total cost value
            </p>
            <p class="text-2xl font-bold text-gray-800 mt-1">
                GHS {{ number_format($totalCostValue, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">At cost price</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total selling value
            </p>
            <p class="text-2xl font-bold text-gray-800 mt-1">
                GHS {{ number_format($totalSellingValue, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">At selling price</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Potential profit
            </p>
            <p class="text-2xl font-bold text-green-600 mt-1">
                GHS {{ number_format($totalSellingValue - $totalCostValue, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Selling minus cost</p>
        </div>
    </div>

    {{-- Stock table grouped by category --}}
    @forelse($groupedStock as $categoryName => $items)

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">

            {{-- Category header --}}
            <div style="padding:10px 20px;background:#1e3a5f;
                display:flex;align-items:center;justify-content:space-between">
                <div style="display:flex;align-items:center;gap:8px">
                    <svg style="width:16px;height:16px;color:#93c5fd;flex-shrink:0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1.5"
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span style="font-size:13px;font-weight:600;color:#fff">
                {{ $categoryName ?? 'Uncategorised' }}
            </span>
                </div>
                <div style="display:flex;align-items:center;gap:16px">
            <span style="font-size:11px;color:#93c5fd">
                {{ $items->count() }} product{{ $items->count() !== 1 ? 's' : '' }}
            </span>
                    <span style="font-size:11px;color:#93c5fd">
                Cost: GHS {{ number_format($items->sum(fn($s) => $s->quantity * $s->product->cost_price), 2) }}
            </span>
                    <span style="font-size:11px;color:#86efac">
                Sell: GHS {{ number_format($items->sum(fn($s) => $s->quantity * $s->product->selling_price), 2) }}
            </span>
                </div>
            </div>

            <table class="w-full text-sm"
                   style="border-collapse:collapse;min-width:700px">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase tracking-wide">Product</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase tracking-wide">SKU</th>
                    @if($isSuperAdmin && !request('branch_id'))
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase tracking-wide">Branch</th>
                    @endif
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase tracking-wide">Qty in stock</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase tracking-wide">Unit</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase tracking-wide">Cost price</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase tracking-wide">Cost value</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase tracking-wide">Selling price</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase tracking-wide">Selling value</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase tracking-wide">Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    @php
                        $isOut = $item->quantity <= 0;
                        $isLow = !$isOut && $item->quantity <= $item->product->reorder_level;
                        $costValue    = $item->quantity * $item->product->cost_price;
                        $sellingValue = $item->quantity * $item->product->selling_price;
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $isOut ? 'bg-red-50' : ($isLow ? 'bg-amber-50' : '') }}">

                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $item->product->name }}
                        </td>

                        <td class="px-5 py-3 font-mono text-xs text-gray-500">
                            {{ $item->product->sku }}
                        </td>

                        @if($isSuperAdmin && !request('branch_id'))
                            <td class="px-5 py-3 text-gray-600 text-xs">
                                {{ $item->branch?->name }}
                            </td>
                        @endif

                        <td class="px-5 py-3 text-right font-bold
                           {{ $isOut ? 'text-red-600' : ($isLow ? 'text-amber-600' : 'text-gray-800') }}">
                            {{ number_format($item->quantity, 2) }}
                        </td>

                        <td class="px-5 py-3 text-gray-500 text-xs">
                            {{ $item->product->unit }}
                        </td>

                        <td class="px-5 py-3 text-right text-gray-700">
                            GHS {{ number_format($item->product->cost_price, 2) }}
                        </td>

                        <td class="px-5 py-3 text-right font-semibold text-gray-800">
                            GHS {{ number_format($costValue, 2) }}
                        </td>

                        <td class="px-5 py-3 text-right text-gray-700">
                            GHS {{ number_format($item->product->selling_price, 2) }}
                        </td>

                        <td class="px-5 py-3 text-right font-semibold text-green-600">
                            GHS {{ number_format($sellingValue, 2) }}
                        </td>

                        <td class="px-5 py-3">
                            @if($isOut)
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#fee2e2;color:#991b1b">
                        Out
                    </span>
                            @elseif($isLow)
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#fef3c7;color:#92400e">
                        Low
                    </span>
                            @else
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#dcfce7;color:#166534">
                        OK
                    </span>
                            @endif
                        </td>

                    </tr>
                @endforeach
                </tbody>
                {{-- Category subtotals --}}
                <tfoot>
                <tr style="background:#f9fafb;border-top:2px solid #e5e7eb">
                    <td colspan="{{ ($isSuperAdmin && !request('branch_id')) ? 6 : 5 }}"
                        class="px-5 py-3 text-xs font-semibold text-gray-500 text-right">
                        Category total
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-gray-800">
                        GHS {{ number_format($items->sum(fn($s) => $s->quantity * $s->product->cost_price), 2) }}
                    </td>
                    <td></td>
                    <td class="px-5 py-3 text-right font-bold text-green-600">
                        GHS {{ number_format($items->sum(fn($s) => $s->quantity * $s->product->selling_price), 2) }}
                    </td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>

    @empty
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="#e5e7eb"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-sm font-medium text-gray-500">No stock records found</p>
            <p class="text-xs text-gray-400 mt-1">
                Try adjusting your filters
            </p>
        </div>
    @endforelse

    {{-- Grand totals --}}
    @if($stock->count() > 0)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div style="padding:14px 20px;background:#111827;
                display:flex;align-items:center;justify-content:space-between;
                flex-wrap:wrap;gap:16px">
        <span style="font-size:13px;font-weight:700;color:#fff">
            Grand total — all categories
        </span>
                <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap">
                    <div style="text-align:right">
                        <p style="font-size:10px;color:#9ca3af">Total products</p>
                        <p style="font-size:14px;font-weight:700;color:#fff">
                            {{ number_format($totalItems) }}
                        </p>
                    </div>
                    <div style="text-align:right">
                        <p style="font-size:10px;color:#9ca3af">Total cost value</p>
                        <p style="font-size:14px;font-weight:700;color:#fff">
                            GHS {{ number_format($totalCostValue, 2) }}
                        </p>
                    </div>
                    <div style="text-align:right">
                        <p style="font-size:10px;color:#9ca3af">Total selling value</p>
                        <p style="font-size:14px;font-weight:700;color:#86efac">
                            GHS {{ number_format($totalSellingValue, 2) }}
                        </p>
                    </div>
                    <div style="text-align:right">
                        <p style="font-size:10px;color:#9ca3af">Potential profit</p>
                        <p style="font-size:14px;font-weight:700;color:#34d399">
                            GHS {{ number_format($totalSellingValue - $totalCostValue, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
