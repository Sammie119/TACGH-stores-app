{{-- resources/views/products/_form.blade.php --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    {{-- ── Basic details ── --}}
    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Basic details
        </p>
        <div class="space-y-4">

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Product name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name"
                       value="{{ old('name', $product?->name) }}"
                       placeholder="e.g. Samsung Galaxy A54"
                       class="w-full h-10 px-3 rounded-lg border text-sm text-gray-800
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- SKU + Barcode --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="sku"
                           value="{{ old('sku', $product?->sku) }}"
                           placeholder="e.g. ELEC-001"
                           class="w-full h-10 px-3 rounded-lg border text-sm text-gray-800
                                  font-mono focus:outline-none focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('sku') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    @error('sku')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Barcode
                        <span class="text-xs text-gray-400 font-normal ml-1">(optional)</span>
                    </label>
                    <input type="text" name="barcode"
                           value="{{ old('barcode', $product?->barcode) }}"
                           placeholder="e.g. 6001234567890"
                           class="w-full h-10 px-3 rounded-lg border text-sm text-gray-800
                                  font-mono focus:outline-none focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('barcode') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    @error('barcode')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Category + Unit --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="category_id"
                                class="w-full h-10 px-3 pr-8 rounded-lg border bg-white
                                       text-sm text-gray-800 focus:outline-none
                                       focus:ring-2 focus:ring-blue-500 appearance-none
                                       {{ $errors->has('category_id') ? 'border-red-400' : 'border-gray-300' }}">
                            <option value="">— Select category —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product?->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </div>
                    @error('category_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Unit <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="unit"
                                class="w-full h-10 px-3 pr-8 rounded-lg border border-gray-300
                                       bg-white text-sm text-gray-800 focus:outline-none
                                       focus:ring-2 focus:ring-blue-500 appearance-none">
                            @foreach(['piece','kg','g','litre','ml','box','carton','pack','dozen','pair','set','roll','bag','bottle','tin'] as $unit)
                                <option value="{{ $unit }}"
                                    {{ old('unit', $product?->unit) === $unit ? 'selected' : '' }}>
                                    {{ ucfirst($unit) }}
                                </option>
                            @endforeach
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
                          placeholder="Product description…"
                          class="w-full px-3 py-2.5 rounded-lg border border-gray-300 bg-white
                                 text-sm text-gray-800 resize-none focus:outline-none
                                 focus:ring-2 focus:ring-blue-500">{{ old('description', $product?->description) }}</textarea>
            </div>

        </div>
    </div>

    {{-- ── Pricing & stock ── --}}
    <div class="p-6 border-t border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Pricing & stock
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">

            {{-- Cost price --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Cost price (GHS) <span class="text-red-500">*</span>
                </label>
                <div style="position:relative">
                <span style="position:absolute;left:12px;top:50%;
                             transform:translateY(-50%);font-size:13px;
                             font-weight:600;color:#9ca3af;pointer-events:none">
                    GHS
                </span>
                    <input type="number" name="cost_price" step="0.01" min="0"
                           value="{{ old('cost_price', $product?->cost_price) }}"
                           placeholder="0.00"
                           style="width:100%;height:40px;padding:0 12px 0 52px;
                              border:1px solid {{ $errors->has('cost_price') ? '#fca5a5' : '#d1d5db' }};
                              border-radius:8px;font-size:14px;color:#111827;
                              background:{{ $errors->has('cost_price') ? '#fef2f2' : '#fff' }};
                              box-sizing:border-box;outline:none"
                           onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
                           onblur="this.style.borderColor='{{ $errors->has('cost_price') ? '#fca5a5' : '#d1d5db' }}';this.style.boxShadow='none'">
                </div>
                @error('cost_price')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Selling price --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Selling price (GHS) <span class="text-red-500">*</span>
                </label>
                <div style="position:relative">
                <span style="position:absolute;left:12px;top:50%;
                             transform:translateY(-50%);font-size:13px;
                             font-weight:600;color:#9ca3af;pointer-events:none">
                    GHS
                </span>
                    <input type="number" name="selling_price" step="0.01" min="0"
                           value="{{ old('selling_price', $product?->selling_price) }}"
                           placeholder="0.00"
                           style="width:100%;height:40px;padding:0 12px 0 52px;
                              border:1px solid {{ $errors->has('selling_price') ? '#fca5a5' : '#d1d5db' }};
                              border-radius:8px;font-size:14px;color:#111827;
                              background:{{ $errors->has('selling_price') ? '#fef2f2' : '#fff' }};
                              box-sizing:border-box;outline:none"
                           onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
                           onblur="this.style.borderColor='{{ $errors->has('selling_price') ? '#fca5a5' : '#d1d5db' }}';this.style.boxShadow='none'">
                </div>
                @error('selling_price')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Reorder level --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Reorder level <span class="text-red-500">*</span>
                </label>
                <input type="number" name="reorder_level" min="0"
                       value="{{ old('reorder_level', $product?->reorder_level ?? 10) }}"
                       placeholder="e.g. 10"
                       style="width:100%;height:40px;padding:0 12px;
                          border:1px solid {{ $errors->has('reorder_level') ? '#fca5a5' : '#d1d5db' }};
                          border-radius:8px;font-size:14px;color:#111827;
                          background:{{ $errors->has('reorder_level') ? '#fef2f2' : '#fff' }};
                          box-sizing:border-box;outline:none"
                       onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
                       onblur="this.style.borderColor='{{ $errors->has('reorder_level') ? '#fca5a5' : '#d1d5db' }}';this.style.boxShadow='none'">
                <p class="mt-1 text-xs text-gray-400">Alert when stock falls below this</p>
                @error('reorder_level')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    {{-- ── Image & status ── --}}
    <div class="p-6 border-t border-gray-100">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Image & status
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Product image
                    <span class="text-xs text-gray-400 font-normal ml-1">(optional, max 2MB)</span>
                </label>
                @if($product?->image)
                    <div style="margin-bottom:10px">
                        <img src="{{ branch_logo_url($product->image) }}"
                             alt="{{ $product->name }}"
                             style="width:80px;height:80px;object-fit:cover;border-radius:8px;
                                border:1px solid #e5e7eb">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                       class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4
                              file:rounded-lg file:border-0 file:text-sm file:font-medium
                              file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                @error('image')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">Status</label>
                <div style="display:flex;align-items:center;gap:12px;margin-top:8px"
                     x-data="{ on: {{ old('is_active', $product?->is_active ?? true) ? 'true' : 'false' }} }">
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
                           x-text="on ? 'Product is available for sale' : 'Product is hidden'"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Danger zone (edit only) --}}
    @if($product)
        @can('delete products')
            <div class="p-6 border-t border-red-100 bg-red-50">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-4">
                    Danger zone
                </p>
                <div class="bg-white border border-red-200 rounded-lg p-4"
                     style="display:flex;align-items:center;justify-content:space-between;gap:16px">
                    <div>
                        <p class="text-sm font-medium text-red-700">Delete this product</p>
                        <p class="text-xs text-red-400 mt-0.5">
                            Soft deleted — stock history is preserved.
                        </p>
                    </div>
                    <button type="submit" form="delete-form"
                            class="h-9 px-4 bg-white border border-red-300 text-red-600
                           text-sm font-medium rounded-lg transition-colors
                           hover:bg-red-600 hover:text-white hover:border-red-600">
                        Delete product
                    </button>
                </div>
            </div>
        @endcan
    @endif

    {{-- Footer --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
         style="display:flex;align-items:center;gap:12px">
        <button type="submit"
                form="main-form"
                class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                   font-medium rounded-lg transition-colors focus:outline-none
                   focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            {{ $product ? 'Save changes' : 'Create product' }}
        </button>
        <a href="{{ route('products.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
              font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>
        @if($product)
            <a href="{{ route('products.show', $product) }}"
               class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
               style="margin-left:auto">
                View product →
            </a>
        @endif
    </div>

</div>
