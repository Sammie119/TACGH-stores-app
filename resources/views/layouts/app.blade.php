{{-- resources/views/layouts/app.blade.php --}}
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Dynamic favicon with app initial --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,{{ rawurlencode('<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'><rect width=\'100\' height=\'100\' rx=\'20\' fill=\'%232563eb\'/><text x=\'50\' y=\'68\' font-family=\'Arial,sans-serif\' font-size=\'44\' font-weight=\'900\' text-anchor=\'middle\' fill=\'white\'>' . strtoupper(substr(setting('app_name', 'S'), 0, 1)) . '</text></svg>') }}">
    <title>{{ config('app.name', 'SMS') }} — @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; background: #f3f4f6; }

        /* ── Sidebar ── */
        #app-sidebar {
            background: #111827 !important;
            color: #fff;
            width: 256px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            transition: transform .25s ease;
        }

        /* Desktop: always visible, sticky */
        @media (min-width: 1024px) {
            #app-sidebar {
                position: sticky !important;
                top: 0 !important;
                height: 100vh !important;
                transform: translateX(0) !important;
                z-index: auto !important;
            }
            #hamburger-btn  { display: none !important; }
            #mobile-brand   { display: none !important; }
            #sidebar-overlay{ display: none !important; }
            #sidebar-close  { display: none !important; }
            #nav-date       { display: block !important; }
        }

        /* Mobile: fixed, hidden by default */
        @media (max-width: 1023px) {
            #app-sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                z-index: 50 !important;
            }
            #nav-date { display: none !important; }
        }

        /* ── Nav link styles ── */
        .snav {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 2px;
            transition: all .15s;
            color: #9ca3af;
            background: transparent;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }
        .snav:hover  { background: #1f2937 !important; color: #fff !important; }
        .snav.active { background: #2563eb !important; color: #fff !important; }
        .snav-sub {
            display: flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 1px;
            transition: all .15s;
            color: #9ca3af;
        }
        .snav-sub:hover  { background: #1f2937 !important; color: #fff !important; }
        .snav-sub.active { background: #2563eb !important; color: #fff !important; }

        /* ── Responsive grids ── */
        @media (max-width: 767px) {
            main { padding: 14px 12px !important; }
            .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .table-wrap table { min-width: 580px; }
        }
    </style>
</head>
<body>

<div style="display:flex;min-height:100vh"
     x-data="{ open: false }">

    {{-- ── Mobile overlay ── --}}
    <div id="sidebar-overlay"
         x-show="open"
         @click="open = false"
         style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:40;
                display:none"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- ── Sidebar ── --}}
    <aside id="app-sidebar"
           :style="open ? 'transform:translateX(0)' : 'transform:translateX(-100%)'"
           style="width:256px;background:#111827 !important;color:#fff;
              display:flex;flex-direction:column;flex-shrink:0;
              transition:transform .25s ease;overflow-y:auto">
        @include('layouts.sidebar')
    </aside>

    {{-- ── Main area ── --}}
    <div style="flex:1;display:flex;flex-direction:column;
                min-width:0;overflow:hidden">

        {{-- Navbar --}}
        @include('layouts.navbar')

        {{-- Page content --}}
        <main style="flex:1;overflow-y:auto;padding:24px 20px">

            {{-- Flash: success --}}
            @if(session('success'))
                <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;
                        background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;
                        margin-bottom:20px">
                    <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p style="font-size:13px;color:#166534;margin:0">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            {{-- Flash: error --}}
            @if(session('error'))
                <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;
                        background:#fef2f2;border:1px solid #fecaca;border-radius:10px;
                        margin-bottom:20px">
                    <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <p style="font-size:13px;color:#dc2626;margin:0">
                        {{ session('error') }}
                    </p>
                </div>
            @endif

            {{-- Validation errors --}}
            @if(!empty($errors) && (is_object($errors) ? $errors->any() : count($errors) > 0))
                <div style="padding:12px 16px;background:#fef2f2;
                border:1px solid #fecaca;border-radius:10px;
                margin-bottom:20px">
                    <ul style="list-style:none;margin:0;padding:0;
               display:flex;flex-direction:column;gap:4px">
                        @foreach((is_object($errors) ? $errors->all() : $errors) as $error)
                            <li style="font-size:13px;color:#dc2626">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Page heading --}}
            @hasSection('header')
                <div style="margin-bottom:24px">
                    <h1 style="font-size:22px;font-weight:600;color:#111827;margin:0">
                        @yield('header')
                    </h1>
                    @hasSection('subheader')
                        <p style="font-size:13px;color:#6b7280;margin:4px 0 0">
                            @yield('subheader')
                        </p>
                    @endif
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
