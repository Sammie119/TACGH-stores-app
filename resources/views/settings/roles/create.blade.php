{{-- resources/views/settings/roles/create.blade.php --}}
@extends('layouts.app')
@section('title', 'New Role')
@section('header', 'New Role')
@section('subheader', 'Create a custom role and assign permissions')

@section('content')

    <form method="POST" action="{{ route('roles.store') }}" id="main-form">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start">

            {{-- Role name --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase
                   tracking-widest mb-4">
                    Role details
                </p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">
                            Role name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name"
                               value="{{ old('name') }}"
                               placeholder="e.g. cashier or inventory_clerk"
                               class="w-full h-10 px-3 rounded-lg border border-gray-300
                              bg-white text-sm text-gray-800 focus:outline-none
                              focus:ring-2 focus:ring-blue-500
                              {{ $errors->has('name') ? 'border-red-400' : '' }}">
                        @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">
                            Spaces will be replaced with underscores.
                            Use lowercase letters only.
                        </p>
                    </div>

                    <div style="padding:12px;background:#fffbeb;border:1px solid #fde68a;
                        border-radius:8px">
                        <p class="text-xs font-medium text-amber-700 mb-1">
                            System roles
                        </p>
                        <p class="text-xs text-amber-600">
                            The following role names are reserved and cannot be used:
                            super_admin, regional_manager, branch_admin,
                            store_manager, sales_officer, stock_officer,
                            auditor, accountant
                        </p>
                    </div>
                </div>

                <div style="margin-top:20px;display:flex;gap:10px">
                    <button type="submit" form="main-form"
                            class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white
                           text-sm font-medium rounded-lg transition-colors
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Create role
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
                    <p class="font-semibold text-gray-700">Assign permissions</p>
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
                        'rolePermissions' => old('permissions', []),
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
                document.querySelectorAll(`.group-${groupName}`)
                    .forEach(cb => cb.checked = check);
            }
        </script>
    @endpush

@endsection
