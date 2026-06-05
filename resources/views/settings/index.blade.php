{{-- resources/views/settings/index.blade.php --}}
@extends('layouts.app')
@section('title', 'System Settings')
@section('header', 'System Settings')
@section('subheader', 'Configure application behaviour')

@section('content')

    <div style="display:grid;grid-template-columns:220px 1fr;gap:24px;align-items:start">

        {{-- Sidebar tabs --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden"
             style="position:sticky;top:80px">
            <div style="padding:12px 8px">
                @foreach($groups as $key => $label)
                    @php
                        $icons = [
                            'general'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                            'sales'         => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>',
                            'notifications' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
                            'security'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                        ];
                        $isActive = request()->get('tab', 'general') === $key
                            || (!request()->get('tab') && $key === 'general');
                    @endphp
                    <a href="{{ route('settings.index') }}?tab={{ $key }}"
                       style="display:flex;align-items:center;gap:10px;padding:9px 12px;
                      border-radius:8px;font-size:13px;font-weight:500;
                      text-decoration:none;margin-bottom:2px;transition:all .15s;
                      background:{{ $isActive ? '#eff6ff' : 'transparent' }};
                      color:{{ $isActive ? '#2563eb' : '#6b7280' }}">
                        <svg style="width:16px;height:16px;flex-shrink:0"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $icons[$key] ?? '' !!}
                        </svg>
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Settings panels --}}
        <div>
            @foreach($groups as $groupKey => $groupLabel)
                @php
                    $activeTab = request()->get('tab', 'general');
                    $isVisible = $activeTab === $groupKey;
                    $groupSettings = $settings->get($groupKey, collect());
                @endphp

                @if($isVisible)
                    <form method="POST"
                          action="{{ route('settings.update', $groupKey) }}">
                        @csrf @method('PUT')

                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                            {{-- Panel header --}}
                            <div class="px-6 py-5 border-b border-gray-100">
                                <p class="font-semibold text-gray-800">{{ $groupLabel }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @switch($groupKey)
                                        @case('general')
                                            Core application configuration
                                            @break
                                        @case('sales')
                                            Invoice numbering and sales behaviour
                                            @break
                                        @case('notifications')
                                            Control which alerts appear in the bell menu
                                            @break
                                        @case('security')
                                            Authentication and access control rules
                                            @break
                                    @endswitch
                                </p>
                            </div>

                            {{-- Settings fields --}}
                            <div class="divide-y divide-gray-50">
                                @forelse($groupSettings as $setting)
                                    <div style="padding:20px 24px;display:grid;
                                grid-template-columns:1fr 1fr;gap:24px;
                                align-items:start">

                                        {{-- Label + description --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700"
                                                   for="setting_{{ $setting->key }}">
                                                {{ $setting->label }}
                                            </label>
                                            @if($setting->description)
                                                <p class="text-xs text-gray-400 mt-1 leading-relaxed">
                                                    {{ $setting->description }}
                                                </p>
                                            @endif
                                        </div>

                                        {{-- Input --}}
                                        <div>
                                            @switch($setting->type)

                                                @case('boolean')
                                                    <div style="display:flex;align-items:center;gap:10px;
                                            padding-top:4px"
                                                         x-data="{ on: {{ $setting->value === '1' ? 'true' : 'false' }} }">
                                                        <button type="button"
                                                                @click="on = !on"
                                                                :class="on ? 'bg-blue-600' : 'bg-gray-200'"
                                                                style="position:relative;display:inline-flex;
                                                   width:44px;height:24px;border-radius:12px;
                                                   border:none;cursor:pointer;
                                                   transition:background .2s;
                                                   flex-shrink:0">
                                                            <input type="hidden"
                                                                   name="{{ $setting->key }}"
                                                                   :value="on ? 'on' : ''">
                                                            <span :style="on ? 'transform:translateX(20px)' : 'transform:translateX(2px)'"
                                                                  style="position:absolute;top:2px;width:20px;height:20px;
                                                     border-radius:50%;background:#fff;
                                                     transition:transform .2s;
                                                     box-shadow:0 1px 3px rgba(0,0,0,.2)">
                                        </span>
                                                        </button>
                                                        <span class="text-sm"
                                                              :class="on ? 'text-gray-800 font-medium' : 'text-gray-400'"
                                                              x-text="on ? 'Enabled' : 'Disabled'">
                                    </span>
                                                    </div>
                                                    @break

                                                @case('textarea')
                                                    <textarea id="setting_{{ $setting->key }}"
                                                              name="{{ $setting->key }}"
                                                              rows="3"
                                                              class="w-full px-3 py-2.5 rounded-lg border
                                                 border-gray-300 bg-white text-sm
                                                 text-gray-800 resize-none focus:outline-none
                                                 focus:ring-2 focus:ring-blue-500">{{ old($setting->key, $setting->value) }}</textarea>
                                                    @break

                                                @case('select')
                                                    @php
                                                        $options = match($setting->key) {
                                                            'date_format' => [
                                                                'd M Y'   => '15 Jan 2025',
                                                                'd/m/Y'   => '15/01/2025',
                                                                'm/d/Y'   => '01/15/2025',
                                                                'Y-m-d'   => '2025-01-15',
                                                                'd-m-Y'   => '15-01-2025',
                                                            ],
                                                            'timezone' => [
                                                                'Africa/Accra'      => 'Africa/Accra (GMT+0)',
                                                                'Africa/Lagos'      => 'Africa/Lagos (GMT+1)',
                                                                'Africa/Nairobi'    => 'Africa/Nairobi (GMT+3)',
                                                                'Africa/Cairo'      => 'Africa/Cairo (GMT+2)',
                                                                'Europe/London'     => 'Europe/London (GMT+0/+1)',
                                                                'America/New_York'  => 'America/New_York (GMT-5/-4)',
                                                                'America/Chicago'   => 'America/Chicago (GMT-6/-5)',
                                                                'Asia/Dubai'        => 'Asia/Dubai (GMT+4)',
                                                            ],
                                                            default => [],
                                                        };
                                                    @endphp
                                                    <div class="relative">
                                                        <select id="setting_{{ $setting->key }}"
                                                                name="{{ $setting->key }}"
                                                                class="w-full h-10 px-3 pr-8 rounded-lg border
                                                   border-gray-300 bg-white text-sm text-gray-800
                                                   focus:outline-none focus:ring-2
                                                   focus:ring-blue-500 appearance-none">
                                                            @foreach($options as $optVal => $optLabel)
                                                                <option value="{{ $optVal }}"
                                                                    {{ old($setting->key, $setting->value) === $optVal ? 'selected' : '' }}>
                                                                    {{ $optLabel }}
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
                                                    @break

                                                @default
                                                    {{-- text --}}
                                                    <input type="text"
                                                           id="setting_{{ $setting->key }}"
                                                           name="{{ $setting->key }}"
                                                           value="{{ old($setting->key, $setting->value) }}"
                                                           class="w-full h-10 px-3 rounded-lg border
                                              border-gray-300 bg-white text-sm text-gray-800
                                              focus:outline-none focus:ring-2
                                              focus:ring-blue-500">
                                                    @break

                                            @endswitch
                                        </div>

                                    </div>
                                @empty
                                    <div class="px-6 py-10 text-center text-gray-400 text-sm">
                                        No settings in this group.
                                    </div>
                                @endforelse
                            </div>

                            {{-- Footer --}}
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
                                 style="display:flex;align-items:center;gap:12px">
                                <button type="submit"
                                        class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white
                                   text-sm font-medium rounded-lg transition-colors
                                   focus:outline-none focus:ring-2 focus:ring-blue-500
                                   focus:ring-offset-2">
                                    Save {{ strtolower($groupLabel) }} settings
                                </button>
                                <p class="text-xs text-gray-400">
                                    Changes take effect immediately.
                                </p>
                            </div>

                        </div>
                    </form>
                @endif
            @endforeach
        </div>

    </div>

@endsection
