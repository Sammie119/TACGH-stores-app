{{-- resources/views/users/_form.blade.php --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    {{-- ── Personal details ── --}}
    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Personal details
        </p>
        <div class="space-y-4">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Full name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <input type="text" name="name"
                               value="{{ old('name', $user?->name) }}"
                               placeholder="e.g. John Mensah"
                               class="w-full h-10 pl-9 pr-3 rounded-lg border text-sm text-gray-800
                                      focus:outline-none focus:ring-2 focus:ring-blue-500
                                      {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    </div>
                    @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Email address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input type="email" name="email"
                               value="{{ old('email', $user?->email) }}"
                               placeholder="user@example.com"
                               class="w-full h-10 pl-9 pr-3 rounded-lg border text-sm text-gray-800
                                      focus:outline-none focus:ring-2 focus:ring-blue-500
                                      {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    </div>
                    @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Password --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        {{ $user ? 'New password' : 'Password' }}
                        @if($user)
                            <span class="text-xs text-gray-400 font-normal ml-1">
                                (leave blank to keep current)
                            </span>
                        @else
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input type="password" name="password"
                               placeholder="Min. 8 characters"
                               class="w-full h-10 pl-9 pr-3 rounded-lg border text-sm text-gray-800
                                      focus:outline-none focus:ring-2 focus:ring-blue-500
                                      {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    </div>
                    @error('password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        {{ $user ? 'Confirm new password' : 'Confirm password' }}
                        @if(!$user)<span class="text-red-500">*</span>@endif
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input type="password" name="password_confirmation"
                               placeholder="Repeat password"
                               class="w-full h-10 pl-9 pr-3 rounded-lg border border-gray-300
                                      bg-white text-sm text-gray-800 focus:outline-none
                                      focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Access & assignment ── --}}
    <div class="p-6 border-t border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Access & assignment
        </p>
        <div class="space-y-4">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Assigned branch
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </span>
                        <select name="branch_id"
                                class="w-full h-10 pl-9 pr-8 rounded-lg border border-gray-300
                                       bg-white text-sm text-gray-800 focus:outline-none
                                       focus:ring-2 focus:ring-blue-500 appearance-none">
                            <option value="">— No branch assigned —</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('branch_id', $user?->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Super admins can leave this empty</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Account status
                    </label>
                    <div style="display:flex;align-items:center;gap:12px;margin-top:8px"
                         x-data="{ on: {{ old('is_active', $user?->is_active ?? true) ? 'true' : 'false' }} }">
                        <input type="hidden" name="is_active" value="0">
                        <button type="button" @click="on = !on"
                                :class="on ? 'bg-blue-600' : 'bg-gray-300'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full
                                       cursor-pointer transition-colors duration-200
                                       focus:outline-none focus:ring-2 focus:ring-blue-500
                                       focus:ring-offset-2 border-2 border-transparent">
                            <input type="checkbox" name="is_active" value="1"
                                   x-model="on" class="sr-only">
                            <span :class="on ? 'translate-x-5' : 'translate-x-0'"
                                  class="pointer-events-none inline-block h-5 w-5 rounded-full
                                         bg-white shadow transform transition duration-200"></span>
                        </button>
                        <div>
                            <p class="text-sm font-medium text-gray-700"
                               x-text="on ? 'Active' : 'Inactive'"></p>
                            <p class="text-xs text-gray-400"
                               x-text="on ? 'User can log in' : 'User is blocked'"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Roles --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-3">
                    Roles <span class="text-red-500">*</span>
                    <span class="text-xs text-gray-400 font-normal ml-1">(select one or more)</span>
                </label>
                @error('roles')
                <p class="mb-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
                    @foreach($roles as $role)
                        <label style="display:flex;align-items:flex-start;gap:10px;padding:12px;
                                  border:1px solid #e5e7eb;border-radius:8px;cursor:pointer;
                                  transition:border-color .15s"
                               onmouseover="this.style.borderColor='#93c5fd'"
                               onmouseout="this.style.borderColor='#e5e7eb'">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                   {{ in_array($role->name, old('roles', $user?->roles->pluck('name')->toArray() ?? [])) ? 'checked' : '' }}
                                   class="mt-0.5 rounded border-gray-300 text-blue-600
                                      focus:ring-blue-500">
                            <div>
                                <p class="text-sm font-medium text-gray-700">
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @switch($role->name)
                                        @case('super_admin') Full system access @break
                                        @case('branch_admin') Branch oversight @break
                                        @case('store_manager') Store operations @break
                                        @case('sales_officer') POS & sales @break
                                        @case('stock_officer') Stock management @break
                                        @case('auditor') View & audit only @break
                                        @case('accountant') Finance & reports @break
                                        @default {{ $role->name }}
                                    @endswitch
                                </p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    {{-- ── Danger zone (edit only) ── --}}
    @if($user)
        @can('delete users')
            @if($user->id !== auth()->id())
                <div class="p-6 border-t border-red-100 bg-red-50">
                    <p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-4">
                        Danger zone
                    </p>
                    <div class="bg-white border border-red-200 rounded-lg p-4"
                         style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                        <div>
                            <p class="text-sm font-medium text-red-700">Delete this user</p>
                            <p class="text-xs text-red-400 mt-0.5">
                                Soft deleted — user history is preserved.
                            </p>
                        </div>
                        {{-- References the external delete form in edit.blade.php --}}
                        <button type="submit" form="delete-form"
                                class="h-9 px-4 bg-white border border-red-300 text-red-600
                                   text-sm font-medium rounded-lg transition-colors
                                   hover:bg-red-600 hover:text-white hover:border-red-600">
                            Delete user
                        </button>
                    </div>
                </div>
            @endif
        @endcan
    @endif

    {{-- ── Footer ── --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
         style="display:flex;align-items:center;gap:12px">

        {{-- ✅ form="main-form" connects this to the outer form in create/edit.blade.php --}}
        <button type="submit" form="main-form"
                class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors focus:outline-none
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            {{ $user ? 'Save changes' : 'Create user' }}
        </button>

        <a href="{{ route('users.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                  font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>

        {{-- ✅ Only show on edit, not create --}}
        @if($user)
            <a href="{{ route('users.show', $user) }}"
               class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
               style="margin-left:auto">
                View profile →
            </a>
        @endif

    </div>

</div>
