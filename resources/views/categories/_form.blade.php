{{-- resources/views/categories/_form.blade.php --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Category details
        </p>
        <div class="space-y-4">

            {{-- Name + Parent --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Category name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', $category?->name) }}"
                           placeholder="e.g. Electronics"
                           class="w-full h-10 px-3 rounded-lg border text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Parent category
                        <span class="text-xs text-gray-400 font-normal ml-1">(optional)</span>
                    </label>
                    <div class="relative">
                        <select name="parent_id"
                                class="w-full h-10 px-3 pr-8 rounded-lg border border-gray-300
                                       bg-white text-sm text-gray-800 focus:outline-none
                                       focus:ring-2 focus:ring-blue-500 appearance-none">
                            <option value="">— Top level category —</option>
                            @forelse($parentCategories as $parent)
                                <option value="{{ $parent?->id }}"
                                    {{ old('parent_id', $category?->parent_id) == $parent?->id ? 'selected' : '' }}>
                                    {{ $parent?->name }}
                                </option>
                            @empty
                                <option value="">— Top level category —</option>
                            @endforelse
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Description
                    <span class="text-xs text-gray-400 font-normal ml-1">(optional)</span>
                </label>
                <textarea name="description" rows="3"
                          placeholder="Brief description of this category…"
                          class="w-full px-3 py-2.5 rounded-lg border border-gray-300 bg-white
                                 text-sm text-gray-800 resize-none focus:outline-none
                                 focus:ring-2 focus:ring-blue-500">{{ old('description', $category?->description) }}</textarea>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">Status</label>
                <div style="display:flex;align-items:center;gap:12px;margin-top:4px"
                     x-data="{ on: {{ old('is_active', $category?->is_active ?? true) ? 'true' : 'false' }} }">
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
                           x-text="on ? 'Category is visible' : 'Category is hidden'"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Danger zone (edit only) --}}
    @if($category)
        @can('delete products')
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
                        Delete category
                    </button>
                </div>
            </div>
        @endcan
    @endif

    {{-- Footer --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
         style="display:flex;align-items:center;gap:12px">
        <button type="submit"
                class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors focus:outline-none
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            {{ $category ? 'Save changes' : 'Create category' }}
        </button>
        <a href="{{ route('categories.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                  font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>
    </div>

</div>
