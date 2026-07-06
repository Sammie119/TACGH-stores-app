{{-- resources/views/layouts/sidebar.blade.php --}}

{{-- Logo --}}
<div style="display:flex;align-items:center;gap:10px;padding:18px 20px 16px;
            border-bottom:1px solid #1f2937;flex-shrink:0">
    <div style="width:32px;height:32px;background:#2563eb;border-radius:8px;
                display:flex;align-items:center;justify-content:center;
                font-size:11px;font-weight:700;color:#fff;flex-shrink:0">
        SMS
    </div>
    <span style="font-size:15px;font-weight:600;color:#fff;letter-spacing:-.01em">
        {{ setting('app_name', 'SMS') }}
    </span>
    <button @click="open = false"
            id="sidebar-close"
            style="margin-left:auto;width:28px;height:28px;border-radius:6px;
                   border:none;background:transparent;cursor:pointer;
                   color:#6b7280;display:flex;align-items:center;
                   justify-content:center;flex-shrink:0">
        <svg width="16" height="16" fill="none" stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

{{-- Branch badge --}}
@if(auth()->user()->branch)
    <div style="padding:8px 20px;background:#1f2937;border-bottom:1px solid #374151">
        <p style="font-size:11px;color:#6b7280;margin:0 0 1px">Branch</p>
        <p style="font-size:12px;color:#60a5fa;font-weight:500;margin:0">
            {{ auth()->user()->branch->name }}
        </p>
    </div>
@endif

{{-- Nav --}}
<nav style="flex:1;overflow-y:auto;padding:12px 10px">

    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}"
       class="snav {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>

    {{-- Products collapsible --}}
    @can('view products')
        @php
            $productsOpen = request()->routeIs('products.*') || request()->routeIs('categories.*');
        @endphp
        <div x-data="{ open: {{ $productsOpen ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="snav {{ $productsOpen ? 'active' : '' }}">
                <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span style="flex:1">Products</span>
                <span x-text="open ? '▲' : '▼'"
                      style="font-size:9px;flex-shrink:0;opacity:.6"></span>
            </button>
            <div x-show="open" style="padding-left:28px;margin-bottom:2px" x-transition>
                <a href="{{ route('products.index') }}"
                   class="snav-sub {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    All products
                </a>
                <a href="{{ route('categories.index') }}"
                   class="snav-sub {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    Categories
                </a>
            </div>
        </div>
    @endcan

    {{-- Inventory --}}
    @can('view stock')
        <a href="{{ route('inventory.index') }}"
           class="snav {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Inventory
        </a>
    @endcan

    {{-- Stock takes --}}
    <a href="{{ route('stock-takes.index') }}"
       class="snav {{ request()->routeIs('stock-takes.*') ? 'active' : '' }}">
        <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        Stock takes
    </a>

    {{-- Sales collapsible --}}
    @can('view sales')
        @php
            $salesOpen = request()->routeIs('sales.*') || request()->routeIs('pos.*') || request()->routeIs('consignments.*');
        @endphp
        <div x-data="{ open: {{ $salesOpen ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="snav {{ $salesOpen ? 'active' : '' }}">
                <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
                <span style="flex:1">Sales</span>
                <span x-text="open ? '▲' : '▼'"
                      style="font-size:9px;flex-shrink:0;opacity:.6"></span>
            </button>
            <div x-show="open" style="padding-left:28px;margin-bottom:2px" x-transition>
                @can('create sales')
                    <a href="{{ route('pos.index') }}"
                       class="snav-sub {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                        Point of sale
                    </a>
                @endcan
                <a href="{{ route('sales.index') }}"
                   class="snav-sub {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    Sales history
                </a>
                @can('view consignments')
                    <a href="{{ route('consignments.index') }}"
                       class="snav-sub {{ request()->routeIs('consignments.*') ? 'active' : '' }}">
                        Consignments
                    </a>
                @endcan
            </div>
        </div>
    @endcan

    {{-- Transfers --}}
    @can('view transfers')
        <a href="{{ route('transfers.index') }}"
           class="snav {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
            <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            Transfers
        </a>
    @endcan

    {{-- Returns --}}
    @can('view returns')
        <a href="{{ route('returns.index') }}"
           class="snav {{ request()->routeIs('returns.*') ? 'active' : '' }}">
            <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
            </svg>
            Returns
        </a>
    @endcan

    {{-- Customers --}}
    <a href="{{ route('customers.index') }}"
       class="snav {{ request()->routeIs('customers.*') ? 'active' : '' }}">
        <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Customers
    </a>

    {{-- Banking --}}
    @can('view deposits')
        <a href="{{ route('deposits.index') }}"
           class="snav {{ request()->routeIs('deposits.*') ? 'active' : '' }}">
            <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
            </svg>
            Banking
        </a>
    @endcan

    {{-- Cashier Accounts --}}
    @can('view cashier accounts')
        <a href="{{ route('cashier-accounts.index') }}"
           class="snav {{ request()->routeIs('cashier-accounts.*') ? 'active' : '' }}">
            <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Cashier Accounts
        </a>
    @endcan

    {{-- Suppliers (collapsible) --}}
    @can('view suppliers')
        @php
            $suppliersOpen = request()->routeIs('suppliers.*')
                || request()->routeIs('purchase-orders.*')
                || request()->routeIs('supplier-payments.*');
        @endphp
        <div x-data="{ open: {{ $suppliersOpen ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="snav {{ $suppliersOpen ? 'active' : '' }}">
                <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span style="flex:1">Suppliers</span>
                <span x-text="open ? '▲' : '▼'"
                      style="font-size:9px;flex-shrink:0;opacity:.6"></span>
            </button>
            <div x-show="open" style="padding-left:28px;margin-bottom:2px" x-transition>
                <a href="{{ route('suppliers.index') }}"
                   class="snav-sub {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    All suppliers
                </a>
                @can('view purchase orders')
                    <a href="{{ route('purchase-orders.index') }}"
                       class="snav-sub {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                        Purchase orders
                    </a>
                @endcan
                @can('view supplier payments')
                    <a href="{{ route('supplier-payments.index') }}"
                       class="snav-sub {{ request()->routeIs('supplier-payments.*') ? 'active' : '' }}">
                        Supplier payments
                    </a>
                @endcan
            </div>
        </div>
    @endcan

    {{-- Audit log --}}
    @can('view audit logs')
        <a href="{{ route('audit.index') }}"
           class="snav {{ request()->routeIs('audit.*') ? 'active' : '' }}">
            <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Audit log
        </a>
    @endcan

    {{-- System Admin --}}
    @canany(['super_admin','view branches', 'view users', 'view financial years', 'view reports'])
        @php
            $systemOpen = request()->routeIs('users.*')
                            || request()->routeIs('settings.*')
                            || request()->routeIs('branches.*')
                            || request()->routeIs('financial-years.*')
                            || request()->routeIs('reports.*')
                            || request()->routeIs('roles.*');
        @endphp
        <div x-data="{ open: {{ $systemOpen ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="snav {{ $systemOpen ? 'active' : '' }}">
                <svg style="width:18px;height:18px;flex-shrink:0" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span style="flex:1">System Admin</span>
                <span x-text="open ? '▲' : '▼'"
                      style="font-size:9px;flex-shrink:0;opacity:.6"></span>
            </button>
            <div x-show="open" style="padding-left:28px;margin-bottom:2px" x-transition>

                {{-- Branches --}}
                @can('view branches')
                    <a href="{{ route('branches.index') }}"
                       class="snav-sub {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                        Branches
                    </a>
                @endcan

                {{-- Users --}}
                @can('view users')
                    <a href="{{ route('users.index') }}"
                       class="snav-sub {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        User Management
                    </a>
                @endcan

                {{-- Financial years --}}
                @can('view financial years')
                    <a href="{{ route('financial-years.index') }}"
                       class="snav-sub {{ request()->routeIs('financial-years.*') ? 'active' : '' }}">
                        Financial years
                    </a>
                @endcan

                {{-- Reports --}}
                @can('view reports')
                    <a href="{{ route('reports.index') }}"
                       class="snav-sub {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        Reports
                    </a>
                @endcan

                {{-- Settings --}}
                @role('super_admin')
                    <a href="{{ route('settings.index') }}"
                       class="snav-sub {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                        Settings
                    </a>

                    <a href="{{ route('roles.index') }}"
                       class="snav-sub {{ request()->routeIs('roles.index') ? 'active' : '' }}">
                        Roles & permissions
                    </a>
                @endrole

            </div>
        </div>
    @endcanany

