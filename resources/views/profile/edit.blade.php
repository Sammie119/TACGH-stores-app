{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'My Profile')
@section('header', 'My Profile')
@section('subheader', 'Manage your account details and password')

@section('content')

    {{-- Two forms declared outside layout --}}
    <form id="profile-form"
          method="POST"
          action="{{ route('profile.update') }}">
        @csrf @method('PUT')
    </form>

    <form id="password-form"
          method="POST"
          action="{{ route('profile.password') }}">
        @csrf @method('PUT')
    </form>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

        {{-- Left: profile info --}}
        <div class="space-y-4">

            {{-- Avatar card --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 text-center">
                <div style="width:72px;height:72px;border-radius:50%;background:#2563eb;
                        color:#fff;display:flex;align-items:center;justify-content:center;
                        font-size:26px;font-weight:700;margin:0 auto 14px">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <p class="font-semibold text-gray-800 text-base">{{ $user->name }}</p>
                <p class="text-sm text-gray-400 mt-0.5">{{ $user->email }}</p>
                <div style="display:flex;flex-wrap:wrap;gap:4px;
                        justify-content:center;margin-top:10px">
                    @foreach($user->roles as $role)
                        <span style="padding:2px 10px;border-radius:20px;font-size:11px;
                             font-weight:500;background:#ede9fe;color:#5b21b6">
                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                </span>
                    @endforeach
                </div>

                {{-- Stats --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;
                        margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6">
                    <div>
                        <p class="text-xs text-gray-400">Branch</p>
                        <p class="text-sm font-medium text-gray-700 mt-0.5">
                            {{ $user->branch?->name ?? 'Not assigned' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Member since</p>
                        <p class="text-sm font-medium text-gray-700 mt-0.5">
                            {{ $user->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Profile form --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase
                           tracking-widest mb-5">
                        Personal details
                    </p>
                    <div class="space-y-4">

                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                Full name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center
                                         pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                                <input type="text"
                                       name="name"
                                       form="profile-form"
                                       value="{{ old('name', $user->name) }}"
                                       class="w-full h-10 pl-9 pr-3 rounded-lg border
                                          text-sm text-gray-800 focus:outline-none
                                          focus:ring-2 focus:ring-blue-500
                                          {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                            </div>
                            @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                Email address <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center
                                         pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                                <input type="email"
                                       name="email"
                                       form="profile-form"
                                       value="{{ old('email', $user->email) }}"
                                       class="w-full h-10 pl-9 pr-3 rounded-lg border
                                          text-sm text-gray-800 focus:outline-none
                                          focus:ring-2 focus:ring-blue-500
                                          {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                            </div>
                            @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
                     style="display:flex;align-items:center;gap:12px">
                    <button type="submit"
                            form="profile-form"
                            class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white
                               text-sm font-medium rounded-lg transition-colors
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               focus:ring-offset-2">
                        Save changes
                    </button>
                </div>
            </div>

        </div>

        {{-- Right: change password --}}
        <div>
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase
                           tracking-widest mb-5">
                        Change password
                    </p>
                    <div class="space-y-4">

                        {{-- Current password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                Current password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative"
                                 x-data="{ show: false }">
                            <span class="absolute inset-y-0 left-3 flex items-center
                                         pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                                <input :type="show ? 'text' : 'password'"
                                       name="current_password"
                                       form="password-form"
                                       placeholder="Enter current password"
                                       class="w-full h-10 pl-9 pr-10 rounded-lg border
                                          text-sm text-gray-800 focus:outline-none
                                          focus:ring-2 focus:ring-blue-500
                                          {{ $errors->has('current_password') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                                <button type="button"
                                        @click="show = !show"
                                        class="absolute inset-y-0 right-3 flex items-center
                                           text-gray-400 hover:text-gray-600">
                                    <svg x-show="!show" class="w-4 h-4" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="1.5"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="1.5"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="show" class="w-4 h-4" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="1.5"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            @error('current_password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- New password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                New password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative"
                                 x-data="{ show: false }">
                            <span class="absolute inset-y-0 left-3 flex items-center
                                         pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                                <input :type="show ? 'text' : 'password'"
                                       name="password"
                                       form="password-form"
                                       id="new-password"
                                       placeholder="Min. 8 characters"
                                       oninput="checkStrength(this.value)"
                                       class="w-full h-10 pl-9 pr-10 rounded-lg border
                                          text-sm text-gray-800 focus:outline-none
                                          focus:ring-2 focus:ring-blue-500
                                          {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                                <button type="button"
                                        @click="show = !show"
                                        class="absolute inset-y-0 right-3 flex items-center
                                           text-gray-400 hover:text-gray-600">
                                    <svg x-show="!show" class="w-4 h-4" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="1.5"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="1.5"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="show" class="w-4 h-4" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="1.5"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Password strength --}}
                            <div id="strength-bar" style="margin-top:8px;display:none">
                                <div style="height:4px;border-radius:2px;
                                        background:#e5e7eb;overflow:hidden">
                                    <div id="strength-fill"
                                         style="height:100%;width:0;border-radius:2px;
                                            transition:all .3s">
                                    </div>
                                </div>
                                <p id="strength-text"
                                   style="font-size:11px;margin-top:4px;color:#9ca3af">
                                </p>
                            </div>

                            @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                Confirm new password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative"
                                 x-data="{ show: false }">
                            <span class="absolute inset-y-0 left-3 flex items-center
                                         pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="1.5"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                                <input :type="show ? 'text' : 'password'"
                                       name="password_confirmation"
                                       form="password-form"
                                       placeholder="Repeat new password"
                                       class="w-full h-10 pl-9 pr-10 rounded-lg border
                                          border-gray-300 bg-white text-sm text-gray-800
                                          focus:outline-none focus:ring-2
                                          focus:ring-blue-500">
                                <button type="button"
                                        @click="show = !show"
                                        class="absolute inset-y-0 right-3 flex items-center
                                           text-gray-400 hover:text-gray-600">
                                    <svg x-show="!show" class="w-4 h-4" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="1.5"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="1.5"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="show" class="w-4 h-4" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="1.5"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Password requirements --}}
                        <div style="padding:12px 14px;background:#f9fafb;
                                border:1px solid #e5e7eb;border-radius:8px">
                            <p class="text-xs font-medium text-gray-600 mb-2">
                                Password requirements
                            </p>
                            <ul style="list-style:none;margin:0;padding:0;
                                   display:flex;flex-direction:column;gap:4px">
                                <li id="req-length"
                                    style="font-size:11px;color:#9ca3af;
                                       display:flex;align-items:center;gap:6px">
                                    <span>○</span> At least 8 characters
                                </li>
                                <li id="req-upper"
                                    style="font-size:11px;color:#9ca3af;
                                       display:flex;align-items:center;gap:6px">
                                    <span>○</span> One uppercase letter
                                </li>
                                <li id="req-lower"
                                    style="font-size:11px;color:#9ca3af;
                                       display:flex;align-items:center;gap:6px">
                                    <span>○</span> One lowercase letter
                                </li>
                                <li id="req-number"
                                    style="font-size:11px;color:#9ca3af;
                                       display:flex;align-items:center;gap:6px">
                                    <span>○</span> One number
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
                     style="display:flex;align-items:center;gap:12px">
                    <button type="submit"
                            form="password-form"
                            class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white
                               text-sm font-medium rounded-lg transition-colors
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               focus:ring-offset-2">
                        Change password
                    </button>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function checkStrength(password) {
                const bar    = document.getElementById('strength-bar');
                const fill   = document.getElementById('strength-fill');
                const text   = document.getElementById('strength-text');

                // Requirements
                const checks = {
                    length : password.length >= 8,
                    upper  : /[A-Z]/.test(password),
                    lower  : /[a-z]/.test(password),
                    number : /[0-9]/.test(password),
                };

                // Update requirement indicators
                Object.keys(checks).forEach(key => {
                    const el   = document.getElementById('req-' + key);
                    const span = el.querySelector('span');
                    if (checks[key]) {
                        el.style.color  = '#16a34a';
                        span.textContent = '✓';
                    } else {
                        el.style.color  = '#9ca3af';
                        span.textContent = '○';
                    }
                });

                if (password.length === 0) {
                    bar.style.display = 'none';
                    return;
                }

                bar.style.display = 'block';

                const score = Object.values(checks).filter(Boolean).length;

                const levels = [
                    { pct: '25%', color: '#ef4444', label: 'Weak' },
                    { pct: '50%', color: '#f59e0b', label: 'Fair' },
                    { pct: '75%', color: '#3b82f6', label: 'Good' },
                    { pct: '100%',color: '#16a34a', label: 'Strong' },
                ];

                const level = levels[score - 1] || levels[0];
                fill.style.width      = level.pct;
                fill.style.background = level.color;
                text.style.color      = level.color;
                text.textContent      = level.label;
            }
        </script>
    @endpush

@endsection
