{{-- resources/views/layouts/navbar.blade.php --}}
<header style="background:#fff;border-bottom:1px solid #e5e7eb;
               padding:0 20px;height:56px;display:flex;align-items:center;
               justify-content:space-between;flex-shrink:0;gap:12px">

    {{-- Left: hamburger + brand --}}
    <div style="display:flex;align-items:center;gap:12px;min-width:0">

        {{-- Hamburger — only visible on mobile via CSS --}}
        <button id="hamburger-btn"
                @click="open = !open"
                style="display:flex;align-items:center;justify-content:center;
                       width:36px;height:36px;border-radius:8px;border:none;
                       background:transparent;cursor:pointer;color:#374151;
                       flex-shrink:0"
                onmouseover="this.style.background='#f3f4f6'"
                onmouseout="this.style.background='transparent'"
                aria-label="Open menu">
            <svg width="20" height="20" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- App name — only on mobile --}}
        <span id="mobile-brand"
              style="font-size:15px;font-weight:600;color:#111827">
            SMS
        </span>

    </div>

    {{-- Right: FY + date + bell --}}
    <div style="display:flex;align-items:center;gap:12px;flex-shrink:0">

        @php $activeYear = \App\Models\FinancialYear::getActive(); @endphp
        @if($activeYear)
            <span style="display:inline-flex;align-items:center;padding:3px 10px;
                     border-radius:20px;font-size:11px;font-weight:500;
                     background:#dcfce7;color:#166534;white-space:nowrap">
            {{ $activeYear->name }}
        </span>
        @else
            <span style="display:inline-flex;align-items:center;padding:3px 10px;
                     border-radius:20px;font-size:11px;font-weight:500;
                     background:#fee2e2;color:#991b1b;white-space:nowrap">
            No active FY
        </span>
        @endif

        {{-- Date --}}
        <span id="nav-date"
              style="font-size:12px;color:#9ca3af;white-space:nowrap">
            {{ now()->format('D, d M Y') }}
        </span>

        {{-- Bell --}}
        <div style="position:relative" x-data="notifPanel()">

            {{-- Bell button --}}
            <button @click="toggle()"
                    style="position:relative;width:36px;height:36px;border-radius:8px;
                   border:none;background:transparent;cursor:pointer;
                   color:#6b7280;display:flex;align-items:center;
                   justify-content:center"
                    onmouseover="this.style.background='#f3f4f6'"
                    onmouseout="this.style.background='transparent'"
                    aria-label="Notifications">
                <svg width="18" height="18" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                {{-- Badge --}}
                <span x-show="total > 0"
                      x-text="total > 9 ? '9+' : total"
                      style="position:absolute;top:4px;right:4px;min-width:16px;height:16px;
                     padding:0 3px;border-radius:20px;background:#ef4444;
                     color:#fff;font-size:9px;font-weight:700;
                     display:flex;align-items:center;justify-content:center;
                     border:1.5px solid #fff;line-height:1">
                </span>
            </button>

            {{-- Dropdown panel --}}
            <div x-show="open"
                 @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 style="display:none;position:absolute;right:0;top:calc(100% + 8px);
                width:360px;background:#fff;border:1px solid #e5e7eb;
                border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,.12);
                z-index:100;overflow:hidden">

                {{-- Header --}}
                <div style="padding:14px 18px;border-bottom:1px solid #f3f4f6;
                    display:flex;align-items:center;justify-content:space-between">
                    <div style="display:flex;align-items:center;gap:8px">
                        <p style="font-size:14px;font-weight:600;color:#111827;margin:0">
                            Notifications
                        </p>
                        <span x-show="total > 0"
                              x-text="total"
                              style="padding:1px 7px;border-radius:20px;font-size:11px;
                             font-weight:600;background:#fee2e2;color:#991b1b">
                </span>
                    </div>
                    <button @click="open = false"
                            style="background:none;border:none;cursor:pointer;
                           color:#9ca3af;font-size:18px;line-height:1;
                           padding:0;width:20px;height:20px">
                        ×
                    </button>
                </div>

                {{-- Loading state --}}
                <div x-show="loading"
                     style="padding:32px;text-align:center;color:#9ca3af;font-size:13px">
                    Loading…
                </div>

                {{-- Empty state --}}
                <div x-show="!loading && total === 0"
                     style="padding:32px;text-align:center">
                    <svg style="width:36px;height:36px;color:#d1d5db;margin:0 auto 10px"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p style="font-size:13px;color:#9ca3af;margin:0">
                        All caught up! No notifications.
                    </p>
                </div>

                {{-- Notification list --}}
                <div x-show="!loading && total > 0"
                     style="max-height:420px;overflow-y:auto">

                    {{-- Low stock --}}
                    <template x-if="notifications.low_stock && notifications.low_stock.length > 0">
                        <div>
                            <div style="padding:8px 18px 4px;background:#f9fafb;
                                border-bottom:1px solid #f3f4f6">
                                <p style="font-size:10px;font-weight:600;color:#9ca3af;
                                   text-transform:uppercase;letter-spacing:.06em;margin:0">
                                    Low stock
                                </p>
                            </div>
                            <template x-for="item in notifications.low_stock" :key="item.id">
                                <a :href="`/inventory/${item.product_id}/adjust`"
                                   style="display:flex;align-items:flex-start;gap:12px;
                                  padding:12px 18px;border-bottom:1px solid #f9fafb;
                                  text-decoration:none;transition:background .1s"
                                   onmouseover="this.style.background='#fef3c7'"
                                   onmouseout="this.style.background='transparent'">
                                    <div style="width:32px;height:32px;border-radius:8px;
                                        background:#fef3c7;display:flex;align-items:center;
                                        justify-content:center;flex-shrink:0">
                                        <svg style="width:16px;height:16px;color:#d97706"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <p style="font-size:13px;font-weight:500;
                                           color:#111827;margin:0" x-text="item.product ? item.product.name : 'Product'"></p>
                                        <p style="font-size:11px;color:#9ca3af;margin:2px 0 0"
                                           x-text="`${item.quantity} remaining · ${item.branch ? item.branch.name : ''}`"></p>
                                    </div>
                                    <span style="font-size:10px;font-weight:600;padding:2px 7px;
                                         border-radius:20px;background:#fef3c7;
                                         color:#92400e;white-space:nowrap;flex-shrink:0">
                                Low
                            </span>
                                </a>
                            </template>
                        </div>
                    </template>

                    {{-- Pending transfers --}}
                    <template x-if="notifications.pending_transfers && notifications.pending_transfers.length > 0">
                        <div>
                            <div style="padding:8px 18px 4px;background:#f9fafb;
                                border-bottom:1px solid #f3f4f6">
                                <p style="font-size:10px;font-weight:600;color:#9ca3af;
                                   text-transform:uppercase;letter-spacing:.06em;margin:0">
                                    Pending transfers
                                </p>
                            </div>
                            <template x-for="transfer in notifications.pending_transfers"
                                      :key="transfer.id">
                                <a :href="`/transfers/${transfer.id}`"
                                   style="display:flex;align-items:flex-start;gap:12px;
                                  padding:12px 18px;border-bottom:1px solid #f9fafb;
                                  text-decoration:none;transition:background .1s"
                                   onmouseover="this.style.background='#eff6ff'"
                                   onmouseout="this.style.background='transparent'">
                                    <div style="width:32px;height:32px;border-radius:8px;
                                        background:#dbeafe;display:flex;align-items:center;
                                        justify-content:center;flex-shrink:0">
                                        <svg style="width:16px;height:16px;color:#2563eb"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <p style="font-size:13px;font-weight:500;
                                           color:#111827;margin:0"
                                           x-text="transfer.reference_no"></p>
                                        <p style="font-size:11px;color:#9ca3af;margin:2px 0 0"
                                           x-text="`${transfer.from_branch ? transfer.from_branch.name : ''} → ${transfer.to_branch ? transfer.to_branch.name : ''}`"></p>
                                    </div>
                                    <span style="font-size:10px;font-weight:600;padding:2px 7px;
                                         border-radius:20px;background:#dbeafe;
                                         color:#1e40af;white-space:nowrap;flex-shrink:0">
                                Pending
                            </span>
                                </a>
                            </template>
                        </div>
                    </template>

                    {{-- Pending returns --}}
                    <template x-if="notifications.pending_returns && notifications.pending_returns.length > 0">
                        <div>
                            <div style="padding:8px 18px 4px;background:#f9fafb;
                                border-bottom:1px solid #f3f4f6">
                                <p style="font-size:10px;font-weight:600;color:#9ca3af;
                                   text-transform:uppercase;letter-spacing:.06em;margin:0">
                                    Pending returns
                                </p>
                            </div>
                            <template x-for="ret in notifications.pending_returns"
                                      :key="ret.id">
                                <a :href="`/returns/${ret.id}`"
                                   style="display:flex;align-items:flex-start;gap:12px;
                                  padding:12px 18px;border-bottom:1px solid #f9fafb;
                                  text-decoration:none;transition:background .1s"
                                   onmouseover="this.style.background='#fdf4ff'"
                                   onmouseout="this.style.background='transparent'">
                                    <div style="width:32px;height:32px;border-radius:8px;
                                        background:#f3e8ff;display:flex;align-items:center;
                                        justify-content:center;flex-shrink:0">
                                        <svg style="width:16px;height:16px;color:#7c3aed"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <p style="font-size:13px;font-weight:500;
                                           color:#111827;margin:0"
                                           x-text="ret.product ? ret.product.name : 'Product'"></p>
                                        <p style="font-size:11px;color:#9ca3af;margin:2px 0 0"
                                           x-text="ret.sale ? `Invoice: ${ret.sale.invoice_no}` : ''"></p>
                                    </div>
                                    <span style="font-size:10px;font-weight:600;padding:2px 7px;
                                         border-radius:20px;background:#f3e8ff;
                                         color:#6b21a8;white-space:nowrap;flex-shrink:0">
                                Return
                            </span>
                                </a>
                            </template>
                        </div>
                    </template>

                    {{-- Pending deposits --}}
                    <template x-if="notifications.pending_deposits && notifications.pending_deposits.length > 0">
                        <div>
                            <div style="padding:8px 18px 4px;background:#f9fafb;
                                border-bottom:1px solid #f3f4f6">
                                <p style="font-size:10px;font-weight:600;color:#9ca3af;
                                   text-transform:uppercase;letter-spacing:.06em;margin:0">
                                    Pending deposits
                                </p>
                            </div>
                            <template x-for="deposit in notifications.pending_deposits"
                                      :key="deposit.id">
                                <a :href="`/deposits/${deposit.id}`"
                                   style="display:flex;align-items:flex-start;gap:12px;
                                  padding:12px 18px;border-bottom:1px solid #f9fafb;
                                  text-decoration:none;transition:background .1s"
                                   onmouseover="this.style.background='#fefce8'"
                                   onmouseout="this.style.background='transparent'">
                                    <div style="width:32px;height:32px;border-radius:8px;
                                        background:#fef9c3;display:flex;align-items:center;
                                        justify-content:center;flex-shrink:0">
                                        <svg style="width:16px;height:16px;color:#ca8a04"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                        </svg>
                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <p style="font-size:13px;font-weight:500;
                                           color:#111827;margin:0"
                                           x-text="`GHS ${parseFloat(deposit.amount).toFixed(2)}`"></p>
                                        <p style="font-size:11px;color:#9ca3af;margin:2px 0 0"
                                           x-text="deposit.branch ? deposit.branch.name : ''"></p>
                                    </div>
                                    <span style="font-size:10px;font-weight:600;padding:2px 7px;
                                         border-radius:20px;background:#fef9c3;
                                         color:#854d0e;white-space:nowrap;flex-shrink:0">
                                Verify
                            </span>
                                </a>
                            </template>
                        </div>
                    </template>

                </div>

                {{-- Footer --}}
                <div x-show="!loading && total > 0"
                     style="padding:10px 18px;border-top:1px solid #f3f4f6;
                    background:#f9fafb;text-align:center">
                    <a href="/reports"
                       style="font-size:12px;color:#2563eb;text-decoration:none;
                      font-weight:500">
                        View all reports →
                    </a>
                </div>

            </div>
        </div>

    </div>
</header>