</nav>

{{-- User footer --}}
<div style="border-top:1px solid #1f2937;padding:14px 16px;flex-shrink:0">
    <div style="display:flex;align-items:center;gap:10px">
        <a href="{{ route('profile.edit') }}"
           style="text-decoration:none;display:flex;align-items:center;
                  gap:10px;flex:1;min-width:0">
            <div style="width:34px;height:34px;border-radius:50%;
                        background:#2563eb;display:flex;align-items:center;
                        justify-content:center;font-size:13px;font-weight:600;
                        color:#fff;flex-shrink:0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div style="flex:1;min-width:0">
                <p style="font-size:13px;font-weight:500;color:#fff;
                          white-space:nowrap;overflow:hidden;
                          text-overflow:ellipsis;margin:0">
                    {{ auth()->user()->name }}
                </p>
                <p style="font-size:11px;color:#6b7280;
                          white-space:nowrap;overflow:hidden;
                          text-overflow:ellipsis;margin:0">
                    {{ auth()->user()->getRoleNames()->implode(', ') }}
                </p>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    style="width:30px;height:30px;border-radius:6px;border:none;
                           background:transparent;cursor:pointer;color:#6b7280;
                           display:flex;align-items:center;justify-content:center"
                    onmouseover="this.style.color='#fff';
                                 this.style.background='#374151'"
                    onmouseout="this.style.color='#6b7280';
                                this.style.background='transparent'"
                    title="Logout">
                <svg width="16" height="16" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                </svg>
            </button>
        </form>
    </div>
</div>
