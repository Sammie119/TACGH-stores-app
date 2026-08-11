{{-- resources/views/purchase-orders/_form.blade.php --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    {{-- Order details --}}
    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Order details
        </p>
        <div class="space-y-4">

            {{-- Supplier + Branch --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Supplier <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="supplier_id"
                                class="w-full h-10 px-3 pr-8 rounded-lg border bg-white
                                       text-sm text-gray-800 focus:outline-none
                                       focus:ring-2 focus:ring-blue-500 appearance-none
                                       {{ $errors->has('supplier_id') ? 'border-red-400' : 'border-gray-300' }}">
                            <option value="">— Select supplier —</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id', $purchaseOrder?->supplier_id ?? request('supplier_id')) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
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
                    @error('supplier_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Deliver to branch <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="branch_id"
                                class="w-full h-10 px-3 pr-8 rounded-lg border bg-white
                                       text-sm text-gray-800 focus:outline-none
                                       focus:ring-2 focus:ring-blue-500 appearance-none
                                       {{ $errors->has('branch_id') ? 'border-red-400' : 'border-gray-300' }}">
                            <option value="">— Select branch —</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('branch_id', $purchaseOrder?->branch_id ?? auth()->user()->branch_id) == $branch->id ? 'selected' : '' }}>
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
                    @error('branch_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Order date + Expected date --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Order date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="order_date"
                           value="{{ old('order_date', $purchaseOrder?->order_date?->format('Y-m-d') ?? today()->format('Y-m-d')) }}"
                           class="w-full h-10 px-3 rounded-lg border text-sm
                                  text-gray-800 focus:outline-none
                                  focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('order_date') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    @error('order_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Expected delivery date
                        <span class="text-xs text-gray-400 font-normal ml-1">
                            (optional)
                        </span>
                    </label>
                    <input type="date" name="expected_date"
                           value="{{ old('expected_date', $purchaseOrder?->expected_date?->format('Y-m-d')) }}"
                           class="w-full h-10 px-3 rounded-lg border border-gray-300
                                  bg-white text-sm text-gray-800 focus:outline-none
                                  focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Notes
                    <span class="text-xs text-gray-400 font-normal ml-1">(optional)</span>
                </label>
                <textarea name="notes" rows="2"
                          placeholder="Any special instructions…"
                          class="w-full px-3 py-2.5 rounded-lg border border-gray-300
                                 bg-white text-sm text-gray-800 resize-none
                                 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $purchaseOrder?->notes) }}</textarea>
            </div>

        </div>
    </div>

    {{-- Order items --}}
    <div class="p-6 border-t border-gray-100">
        <div style="display:flex;align-items:center;justify-content:space-between;
                    margin-bottom:20px">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">
                    Order items
                </p>
                @error('items')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <button type="button" onclick="addPoItem()"
                    class="h-8 px-3 bg-blue-50 hover:bg-blue-100 text-blue-600
                           text-xs font-medium rounded-lg transition-colors"
                    style="display:inline-flex;align-items:center;gap:4px">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add item
            </button>
        </div>

        {{-- Column headers --}}
        <div style="display:grid;grid-template-columns:1fr 120px 130px 120px 40px;
                    gap:12px;margin-bottom:8px;padding:0 4px">
            <p class="text-xs font-medium text-gray-500">Product</p>
            <p class="text-xs font-medium text-gray-500">Quantity</p>
            <p class="text-xs font-medium text-gray-500">Unit cost (GHS)</p>
            <p class="text-xs font-medium text-gray-500">Subtotal</p>
            <p></p>
        </div>

        <div id="po-items-container" class="space-y-3">

            @if($purchaseOrder && $purchaseOrder->items->count())
                @foreach($purchaseOrder->items as $i => $item)
                    <div class="po-item-row"
                         style="display:grid;grid-template-columns:1fr 120px 130px 120px 40px;
                            gap:12px;align-items:center">
                        <div class="relative po-product-field">
                            <input type="text"
                                   class="po-product-search w-full h-10 px-3 rounded-lg border
                                      border-gray-300 bg-white text-sm text-gray-800
                                      focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Search product…"
                                   autocomplete="off"
                                   value="{{ $item->product ? $item->product->name.' ('.$item->product->sku.')' : '' }}"
                                   oninput="searchPoProduct(this)"
                                   onkeydown="handlePoProductKey(event, this)">
                            <input type="hidden" name="items[{{ $i }}][product_id]"
                                   class="po-product-id"
                                   value="{{ $item->product_id }}">
                            <div class="po-product-results"
                                 style="display:none;position:absolute;top:calc(100% + 4px);
                                        left:0;right:0;z-index:20;background:#fff;
                                        border:1px solid #e5e7eb;border-radius:8px;
                                        box-shadow:0 4px 12px rgba(0,0,0,.08);
                                        max-height:240px;overflow-y:auto"></div>
                        </div>
                        <input type="number" name="items[{{ $i }}][quantity]"
                               value="{{ $item->quantity_ordered }}"
                               min="0.01" step="0.01"
                               onchange="updateSubtotal(this)"
                               class="w-full h-10 px-3 rounded-lg border border-gray-300
                                  bg-white text-sm text-gray-800 focus:outline-none
                                  focus:ring-2 focus:ring-blue-500">
                        <input type="number" name="items[{{ $i }}][unit_cost]"
                               value="{{ $item->unit_cost }}"
                               min="0" step="0.01"
                               onchange="updateSubtotal(this)"
                               class="w-full h-10 px-3 rounded-lg border border-gray-300
                                  bg-white text-sm text-gray-800 focus:outline-none
                                  focus:ring-2 focus:ring-blue-500">
                        <div style="height:40px;display:flex;align-items:center;
                                justify-content:flex-end;font-size:13px;
                                font-weight:500;color:#374151"
                             class="subtotal-display">
                            GHS {{ number_format($item->subtotal, 2) }}
                        </div>
                        <button type="button" onclick="removePoItem(this)"
                                class="h-10 w-10 flex items-center justify-center
                                   text-gray-400 hover:text-red-500 hover:bg-red-50
                                   rounded-lg transition-colors"
                                style="border:none;background:transparent;cursor:pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            @else
                {{-- First blank row --}}
                <div class="po-item-row"
                     style="display:grid;grid-template-columns:1fr 120px 130px 120px 40px;
                        gap:12px;align-items:center">
                    <div class="relative po-product-field">
                        <input type="text"
                               class="po-product-search w-full h-10 px-3 rounded-lg border
                                  border-gray-300 bg-white text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Search product…"
                               autocomplete="off"
                               oninput="searchPoProduct(this)"
                               onkeydown="handlePoProductKey(event, this)">
                        <input type="hidden" name="items[0][product_id]"
                               class="po-product-id" value="">
                        <div class="po-product-results"
                             style="display:none;position:absolute;top:calc(100% + 4px);
                                    left:0;right:0;z-index:20;background:#fff;
                                    border:1px solid #e5e7eb;border-radius:8px;
                                    box-shadow:0 4px 12px rgba(0,0,0,.08);
                                    max-height:240px;overflow-y:auto"></div>
                    </div>
                    <input type="number" name="items[0][quantity]"
                           min="0.01" step="0.01" placeholder="0"
                           onchange="updateSubtotal(this)"
                           class="w-full h-10 px-3 rounded-lg border border-gray-300
                              bg-white text-sm text-gray-800 focus:outline-none
                              focus:ring-2 focus:ring-blue-500">
                    <input type="number" name="items[0][unit_cost]"
                           min="0" step="0.01" placeholder="0.00"
                           onchange="updateSubtotal(this)"
                           class="w-full h-10 px-3 rounded-lg border border-gray-300
                              bg-white text-sm text-gray-800 focus:outline-none
                              focus:ring-2 focus:ring-blue-500">
                    <div style="height:40px;display:flex;align-items:center;
                            justify-content:flex-end;font-size:13px;
                            font-weight:500;color:#9ca3af"
                         class="subtotal-display">
                        GHS 0.00
                    </div>
                    <button type="button" onclick="removePoItem(this)"
                            class="h-10 w-10 flex items-center justify-center
                               text-gray-400 hover:text-red-500 hover:bg-red-50
                               rounded-lg transition-colors"
                            style="border:none;background:transparent;cursor:pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            @endif

        </div>

        {{-- Order total --}}
        <div style="display:flex;justify-content:flex-end;margin-top:16px;
                    padding-top:16px;border-top:1px solid #f3f4f6">
            <div style="text-align:right">
                <p class="text-xs text-gray-500 mb-1">Order total</p>
                <p id="order-total"
                   style="font-size:20px;font-weight:700;color:#2563eb">
                    GHS 0.00
                </p>
            </div>
        </div>

    </div>

    {{-- Danger zone (edit only) --}}
    @if($purchaseOrder && in_array($purchaseOrder->status, ['draft','cancelled']))
        <div class="p-6 border-t border-red-100 bg-red-50">
            <p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-4">
                Danger zone
            </p>
            <div class="bg-white border border-red-200 rounded-lg p-4"
                 style="display:flex;align-items:center;
                    justify-content:space-between;gap:16px">
                <div>
                    <p class="text-sm font-medium text-red-700">
                        Delete this purchase order
                    </p>
                    <p class="text-xs text-red-400 mt-0.5">
                        Only draft or cancelled orders can be deleted.
                    </p>
                </div>
                <button type="submit" form="delete-form"
                        class="h-9 px-4 bg-white border border-red-300 text-red-600
                           text-sm font-medium rounded-lg transition-colors
                           hover:bg-red-600 hover:text-white hover:border-red-600">
                    Delete order
                </button>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
         style="display:flex;align-items:center;gap:12px">
        <button type="submit" form="main-form"
                class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors focus:outline-none
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            {{ $purchaseOrder ? 'Save changes' : 'Create purchase order' }}
        </button>
        <a href="{{ route('purchase-orders.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                  font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>
        @if($purchaseOrder)
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}"
               class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
               style="margin-left:auto">
                View order →
            </a>
        @endif
    </div>

</div>

@push('scripts')
    <script>
        let poItemCount = {{ $purchaseOrder ? $purchaseOrder->items->count() : 1 }};

        @php
            $poProductsData = $products->map(fn($product) => [
                'id'         => $product->id,
                'name'       => $product->name,
                'sku'        => $product->sku,
                'cost_price' => $product->cost_price,
            ])->values();
        @endphp
        const poProducts = @json($poProductsData);

        function searchPoProduct(input) {
            const row    = input.closest('.po-item-row');
            const hidden = row.querySelector('.po-product-id');
            const box    = row.querySelector('.po-product-results');
            const q      = input.value.trim().toLowerCase();

            // Typing invalidates any previous selection until a result is re-picked.
            hidden.value = '';

            if (!q) {
                box.style.display = 'none';
                row._poIndex = -1;
                return;
            }

            const matches = poProducts.filter(p =>
                p.name.toLowerCase().includes(q) || (p.sku ?? '').toLowerCase().includes(q)
            ).slice(0, 8);

            if (!matches.length) {
                box.innerHTML = `
            <div style="padding:10px 14px;font-size:13px;color:#9ca3af">
                No products found
            </div>`;
                box.style.display = 'block';
                row._poIndex = -1;
                return;
            }

            box.innerHTML = matches.map((p, i) => `
            <div class="po-result-item" data-index="${i}"
                 data-id="${p.id}" data-name="${p.name.replace(/"/g, '&quot;')}"
                 data-sku="${(p.sku ?? '').replace(/"/g, '&quot;')}" data-cost="${p.cost_price}"
                 onclick="selectPoProduct(this)"
                 style="padding:10px 14px;font-size:13px;color:#111827;cursor:pointer;
                        border-bottom:1px solid #f9fafb"
                 onmouseover="this.style.background='#f0f9ff'"
                 onmouseout="this.style.background='#fff'">
                ${p.name} <span style="color:#9ca3af">(${p.sku ?? ''})</span>
            </div>`).join('');

            box.style.display = 'block';
            row._poIndex = -1;
        }

        function handlePoProductKey(e, input) {
            const row   = input.closest('.po-item-row');
            const box   = row.querySelector('.po-product-results');
            const items = box.querySelectorAll('.po-result-item');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                row._poIndex = Math.min((row._poIndex ?? -1) + 1, items.length - 1);
                highlightPoResult(items, row._poIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                row._poIndex = Math.max((row._poIndex ?? -1) - 1, -1);
                highlightPoResult(items, row._poIndex);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                const idx = row._poIndex ?? -1;
                if (idx >= 0 && items[idx]) {
                    items[idx].click();
                } else if (items.length === 1) {
                    items[0].click();
                }
            } else if (e.key === 'Escape') {
                box.style.display = 'none';
            }
        }

        function highlightPoResult(items, idx) {
            items.forEach((el, i) => {
                el.style.background = i === idx ? '#eff6ff' : '#fff';
            });
        }

        function selectPoProduct(el) {
            const row    = el.closest('.po-item-row');
            const input  = row.querySelector('.po-product-search');
            const hidden = row.querySelector('.po-product-id');
            const box    = row.querySelector('.po-product-results');

            input.value        = `${el.dataset.name} (${el.dataset.sku})`;
            hidden.value       = el.dataset.id;
            hidden.dataset.cost = el.dataset.cost;
            box.style.display  = 'none';

            updateSubtotal(hidden);
        }

        document.addEventListener('click', function (e) {
            document.querySelectorAll('.po-product-results').forEach(box => {
                const field = box.closest('.po-product-field');
                if (field && !field.contains(e.target)) {
                    box.style.display = 'none';
                }
            });
        });

        function addPoItem() {
            const container = document.getElementById('po-items-container');
            const div       = document.createElement('div');
            div.className   = 'po-item-row';
            div.style.cssText = 'display:grid;grid-template-columns:1fr 120px 130px 120px 40px;gap:12px;align-items:center';

            div.innerHTML = `
        <div class="po-product-field" style="position:relative">
            <input type="text" class="po-product-search"
                   placeholder="Search product…" autocomplete="off"
                   oninput="searchPoProduct(this)"
                   onkeydown="handlePoProductKey(event, this)"
                   style="width:100%;height:40px;padding:0 12px;box-sizing:border-box;
                          border:1px solid #d1d5db;border-radius:8px;font-size:14px;
                          color:#111827;background:#fff;outline:none">
            <input type="hidden" name="items[${poItemCount}][product_id]" class="po-product-id" value="">
            <div class="po-product-results"
                 style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;
                        z-index:20;background:#fff;border:1px solid #e5e7eb;border-radius:8px;
                        box-shadow:0 4px 12px rgba(0,0,0,.08);max-height:240px;overflow-y:auto"></div>
        </div>
        <input type="number" name="items[${poItemCount}][quantity]"
               min="0.01" step="0.01" placeholder="0"
               onchange="updateSubtotal(this)"
               style="width:100%;height:40px;padding:0 12px;border:1px solid #d1d5db;
                      border-radius:8px;font-size:14px;color:#111827;outline:none">
        <input type="number" name="items[${poItemCount}][unit_cost]"
               min="0" step="0.01" placeholder="0.00"
               onchange="updateSubtotal(this)"
               style="width:100%;height:40px;padding:0 12px;border:1px solid #d1d5db;
                      border-radius:8px;font-size:14px;color:#111827;outline:none">
        <div class="subtotal-display"
             style="height:40px;display:flex;align-items:center;justify-content:flex-end;
                    font-size:13px;font-weight:500;color:#9ca3af">
            GHS 0.00
        </div>
        <button type="button" onclick="removePoItem(this)"
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
            poItemCount++;
        }

        function removePoItem(btn) {
            const rows = document.querySelectorAll('.po-item-row');
            if (rows.length === 1) {
                alert('At least one item is required.');
                return;
            }
            btn.closest('.po-item-row').remove();
            updateOrderTotal();
        }

        function updateSubtotal(input) {
            const row       = input.closest('.po-item-row');
            const productId = row.querySelector('.po-product-id');
            const qtyInput  = row.querySelector('input[name*="quantity"]');
            const costInput = row.querySelector('input[name*="unit_cost"]');
            const display   = row.querySelector('.subtotal-display');

            // Auto-fill cost from product data
            if (input === productId && productId.dataset.cost) {
                costInput.value = parseFloat(productId.dataset.cost).toFixed(2);
            }

            const qty  = parseFloat(qtyInput?.value) || 0;
            const cost = parseFloat(costInput?.value) || 0;
            const sub  = qty * cost;

            if (display) {
                display.textContent = `GHS ${sub.toFixed(2)}`;
                display.style.color = sub > 0 ? '#374151' : '#9ca3af';
            }

            updateOrderTotal();
        }

        function updateOrderTotal() {
            const displays = document.querySelectorAll('.subtotal-display');
            let total = 0;
            displays.forEach(d => {
                const val = parseFloat(d.textContent.replace('GHS ', '')) || 0;
                total += val;
            });
            const totalEl = document.getElementById('order-total');
            if (totalEl) totalEl.textContent = `GHS ${total.toFixed(2)}`;
        }

        // Init totals on page load
        document.addEventListener('DOMContentLoaded', updateOrderTotal);
    </script>
@endpush
