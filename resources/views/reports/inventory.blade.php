{{-- resources/views/reports/inventory.blade.php --}}
@extends('layouts.app')
@section('title', 'Inventory Report')
@section('header', 'Inventory Report')
@section('subheader', 'Stock levels and valuations')

@section('content')

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <form method="GET" action="{{ route('reports.inventory') }}"
              style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap">

            @if($isSuperAdmin)
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Branch</label>
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
                </div>
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Parent category</label>
                <select id="parent-category"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                           text-sm text-gray-700 focus:outline-none
                           focus:ring-2 focus:ring-blue-500">
                    <option value="">All parent categories</option>
                    @foreach($categories->whereNull('parent_id') as $root)
                        <option value="{{ $root->id }}">{{ $root->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                <select name="category_id" id="category-select"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                           text-sm text-gray-700 focus:outline-none
                           focus:ring-2 focus:ring-blue-500">
                    <option value="">All categories</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Stock status
                </label>
                <select name="stock_status"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                           text-sm text-gray-700 focus:outline-none
                           focus:ring-2 focus:ring-blue-500">
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
            </div>

            <button type="submit"
                    class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors">
                Apply
            </button>

            @if(request()->hasAny(['branch_id','category_id','stock_status']))
                <a href="{{ route('reports.inventory') }}"
                   class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center">
                    Clear
                </a>
            @endif

        </form>
    </div>

    {{-- Summary --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Cost value
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                GHS {{ number_format($totalValue->cost_value ?? 0, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Total stock at cost</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Selling value
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                GHS {{ number_format($totalValue->selling_value ?? 0, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Total stock at selling price</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Low stock items
            </p>
            <p class="text-2xl font-semibold mt-1
                  {{ $lowCount > 0 ? 'text-amber-600' : 'text-gray-800' }}">
                {{ $lowCount }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Below reorder level</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Out of stock
            </p>
            <p class="text-2xl font-semibold mt-1
                  {{ $outCount > 0 ? 'text-red-600' : 'text-gray-800' }}">
                {{ $outCount }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Zero quantity items</p>
        </div>
    </div>

    {{-- Stock table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100"
             style="display:flex;align-items:center;justify-content:space-between">
            <p class="font-semibold text-gray-700">
                Stock records
                <span class="text-sm font-normal text-gray-400 ml-2">
                {{ $stock->total() }} items
            </span>
            </p>
            @can('export reports')
                <a href="{{ route('reports.export.inventory') }}?{{ http_build_query(request()->query()) }}"
                   class="h-8 px-3 bg-green-50 hover:bg-green-100 text-green-700 text-xs
                  font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center;gap:4px">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>

                <a href="{{ route('pdf.inventory-report') }}?{{ http_build_query(request()->query()) }}"
                   target="_blank"
                   class="h-8 px-3 bg-red-50 hover:bg-red-100 text-red-600 text-xs
                    font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center;gap:4px">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export PDF
                </a>
            @endcan
        </div>
        <table class="w-full text-sm" style="border-collapse:collapse">
            <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Product</th>
                @if($isSuperAdmin)
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Branch</th>
                @endif
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Category</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Qty</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Reorder</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Cost value</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Selling value</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Status</th>
            </tr>
            </thead>
            <tbody>
            @forelse($stock as $item)
                @php
                    $isLow = $item->quantity <= $item->product->reorder_level
                             && $item->quantity > 0;
                    $isOut = $item->quantity <= 0;
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $item->product->name }}</p>
                        <p class="text-xs text-gray-400 font-mono">
                            {{ $item->product->sku }}
                        </p>
                    </td>
                    @if($isSuperAdmin)
                        <td class="px-5 py-3 text-gray-600">{{ $item->branch->name }}</td>
                    @endif
                    <td class="px-5 py-3 text-gray-600">
                        {{ $item->product->category?->name ?? '—' }}
                    </td>
                    <td class="px-5 py-3 text-right font-medium
                           {{ $isOut ? 'text-red-600' : ($isLow ? 'text-amber-600' : 'text-gray-800') }}">
                        {{ number_format($item->quantity, 0) }}
                        <span class="text-xs font-normal text-gray-400">
                        {{ $item->product->unit }}
                    </span>
                    </td>
                    <td class="px-5 py-3 text-right text-gray-600">
                        {{ $item->product->reorder_level }}
                    </td>
                    <td class="px-5 py-3 text-right text-gray-700">
                        GHS {{ number_format($item->quantity * $item->product->cost_price, 2) }}
                    </td>
                    <td class="px-5 py-3 text-right text-gray-700">
                        GHS {{ number_format($item->quantity * $item->product->selling_price, 2) }}
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

@push('scripts')
<script>
    @php
        $categoriesData = $categories->map(fn($c) => [
            'id' => $c->id, 'name' => $c->name, 'parent_id' => $c->parent_id,
        ]);
    @endphp
    const allCategories = @json($categoriesData);

    function collectDescendants(parentId, depth, acc) {
        allCategories
            .filter(c => c.parent_id == parentId)
            .sort((a, b) => a.name.localeCompare(b.name))
            .forEach(c => {
                acc.push({ id: c.id, name: c.name, depth });
                collectDescendants(c.id, depth + 1, acc);
            });
        return acc;
    }

    function populateCategorySelect(parentId, selectedId = '') {
        const sel = document.getElementById('category-select');
        sel.innerHTML = '';
        if (!parentId) {
            sel.appendChild(new Option('All categories', ''));
            return;
        }
        const parent = allCategories.find(c => c.id == parentId);
        sel.appendChild(new Option('All in ' + (parent?.name ?? ''), parentId));
        collectDescendants(parentId, 1, []).forEach(c => {
            sel.appendChild(new Option('  '.repeat(c.depth - 1) + '— ' + c.name, c.id));
        });
        sel.value = selectedId || parentId;
    }

    document.getElementById('parent-category').addEventListener('change', function () {
        populateCategorySelect(this.value);
    });

    // Restore state from the current filter value on load
    (function () {
        const currentId = '{{ request('category_id') }}';
        if (currentId) {
            let current = allCategories.find(c => c.id == currentId);
            let rootId = current?.id ?? '';
            while (current && current.parent_id) {
                current = allCategories.find(c => c.id == current.parent_id);
                if (current) rootId = current.id;
            }
            document.getElementById('parent-category').value = rootId;
            populateCategorySelect(rootId, currentId);
        } else {
            populateCategorySelect('');
        }
    })();
</script>
@endpush
