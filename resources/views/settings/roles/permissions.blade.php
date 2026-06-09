{{-- resources/views/settings/roles/permissions.blade.php --}}
@extends('layouts.app')
@section('title', 'Permissions Matrix')
@section('header', 'Roles & Permissions')
@section('subheader', 'Overview of all roles and their permissions')

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

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="font-semibold text-gray-700">Permissions matrix</p>
            <p class="text-xs text-gray-400 mt-0.5">
                Shows which roles have each permission.
                Super admin has all permissions.
            </p>
        </div>

        <div style="overflow-x:auto">
            <table class="w-full text-sm" style="border-collapse:collapse;
                                              min-width:800px">
                <thead>
                <tr style="border-bottom:2px solid #e5e7eb">
                    <th style="padding:10px 16px;text-align:left;font-size:11px;
                               font-weight:600;color:#374151;text-transform:uppercase;
                               letter-spacing:.05em;min-width:200px;position:sticky;
                               left:0;background:#fff;z-index:1">
                        Permission
                    </th>
                    @foreach($roles as $role)
                        <th style="padding:10px 12px;text-align:center;font-size:11px;
                               font-weight:600;color:#374151;text-transform:uppercase;
                               letter-spacing:.05em;min-width:110px">
                            <div style="writing-mode:vertical-rl;
                                    transform:rotate(180deg);
                                    white-space:nowrap;
                                    padding:4px 0">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </div>
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($permissions as $group => $perms)
                    {{-- Group header row --}}
                    <tr style="background:#f9fafb">
                        <td colspan="{{ $roles->count() + 1 }}"
                            style="padding:6px 16px;font-size:11px;font-weight:700;
                               color:#6b7280;text-transform:uppercase;
                               letter-spacing:.06em">
                            {{ ucfirst($group) }}
                        </td>
                    </tr>
                    {{-- Permission rows --}}
                    @foreach($perms as $permission)
                        <tr style="border-bottom:1px solid #f3f4f6"
                            onmouseover="this.style.background='#f9fafb'"
                            onmouseout="this.style.background='#fff'">
                            <td style="padding:8px 16px;font-size:11px;color:#374151;
                               position:sticky;left:0;background:inherit;z-index:1">
                                {{ $permission->name }}
                            </td>
                            @foreach($roles as $role)
                                <td style="padding:8px 12px;text-align:center">
                                    @if($role->name === 'super_admin')
                                        {{-- Super admin always has all --}}
                                        <span style="display:inline-flex;align-items:center;
                                     justify-content:center;width:20px;height:20px;
                                     border-radius:50%;background:#fef3c7">
                            <svg width="10" height="10" fill="#f59e0b"
                                 viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </span>
                                    @elseif($role->hasPermissionTo($permission->name))
                                        <span style="display:inline-flex;align-items:center;
                                     justify-content:center;width:20px;height:20px;
                                     border-radius:50%;background:#dcfce7">
                            <svg width="10" height="10" fill="#16a34a"
                                 viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </span>
                                    @else
                                        <span style="display:inline-flex;align-items:center;
                                     justify-content:center;width:20px;height:20px;
                                     border-radius:50%;background:#f3f4f6">
                            <svg width="10" height="10" fill="#d1d5db"
                                 viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
