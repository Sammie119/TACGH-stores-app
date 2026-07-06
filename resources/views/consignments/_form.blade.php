{{-- resources/views/consignments/_form.blade.php --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    {{-- Recipient --}}
    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Recipient
        </p>
        <div class="space-y-4">

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Customer <span class="text-xs text-gray-400 font-normal ml-1">(optional — leave blank for walk-in)</span>
                </label>
                <div class="customer-picker" style="position:relative">
                    <input type="text"
                           id="customer-search"
                           placeholder="Search by name or phone… (leave blank for walk-in)"
                           autocomplete="off"
                           oninput="searchCustomer(this)"
                           onkeydown="handleCustomerKey(event, this)"
                           class="w-full h-10 px-3 rounded-lg border bg-white
                                  text-sm text-gray-800 focus:outline-none
                                  focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('customer_id') ? 'border-red-400' : 'border-gray-300' }}"
                           style="box-sizing:border-box">
                    <input type="hidden" name="customer_id" id="customer-id-input"
                           value="{{ old('customer_id') }}">
                    <div id="customer-dropdown"
                         style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;
                                background:#fff;border:1px solid #e5e7eb;border-radius:8px;z-index:50;
                                max-height:240px;overflow-y:auto;
                                box-shadow:0 8px 24px rgba(0,0,0,.08)">
                    </div>
                </div>
                @error('customer_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div id="walkin-section"@if(old('customer_id')) style="display:none"@endif>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Walk-in name
                    <span class="text-xs text-gray-400 font-normal ml-1">(optional)</span>
                </label>
                <input type="text" name="walkin_name" value="{{ old('walkin_name') }}"
                       placeholder="Recipient's name…"
                       class="w-full h-10 px-3 rounded-lg border bg-white text-sm text-gray-800
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              {{ $errors->has('walkin_name') ? 'border-red-400' : 'border-gray-300' }}">
                @error('walkin_name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    {{-- Notes --}}
    <div class="px-6 pb-6 border-t border-gray-100 pt-5">
        <label class="block text-sm font-medium text-gray-600 mb-1.5">
            Notes <span class="text-xs text-gray-400 font-normal ml-1">(optional)</span>
        </label>
        <textarea name="notes" rows="2"
                  placeholder="Any notes about this consignment…"
                  class="w-full px-3 py-2.5 rounded-lg border border-gray-300 bg-white
                         text-sm text-gray-800 resize-none
                         focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
    </div>

    {{-- Items --}}
    <div class="p-6 border-t border-gray-100">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">
                    Products
                </p>
                @error('items')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <button type="button" onclick="addConsignmentItem()"
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
        <div style="display:grid;grid-template-columns:1fr 120px 140px 40px;gap:12px;
                    margin-bottom:8px;padding:0 4px">
            <p class="text-xs font-medium text-gray-500">Product</p>
            <p class="text-xs font-medium text-gray-500">Quantity</p>
            <p class="text-xs font-medium text-gray-500">Unit Price (GHS)</p>
            <p></p>
        </div>

        <div id="items-container" class="space-y-3">
            {{-- First blank row --}}
            <div class="item-row"
                 style="display:grid;grid-template-columns:1fr 120px 140px 40px;gap:12px;align-items:center">
                <div class="product-picker" style="position:relative">
                    <input type="text"
                           class="product-search-input"
                           placeholder="Search product…"
                           autocomplete="off"
                           oninput="searchProduct(this)"
                           onkeydown="handleProductKey(event, this)"
                           style="width:100%;height:40px;padding:0 12px;border:1px solid #d1d5db;
                                  border-radius:8px;font-size:14px;color:#111827;outline:none;
                                  box-sizing:border-box">
                    <input type="hidden" name="items[0][product_id]" class="product-id-input">
                    <div class="product-dropdown"
                         style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;
                                background:#fff;border:1px solid #e5e7eb;border-radius:8px;z-index:50;
                                max-height:240px;overflow-y:auto;
                                box-shadow:0 8px 24px rgba(0,0,0,.08)">
                    </div>
                </div>
                <input type="number" name="items[0][quantity]"
                       min="0.01" step="0.01" placeholder="0"
                       oninput="updateTotal()"
                       class="w-full h-10 px-3 rounded-lg border border-gray-300
                              bg-white text-sm text-gray-800 focus:outline-none
                              focus:ring-2 focus:ring-blue-500">
                <input type="number" name="items[0][unit_price]"
                       min="0" step="0.01" placeholder="0.00"
                       oninput="updateTotal()"
                       class="w-full h-10 px-3 rounded-lg border border-gray-300
                              bg-white text-sm text-gray-800 focus:outline-none
                              focus:ring-2 focus:ring-blue-500">
                <button type="button" onclick="removeConsignmentItem(this)"
                        class="h-10 w-10 flex items-center justify-center text-gray-400
                               hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                        style="border:none;background:transparent;cursor:pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Running total --}}
        <div style="display:flex;justify-content:flex-end;margin-top:16px">
            <div class="bg-gray-50 border border-gray-200 rounded-lg px-5 py-3"
                 style="min-width:200px;text-align:right">
                <p class="text-xs text-gray-500 mb-0.5">Total consignment value</p>
                <p id="running-total" class="text-lg font-semibold text-gray-800">GHS 0.00</p>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
         style="display:flex;align-items:center;gap:12px">
        <button type="submit" form="main-form"
                class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors focus:outline-none
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Create consignment
        </button>
        <a href="{{ route('consignments.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                  font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>
    </div>

</div>

@push('scripts')
<script>
    @php
        $productsData  = $products->map(fn($p) => [
            'id'            => $p->id,
            'name'          => $p->name,
            'sku'           => $p->sku ?? '',
            'selling_price' => (float) $p->selling_price,
        ]);
        $customersData = $customers->map(fn($c) => [
            'id'    => $c->id,
            'name'  => $c->name,
            'phone' => $c->phone ?? '',
        ]);
        $oldCustomerId   = old('customer_id');
        $oldCustomerText = '';
        if ($oldCustomerId) {
            $oc = $customers->firstWhere('id', $oldCustomerId);
            if ($oc) {
                $oldCustomerText = $oc->name . ($oc->phone ? ' — ' . $oc->phone : '');
            }
        }
    @endphp

    // ── Data ──────────────────────────────────────────────────────
    const allProducts  = @json($productsData);
    const allCustomers = @json($customersData);

    let consignmentItemCount = 1;

    function escHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Pre-fill customer text when returning with old input after validation error
    @if($oldCustomerText)
        document.getElementById('customer-search').value = @json($oldCustomerText);
    @endif

    // ── Customer picker ───────────────────────────────────────────
    function searchCustomer(input) {
        const dropdown = document.getElementById('customer-dropdown');
        const q        = input.value.toLowerCase().trim();

        document.getElementById('customer-id-input').value    = '';
        document.getElementById('walkin-section').style.display = '';

        if (!q) { dropdown.style.display = 'none'; return; }

        const results = allCustomers.filter(c =>
            c.name.toLowerCase().includes(q) || (c.phone && c.phone.includes(q))
        ).slice(0, 15);

        dropdown.innerHTML = results.length
            ? results.map(c => `
                <div class="customer-result" data-id="${c.id}"
                     onclick="selectCustomerById(this.dataset.id)"
                     style="padding:8px 14px;cursor:pointer;font-size:13px;
                            border-bottom:1px solid #f3f4f6">
                    <span style="font-weight:500;color:#111827">${escHtml(c.name)}</span>
                    ${c.phone ? `<span style="font-size:11px;color:#9ca3af;margin-left:6px">${escHtml(c.phone)}</span>` : ''}
                </div>`).join('')
            : '<div style="padding:10px 14px;font-size:13px;color:#9ca3af">No customers found</div>';

        dropdown.style.display = 'block';
    }

    function selectCustomerById(id) {
        const c = allCustomers.find(c => c.id == id);
        if (!c) return;
        document.getElementById('customer-search').value          = c.name + (c.phone ? ' — ' + c.phone : '');
        document.getElementById('customer-id-input').value        = c.id;
        document.getElementById('customer-dropdown').style.display = 'none';
        document.getElementById('walkin-section').style.display   = 'none';
    }

    function handleCustomerKey(e, input) {
        const dropdown = document.getElementById('customer-dropdown');
        let active     = dropdown.querySelector('.customer-result.active');

        if (e.key === 'Escape') { dropdown.style.display = 'none'; return; }
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (active) { active.classList.remove('active'); active.style.background = ''; }
            const next = active ? active.nextElementSibling : dropdown.querySelector('.customer-result');
            if (next) { next.classList.add('active'); next.style.background = '#eff6ff'; }
            return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (active) { active.classList.remove('active'); active.style.background = ''; }
            const prev = active?.previousElementSibling;
            if (prev) { prev.classList.add('active'); prev.style.background = '#eff6ff'; }
            return;
        }
        if (e.key === 'Enter' && active) { e.preventDefault(); active.click(); }
    }

    // ── Product picker ────────────────────────────────────────────
    function searchProduct(input) {
        const picker   = input.closest('.product-picker');
        const dropdown = picker.querySelector('.product-dropdown');
        const q        = input.value.toLowerCase().trim();

        picker.querySelector('.product-id-input').value = '';

        if (!q) { dropdown.style.display = 'none'; return; }

        const results = allProducts.filter(p =>
            p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q)
        ).slice(0, 20);

        dropdown.innerHTML = results.length
            ? results.map(p => `
                <div class="product-result" data-id="${p.id}"
                     onclick="selectProductById(this.closest('.product-picker'), this.dataset.id)"
                     style="padding:8px 14px;cursor:pointer;font-size:13px;
                            border-bottom:1px solid #f3f4f6">
                    <span style="font-weight:500;color:#111827">${escHtml(p.name)}</span>
                    <span style="font-size:11px;color:#9ca3af;margin-left:6px">${escHtml(p.sku)}</span>
                    <span style="float:right;font-size:12px;color:#6b7280">GHS ${p.selling_price.toFixed(2)}</span>
                </div>`).join('')
            : '<div style="padding:10px 14px;font-size:13px;color:#9ca3af">No products found</div>';

        dropdown.style.display = 'block';
    }

    function selectProductById(picker, id) {
        const p = allProducts.find(p => p.id == id);
        if (!p) return;

        picker.querySelector('.product-search-input').value     = p.name;
        picker.querySelector('.product-id-input').value         = p.id;
        picker.querySelector('.product-dropdown').style.display = 'none';

        const priceInput = picker.closest('.item-row').querySelector('input[name$="[unit_price]"]');
        if (priceInput && !priceInput.value) {
            priceInput.value = p.selling_price.toFixed(2);
        }
        updateTotal();
    }

    function handleProductKey(e, input) {
        const picker   = input.closest('.product-picker');
        const dropdown = picker.querySelector('.product-dropdown');
        let active     = dropdown.querySelector('.product-result.active');

        if (e.key === 'Escape') { dropdown.style.display = 'none'; return; }
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (active) { active.classList.remove('active'); active.style.background = ''; }
            const next = active ? active.nextElementSibling : dropdown.querySelector('.product-result');
            if (next) { next.classList.add('active'); next.style.background = '#eff6ff'; }
            return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (active) { active.classList.remove('active'); active.style.background = ''; }
            const prev = active?.previousElementSibling;
            if (prev) { prev.classList.add('active'); prev.style.background = '#eff6ff'; }
            return;
        }
        if (e.key === 'Enter' && active) { e.preventDefault(); active.click(); }
    }

    // ── Click outside closes all dropdowns ────────────────────────
    document.addEventListener('click', e => {
        if (!e.target.closest('.product-picker')) {
            document.querySelectorAll('.product-dropdown').forEach(d => d.style.display = 'none');
        }
        if (!e.target.closest('.customer-picker')) {
            document.getElementById('customer-dropdown').style.display = 'none';
        }
    });

    // ── Add / remove item rows ────────────────────────────────────
    function addConsignmentItem() {
        const container = document.getElementById('items-container');
        const div       = document.createElement('div');
        div.className   = 'item-row';
        div.style.cssText = 'display:grid;grid-template-columns:1fr 120px 140px 40px;gap:12px;align-items:center';

        div.innerHTML = `
            <div class="product-picker" style="position:relative">
                <input type="text"
                       class="product-search-input"
                       placeholder="Search product…"
                       autocomplete="off"
                       oninput="searchProduct(this)"
                       onkeydown="handleProductKey(event, this)"
                       style="width:100%;height:40px;padding:0 12px;border:1px solid #d1d5db;
                              border-radius:8px;font-size:14px;color:#111827;outline:none;
                              box-sizing:border-box">
                <input type="hidden" name="items[${consignmentItemCount}][product_id]" class="product-id-input">
                <div class="product-dropdown"
                     style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;
                            background:#fff;border:1px solid #e5e7eb;border-radius:8px;z-index:50;
                            max-height:240px;overflow-y:auto;
                            box-shadow:0 8px 24px rgba(0,0,0,.08)">
                </div>
            </div>
            <input type="number" name="items[${consignmentItemCount}][quantity]"
                   min="0.01" step="0.01" placeholder="0"
                   oninput="updateTotal()"
                   style="width:100%;height:40px;padding:0 12px;border:1px solid #d1d5db;
                          border-radius:8px;font-size:14px;color:#111827;outline:none">
            <input type="number" name="items[${consignmentItemCount}][unit_price]"
                   min="0" step="0.01" placeholder="0.00"
                   oninput="updateTotal()"
                   style="width:100%;height:40px;padding:0 12px;border:1px solid #d1d5db;
                          border-radius:8px;font-size:14px;color:#111827;outline:none">
            <button type="button" onclick="removeConsignmentItem(this)"
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
        consignmentItemCount++;
    }

    function removeConsignmentItem(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length === 1) {
            alert('At least one product is required.');
            return;
        }
        btn.closest('.item-row').remove();
        updateTotal();
    }

    function validateConsignmentForm(e) {
        // Remove rows where no product was selected
        document.querySelectorAll('.item-row').forEach(row => {
            if (!row.querySelector('.product-id-input').value) {
                row.remove();
            }
        });

        // Must have at least one valid item left
        if (!document.querySelectorAll('.item-row').length) {
            e.preventDefault();
            alert('Please select at least one product before submitting.');
            return false;
        }
        return true;
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty   = parseFloat(row.querySelector('input[name$="[quantity]"]')?.value) || 0;
            const price = parseFloat(row.querySelector('input[name$="[unit_price]"]')?.value) || 0;
            total += qty * price;
        });
        document.getElementById('running-total').textContent =
            'GHS ' + total.toLocaleString('en-GH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
</script>
@endpush
