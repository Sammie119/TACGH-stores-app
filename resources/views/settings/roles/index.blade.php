{{-- resources/views/settings/roles/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('header', 'Roles & Permissions')
@section('subheader', 'Manage system roles and what each role can do')

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

    <div style="display:flex;justify-content:flex-end;margin-bottom:16px">
        <a href="{{ route('roles.create') }}"
           class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
              font-medium rounded-lg transition-colors"
           style="display:inline-flex;align-items:center;gap:6px">
            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New role
        </a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));
            gap:16px">
        @foreach($roles as $role)
            @php
                $isSystem = in_array($role->name, [
                    'super_admin','general_manager','branch_admin',
                    'store_manager','sales_officer','stock_officer',
                    'auditor','accountant'
                ]);
                $colors = [
                    'super_admin'      => ['bg'=>'#fef3c7','text'=>'#92400e','dot'=>'#f59e0b'],
                    'general_manager' => ['bg'=>'#ede9fe','text'=>'#5b21b6','dot'=>'#7c3aed'],
                    'branch_admin'     => ['bg'=>'#dbeafe','text'=>'#1e40af','dot'=>'#2563eb'],
                    'store_manager'    => ['bg'=>'#dcfce7','text'=>'#166534','dot'=>'#16a34a'],
                    'sales_officer'    => ['bg'=>'#ffedd5','text'=>'#9a3412','dot'=>'#ea580c'],
                    'stock_officer'    => ['bg'=>'#e0f2fe','text'=>'#075985','dot'=>'#0284c7'],
                    'auditor'          => ['bg'=>'#fce7f3','text'=>'#9d174d','dot'=>'#db2777'],
                    'accountant'       => ['bg'=>'#f0fdf4','text'=>'#14532d','dot'=>'#15803d'],
                ][$role->name] ?? ['bg'=>'#f3f4f6','text'=>'#374151','dot'=>'#6b7280'];
            @endphp

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                {{-- Role header --}}
                <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;
                    display:flex;align-items:flex-start;
                    justify-content:space-between;gap:12px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:10px;height:10px;border-radius:50%;
                            background:{{ $colors['dot'] }};flex-shrink:0">
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $role->users_count }}
                                user{{ $role->users_count !== 1 ? 's' : '' }}
                                assigned
                            </p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px">
                        @if($isSystem)
                            <span style="padding:2px 8px;border-radius:20px;font-size:10px;
                             font-weight:600;background:{{ $colors['bg'] }};
                             color:{{ $colors['text'] }}">
                    System
                </span>
                        @endif
                    </div>
                </div>

                {{-- Permissions preview --}}
                <div style="padding:12px 20px">
                    <p class="text-xs font-medium text-gray-500 mb-2">
                        {{ $role->permissions->count() }} permissions
                    </p>
                    @if($role->name === 'super_admin')
                        <p class="text-xs text-amber-600 font-medium">
                            Full access — all permissions granted via gate bypass
                        </p>
                    @else
                        <div style="display:flex;flex-wrap:wrap;gap:4px">
                            @foreach($role->permissions->take(8) as $perm)
                                <span style="padding:1px 6px;border-radius:4px;font-size:10px;
                             background:#f3f4f6;color:#374151">
                    {{ $perm->name }}
                </span>
                            @endforeach
                            @if($role->permissions->count() > 8)
                                <span style="padding:1px 6px;border-radius:4px;font-size:10px;
                             background:#eff6ff;color:#2563eb">
                    +{{ $role->permissions->count() - 8 }} more
                </span>
                            @endif
                            @if($role->permissions->count() === 0)
                                <span class="text-xs text-gray-400">No permissions assigned</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div style="padding:10px 20px;border-top:1px solid #f3f4f6;
                    background:#fafafa;display:flex;gap:10px">
                    @if($role->name !== 'super_admin')
                        <a href="{{ route('roles.edit', $role) }}"
                           class="text-xs text-blue-600 hover:underline font-medium">
                            Edit permissions
                        </a>
                    @endif
                    @if(!$isSystem && $role->users_count === 0)
                        <form method="POST"
                              action="{{ route('roles.destroy', $role) }}"
                              onsubmit="return confirm('Delete role {{ addslashes(str_replace('_',' ',$role->name)) }}?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline font-medium"
                                    style="background:none;border:none;cursor:pointer;
                               padding:0">
                                Delete
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('roles.user-roles') }}?role={{ $role->name }}"
                       class="text-xs text-gray-400 hover:text-gray-600 ml-auto">
                        View users →
                    </a>
                </div>

            </div>
        @endforeach
    </div>

@endsection
