{{-- resources/views/settings/roles/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Role — ' . ucfirst(str_replace('_', ' ', $role->name)))
@section('header', 'Edit Role')
@section('subheader', 'Update permissions for: ' . ucfirst(str_replace('_', ' ', $role->name)))

@section('content')

    <form method="POST" action="{{ route('roles.update', $role) }}" id="main-form">
        @csrf @method('PUT')

        <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start">

            {{-- Role info --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase
                   tracking-widest mb-4">
                    Role info
                </p>

                <div style="padding:14px;background:#f9fafb;border:1px solid #e5e7eb;
                    border-radius:8px;margin-bottom:16px">
                    <p class="text-sm font-semibold text-gray-800">
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $role->users()->count() }}
                        user{{ $role->users()->count() !== 1 ? 's' : '' }}
                        currently assigned
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $role->permissions->count() }} permissions assigned
                    </p>
                </div>

                <div style="margin-top:20px;display:flex;gap:10px">
                    <button type="submit" form="main-form"
                            class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white
                           text-sm font-medium rounded-lg transition-colors
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Save permissions
                    </button>
                    <a href="{{ route('roles.index') }}"
                       class="h-10 px-5 bg-white border border-gray-300 hover:bg-gray-50
                      text-gray-600 text-sm font-medium rounded-lg transition-colors"
                       style="display:inline-flex;align-items:center">
                        Cancel
                    </a>
                </div>
            </div>

            {{-- Permissions --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100"
                     style="display:flex;align-items:center;justify-content:space-between">
                    <p class="font-semibold text-gray-700">Permissions</p>
                    <div style="display:flex;gap:8px">
                        <button type="button"
                                onclick="toggleAll(true)"
                                class="text-xs text-blue-600 hover:underline">
                            Select all
                        </button>
                        <span class="text-gray-300">|</span>
                        <button type="button"
                                onclick="toggleAll(false)"
                                class="text-xs text-gray-400 hover:underline">
                            Clear all
                        </button>
                    </div>
                </div>

                <div style="padding:20px;max-height:600px;overflow-y:auto">
                    @include('settings.roles._permissions_grid', [
                        'permissions'     => $permissions,
                        'rolePermissions' => $rolePermissions,
                    ])
                </div>
            </div>

        </div>
    </form>

    @push('scripts')
        <script>
            function toggleAll(check) {
                document.querySelectorAll('input[name="permissions[]"]')
                    .forEach(cb => cb.checked = check);
            }

            function toggleGroup(groupName, check) {
                document.querySelectorAll(`.group-${groupName.replace(/\s+/g, '-')}`)
                    .forEach(cb => cb.checked = check);
            }
        </script>
    @endpush

@endsection
