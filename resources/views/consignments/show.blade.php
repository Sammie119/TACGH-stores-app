{{-- resources/views/consignments/show.blade.php --}}
@extends('layouts.app')
@section('title', $consignment->reference_no)
@section('header', 'Consignment ' . $consignment->reference_no)
@section('subheader', 'Created ' . $consignment->created_at->format('d M Y H:i'))

@section('content')

    {{-- Status timeline --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <div style="display:flex;align-items:center">
            @php
                $steps        = ['pending', 'dispatched', 'partial', 'completed'];
                $currentIndex = array_search($consignment->status, $steps);
                $isCancelled  = $consignment->status === 'cancelled';
            @endphp

            @foreach($steps as $i => $step)
                @php $done = !$isCancelled && $currentIndex !== false && $currentIndex >= $i; @endphp
                <div style="display:flex;align-items:center;flex:1">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:6px">
                        <div style="width:32px;height:32px;border-radius:50%;display:flex;
                            align-items:center;justify-content:center;font-size:12px;
                            font-weight:600;flex-shrink:0;
                            background:{{ $done ? '#2563eb' : '#f3f4f6' }};
                            color:{{ $done ? '#fff' : '#9ca3af' }}">
                            @if($done)
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <p style="font-size:11px;font-weight:500;white-space:nowrap;
                          color:{{ $done ? '#2563eb' : '#9ca3af' }}">
                            {{ ucfirst($step) }}
                        </p>
                    </div>
                    @if(!$loop->last)
                        <div style="flex:1;height:2px;margin:0 8px;margin-bottom:18px;
                        background:{{ ($done && $currentIndex !== false && $currentIndex > $i) ? '#2563eb' : '#e5e7eb' }}">
                        </div>
                    @endif
                </div>
            @endforeach

            @if($isCancelled)
                <div style="margin-left:16px;margin-bottom:18px;padding:4px 12px;
                    border-radius:20px;background:#fee2e2;color:#991b1b;
                    font-size:12px;font-weight:500;white-space:nowrap">
                    Cancelled
                </div>
            @endif
        </div>
    </div>

    {{-- Main grid --}}
    <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:20px;align-items:start">

        {{-- Left: info + financial summary + actions --}}
        <div class="space-y-4">

            {{-- Consignment info --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Consignment info
                </p>
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Invoice</dt>
                        <dd class="font-mono font-medium text-gray-800">{{ $consignment->reference_no }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Branch</dt>
                        <dd class="font-medium text-gray-800">{{ $consignment->branch?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Dispatched by</dt>
                        <dd class="text-gray-700">{{ $consignment->user?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Recipient</dt>
                        <dd class="font-medium text-gray-800">{{ $consignment->recipient_name }}</dd>
                    </div>
                    @if($consignment->customer)
                        <div style="display:flex;justify-content:space-between;gap:8px">
                            <dt class="text-gray-500 flex-shrink-0">Phone</dt>
                            <dd class="text-gray-700">{{ $consignment->customer->phone ?? '—' }}</dd>
                        </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Financial year</dt>
                        <dd class="text-gray-700">{{ $consignment->financialYear?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Date</dt>
                        <dd class="text-gray-700">{{ $consignment->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Status</dt>
                        <dd>
                            @php
                                $statusColors = [
                                    'pending'    => 'background:#fef3c7;color:#92400e',
                                    'dispatched' => 'background:#dbeafe;color:#1e40af',
                                    'partial'    => 'background:#ffedd5;color:#9a3412',
                                    'completed'  => 'background:#dcfce7;color:#166534',
                                    'cancelled'  => 'background:#f3f4f6;color:#374151',
                                ];
                                $style = $statusColors[$consignment->status] ?? 'background:#f3f4f6;color:#374151';
                            @endphp
                            <span style="padding:2px 10px;border-radius:20px;font-size:11px;
                                         font-weight:500;{{ $style }}">
                                {{ ucfirst($consignment->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($consignment->notes)
                        <div>
                            <dt class="text-gray-500 mb-1">Notes</dt>
                            <dd class="text-gray-700 text-xs bg-gray-50 p-3 rounded-lg leading-relaxed">
                                {{ $consignment->notes }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Financial summary --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Financial summary
                </p>
                <div class="space-y-3">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span class="text-sm text-gray-500">Total value</span>
                        <span class="font-semibold text-gray-800">GHS {{ number_format($consignment->total_value, 2) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span class="text-sm text-gray-500">Amount paid</span>
                        <span class="font-semibold text-green-600">GHS {{ number_format($consignment->amount_paid, 2) }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3"
                         style="display:flex;justify-content:space-between;align-items:center">
                        <span class="text-sm font-medium text-gray-700">Balance due</span>
                        <span class="text-lg font-bold {{ $consignment->balance_due > 0 ? 'text-red-600' : 'text-green-600' }}">
                            GHS {{ number_format($consignment->balance_due, 2) }}
                        </span>
                    </div>
                    @if($consignment->total_value > 0)
                        @php $pct = min(100, round(($consignment->amount_paid / $consignment->total_value) * 100)); @endphp
                        <div>
                            <div style="height:6px;background:#e5e7eb;border-radius:99px;overflow:hidden">
                                <div style="height:100%;width:{{ $pct }}%;background:#16a34a;border-radius:99px;transition:width 0.3s"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ $pct }}% paid</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Actions
                </p>
                <div class="space-y-3">

                    @if($consignment->status === 'pending')
                        @can('dispatch consignments')
                            <form method="POST" action="{{ route('consignments.dispatch', $consignment) }}"
                                  onsubmit="return confirm('Dispatch this consignment? Stock will be deducted from {{ addslashes($consignment->branch?->name) }}.')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white
                                               text-sm font-medium rounded-lg transition-colors">
                                    Dispatch consignment
                                </button>
                            </form>
                        @endcan
                    @endif

                    @if($consignment->status === 'completed')
                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-center">
                            <p class="text-sm font-medium text-green-700">Fully paid</p>
                            <p class="text-xs text-green-500 mt-0.5">This consignment is complete</p>
                        </div>
                    @endif

                    @if($consignment->status === 'cancelled')
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-center">
                            <p class="text-sm font-medium text-gray-600">Cancelled</p>
                        </div>
                    @endif

                    @if(!in_array($consignment->status, ['completed', 'cancelled']))
                        @can('cancel consignments')
                            <form method="POST" action="{{ route('consignments.cancel', $consignment) }}"
                                  onsubmit="return confirm('Cancel this consignment?{{ in_array($consignment->status, ["dispatched","partial"]) ? " Stock will be restored to the branch." : "" }}')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full h-10 bg-white border border-red-300 text-red-600
                                               hover:bg-red-600 hover:text-white text-sm font-medium
                                               rounded-lg transition-colors">
                                    Cancel consignment
                                </button>
                            </form>
                        @endcan
                    @endif

                    <a href="{{ route('pdf.consignment-invoice', $consignment) }}"
                       target="_blank"
                       class="w-full h-10 bg-red-600 hover:bg-red-700 text-white text-sm
                              font-medium rounded-lg transition-colors"
                       style="display:flex;align-items:center;justify-content:center;gap:6px">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Print invoice
                    </a>

                    <a href="{{ route('consignments.index') }}"
                       class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                              text-gray-600 text-sm font-medium rounded-lg transition-colors"
                       style="display:flex;align-items:center;justify-content:center">
                        ← Back to consignments
                    </a>

                </div>
            </div>

        </div>

        {{-- Right: items + payments + payment form --}}
        <div class="space-y-4">

            {{-- Items table --}}
            @php $canEditItems = $consignment->status === 'pending' && auth()->user()->can('create consignments'); @endphp
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <p class="font-semibold text-gray-700">
                        {{ $consignment->status === 'pending' ? 'Products (editable)' : 'Products dispatched' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $consignment->items->count() }} {{ Str::plural('item', $consignment->items->count()) }}
                    </p>
                </div>
                <table class="w-full text-sm" style="border-collapse:collapse">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Product</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">SKU</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Unit Price</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Subtotal</th>
                        @if($canEditItems)
                            <th class="px-5 py-2.5" width="3%">...</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($consignment->items as $item)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-800">
                                {{ $item->product?->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-gray-400">
                                {{ $item->product?->sku ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700">
                                {{ $item->quantity + 0 }}
                            </td>
                            <td class="px-5 py-3 text-right text-gray-700">
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-5 py-3 text-right font-medium text-gray-800">
                                {{ number_format($item->subtotal, 2) }}
                            </td>
                            @if($canEditItems)
                                <td class="px-5 py-3" width="3%">
                                    <form method="POST"
                                          action="{{ route('consignments.items.remove', [$consignment, $item]) }}"
                                          onsubmit="return confirm('Remove {{ addslashes($item->product?->name) }} from this consignment?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="h-8 w-8 flex items-center justify-center rounded-lg text-gray-400
                                                       hover:text-red-500 hover:bg-red-50 transition-colors"
                                                style="border:none;background:transparent;cursor:pointer"
                                                title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr class="bg-gray-50 border-t border-gray-200">
                        <td colspan="{{ $canEditItems ? 4 : 3 }}" class="px-5 py-3 text-right text-sm font-semibold text-gray-600">
                            Total
                        </td>
                        <td colspan="2" class="px-5 py-3 text-right font-bold text-gray-800">
                            GHS {{ number_format($consignment->total_value, 2) }}
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Add product (pending only) --}}
            @if($canEditItems)
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="font-semibold text-gray-700">Add a product</p>
                        <p class="text-xs text-gray-400 mt-0.5">Add more products before dispatching</p>
                    </div>
                    <form method="POST" action="{{ route('consignments.items.add', $consignment) }}" class="p-5">
                        @csrf
                        <div style="display:grid;grid-template-columns:1fr 90px 120px auto;gap:10px;align-items:end">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Product</label>
                                <div class="relative">
                                    <select name="product_id"
                                            onchange="fillAddPrice(this)"
                                            class="w-full h-10 px-3 pr-8 rounded-lg border bg-white
                                                   text-sm text-gray-800 focus:outline-none focus:ring-2
                                                   focus:ring-blue-500 appearance-none
                                                   {{ $errors->has('product_id') ? 'border-red-400' : 'border-gray-300' }}">
                                        <option value="">— Select —</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                    data-price="{{ $product->selling_price }}">
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </span>
                                </div>
                                @error('product_id')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Qty</label>
                                <input type="number" name="quantity"
                                       min="0.01" step="0.01" placeholder="0"
                                       class="w-full h-10 px-3 rounded-lg border bg-white text-sm
                                              text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500
                                              {{ $errors->has('quantity') ? 'border-red-400' : 'border-gray-300' }}">
                                @error('quantity')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Unit Price (GHS)</label>
                                <input type="number" name="unit_price" id="add-unit-price"
                                       min="0" step="0.01" placeholder="0.00"
                                       class="w-full h-10 px-3 rounded-lg border bg-white text-sm
                                              text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500
                                              {{ $errors->has('unit_price') ? 'border-red-400' : 'border-gray-300' }}">
                                @error('unit_price')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <button type="submit"
                                        class="h-10 px-4 bg-blue-600 hover:bg-blue-700 text-white
                                               text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                                    Add
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                @push('scripts')
                <script>
                    function fillAddPrice(select) {
                        const price = select.selectedOptions[0]?.dataset?.price ?? '';
                        if (price) {
                            document.getElementById('add-unit-price').value = parseFloat(price).toFixed(2);
                        }
                    }
                </script>
                @endpush
            @endif

            {{-- Payment history --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <p class="font-semibold text-gray-700">Payment history</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $consignment->payments->count() }} {{ Str::plural('payment', $consignment->payments->count()) }} recorded
                    </p>
                </div>

                @if($consignment->payments->isEmpty())
                    <div class="px-5 py-8 text-center text-gray-400 text-sm">
                        No payments recorded yet.
                    </div>
                @else
                    <table class="w-full text-sm" style="border-collapse:collapse">
                        <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Receipt</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                            <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Method</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Recorded by</th>
                            <th class="px-5 py-2.5" width="4%">...</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($consignment->payments as $payment)
                            <tr class="border-b border-gray-50 hover:bg-gray-50">
                                <td class="px-5 py-3 font-mono text-xs text-blue-600">
                                    {{ $payment->reference }}
                                </td>
                                <td class="px-5 py-3 text-gray-600 text-xs">
                                    {{ $payment->payment_date->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-green-600">
                                    GHS {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-5 py-3">
                                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:500;
                                                 background:#f0fdf4;color:#166534;text-transform:capitalize">
                                        {{ $payment->payment_method }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-600 text-xs">
                                    {{ $payment->paidBy?->name ?? '—' }}
                                </td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('pdf.consignment-payment', $payment) }}"
                                       target="_blank"
                                       title="Print receipt"
                                       class="inline-flex items-center justify-center h-7 w-7 rounded-lg text-gray-400
                                              hover:text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr class="bg-gray-50 border-t border-gray-200">
                            <td colspan="2" class="px-5 py-3 text-right text-sm font-semibold text-gray-600">
                                Total paid
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-green-700">
                                GHS {{ number_format($consignment->amount_paid, 2) }}
                            </td>
                            <td colspan="3"></td>
                        </tr>
                        </tfoot>
                    </table>
                @endif
            </div>

            {{-- Add Payment form --}}
            @if(in_array($consignment->status, ['dispatched', 'partial']))
                @can('record consignment payment')
                    <div id="payment" class="bg-white border border-blue-200 rounded-xl overflow-hidden"
                         x-data="{ method: '{{ old('payment_method', 'cash') }}' }">
                        <div class="px-5 py-4 border-b border-blue-100 bg-blue-50">
                            <p class="font-semibold text-blue-800">Record a payment</p>
                            <p class="text-xs text-blue-600 mt-0.5">
                                Balance remaining: <strong>GHS {{ number_format($consignment->balance_due, 2) }}</strong>
                            </p>
                        </div>
                        <form method="POST" action="{{ route('consignments.payment', $consignment) }}"
                              class="p-5 space-y-4">
                            @csrf

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Amount (GHS) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="amount"
                                           min="0.01" step="0.01"
                                           max="{{ $consignment->balance_due }}"
                                           value="{{ old('amount') }}"
                                           placeholder="0.00"
                                           class="w-full h-10 px-3 rounded-lg border bg-white text-sm
                                                  text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                  {{ $errors->has('amount') ? 'border-red-400' : 'border-gray-300' }}">
                                    @error('amount')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Payment date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="payment_date"
                                           value="{{ old('payment_date', date('Y-m-d')) }}"
                                           class="w-full h-10 px-3 rounded-lg border bg-white text-sm
                                                  text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                  {{ $errors->has('payment_date') ? 'border-red-400' : 'border-gray-300' }}">
                                    @error('payment_date')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                    Payment method <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="payment_method"
                                            x-model="method"
                                            class="w-full h-10 px-3 pr-8 rounded-lg border bg-white text-sm
                                                   text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                   appearance-none
                                                   {{ $errors->has('payment_method') ? 'border-red-400' : 'border-gray-300' }}">
                                        @foreach(['cash' => 'Cash', 'momo' => 'Mobile Money', 'bank' => 'Bank Transfer', 'cheque' => 'Cheque'] as $val => $label)
                                            <option value="{{ $val }}" {{ old('payment_method') === $val ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </span>
                                </div>
                                @error('payment_method')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror

                                {{-- Cashier banking notice — only for cash --}}
                                @php
                                    $myCashBalance = \App\Models\Sale::where('user_id', auth()->id())
                                            ->where('payment_method', 'cash')->where('status', 'completed')->sum('amount_paid')
                                        + \App\Models\Sale::where('user_id', auth()->id())
                                            ->where('payment_method', 'split')->where('split_method_1', 'cash')
                                            ->where('status', 'completed')->sum('split_amount_1')
                                        + \App\Models\Sale::where('user_id', auth()->id())
                                            ->where('payment_method', 'split')->where('split_method_2', 'cash')
                                            ->where('status', 'completed')->sum('split_amount_2')
                                        + \App\Models\ConsignmentPayment::where('paid_by', auth()->id())
                                            ->where('payment_method', 'cash')->sum('amount')
                                        - \App\Models\BankDeposit::where('deposited_by', auth()->id())
                                            ->where('status', 'verified')->sum('amount');
                                @endphp
                                <div x-show="method === 'cash'" x-cloak>
                                    <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mt-2">
                                        Cash payments are added to your banking account.
                                        Your current outstanding cash balance is
                                        <strong>GHS {{ number_format($myCashBalance, 2) }}</strong>.
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                    Notes <span class="text-xs text-gray-400 font-normal ml-1">(optional)</span>
                                </label>
                                <textarea name="notes" rows="2"
                                          placeholder="Payment notes…"
                                          class="w-full px-3 py-2.5 rounded-lg border border-gray-300 bg-white
                                                 text-sm text-gray-800 resize-none
                                                 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                            </div>

                            <button type="submit"
                                    class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white
                                           text-sm font-medium rounded-lg transition-colors
                                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Record payment
                            </button>
                        </form>
                    </div>
                @endcan
            @endif

        </div>

    </div>

@endsection
