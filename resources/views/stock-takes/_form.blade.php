{{-- resources/views/stock-takes/_form.blade.php --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Stock take details
        </p>

        <div class="space-y-5">

            {{-- Branch --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Branch <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="branch_id"
                            class="w-full h-10 px-3 pr-8 rounded-lg border bg-white
                                   text-sm text-gray-800 focus:outline-none
                                   focus:ring-2 focus:ring-blue-500 appearance-none
                                   {{ $errors->has('branch_id') ? 'border-red-400' : 'border-gray-300' }}">
                        <option value="">— Select branch —</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ old('branch_id', auth()->user()->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center
                                 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>
                </div>
                @error('branch_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Category filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Product category
                    <span class="text-xs text-gray-400 font-normal ml-1">
                        (optional — leave blank to count all products)
                    </span>
                </label>
                <div class="relative">
                    <select name="category_id"
                            class="w-full h-10 px-3 pr-8 rounded-lg border
                                   border-gray-300 bg-white text-sm text-gray-800
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-500 appearance-none">
                        <option value="">— All categories —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center
                                 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Notes
                    <span class="text-xs text-gray-400 font-normal ml-1">
                        (optional)
                    </span>
                </label>
                <textarea name="notes" rows="3"
                          placeholder="e.g. End of month stock count — Electronics section"
                          class="w-full px-3 py-2.5 rounded-lg border border-gray-300
                                 bg-white text-sm text-gray-800 resize-none
                                 focus:outline-none focus:ring-2
                                 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>

            {{-- Info box --}}
            <div style="padding:14px 16px;background:#eff6ff;border:1px solid #bfdbfe;
                        border-radius:8px">
                <p class="text-sm font-medium text-blue-800 mb-1">
                    How it works
                </p>
                <ul style="list-style:none;padding:0;margin:0;
                           display:flex;flex-direction:column;gap:4px">
                    @foreach([
                        'All active products in the selected branch will be loaded',
                        'You physically count each product and enter the quantity',
                        'The system calculates variances automatically',
                        'A manager reviews and approves the count',
                        'On approval, stock levels are adjusted to match your count',
                    ] as $step)
                        <li style="font-size:12px;color:#1e40af;
                               display:flex;align-items:flex-start;gap:6px">
                            <span style="color:#3b82f6;flex-shrink:0">→</span>
                            {{ $step }}
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>

    {{-- Footer --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
         style="display:flex;align-items:center;gap:12px">
        <button type="submit" form="main-form"
                class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors focus:outline-none
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Start stock take
        </button>
        <a href="{{ route('stock-takes.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                  font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>
    </div>

</div>
