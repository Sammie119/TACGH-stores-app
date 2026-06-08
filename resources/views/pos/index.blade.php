{{-- resources/views/pos/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Point of Sale')
@section('header', 'Point of Sale')
@section('subheader', $user->branch->name ?? 'POS Terminal')

@section('content')

    {{-- Main POS grid --}}
    <div style="display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start">

        {{-- ── LEFT: Search + Items ── --}}
        <div style="display:flex;flex-direction:column;gap:16px">

            {{-- Product search --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">
                    Search products
                </p>
                <div style="position:relative">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);
                             color:#9ca3af;pointer-events:none;display:flex">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                    <input type="text"
                           id="product-search"
                           placeholder="Type product name, SKU or scan barcode…"
                           autocomplete="off"
                           oninput="searchProducts(this.value)"
                           onkeydown="handleSearchKey(event)"
                           style="width:100%;height:44px;padding:0 14px 0 40px;
                              border:1px solid #d1d5db;border-radius:10px;
                              font-size:14px;color:#111827;outline:none;
                              box-sizing:border-box">

                    {{-- Dropdown --}}
                    <div id="search-results"
                         style="display:none;position:absolute;top:calc(100% + 4px);
                            left:0;right:0;background:#fff;border:1px solid #e5e7eb;
                            border-radius:10px;z-index:50;max-height:320px;
                            overflow-y:auto;box-shadow:0 8px 24px rgba(0,0,0,.08)">
                    </div>
                </div>
                <p style="font-size:12px;color:#9ca3af;margin-top:8px">
                    Press
                    <kbd style="background:#f3f4f6;padding:1px 6px;border-radius:4px;
                            font-size:11px;border:1px solid #d1d5db">Enter</kbd>
                    or click a result to add to cart
                </p>
            </div>

            {{-- Cart / sale items --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;
                        display:flex;align-items:center;justify-content:space-between">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">
                        Sale items
                    </p>
                    <div style="display:flex;align-items:center;gap:14px">
                        <span id="cart-count" style="font-size:12px;color:#9ca3af">0 items</span>
                        <button onclick="clearCart()"
                                style="font-size:12px;color:#ef4444;background:none;
                                   border:none;cursor:pointer;font-weight:500">
                            Clear all
                        </button>
                    </div>
                </div>

                {{-- Empty state --}}
                <div id="cart-empty" style="padding:56px 20px;text-align:center">
                    <svg width="44" height="44" fill="none" stroke="#e5e7eb"
                         viewBox="0 0 24 24" style="margin:0 auto 12px">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p style="font-size:13px;color:#d1d5db">
                        Search for a product above to start
                    </p>
                </div>

                {{-- Cart table --}}
                <div id="cart-table" style="display:none">
                    <table style="width:100%;border-collapse:collapse;font-size:13px">
                        <thead>
                        <tr style="background:#f9fafb;border-bottom:1px solid #f3f4f6">
                            <th style="padding:10px 20px;text-align:left;font-size:11px;
                                       font-weight:600;color:#9ca3af;text-transform:uppercase;
                                       letter-spacing:.05em">Product</th>
                            <th style="padding:10px 20px;text-align:center;font-size:11px;
                                       font-weight:600;color:#9ca3af;text-transform:uppercase;
                                       letter-spacing:.05em;width:110px">Qty</th>
                            <th style="padding:10px 20px;text-align:right;font-size:11px;
                                       font-weight:600;color:#9ca3af;text-transform:uppercase;
                                       letter-spacing:.05em;width:100px">Price</th>
                            <th style="padding:10px 20px;text-align:right;font-size:11px;
                                       font-weight:600;color:#9ca3af;text-transform:uppercase;
                                       letter-spacing:.05em;width:90px">Disc</th>
                            <th style="padding:10px 20px;text-align:right;font-size:11px;
                                       font-weight:600;color:#9ca3af;text-transform:uppercase;
                                       letter-spacing:.05em;width:110px">Subtotal</th>
                            <th style="width:44px"></th>
                        </tr>
                        </thead>
                        <tbody id="cart-body"></tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- ── RIGHT: Customer + Payment + Checkout ── --}}
        <div style="display:flex;flex-direction:column;gap:16px">

            {{-- Customer --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">
                    Customer
                </p>
                <div style="position:relative">
                <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);
                             color:#9ca3af;pointer-events:none;display:flex">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                    <select id="customer-select"
                            style="width:100%;height:38px;padding:0 32px 0 34px;
                               border:1px solid #d1d5db;border-radius:8px;font-size:13px;
                               color:#111827;background:#fff;outline:none;appearance:none;
                               box-sizing:border-box">
                        <option value="">— Walk-in customer —</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->name }}
                                @if($customer->balance > 0)
                                    (owes GHS {{ number_format($customer->balance, 2) }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                             color:#9ca3af;pointer-events:none;display:flex">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
                </div>
            </div>

            {{-- Payment method --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">
                    Payment method
                </p>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:20px">
                    @foreach(['cash' => 'Cash', 'momo' => 'MoMo', 'bank' => 'Bank',
                              'credit' => 'Credit', 'split' => 'Split'] as $val => $label)
                        <div id="pm-{{ $val }}"
                             onclick="selectPayment('{{ $val }}')"
                             style="padding:9px 4px;border:1px solid {{ $val === 'cash' ? '#2563eb' : '#e5e7eb' }};
                            border-radius:8px;text-align:center;cursor:pointer;
                            background:{{ $val === 'cash' ? '#eff6ff' : '#fff' }};
                            transition:all .15s">
                    <span id="pm-label-{{ $val }}"
                          style="font-size:12px;font-weight:500;
                                 color:{{ $val === 'cash' ? '#2563eb' : '#374151' }}">
                        {{ $label }}
                    </span>
                        </div>
                    @endforeach
                </div>

                {{-- Totals box --}}
                <div style="background:#f9fafb;border-radius:10px;padding:14px;margin-bottom:14px">
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                        <span style="font-size:12px;color:#6b7280">Subtotal</span>
                        <span style="font-size:12px;color:#374151" id="subtotal-display">
                        GHS 0.00
                    </span>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;
                            margin-bottom:8px">
                        <span style="font-size:12px;color:#6b7280">Discount (GHS)</span>
                        <input type="number" id="overall-discount"
                               value="0" min="0" step="0.01"
                               oninput="updateTotals()"
                               style="width:90px;height:28px;padding:0 8px;
                                  border:1px solid #d1d5db;border-radius:6px;
                                  font-size:12px;text-align:right;outline:none">
                    </div>
                    <div style="display:flex;justify-content:space-between;
                            border-top:1px solid #e5e7eb;padding-top:10px;margin-top:4px">
                        <span style="font-size:15px;font-weight:600;color:#111827">Total</span>
                        <span style="font-size:20px;font-weight:700;color:#2563eb"
                              id="total-display">GHS 0.00</span>
                    </div>
                </div>

                {{-- Amount paid --}}
                <div style="margin-bottom:14px">
                    <div style="display:flex;justify-content:space-between;
                            align-items:center;margin-bottom:6px">
                        <label style="font-size:12px;color:#6b7280;font-weight:500">
                            Amount paid (GHS)
                        </label>
                        <span id="change-display"
                              style="font-size:12px;font-weight:500;color:#9ca3af">
                        Change: GHS 0.00
                    </span>
                    </div>
                    <input type="number" id="amount-paid"
                           value="0" min="0" step="0.01"
                           oninput="updateChange()"
                           style="width:100%;height:48px;padding:0 14px;
                              border:1px solid #d1d5db;border-radius:8px;
                              font-size:20px;font-weight:700;text-align:right;
                              color:#111827;outline:none;box-sizing:border-box">
                </div>

                {{-- Notes --}}
                <div style="margin-bottom:16px">
                    <label style="font-size:12px;color:#6b7280;display:block;margin-bottom:5px">
                        Notes (optional)
                    </label>
                    <input type="text" id="sale-notes"
                           placeholder="Any sale notes…"
                           style="width:100%;height:36px;padding:0 10px;
                              border:1px solid #d1d5db;border-radius:8px;
                              font-size:13px;color:#111827;outline:none;
                              box-sizing:border-box">
                </div>

                {{-- Process button --}}
                <button onclick="processSale()"
                        id="process-btn"
                        style="width:100%;height:50px;background:#2563eb;color:#fff;
                           border:none;border-radius:10px;font-size:15px;font-weight:600;
                           cursor:pointer;transition:background .15s"
                        onmouseover="this.style.background='#1d4ed8'"
                        onmouseout="this.style.background='#2563eb'">
                    Process sale — GHS 0.00
                </button>

            </div>

        </div>
    </div>

    {{-- Hidden form --}}
    <form id="sale-form" method="POST"
          action="{{ route('pos.process') }}" style="display:none">
        @csrf
        <input type="hidden" name="payment_method" id="form-payment">
        <input type="hidden" name="amount_paid"    id="form-paid">
        <input type="hidden" name="customer_id"    id="form-customer">
        <input type="hidden" name="discount"       id="form-discount">
        <input type="hidden" name="notes"          id="form-notes">
        <div id="form-items"></div>
    </form>

    @push('scripts')
        <script>
            const allProducts = @json($products);
            let cart           = [];
            let selectedPayment = 'cash';
            let searchIndex    = -1;

            // ── Search ────────────────────────────────────────────────────────
            function searchProducts(query) {
                const box = document.getElementById('search-results');
                if (!query || query.length < 1) {
                    box.style.display = 'none';
                    searchIndex = -1;
                    return;
                }

                const q = query.toLowerCase();
                const matches = allProducts.filter(p =>
                    p.name.toLowerCase().includes(q) ||
                    p.sku?.toLowerCase().includes(q)
                ).slice(0, 8);

                if (matches.length === 0) {
                    box.innerHTML = `
            <div style="padding:16px;text-align:center;color:#9ca3af;font-size:13px">
                No products found
            </div>`;
                    box.style.display = 'block';
                    return;
                }

                box.innerHTML = matches.map((p, i) => `
        <div class="search-result-item"
             data-index="${i}"
             onclick="addToCartById(${p.id})"
             style="padding:10px 14px;display:flex;align-items:center;
                    justify-content:space-between;cursor:pointer;
                    border-bottom:1px solid #f9fafb;transition:background .1s"
             onmouseover="this.style.background='#f0f9ff'"
             onmouseout="this.style.background='#fff'">
            <div>
                <p style="font-size:13px;font-weight:500;color:#111827">${p.name}</p>
                <p style="font-size:11px;color:#9ca3af">
                    ${p.sku} · ${p.category ?? ''}
                    · <span style="color:${p.stock <= 0 ? '#ef4444' : p.stock <= 5 ? '#f59e0b' : '#6b7280'}">
                        ${p.stock} ${p.unit} in stock
                    </span>
                </p>
            </div>
            <div style="text-align:right;flex-shrink:0;margin-left:12px">
                <p style="font-size:13px;font-weight:600;color:#2563eb">
                    GHS ${parseFloat(p.selling_price).toFixed(2)}
                </p>
                ${p.stock <= 0
                    ? '<span style="font-size:10px;background:#fee2e2;color:#991b1b;padding:1px 6px;border-radius:20px">Out of stock</span>'
                    : p.stock <= 5
                        ? '<span style="font-size:10px;background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:20px">Low stock</span>'
                        : '<span style="font-size:10px;background:#dcfce7;color:#166534;padding:1px 6px;border-radius:20px">In stock</span>'
                }
            </div>
        </div>
    `).join('');

                box.style.display = 'block';
                searchIndex = -1;
            }

            function handleSearchKey(e) {
                const box   = document.getElementById('search-results');
                const items = box.querySelectorAll('.search-result-item');

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    searchIndex = Math.min(searchIndex + 1, items.length - 1);
                    highlightResult(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    searchIndex = Math.max(searchIndex - 1, -1);
                    highlightResult(items);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (searchIndex >= 0 && items[searchIndex]) {
                        items[searchIndex].click();
                    } else if (items.length === 1) {
                        items[0].click();
                    }
                } else if (e.key === 'Escape') {
                    box.style.display = 'none';
                    searchIndex = -1;
                }
            }

            function highlightResult(items) {
                items.forEach((item, i) => {
                    item.style.background = i === searchIndex ? '#eff6ff' : '#fff';
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const search = document.getElementById('product-search');
                const box    = document.getElementById('search-results');
                if (!search.contains(e.target) && !box.contains(e.target)) {
                    box.style.display = 'none';
                }
            });

            // ── Cart operations ───────────────────────────────────────────────
            function addToCartById(id) {
                const product = allProducts.find(p => p.id === id);
                if (!product) return;

                if (product.stock <= 0) {
                    alert(`${product.name} is out of stock.`);
                    return;
                }

                const existing = cart.find(i => i.id === id);
                if (existing) {
                    if (existing.qty + 1 > product.stock) {
                        alert(`Only ${product.stock} ${product.unit} available.`);
                        return;
                    }
                    existing.qty += 1;
                } else {
                    cart.push({
                        id       : product.id,
                        name     : product.name,
                        price    : parseFloat(product.selling_price),
                        unit     : product.unit,
                        stock    : parseFloat(product.stock),
                        qty      : 1,
                        discount : 0,
                    });
                }

                // Clear search
                document.getElementById('product-search').value = '';
                document.getElementById('search-results').style.display = 'none';
                searchIndex = -1;

                renderCart();
                updateTotals();

                // Focus back on search
                document.getElementById('product-search').focus();
            }

            function updateQty(id, val) {
                const item = cart.find(i => i.id === id);
                if (!item) return;
                const qty = parseFloat(val);
                if (isNaN(qty) || qty <= 0) {
                    removeFromCart(id);
                    return;
                }
                if (qty > item.stock) {
                    alert(`Only ${item.stock} ${item.unit} available.`);
                    const input = document.querySelector(`input[data-qty="${id}"]`);
                    if (input) input.value = item.qty;
                    return;
                }
                item.qty = qty;
                renderCart();      // ← add this
                updateTotals();
            }

            function updateDiscount(id, val) {
                const item = cart.find(i => i.id === id);
                if (!item) return;
                item.discount = parseFloat(val) || 0;
                renderCart();      // ← add this
                updateTotals();
            }

            function removeFromCart(id) {
                cart = cart.filter(i => i.id !== id);
                renderCart();
                updateTotals();
            }

            function clearCart() {
                if (cart.length && !confirm('Clear all items?')) return;
                cart = [];
                renderCart();
                updateTotals();
            }

            function renderCart() {
                const empty = document.getElementById('cart-empty');
                const table = document.getElementById('cart-table');
                const body  = document.getElementById('cart-body');
                const count = document.getElementById('cart-count');

                if (cart.length === 0) {
                    empty.style.display = 'block';
                    table.style.display = 'none';
                    count.textContent   = '0 items';
                    return;
                }

                empty.style.display = 'none';
                table.style.display = 'block';
                count.textContent   = `${cart.length} item${cart.length !== 1 ? 's' : ''}`;

                body.innerHTML = cart.map(item => {
                    const subtotal = (item.price * item.qty) - item.discount;
                    return `
        <tr style="border-bottom:1px solid #f9fafb">
            <td style="padding:12px 20px">
                <p style="font-size:13px;font-weight:500;color:#111827">${item.name}</p>
                <p style="font-size:11px;color:#9ca3af">
                    GHS ${item.price.toFixed(2)} / ${item.unit}
                </p>
            </td>
            <td style="padding:12px 20px;text-align:center">
                <input type="number"
                       data-qty="${item.id}"
                       value="${item.qty}"
                       min="0.01" step="0.01"
                       onchange="updateQty(${item.id}, this.value)"
                       style="width:80px;height:32px;text-align:center;
                              border:1px solid #d1d5db;border-radius:6px;
                              font-size:13px;outline:none;padding:0 4px">
            </td>
            <td style="padding:12px 20px;text-align:right;font-size:13px;color:#6b7280">
                ${item.price.toFixed(2)}
            </td>
            <td style="padding:12px 20px;text-align:right">
                <input type="number"
                       value="${item.discount}"
                       min="0" step="0.01"
                       onchange="updateDiscount(${item.id}, this.value)"
                       style="width:70px;height:32px;text-align:right;
                              border:1px solid #d1d5db;border-radius:6px;
                              font-size:12px;outline:none;padding:0 6px">
            </td>
            <td style="padding:12px 20px;text-align:right;font-size:13px;
                       font-weight:600;color:#111827">
                ${subtotal.toFixed(2)}
            </td>
            <td style="padding:12px 16px;text-align:right">
                <button onclick="removeFromCart(${item.id})"
                        style="background:none;border:none;cursor:pointer;
                               color:#d1d5db;font-size:18px;line-height:1"
                        onmouseover="this.style.color='#ef4444'"
                        onmouseout="this.style.color='#d1d5db'">×</button>
            </td>
        </tr>
        `;
                }).join('');
            }

            // ── Totals ────────────────────────────────────────────────────────
            function updateTotals() {
                const subtotal        = cart.reduce((s, i) => s + (i.price * i.qty) - i.discount, 0);
                const overallDiscount = parseFloat(document.getElementById('overall-discount').value) || 0;
                const total           = Math.max(0, subtotal - overallDiscount);

                document.getElementById('subtotal-display').textContent = `GHS ${subtotal.toFixed(2)}`;
                document.getElementById('total-display').textContent    = `GHS ${total.toFixed(2)}`;
                document.getElementById('amount-paid').value            = total.toFixed(2);

                // Update process button
                document.getElementById('process-btn').textContent =
                    `Process sale — GHS ${total.toFixed(2)}`;

                updateChange();
            }

            function updateChange() {
                const total  = parseFloat(
                    document.getElementById('total-display').textContent.replace('GHS ', '')) || 0;
                const paid   = parseFloat(document.getElementById('amount-paid').value) || 0;
                const change = paid - total;
                const el     = document.getElementById('change-display');

                if (change >= 0) {
                    el.textContent = `Change: GHS ${change.toFixed(2)}`;
                    el.style.color = '#16a34a';
                } else {
                    el.textContent = `Balance due: GHS ${Math.abs(change).toFixed(2)}`;
                    el.style.color = '#dc2626';
                }
            }

            // ── Payment method ────────────────────────────────────────────────
            function selectPayment(method) {
                selectedPayment = method;
                ['cash', 'momo', 'bank', 'credit', 'split'].forEach(m => {
                    const label = document.getElementById(`pm-${m}`);
                    const span  = document.getElementById(`pm-label-${m}`);
                    if (!label) return;
                    label.style.borderColor = m === method ? '#2563eb' : '#e5e7eb';
                    label.style.background  = m === method ? '#eff6ff' : '#fff';
                    span.style.color        = m === method ? '#2563eb' : '#374151';
                });
            }

            // ── Process sale ──────────────────────────────────────────────────
            function processSale() {
                if (cart.length === 0) {
                    alert('Add at least one product to proceed.');
                    document.getElementById('product-search').focus();
                    return;
                }

                const total = parseFloat(
                    document.getElementById('total-display').textContent.replace('GHS ', '')) || 0;
                const paid  = parseFloat(document.getElementById('amount-paid').value) || 0;

                if (paid < total && selectedPayment !== 'credit') {
                    const diff = (total - paid).toFixed(2);
                    if (!confirm(`Amount paid is GHS ${diff} short of total. Record as partial payment?`)) {
                        return;
                    }
                }

                document.getElementById('form-payment').value  = selectedPayment;
                document.getElementById('form-paid').value     = paid;
                document.getElementById('form-customer').value = document.getElementById('customer-select').value;
                document.getElementById('form-discount').value = document.getElementById('overall-discount').value || 0;
                document.getElementById('form-notes').value    = document.getElementById('sale-notes').value;

                const itemsDiv = document.getElementById('form-items');
                itemsDiv.innerHTML = '';
                cart.forEach((item, i) => {
                    itemsDiv.innerHTML += `
            <input type="hidden" name="items[${i}][product_id]" value="${item.id}">
            <input type="hidden" name="items[${i}][quantity]"   value="${item.qty}">
            <input type="hidden" name="items[${i}][unit_price]" value="${item.price}">
            <input type="hidden" name="items[${i}][discount]"   value="${item.discount}">
        `;
                });

                const btn = document.getElementById('process-btn');
                btn.disabled      = true;
                btn.textContent   = 'Processing…';
                btn.style.background = '#93c5fd';

                document.getElementById('sale-form').submit();
            }

            // Focus search on load
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('product-search').focus();
                selectPayment('cash');
            });
        </script>
    @endpush

@endsection
