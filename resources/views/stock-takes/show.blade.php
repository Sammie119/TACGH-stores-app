{{-- resources/views/stock-takes/show.blade.php --}}
@extends('layouts.app')
@section('title', $stockTake->reference)
@section('header', 'Stock Take')
@section('subheader', $stockTake->reference . ' — ' . $stockTake->branch?->name)

@section('content')

    {{-- Progress bar + summary --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;
                gap:20px;margin-bottom:20px">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                    Status
                </p>
                @php
                    $sc = [
                        'draft'            => 'background:#f3f4f6;color:#374151',
                        'in_progress'      => 'background:#dbeafe;color:#1e40af',
                        'pending_approval' => 'background:#fef3c7;color:#92400e',
                        'approved'         => 'background:#dcfce7;color:#166534',
                        'cancelled'        => 'background:#fee2e2;color:#991b1b',
                    ][$stockTake->status] ?? 'background:#f3f4f6;color:#374151';
                @endphp
                <span style="padding:3px 10px;border-radius:20px;font-size:12px;
                         font-weight:500;{{ $sc }};display:inline-block;margin-top:4px">
                {{ ucfirst(str_replace('_', ' ', $stockTake->status)) }}
            </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                    Counted
                </p>
                <p class="text-xl font-semibold text-gray-800 mt-1" id="counted-display">
                    {{ $counted }}/{{ $total }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                    Variances
                </p>
                <p class="text-xl font-semibold mt-1
                      {{ $stockTake->variance_items > 0 ? 'text-amber-600' : 'text-gray-800' }}"
                   id="variance-count">
                    {{ $stockTake->variance_items }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                    Started
                </p>
                <p class="text-sm text-gray-700 mt-1">
                    {{ $stockTake->started_at?->format('d M Y H:i') ?? '—' }}
                </p>
            </div>
        </div>

        {{-- Progress bar --}}
        <div>
            <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                <span class="text-xs text-gray-500">Progress</span>
                <span class="text-xs font-semibold text-gray-700"
                      id="progress-text">{{ $progress }}%</span>
            </div>
            <div style="height:8px;background:#e5e7eb;border-radius:4px;overflow:hidden">
                <div id="progress-bar"
                     style="height:100%;width:{{ $progress }}%;border-radius:4px;
                        background:{{ $progress === 100 ? '#16a34a' : '#2563eb' }};
                        transition:width .4s ease">
                </div>
            </div>
        </div>
    </div>

    {{-- Action buttons --}}
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap">

        @if($stockTake->status === 'in_progress')
            <button onclick="submitStockTake()"
                    class="h-9 px-4 bg-amber-500 hover:bg-amber-600 text-white text-sm
                       font-medium rounded-lg transition-colors"
                    style="border:none;cursor:pointer">
                Submit for approval
            </button>
            <form method="POST" id="submit-form"
                  action="{{ route('stock-takes.submit', $stockTake) }}"
                  style="display:none">
                @csrf @method('PATCH')
            </form>
        @endif

        @if($stockTake->status === 'pending_approval')
            <form method="POST"
                  action="{{ route('stock-takes.approve', $stockTake) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="h-9 px-4 bg-green-600 hover:bg-green-700 text-white
                           text-sm font-medium rounded-lg transition-colors"
                        style="border:none;cursor:pointer">
                    ✓ Approve & adjust stock
                </button>
            </form>
        @endif

        @if(!in_array($stockTake->status, ['approved','cancelled']))
            <form method="POST"
                  action="{{ route('stock-takes.cancel', $stockTake) }}"
                  onsubmit="return confirm('Cancel this stock take?')">
                @csrf @method('PATCH')
                <button type="submit"
                        class="h-9 px-4 bg-white border border-red-300 text-red-600
                           text-sm font-medium rounded-lg hover:bg-red-600
                           hover:text-white transition-colors"
                        style="cursor:pointer">
                    Cancel
                </button>
            </form>
        @endif

        {{-- Print PDF --}}
        @if(in_array($stockTake->status, ['pending_approval','approved']))
            <a href="{{ route('pdf.stock-take', $stockTake) }}"
               target="_blank"
               class="h-9 px-4 bg-red-600 hover:bg-red-700 text-white text-sm
                font-medium rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;border:none;cursor:pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download PDF
            </a>
        @endif

        <a href="{{ route('stock-takes.index') }}"
           class="h-9 px-4 bg-white border border-gray-300 text-gray-600
              text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors"
           style="display:inline-flex;align-items:center">
            ← Back
        </a>

        {{-- Search filter --}}
        <div class="relative" style="margin-left:auto">
        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);
                     color:#9ca3af;pointer-events:none;display:flex">
            <svg width="15" height="15" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </span>
            <input type="text"
                   id="search-items"
                   placeholder="Filter products…"
                   oninput="filterItems(this.value)"
                   style="height:36px;padding:0 12px 0 34px;border:1px solid #d1d5db;
                      border-radius:8px;font-size:13px;color:#111827;outline:none;
                      width:200px">
        </div>
    </div>

    {{-- Items table --}}
    @if($stockTake->status === 'in_progress')
        {{-- Counting mode — show each item with input --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm" id="items-table"
                   style="border-collapse:collapse;min-width:600px">
                <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Product</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Category</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">System qty</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Counted qty</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Variance</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    @php
                        $hasCounted = $item->counted_quantity !== null;
                        $variance   = $hasCounted ? $item->variance : null;
                    @endphp
                    <tr class="item-row border-b border-gray-50"
                        id="row-{{ $item->id }}"
                        data-name="{{ strtolower($item->product?->name) }}"
                        data-sku="{{ strtolower($item->product?->sku) }}"
                        style="{{ $hasCounted ? 'background:#f9fafb' : '' }}">

                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">
                                {{ $item->product?->name }}
                            </p>
                            <p class="text-xs text-gray-400 font-mono">
                                {{ $item->product?->sku }}
                            </p>
                        </td>

                        <td class="px-5 py-3 text-gray-600 text-xs">
                            {{ $item->product?->category?->name ?? '—' }}
                        </td>

                        <td class="px-5 py-3 text-right font-medium text-gray-700">
                            {{ number_format($item->system_quantity, 0) }}
                            <span class="text-xs text-gray-400">
                        {{ $item->product?->unit }}
                    </span>
                        </td>

                        <td class="px-5 py-3 text-center">
                            <div style="display:flex;align-items:center;
                                justify-content:center;gap:6px">
                                <input type="number"
                                       id="input-{{ $item->id }}"
                                       value="{{ $hasCounted ? $item->counted_quantity : '' }}"
                                       min="0" step="0.01"
                                       placeholder="0"
                                       style="width:80px;height:34px;text-align:center;
                                      border:1px solid {{ $hasCounted ? '#16a34a' : '#d1d5db' }};
                                      border-radius:6px;font-size:13px;
                                      font-weight:500;outline:none;padding:0 6px;
                                      background:{{ $hasCounted ? '#f0fdf4' : '#fff' }}"
                                       onfocus="this.style.borderColor='#2563eb';
                                        this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
                                       onblur="saveCount({{ $item->id }}, this.value)">
                                <button onclick="saveCount({{ $item->id }},
                                    document.getElementById('input-{{ $item->id }}').value)"
                                        style="width:30px;height:34px;border:1px solid #d1d5db;
                                       border-radius:6px;background:#fff;cursor:pointer;
                                       color:#6b7280;font-size:16px;line-height:1;
                                       display:flex;align-items:center;justify-content:center"
                                        onmouseover="this.style.background='#2563eb';
                                             this.style.color='#fff';
                                             this.style.borderColor='#2563eb'"
                                        onmouseout="this.style.background='#fff';
                                            this.style.color='#6b7280';
                                            this.style.borderColor='#d1d5db'"
                                        title="Save">
                                    ✓
                                </button>
                            </div>
                        </td>

                        <td class="px-5 py-3 text-right" id="variance-{{ $item->id }}">
                            @if($hasCounted && $variance !== null)
                                @if($variance > 0)
                                    <span style="color:#16a34a;font-weight:600">
                            +{{ number_format($variance, 0) }}
                        </span>
                                @elseif($variance < 0)
                                    <span style="color:#dc2626;font-weight:600">
                            {{ number_format($variance, 0) }}
                        </span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        <td class="px-5 py-3" id="status-{{ $item->id }}">
                            @if($hasCounted)
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#dcfce7;color:#166534">
                        Counted ✓
                    </span>
                            @else
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#fef3c7;color:#92400e">
                        Pending
                    </span>
                            @endif
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    @else
        {{-- View mode — read only with variances --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100"
                 style="display:flex;align-items:center;justify-content:space-between">
                <p class="font-semibold text-gray-700">Count results</p>
                @if($stockTake->status === 'approved')
                    <span class="text-xs text-gray-400">
            Approved by {{ $stockTake->approvedBy?->name }}
            on {{ $stockTake->updated_at->format('d M Y H:i') }}
        </span>
                @endif
            </div>
            <table class="w-full text-sm" id="items-table"
                   style="border-collapse:collapse;min-width:600px">
                <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Product</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Category</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">System qty</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Counted qty</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Variance</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Notes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    @php
                        $variance   = $item->variance;
                        $hasVariance = $variance !== null && $variance != 0;
                    @endphp
                    <tr class="item-row border-b border-gray-50 hover:bg-gray-50
                       {{ $hasVariance ? 'bg-amber-50' : '' }}"
                        id="row-{{ $item->id }}"
                        data-name="{{ strtolower($item->product?->name) }}"
                        data-sku="{{ strtolower($item->product?->sku) }}">

                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">
                                {{ $item->product?->name }}
                            </p>
                            <p class="text-xs text-gray-400 font-mono">
                                {{ $item->product?->sku }}
                            </p>
                        </td>

                        <td class="px-5 py-3 text-gray-600 text-xs">
                            {{ $item->product?->category?->name ?? '—' }}
                        </td>

                        <td class="px-5 py-3 text-right text-gray-700">
                            {{ number_format($item->system_quantity, 0) }}
                        </td>

                        <td class="px-5 py-3 text-right font-medium text-gray-800">
                            {{ $item->counted_quantity !== null
                                ? number_format($item->counted_quantity, 0)
                                : '—' }}
                        </td>

                        <td class="px-5 py-3 text-right font-semibold">
                            @if($variance === null)
                                <span class="text-gray-300">—</span>
                            @elseif($variance > 0)
                                <span style="color:#16a34a">+{{ number_format($variance, 0) }}</span>
                            @elseif($variance < 0)
                                <span style="color:#dc2626">{{ number_format($variance, 0) }}</span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>

                        <td class="px-5 py-3 text-xs text-gray-500">
                            {{ $item->notes ?? '—' }}
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @push('scripts')
        <script>
            const stockTakeId = {{ $stockTake->id }};
            const csrfToken   = document.querySelector('meta[name="csrf-token"]').content;
            let saveTimeout   = null;

            async function saveCount(itemId, value) {
                const qty = parseFloat(value);
                if (value === '' || isNaN(qty) || qty < 0) return;

                const input      = document.getElementById(`input-${itemId}`);
                const varianceEl = document.getElementById(`variance-${itemId}`);
                const statusEl   = document.getElementById(`status-${itemId}`);
                const row        = document.getElementById(`row-${itemId}`);

                // Visual feedback — saving
                input.style.borderColor  = '#f59e0b';
                input.style.background   = '#fefce8';
                input.style.boxShadow    = 'none';

                try {
                    const res = await fetch(`/stock-takes/${stockTakeId}/count`, {
                        method : 'POST',
                        headers: {
                            'Content-Type'    : 'application/json',
                            'X-CSRF-TOKEN'    : csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept'          : 'application/json',
                        },
                        body: JSON.stringify({
                            item_id          : itemId,
                            counted_quantity : qty,
                        }),
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        throw new Error(data.error || 'Save failed');
                    }

                    // Update input style — saved
                    input.style.borderColor = '#16a34a';
                    input.style.background  = '#f0fdf4';

                    // Update variance cell
                    const v = data.variance;
                    if (v > 0) {
                        varianceEl.innerHTML = `<span style="color:#16a34a;font-weight:600">+${v.toFixed(2)}</span>`;
                    } else if (v < 0) {
                        varianceEl.innerHTML = `<span style="color:#dc2626;font-weight:600">${v.toFixed(2)}</span>`;
                    } else {
                        varianceEl.innerHTML = `<span class="text-gray-400">0.00</span>`;
                    }

                    // Update status cell
                    statusEl.innerHTML = `
            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                         font-weight:500;background:#dcfce7;color:#166534">
                Counted ✓
            </span>`;

                    // Update progress
                    document.getElementById('progress-text').textContent = data.progress + '%';
                    document.getElementById('counted-display').textContent =
                        `${data.counted}/${data.total}`;

                    const bar = document.getElementById('progress-bar');
                    bar.style.width      = data.progress + '%';
                    bar.style.background = data.progress === 100 ? '#16a34a' : '#2563eb';

                    // If all counted — show success banner
                    if (data.all_counted) {
                        showAllCountedBanner();
                    }

                } catch (err) {
                    input.style.borderColor = '#ef4444';
                    input.style.background  = '#fef2f2';
                    alert('Failed to save: ' + err.message);
                }
            }

            function showAllCountedBanner() {
                const existing = document.getElementById('all-counted-banner');
                if (existing) return;

                const banner = document.createElement('div');
                banner.id    = 'all-counted-banner';
                banner.style.cssText = `
        position:fixed;bottom:24px;right:24px;
        padding:16px 20px;background:#16a34a;color:#fff;
        border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.15);
        z-index:100;font-size:14px;font-weight:500;
        display:flex;align-items:center;gap:10px;
        animation:slideIn .3s ease
    `;
                banner.innerHTML = `
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                  d="M5 13l4 4L19 7"/>
        </svg>
        All products counted! Submit for approval.
        <button onclick="this.parentElement.remove()"
                style="margin-left:8px;background:rgba(255,255,255,.2);border:none;
                       color:#fff;border-radius:6px;padding:2px 8px;cursor:pointer;
                       font-size:12px">
            ×
        </button>
    `;
                document.body.appendChild(banner);
            }

            function filterItems(query) {
                const q    = query.toLowerCase();
                const rows = document.querySelectorAll('.item-row');
                rows.forEach(row => {
                    const name = row.dataset.name || '';
                    const sku  = row.dataset.sku  || '';
                    row.style.display = (name.includes(q) || sku.includes(q)) ? '' : 'none';
                });
            }

            function submitStockTake() {
                const uncounted = document.querySelectorAll(
                    '.item-row td span[style*="fef3c7"]').length;

                if (uncounted > 0) {
                    if (!confirm(`${uncounted} product(s) still pending. Submit anyway?`)) return;
                }

                document.getElementById('submit-form').submit();
            }
        </script>

        <style>
            @keyframes slideIn {
                from { transform: translateY(20px); opacity: 0; }
                to   { transform: translateY(0);    opacity: 1; }
            }
        </style>
    @endpush

@endsection
