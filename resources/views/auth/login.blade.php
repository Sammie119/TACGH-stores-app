{{-- resources/views/auth/login.blade.php --}}
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('app_name', 'Stores Manager') }} — Sign in</title>
    {{-- Dynamic favicon with app initial --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,{{ rawurlencode('<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'><rect width=\'100\' height=\'100\' rx=\'20\' fill=\'%232563eb\'/><text x=\'50\' y=\'68\' font-family=\'Arial,sans-serif\' font-size=\'44\' font-weight=\'900\' text-anchor=\'middle\' fill=\'white\'>' . strtoupper(substr(setting('app_name', 'S'), 0, 1)) . '</text></svg>') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI',
            Roboto, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 768px) {
            body > div {
                grid-template-columns: 1fr !important;
            }
            /* Hide branding panel on mobile */
            body > div > div:first-child {
                display: none !important;
            }
            /* Full width form on mobile */
            body > div > div:last-child {
                padding: 32px 24px !important;
                min-height: 100vh;
            }
        }
    </style>
</head>
<body>

<div style="display:grid;grid-template-columns:1fr 1fr;
            min-height:100vh;width:100%">

    {{-- ── Left: branding panel ── --}}
    <div style="background:linear-gradient(135deg,#1e3a5f 0%,#2563eb 100%);
                display:flex;flex-direction:column;justify-content:space-between;
                padding:48px;position:relative;overflow:hidden">

        {{-- Background decoration --}}
        <div style="position:absolute;top:-80px;right:-80px;width:320px;height:320px;
                    border-radius:50%;background:rgba(255,255,255,.05)"></div>
        <div style="position:absolute;bottom:-120px;left:-60px;width:400px;height:400px;
                    border-radius:50%;background:rgba(255,255,255,.04)"></div>
        <div style="position:absolute;top:40%;right:10%;width:180px;height:180px;
                    border-radius:50%;background:rgba(255,255,255,.03)"></div>

        {{-- Logo --}}
        <div style="position:relative;z-index:1">
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:42px;height:42px;background:#fff;border-radius:10px;
                            display:flex;align-items:center;justify-content:center">
                    <span style="font-size:13px;font-weight:800;color:#2563eb;
                                 letter-spacing:-.5px">SMS</span>
                </div>
                <span style="font-size:18px;font-weight:700;color:#fff;
                             letter-spacing:-.02em">
                    {{ setting('app_name', 'Stores Manager') }}
                </span>
            </div>
        </div>

        {{-- Middle content --}}
        <div style="position:relative;z-index:1">
            <p style="font-size:13px;font-weight:600;color:#93c5fd;
                      text-transform:uppercase;letter-spacing:.1em;
                      margin-bottom:16px">
                Multi-branch management
            </p>
            <h1 style="font-size:40px;font-weight:700;color:#fff;
                       line-height:1.2;margin-bottom:20px;
                       letter-spacing:-.03em">
                Run your stores<br>from one place
            </h1>
            <p style="font-size:15px;color:#bfdbfe;line-height:1.7;
                      max-width:380px">
                {{ setting('app_tagline', 'Inventory, sales, transfers and reporting — all in one system.') }}
            </p>

            {{-- Feature list --}}
            <div style="margin-top:36px;display:flex;flex-direction:column;gap:12px">
                @foreach([
                    'Real-time stock across all branches',
                    'Point of sale with instant receipts',
                    'Supplier orders and payments',
                    'Full audit trail of every action',
                ] as $feature)
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:20px;height:20px;border-radius:50%;
                                background:rgba(255,255,255,.15);display:flex;
                                align-items:center;justify-content:center;
                                flex-shrink:0">
                            <svg width="11" height="11" fill="none" stroke="#fff"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span style="font-size:14px;color:#dbeafe">{{ $feature }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div style="position:relative;z-index:1">
            <p style="font-size:12px;color:#60a5fa">
                &copy; {{ date('Y') }} {{ setting('app_name', 'Stores Manager') }}.
                All rights reserved.
            </p>
        </div>

    </div>

    {{-- ── Right: login form ── --}}
    <div style="display:flex;align-items:center;justify-content:center;
                padding:40px;background:#fff">

        <div style="width:100%;max-width:400px">

            {{-- Header --}}
            <div style="margin-bottom:36px">
                <h2 style="font-size:26px;font-weight:700;color:#111827;
                           letter-spacing:-.02em">
                    Welcome back
                </h2>
                <p style="font-size:14px;color:#6b7280;margin-top:6px">
                    Sign in to your account to continue
                </p>
            </div>

            {{-- Error messages --}}
            @if($errors->any())
                <div style="padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;
                        border-radius:10px;margin-bottom:20px">
                    @foreach($errors->all() as $error)
                        <p style="font-size:13px;color:#dc2626">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Session status --}}
            @if(session('status'))
                <div style="padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;
                        border-radius:10px;margin-bottom:20px">
                    <p style="font-size:13px;color:#166534">{{ session('status') }}</p>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div style="margin-bottom:20px">
                    <label style="display:block;font-size:13px;font-weight:500;
                                  color:#374151;margin-bottom:6px">
                        Email address
                    </label>
                    <div style="position:relative">
                        <span style="position:absolute;left:12px;top:50%;
                                     transform:translateY(-50%);color:#9ca3af;
                                     display:flex;pointer-events:none">
                            <svg width="16" height="16" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="you@example.com"
                               autofocus
                               autocomplete="email"
                               required
                               style="width:100%;height:44px;padding:0 14px 0 40px;
                                      border:1.5px solid {{ $errors->has('email') ? '#fca5a5' : '#e5e7eb' }};
                                      border-radius:10px;font-size:14px;color:#111827;
                                      background:{{ $errors->has('email') ? '#fef2f2' : '#fff' }};
                                      outline:none;transition:border-color .15s"
                               onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,.12)'"
                               onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                    </div>
                </div>

                {{-- Password --}}
                <div style="margin-bottom:24px" x-data="{ show: false }">
                    <div style="display:flex;align-items:center;
                                justify-content:space-between;margin-bottom:6px">
                        <label style="font-size:13px;font-weight:500;color:#374151">
                            Password
                        </label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               style="font-size:12px;color:#2563eb;text-decoration:none;
                                  font-weight:500"
                               onmouseover="this.style.textDecoration='underline'"
                               onmouseout="this.style.textDecoration='none'">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div style="position:relative">
                        <span style="position:absolute;left:12px;top:50%;
                                     transform:translateY(-50%);color:#9ca3af;
                                     display:flex;pointer-events:none">
                            <svg width="16" height="16" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input :type="show ? 'text' : 'password'"
                               name="password"
                               placeholder="Your password"
                               autocomplete="current-password"
                               required
                               style="width:100%;height:44px;padding:0 44px 0 40px;
                                      border:1.5px solid {{ $errors->has('password') ? '#fca5a5' : '#e5e7eb' }};
                                      border-radius:10px;font-size:14px;color:#111827;
                                      background:{{ $errors->has('password') ? '#fef2f2' : '#fff' }};
                                      outline:none;transition:border-color .15s"
                               onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,.12)'"
                               onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                        <button type="button"
                                @click="show = !show"
                                style="position:absolute;right:12px;top:50%;
                                       transform:translateY(-50%);background:none;
                                       border:none;cursor:pointer;color:#9ca3af;
                                       display:flex;padding:0"
                                onmouseover="this.style.color='#6b7280'"
                                onmouseout="this.style.color='#9ca3af'">
                            <svg x-show="!show" width="16" height="16" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" width="16" height="16" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px"
                     x-data="{ checked: false }">
                    <button type="button"
                            @click="checked = !checked"
                            :style="checked
                                ? 'background:#2563eb;border-color:#2563eb'
                                : 'background:#fff;border-color:#d1d5db'"
                            style="width:18px;height:18px;border-radius:4px;
                                   border:1.5px solid;cursor:pointer;
                                   display:flex;align-items:center;
                                   justify-content:center;flex-shrink:0;
                                   transition:all .15s">
                        <input type="checkbox"
                               name="remember"
                               x-model="checked"
                               style="display:none">
                        <svg x-show="checked" width="10" height="10" fill="none"
                             stroke="#fff" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                    <label style="font-size:13px;color:#374151;cursor:pointer"
                           @click="checked = !checked">
                        Keep me signed in
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        style="width:100%;height:46px;background:#2563eb;color:#fff;
                               border:none;border-radius:10px;font-size:15px;
                               font-weight:600;cursor:pointer;
                               transition:background .15s;letter-spacing:-.01em"
                        onmouseover="this.style.background='#1d4ed8'"
                        onmouseout="this.style.background='#2563eb'">
                    Sign in to your account
                </button>

            </form>

            {{-- Demo credentials hint --}}
            @if(app()->environment('local'))
                <div style="margin-top:24px;padding:14px 16px;background:#f8fafc;
                        border:1px solid #e2e8f0;border-radius:10px">
                    <p style="font-size:11px;font-weight:600;color:#64748b;
                          text-transform:uppercase;letter-spacing:.06em;
                          margin-bottom:8px">
                        Demo credentials
                    </p>
                    <div style="display:grid;grid-template-columns:1fr 1fr;
                            gap:8px">
                        @foreach([
                            ['Super Admin', 'admin@admin.com', 'Admin@1234'],
                        ] as [$role, $email, $pass])
                            <div style="padding:8px 10px;background:#fff;border-radius:7px;
                                border:1px solid #e2e8f0;cursor:pointer"
                                 onclick="fillCredentials('{{ $email }}','{{ $pass }}')"
                                 onmouseover="this.style.borderColor='#2563eb';this.style.background='#eff6ff'"
                                 onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#fff'">
                                <p style="font-size:11px;font-weight:600;color:#374151">
                                    {{ $role }}
                                </p>
                                <p style="font-size:10px;color:#94a3b8;margin-top:1px;
                                  font-family:monospace">
                                    {{ $email }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    <p style="font-size:10px;color:#94a3b8;margin-top:8px">
                        Click a card to auto-fill credentials
                    </p>
                </div>
            @endif

            {{-- Footer --}}
            <p style="font-size:12px;color:#9ca3af;text-align:center;margin-top:28px">
                &copy; {{ date('Y') }} {{ setting('app_name', 'Stores Manager') }}.
                Secure access only.
            </p>

        </div>
    </div>

</div>

<script>
    function fillCredentials(email, password) {
        document.querySelector('input[name="email"]').value    = email;
        document.querySelector('input[name="password"]').value = password;
    }
</script>

</body>
</html>
