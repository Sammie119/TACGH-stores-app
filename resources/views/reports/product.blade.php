{{-- resources/views/reports/product.blade.php --}}
@extends('layouts.app')
@section('title', 'Product Report')
@section('header', 'Product Report')
@section('subheader', $selectedProduct ? $selectedProduct->name : 'Select a product to view its report')

@section('content')

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <form method="GET" action="{{ route('reports.product') }}"
              style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap">

            {{-- Product --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Product <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="product_id"
                            class="h-9 px-3 pr-8 rounded-lg border border-gray-300
                               bg-white text-sm text-gray-700 focus:outline-none
                               focus:ring-2 focus:ring-blue-500 appearance-none"
                            style="min-width:200px">
                        <option value="">— Select product —</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} ({{ $product->sku }})
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

            @if($isSuperAdmin)
                {{-- Branch --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">
                        Branch
                    </label>
                    <div class="relative">
                        <select name="branch_id"
                                class="h-9 px-3 pr-8 rounded-lg border border-gray-300
                               bg-white text-sm text-gray-700 focus:outline-none
                               focus:ring-2 focus:ring-blue-500 appearance-none">
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

            {{-- Date range --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="date_from"
                       value="{{ request('date_from') }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-700 focus:outline-none
                          focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="date_to"
                       value="{{ request('date_to') }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-700 focus:outline-none
                          focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors">
                Generate
            </button>

            @if(request()->hasAny(['product_id','branch_id','date_from','date_to']))
                <a href="{{ route('reports.product') }}"
                   class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center">
                    Clear
                </a>
            @endif

            @if($selectedProduct)
                <a href="{{ route('pdf.product-report') }}?{{ http_build_query(request()->query()) }}"
                   target="_blank"
                   class="h-9 px-4 bg-red-600 hover:bg-red-700 text-white text-sm
          font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print PDF
                </a>
            @endif
        </form>
    </div>

    @if($selectedProduct)

        {{-- Product info card --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                        Product
                    </p>
                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $selectedProduct->name }}
                    </p>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">
                        {{ $selectedProduct->sku }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                        Category
                    </p>
                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $selectedProduct->category?->name ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                        Cost price
                    </p>
                    <p class="font-semibold text-gray-800 mt-1">
                        GHS {{ number_format($selectedProduct->cost_price, 2) }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                        Selling price
                    </p>
                    <p class="font-semibold text-gray-800 mt-1">
                        GHS {{ number_format($selectedProduct->selling_price, 2) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Stock levels per branch --}}
        @if($stockLevel && $stockLevel->count() > 0)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-gray-100">
                    <p class="font-semibold text-gray-700">Stock levels by branch</p>
                </div>
                <table class="w-full text-sm" style="border-collapse:collapse">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Branch</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Quantity</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Cost value</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Selling value</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($stockLevel as $stock)
                        @php
                            $isOut = $stock->quantity <= 0;
                            $isLow = !$isOut && $stock->quantity <= $selectedProduct->reorder_level;
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-800">
                                {{ $stock->branch?->name }}
                            </td>
                            <td class="px-5 py-3 text-right font-bold
                           {{ $isOut ? 'text-red-600' : ($isLow ? 'text-amber-600' : 'text-gray-800') }}">
                                {{ number_format($stock->quantity, 2) }}
                                <span class="text-xs font-normal text-gray-400">
                        {{ $selectedProduct->unit }}
                    </span>
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700">
                                GHS {{ number_format($stock->quantity * $selectedProduct->cost_price, 2) }}
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700">
                                GHS {{ number_format($stock->quantity * $selectedProduct->selling_price, 2) }}
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
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Sales history --}}
        @if($salesData->count() > 0)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-gray-100"
                     style="display:flex;align-items:center;justify-content:space-between">
                    <p class="font-semibold text-gray-700">
                        Sales history
                        <span class="text-sm font-normal text-gray-400 ml-2">
                {{ $salesData->count() }} transactions
            </span>
                    </p>
                    <p class="text-sm font-semibold text-green-600">
                        GHS {{ number_format($salesData->sum('subtotal'), 2) }} total
                    </p>
                </div>
                <table class="w-full text-sm" style="border-collapse:collapse;min-width:500px">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Invoice</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Branch</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Qty sold</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Unit price</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Subtotal</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($salesData as $item)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <a href="{{ route('sales.show', $item->sale) }}"
                                   class="font-mono text-xs text-blue-600 hover:underline">
                                    {{ $item->sale->invoice_no }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $item->sale->branch?->name }}
                            </td>
                            <td class="px-5 py-3 text-right font-medium text-gray-800">
                                {{ number_format($item->quantity, 2) }}
                            </td>
                            <td class="px-5 py-3 text-right text-gray-600">
                                GHS {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-800">
                                GHS {{ number_format($item->subtotal, 2) }}
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">
                                {{ $item->sale->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="bg-gray-50 border-t border-gray-200">
                        <td colspan="2" class="px-5 py-3 font-semibold text-gray-700">
                            Total
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-gray-800">
                            {{ number_format($salesData->sum('quantity'), 2) }}
                        </td>
                        <td></td>
                        <td class="px-5 py-3 text-right font-bold text-green-600">
                            GHS {{ number_format($salesData->sum('subtotal'), 2) }}
                        </td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        {{-- Returns history --}}
        @if($returnsData->count() > 0)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-gray-100"
                     style="display:flex;align-items:center;justify-content:space-between">
                    <p class="font-semibold text-gray-700">
                        Returns
                        <span class="text-sm font-normal text-gray-400 ml-2">
                {{ $returnsData->count() }} returns
            </span>
                    </p>
                    <p class="text-sm font-semibold text-red-500">
                        GHS {{ number_format($returnsData->sum('refund_amount'), 2) }} refunded
                    </p>
                </div>
                <table class="w-full text-sm" style="border-collapse:collapse">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Invoice</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Type</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Qty</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Refund</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($returnsData as $ret)
                        @php
                            $tc = [
                                'refund'   => 'background:#dcfce7;color:#166534',
                                'exchange' => 'background:#dbeafe;color:#1e40af',
                                'damaged'  => 'background:#fee2e2;color:#991b1b',
                            ][$ret->type] ?? '';
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3 font-mono text-xs text-blue-600">
                                {{ $ret->sale?->invoice_no }}
                            </td>
                            <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;
                                 font-size:11px;font-weight:500;{{ $tc }}">
                        {{ ucfirst($ret->type) }}
                    </span>
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700">
                                {{ number_format($ret->quantity, 2) }}
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-red-500">
                                GHS {{ number_format($ret->refund_amount, 2) }}
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">
                                {{ $ret->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Purchases history --}}
        @if($purchasesData->count() > 0)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-gray-100"
                     style="display:flex;align-items:center;justify-content:space-between">
                    <p class="font-semibold text-gray-700">
                        Purchase history
                        <span class="text-sm font-normal text-gray-400 ml-2">
                {{ $purchasesData->count() }} orders
            </span>
                    </p>
                    <p class="text-sm font-semibold text-gray-700">
                        GHS {{ number_format($purchasesData->sum('subtotal'), 2) }} total cost
                    </p>
                </div>
                <table class="w-full text-sm" style="border-collapse:collapse">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">PO Number</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Supplier</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Qty ordered</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Qty received</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Unit cost</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Total</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($purchasesData as $purchase)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <a href="{{ route('purchase-orders.show', $purchase->purchaseOrder) }}"
                                   class="font-mono text-xs text-blue-600 hover:underline">
                                    {{ $purchase->purchaseOrder->po_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $purchase->purchaseOrder->supplier?->name }}
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700">
                                {{ number_format($purchase->quantity_ordered, 2) }}
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700">
                                {{ number_format($purchase->quantity_received, 2) }}
                            </td>
                            <td class="px-5 py-3 text-right text-gray-600">
                                GHS {{ number_format($purchase->unit_cost, 2) }}
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-800">
                                GHS {{ number_format($purchase->subtotal, 2) }}
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">
                                {{ $purchase->purchaseOrder->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Stock movements --}}
        @if($movements->count() > 0)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <p class="font-semibold text-gray-700">
                        Stock movements
                        <span class="text-sm font-normal text-gray-400 ml-2">
                {{ $movements->count() }} events
            </span>
                    </p>
                </div>
                <table class="w-full text-sm" style="border-collapse:collapse;min-width:500px">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Type</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Branch</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Quantity</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Balance after</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Notes</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($movements as $mov)
                        @php
                            $isIn  = in_array($mov->type, ['restock','return','adjustment_in','transfer_in']);
                            $mc = $isIn
                                ? 'background:#dcfce7;color:#166534'
                                : 'background:#fee2e2;color:#991b1b';
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;
                                 font-size:11px;font-weight:500;{{ $mc }}">
                        {{ ucfirst(str_replace('_', ' ', $mov->type)) }}
                    </span>
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $mov->branch?->name }}
                            </td>
                            <td class="px-5 py-3 text-right font-medium
                           {{ $isIn ? 'text-green-600' : 'text-red-500' }}">
                                {{ $isIn ? '+' : '-' }}{{ number_format($mov->quantity, 2) }}
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700">
                                {{ number_format($mov->balance_after, 2) }}
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">
                                {{ $mov->notes ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">
                                {{ $mov->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    @else

        {{-- Empty state --}}
        <div class="bg-white border border-gray-200 rounded-xl p-16 text-center">
            <svg class="w-10 h-10 mx-auto mb-4" fill="none" stroke="#e5e7eb"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-gray-400 text-sm">
                Select a product above to generate its report
            </p>
        </div>

    @endif

@endsection
