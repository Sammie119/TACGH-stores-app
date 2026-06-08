{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')
@section('title', 'User Management')
@section('header', 'User Management')
@section('subheader', 'Manage system users and their roles')

@section('content')

    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;flex-wrap:wrap">

        {{-- Search + filters --}}
        <form style="display:flex;align-items:center;gap:10px;flex-wrap:wrap" method="GET"
              action="{{ route('users.index') }}">

            <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search name or email…"
                       class="h-9 pl-9 pr-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none focus:ring-2
                          focus:ring-blue-500"
                       style="width:220px">
            </div>

            <select name="branch"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                       text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All branches</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}"
                        {{ request('branch') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>

            <select name="role"
                    class="h-9 px-3 rounded-lg border border-gray-300 bg-white text-sm
                       text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}"
                        {{ request('role') == $role->name ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600 text-sm
                       font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search', 'branch', 'role']))
                <a href="{{ route('users.index') }}"
                   class="h-9 px-3 flex items-center text-sm text-gray-500 hover:text-red-500
                  transition-colors">
                    Clear
                </a>
            @endif
        </form>

        @can('create users')
            <a href="{{ route('users.create') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium
              rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add user
            </a>
        @endcan

    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('users.index')"
    />

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    User
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Roles
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Branch
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Status
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                @if($user->id !== 1)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                           {{ $user->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                        {{-- User info --}}
                        <td class="px-5 py-3">
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="width:34px;height:34px;border-radius:50%;background:#dbeafe;
                                        color:#1d4ed8;display:flex;align-items:center;justify-content:center;
                                        font-size:13px;font-weight:600;flex-shrink:0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Roles --}}
                        <td class="px-5 py-3">
                            <div style="display:flex;flex-wrap:wrap;gap:4px">
                                @foreach($user->roles as $role)
                                    <span style="display:inline-flex;align-items:center;padding:2px 8px;
                                         border-radius:20px;font-size:11px;font-weight:500;
                                         background:#ede9fe;color:#5b21b6">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </span>
                                @endforeach
                            </div>
                        </td>

                        {{-- Branch --}}
                        <td class="px-5 py-3 text-gray-600">
                            {{ $user->branch?->name ?? '—' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-3">
                            @if($user->trashed())
                                <span style="display:inline-flex;align-items:center;padding:2px 8px;
                                         border-radius:20px;font-size:11px;font-weight:500;
                                         background:#fee2e2;color:#991b1b">
                                Deleted
                            </span>
                            @elseif($user->is_active)
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:2px 8px;
                                         border-radius:20px;font-size:11px;font-weight:500;
                                         background:#dcfce7;color:#166534">
                                <span style="width:5px;height:5px;border-radius:50%;background:#16a34a"></span>
                                Active
                            </span>
                            @else
                                <span style="display:inline-flex;align-items:center;padding:2px 8px;
                                         border-radius:20px;font-size:11px;font-weight:500;
                                         background:#f3f4f6;color:#374151">
                                Inactive
                            </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3">
                            <div style="display:flex;align-items:center;gap:12px">
                                @if($user->trashed())
                                    @can('create users')
                                        <form method="POST" action="{{ route('users.restore', $user->id) }}">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-green-600 hover:underline font-medium">
                                                Restore
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('users.force-delete', $user->id) }}"
                                              onsubmit="return confirm('Permanently delete this user?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-600 hover:underline font-medium">
                                                Delete permanently
                                            </button>
                                        </form>
                                    @endcan
                                @else
                                    <a href="{{ route('users.show', $user) }}"
                                       class="text-xs text-blue-600 hover:underline font-medium">
                                        View
                                    </a>
                                    @can('edit users')
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="text-xs text-gray-600 hover:underline font-medium">
                                            Edit
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form method="POST"
                                                  action="{{ route('users.toggle-status', $user) }}">
                                                @csrf @method('PATCH')
                                                <button class="text-xs font-medium
                                        {{ $user->is_active ? 'text-amber-600' : 'text-green-600' }}
                                        hover:underline">
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                    @can('delete users')
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                                  onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                                                @csrf @method('DELETE')
                                                <button class="text-xs text-red-500 hover:underline font-medium">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                @endif
                            </div>
                        </td>

                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400">
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
