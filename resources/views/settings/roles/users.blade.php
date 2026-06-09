{{-- resources/views/settings/roles/users.blade.php --}}
@extends('layouts.app')
@section('title', 'User Roles')
@section('header', 'Roles & Permissions')
@section('subheader', 'Manage role assignments for each user')

@section('content')

    {{-- Tabs --}}
    <div style="display:flex;gap:4px;margin-bottom:24px;
            background:#f3f4f6;padding:4px;border-radius:10px;
            width:fit-content">
        @foreach([
            ['roles.index',       'Roles'],
            ['roles.permissions', 'Permissions matrix'],
            ['roles.user-roles',  'User roles'],
        ] as [$route, $label])
            <a href="{{ route($route) }}"
               style="padding:6px 16px;border-radius:7px;font-size:13px;
              font-weight:500;text-decoration:none;transition:all .15s;
              {{ request()->routeIs($route)
                  ? 'background:#fff;color:#111827;box-shadow:0 1px 3px rgba(0,0,0,.08)'
                  : 'color:#6b7280' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('roles.user-roles') }}"
          style="margin-bottom:16px;display:flex;gap:10px;align-items:center">
        <div class="relative">
        <span class="absolute inset-y-0 left-3 flex items-center
                     pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="1.5"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </span>
            <input type="text" name="search"
                   value="{{ request('search') }}"
                   placeholder="Search users…"
                   class="h-9 pl-9 pr-3 rounded-lg border border-gray-300
                      bg-white text-sm text-gray-800 focus:outline-none
                      focus:ring-2 focus:ring-blue-500"
                   style="width:220px">
        </div>
        <button type="submit"
                class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                   text-sm font-medium rounded-lg hover:bg-gray-50">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('roles.user-roles') }}"
               class="text-sm text-gray-400 hover:text-red-500">Clear</a>
        @endif
    </form>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse">
            <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-5 py-3 text-left text-xs font-semibold
                           text-gray-500 uppercase">User</th>
                <th class="px-5 py-3 text-left text-xs font-semibold
                           text-gray-500 uppercase">Branch</th>
                <th class="px-5 py-3 text-left text-xs font-semibold
                           text-gray-500 uppercase">Current role(s)</th>
                <th class="px-5 py-3 text-left text-xs font-semibold
                           text-gray-500 uppercase">Change role</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr class="border-b border-gray-50 hover:bg-gray-50">

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:50%;
                                    background:#eff6ff;display:flex;
                                    align-items:center;justify-content:center;
                                    font-size:13px;font-weight:600;
                                    color:#2563eb;flex-shrink:0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ $user->name }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td class="px-5 py-3 text-gray-600 text-sm">
                        {{ $user->branch?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;flex-wrap:wrap;gap:4px">
                            @foreach($user->roles as $role)
                                <span style="padding:2px 8px;border-radius:20px;
                                     font-size:11px;font-weight:500;
                                     background:#eff6ff;color:#1d4ed8">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </span>
                            @endforeach
                            @if($user->roles->isEmpty())
                                <span class="text-xs text-gray-400">No role</span>
                            @endif
                        </div>
                    </td>

                    <td class="px-5 py-3">
                        @if(!$user->hasRole('super_admin')
                            || auth()->id() === $user->id)
                            <form method="POST"
                                  action="{{ route('roles.update-user-role', $user) }}"
                                  style="display:flex;align-items:center;gap:8px">
                                @csrf @method('PUT')
                                <div class="relative">
                                    <select name="roles[]"
                                            class="h-8 px-2 pr-7 rounded-lg border
                                           border-gray-300 bg-white text-xs
                                           text-gray-700 focus:outline-none
                                           focus:ring-2 focus:ring-blue-500
                                           appearance-none">
                                        @foreach($roles as $role)
                                            @if($role->name !== 'super_admin'
                                                || auth()->user()->hasRole('super_admin'))
                                                <option value="{{ $role->name }}"
                                                    {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <span class="absolute inset-y-0 right-2 flex
                                         items-center pointer-events-none
                                         text-gray-400">
                                <svg class="w-3 h-3" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </span>
                                </div>
                                <button type="submit"
                                        class="h-8 px-3 bg-blue-600 hover:bg-blue-700
                                       text-white text-xs font-medium rounded-lg
                                       transition-colors"
                                        style="border:none;cursor:pointer">
                                    Update
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-gray-400">
                        Protected — cannot change
                    </span>
                        @endif
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="4"
                        class="px-5 py-10 text-center text-gray-400 text-sm">
                        No users found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

@endsection
