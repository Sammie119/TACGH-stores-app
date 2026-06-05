<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    {{-- ── Basic information ── --}}
    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Basic information
        </p>
        <div class="space-y-4">

            {{-- Name + Code --}}
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Branch name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', $branch?->name) }}"
                           class="w-full h-10 px-3 rounded-lg border text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code"
                           value="{{ old('code', $branch?->code) }}"
                           maxlength="10"
                           class="w-full h-10 px-3 rounded-lg border text-sm text-gray-800
                                  font-mono uppercase tracking-widest
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('code') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    @error('code')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Phone + Email --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Phone number
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </span>
                        <input type="text" name="phone"
                               value="{{ old('phone', $branch?->phone) }}"
                               placeholder="030-000-0001"
                               class="w-full h-10 pl-9 pr-3 rounded-lg border border-gray-300
                                      bg-white text-sm text-gray-800 focus:outline-none
                                      focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Email address
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input type="email" name="email"
                               value="{{ old('email', $branch?->email) }}"
                               placeholder="branch@example.com"
                               class="w-full h-10 pl-9 pr-3 rounded-lg border border-gray-300
                                      bg-white text-sm text-gray-800 focus:outline-none
                                      focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Physical address
                </label>
                <textarea name="address" rows="3"
                          placeholder="Street, city, region…"
                          class="w-full px-3 py-2.5 rounded-lg border border-gray-300 bg-white
                                 text-sm text-gray-800 resize-none focus:outline-none
                                 focus:ring-2 focus:ring-blue-500">{{ old('address', $branch?->address) }}</textarea>
            </div>

        </div>
    </div>

    {{-- ── Logo ── --}}
    <div class="p-6 border-t border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Branch logo
        </p>
        <div class="space-y-4">

            @if(isset($branch) && $branch?->logo)
                {{-- Current logo --}}
                <div style="display:flex;align-items:center;gap:14px;padding:14px;
                    background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px">
                    <img src="{{ Storage::url($branch->logo) }}"
                         alt="{{ $branch->name }} logo"
                         style="width:64px;height:64px;object-fit:contain;
                        border-radius:8px;border:1px solid #e5e7eb;
                        background:#fff;padding:4px">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Current logo</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Upload a new image below to replace it
                        </p>
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    {{ isset($branch) && $branch?->logo ? 'Replace logo' : 'Upload logo' }}
                    <span class="text-xs text-gray-400 font-normal ml-1">
                    (PNG, JPG — max 2MB, recommended 200×200px)
                </span>
                </label>
                <input type="file"
                       name="logo"
                       form="main-form"
                       accept="image/*"
                       class="w-full text-sm text-gray-600
                          file:mr-3 file:py-2 file:px-4 file:rounded-lg
                          file:border-0 file:text-sm file:font-medium
                          file:bg-blue-50 file:text-blue-600
                          hover:file:bg-blue-100">
                @error('logo')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    {{-- ── Management ── --}}
    <div class="p-6 border-t border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Management
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

            {{-- Manager --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Branch manager
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    <select name="manager_id"
                            class="w-full h-10 pl-9 pr-8 rounded-lg border border-gray-300
                                   bg-white text-sm text-gray-800 focus:outline-none
                                   focus:ring-2 focus:ring-blue-500 appearance-none">
                        <option value="">— No manager assigned —</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}"
                                {{ old('manager_id', $branch?->manager_id) == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>
                </div>
                <p class="mt-1 text-xs text-gray-400">Can be assigned later</p>
            </div>

            {{-- Status toggle --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Branch status
                </label>
                <div style="display:flex;align-items:center;gap:12px;margin-top:8px"
                     x-data="{ on: {{ old('is_active', $branch?->is_active) ? 'true' : 'false' }} }">
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
                           x-text="on ? 'Branch is operational' : 'Branch is disabled'"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Danger zone (edit only) --}}
    @if($branch)
        @can('delete branches')
            <div class="p-6 border-t border-red-100 bg-red-50">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-4">
                    Danger zone
                </p>
                <div class="bg-white border border-red-200 rounded-lg p-4"
                     style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <div>
                        <p class="text-sm font-medium text-red-700">Delete this category</p>
                        <p class="text-xs text-red-400 mt-0.5">
                            Products in this category will not be deleted.
                        </p>
                    </div>
                    {{-- Button submits the EXTERNAL delete form, not this form --}}
                    <button type="submit" form="delete-form"
                            class="h-9 px-4 bg-white border border-red-300 text-red-600
                           text-sm font-medium rounded-lg transition-colors
                           hover:bg-red-600 hover:text-white hover:border-red-600">
                        Delete branch
                    </button>
                </div>
            </div>
        @endcan
    @endif

    {{-- ── Footer ── --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
         style="display:flex;align-items:center;gap:12px">
        <button type="submit"
                class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors focus:outline-none
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            {{ $branch ? 'Save changes' : 'Create branch' }}
        </button>
        <a href="{{ route('branches.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                  font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>
        @if($branch)
            <a href="{{ route('branches.show', $branch) }}"
               class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
               style="margin-left:auto">
                View branch →
            </a>
        @endif
    </div>

</div>
