{{-- resources/views/transfers/_form.blade.php --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    {{-- ── Transfer details ── --}}
    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Transfer details
        </p>
        <div class="space-y-4">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        From branch <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </span>
                        <select name="from_branch_id"
                                class="w-full h-10 pl-9 pr-8 rounded-lg border bg-white
                                       text-sm text-gray-800 focus:outline-none
                                       focus:ring-2 focus:ring-blue-500 appearance-none
                                       {{ $errors->has('from_branch_id') ? 'border-red-400' : 'border-gray-300' }}">
                            <option value="">— Select source branch —</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('from_branch_id', $transfer?->from_branch_id ?? auth()->user()->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </div>
                    @error('from_branch_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        To branch <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </span>
                        <select name="to_branch_id"
                                class="w-full h-10 pl-9 pr-8 rounded-lg border bg-white
                                       text-sm text-gray-800 focus:outline-none
                                       focus:ring-2 focus:ring-blue-500 appearance-none
                                       {{ $errors->has('to_branch_id') ? 'border-red-400' : 'border-gray-300' }}">
                            <option value="">— Select destination branch —</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('to_branch_id', $transfer?->to_branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </div>
                    @error('to_branch_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Notes
                    <span class="text-xs text-gray-400 font-normal ml-1">(optional)</span>
                </label>
                <textarea name="notes" rows="2"
                          placeholder="Reason for transfer…"
                          class="w-full px-3 py-2.5 rounded-lg border border-gray-300
                                 bg-white text-sm text-gray-800 resize-none
                                 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $transfer?->notes) }}</textarea>
            </div>

        </div>
    </div>

    {{-- ── Transfer items ── --}}
    <div class="p-6 border-t border-gray-100">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">
                    Transfer items
                </p>
                @error('items')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <button type="button" onclick="addTransferItem()"
                    class="h-8 px-3 bg-blue-50 hover:bg-blue-100 text-blue-600
                           text-xs font-medium rounded-lg transition-colors"
                    style="display:inline-flex;align-items:center;gap:4px">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add item
            </button>
        </div>

        {{-- Column headers --}}
        <div style="display:grid;grid-template-columns:1fr 140px 40px;gap:12px;
                    margin-bottom:8px;padding:0 4px">
            <p class="text-xs font-medium text-gray-500">Product</p>
            <p class="text-xs font-medium text-gray-500">Quantity</p>
            <p></p>
        </div>

        <div id="items-container" class="space-y-3">

            {{-- Existing items on edit, one blank row on create --}}
            @if($transfer && $transfer->items->count())
                @foreach($transfer->items as $i => $item)
                    <div class="item-row"
                         style="display:grid;grid-template-columns:1fr 140px 40px;
                            gap:12px;align-items:center">
                        <div class="relative">
                            <select name="items[{{ $i }}][product_id]"
                                    class="w-full h-10 px-3 pr-8 rounded-lg border border-gray-300
                                       bg-white text-sm text-gray-800 focus:outline-none
                                       focus:ring-2 focus:ring-blue-500 appearance-none">
                                <option value="">— Select product —</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                        </div>
                        <input type="number" name="items[{{ $i }}][quantity]"
                               value="{{ $item->quantity_requested }}"
                               min="0.01" step="0.01"
                               class="w-full h-10 px-3 rounded-lg border border-gray-300
                                  bg-white text-sm text-gray-800 focus:outline-none
                                  focus:ring-2 focus:ring-blue-500">
                        <button type="button" onclick="removeTransferItem(this)"
                                class="h-10 w-10 flex items-center justify-center text-gray-400
                                   hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                style="border:none;background:transparent;cursor:pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            @else
                {{-- Blank first row for create --}}
                <div class="item-row"
                     style="display:grid;grid-template-columns:1fr 140px 40px;
                            gap:12px;align-items:center">
                    <div class="relative">
                        <select name="items[0][product_id]"
                                class="w-full h-10 px-3 pr-8 rounded-lg border border-gray-300
                                       bg-white text-sm text-gray-800 focus:outline-none
                                       focus:ring-2 focus:ring-blue-500 appearance-none">
                            <option value="">— Select product —</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </div>
                    <input type="number" name="items[0][quantity]"
                           min="0.01" step="0.01" placeholder="0"
                           class="w-full h-10 px-3 rounded-lg border border-gray-300
                                  bg-white text-sm text-gray-800 focus:outline-none
                                  focus:ring-2 focus:ring-blue-500">
                    <button type="button" onclick="removeTransferItem(this)"
                            class="h-10 w-10 flex items-center justify-center text-gray-400
                                   hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                            style="border:none;background:transparent;cursor:pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            @endif

        </div>
    </div>

    {{-- ── Danger zone (edit only) ── --}}
    @if($transfer && $transfer->status === 'pending')
        @can('create transfers')
            <div class="p-6 border-t border-red-100 bg-red-50">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-4">
                    Danger zone
                </p>
                <div class="bg-white border border-red-200 rounded-lg p-4"
                     style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <div>
                        <p class="text-sm font-medium text-red-700">Delete this transfer</p>
                        <p class="text-xs text-red-400 mt-0.5">
                            Only pending transfers can be deleted.
                        </p>
                    </div>
                    <button type="submit" form="delete-form"
                            class="h-9 px-4 bg-white border border-red-300 text-red-600
                               text-sm font-medium rounded-lg transition-colors
                               hover:bg-red-600 hover:text-white hover:border-red-600">
                        Delete transfer
                    </button>
                </div>
            </div>
        @endcan
    @endif

    {{-- ── Footer ── --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
         style="display:flex;align-items:center;gap:12px">
        <button type="submit" form="main-form"
                class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors focus:outline-none
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            {{ $transfer ? 'Save changes' : 'Submit transfer request' }}
        </button>
        <a href="{{ $transfer ? route('transfers.show', $transfer) : route('transfers.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                  font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>
        @if($transfer)
            <a href="{{ route('transfers.show', $transfer) }}"
               class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
               style="margin-left:auto">
                View transfer →
            </a>
        @endif
    </div>

</div>

{{-- ── JS for dynamic item rows ── --}}
@push('scripts')
    <script>
        let transferItemCount = {{ $transfer ? $transfer->items->count() : 1 }};

        const productOptions = `
    <option value="">— Select product —</option>
    @foreach($products as $product)
        <option value="{{ $product->id }}">{{ addslashes($product->name) }} ({{ $product->sku }})</option>
    @endforeach
        `;

        function addTransferItem() {
            const container = document.getElementById('items-container');
            const div       = document.createElement('div');
            div.className   = 'item-row';
            div.style.cssText = 'display:grid;grid-template-columns:1fr 140px 40px;gap:12px;align-items:center';

            div.innerHTML = `
        <div style="position:relative">
            <select name="items[${transferItemCount}][product_id]"
                    style="width:100%;height:40px;padding:0 32px 0 12px;
                           border:1px solid #d1d5db;border-radius:8px;font-size:14px;
                           color:#111827;background:#fff;outline:none;appearance:none">
                ${productOptions}
            </select>
            <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                         color:#9ca3af;pointer-events:none;display:flex">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </span>
        </div>
        <input type="number" name="items[${transferItemCount}][quantity]"
               min="0.01" step="0.01" placeholder="0"
               style="width:100%;height:40px;padding:0 12px;border:1px solid #d1d5db;
                      border-radius:8px;font-size:14px;color:#111827;outline:none">
        <button type="button" onclick="removeTransferItem(this)"
                style="height:40px;width:40px;display:flex;align-items:center;
                       justify-content:center;border:none;background:transparent;
                       cursor:pointer;border-radius:8px;color:#9ca3af"
                onmouseover="this.style.color='#ef4444';this.style.background='#fef2f2'"
                onmouseout="this.style.color='#9ca3af';this.style.background='transparent'">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;

            container.appendChild(div);
            transferItemCount++;
        }

        function removeTransferItem(btn) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length === 1) {
                alert('At least one item is required.');
                return;
            }
            btn.closest('.item-row').remove();
        }
    </script>
@endpush
