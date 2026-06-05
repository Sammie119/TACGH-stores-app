{{-- resources/views/returns/_form.blade.php --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

    {{-- Step 1: Find invoice --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="p-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
                Step 1 — Find invoice
            </p>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Invoice number
                </label>
                <div style="display:flex;gap:10px">
                    {{-- Connected to search-form via form attribute --}}
                    <input type="text"
                           name="invoice"
                           form="search-form"
                           value="{{ $invoiceNo }}"
                           placeholder="e.g. INV-000001"
                           autocomplete="off"
                           class="w-full h-10 px-3 rounded-lg border border-gray-300
                                  bg-white text-sm font-mono text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit"
                            form="search-form"
                            class="h-10 px-5 bg-gray-800 hover:bg-gray-900 text-white
                                   text-sm font-medium rounded-lg transition-colors
                                   flex-shrink-0">
                        Search
                    </button>
                </div>
            </div>

            {{-- Not found --}}
            @if($invoiceNo && !$sale)
                <div style="margin-top:16px;padding:12px 14px;background:#fef2f2;
                        border:1px solid #fecaca;border-radius:8px">
                    <p style="font-size:13px;color:#dc2626">
                        Invoice <strong>{{ $invoiceNo }}</strong> not found
                        or does not belong to your branch.
                    </p>
                </div>
            @endif

            {{-- Found --}}
            @if($sale)
                <div style="margin-top:16px;padding:14px;background:#f0fdf4;
                        border:1px solid #bbf7d0;border-radius:8px">
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none"
                             stroke="#16a34a" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <p style="font-size:13px;font-weight:600;color:#166534">
                            Invoice found
                        </p>
                    </div>
                    <dl class="space-y-2">
                        <div style="display:flex;justify-content:space-between;font-size:12px">
                            <dt style="color:#16a34a">Invoice no</dt>
                            <dd style="font-family:monospace;font-weight:600;color:#166534">
                                {{ $sale->invoice_no }}
                            </dd>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:12px">
                            <dt style="color:#16a34a">Sale date</dt>
                            <dd style="color:#166534">
                                {{ $sale->created_at->format('d M Y H:i') }}
                            </dd>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:12px">
                            <dt style="color:#16a34a">Total</dt>
                            <dd style="font-weight:600;color:#166534">
                                GHS {{ number_format($sale->total_amount, 2) }}
                            </dd>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:12px">
                            <dt style="color:#16a34a">Status</dt>
                            <dd style="color:#166534">{{ ucfirst($sale->status) }}</dd>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:12px">
                            <dt style="color:#16a34a">Products</dt>
                            <dd style="color:#166534">
                                {{ $saleItems->count() }} item(s)
                            </dd>
                        </div>
                    </dl>
                </div>
            @endif

        </div>
    </div>

    {{-- Step 2: Return details --}}
    <div>
        @if($sale)

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase
                           tracking-widest mb-5">
                        Step 2 — Return details
                    </p>
                    <div class="space-y-4">

                        {{-- Product --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                Product <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="product_id"
                                        form="main-form"
                                        class="w-full h-10 px-3 pr-8 rounded-lg border
                                           bg-white text-sm text-gray-800
                                           focus:outline-none focus:ring-2
                                           focus:ring-blue-500 appearance-none
                                           {{ $errors->has('product_id') ? 'border-red-400' : 'border-gray-300' }}">
                                    <option value="">— Select product to return —</option>
                                    @foreach($saleItems as $item)
                                        <option value="{{ $item->product_id }}"
                                            {{ old('product_id') == $item->product_id ? 'selected' : '' }}>
                                            {{ $item->product?->name }}
                                            — sold {{ number_format($item->quantity, 2) }}
                                            × GHS {{ number_format($item->unit_price, 2) }}
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
                            @error('product_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Quantity + Type --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                    Quantity <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="quantity"
                                       form="main-form"
                                       value="{{ old('quantity') }}"
                                       min="0.01" step="0.01"
                                       placeholder="0"
                                       class="w-full h-10 px-3 rounded-lg border
                                          text-sm text-gray-800 focus:outline-none
                                          focus:ring-2 focus:ring-blue-500
                                          {{ $errors->has('quantity') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                                @error('quantity')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                    Return type <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="type"
                                            form="main-form"
                                            class="w-full h-10 px-3 pr-8 rounded-lg border
                                               border-gray-300 bg-white text-sm
                                               text-gray-800 focus:outline-none
                                               focus:ring-2 focus:ring-blue-500
                                               appearance-none">
                                        <option value="refund"
                                            {{ old('type') === 'refund' ? 'selected' : '' }}>
                                            Refund — stock returned
                                        </option>
                                        <option value="exchange"
                                            {{ old('type') === 'exchange' ? 'selected' : '' }}>
                                            Exchange — swap product
                                        </option>
                                        <option value="damaged"
                                            {{ old('type') === 'damaged' ? 'selected' : '' }}>
                                            Damaged — write off
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
                        </div>

                        {{-- Reason --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                Reason
                                <span class="text-xs text-gray-400 font-normal ml-1">
                                (optional)
                            </span>
                            </label>
                            <textarea name="reason"
                                      form="main-form"
                                      rows="3"
                                      placeholder="Describe the reason for return…"
                                      class="w-full px-3 py-2.5 rounded-lg border
                                         border-gray-300 bg-white text-sm text-gray-800
                                         resize-none focus:outline-none
                                         focus:ring-2 focus:ring-blue-500">{{ old('reason') }}</textarea>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
                     style="display:flex;align-items:center;gap:12px">
                    <button type="submit"
                            form="main-form"
                            class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white
                               text-sm font-medium rounded-lg transition-colors
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               focus:ring-offset-2">
                        Submit return request
                    </button>
                    <a href="{{ route('returns.index') }}"
                       class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                          font-medium rounded-lg border border-gray-300 transition-colors"
                       style="display:inline-flex;align-items:center">
                        Cancel
                    </a>
                </div>

            </div>

        @else

            {{-- Empty state --}}
            <div class="bg-white border border-gray-200 rounded-xl p-10 text-center">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none"
                     stroke="#e5e7eb" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm text-gray-400 mt-1">
                    Search for an invoice on the left to begin
                </p>
            </div>

        @endif
    </div>

</div>
