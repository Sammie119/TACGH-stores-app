{{-- resources/views/financial-years/_form.blade.php --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Financial year details
        </p>
        <div class="space-y-4">

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name"
                       value="{{ old('name', $financialYear?->name) }}"
                       placeholder="e.g. FY 2025/2026"
                       class="w-full h-10 px-3 rounded-lg border text-sm text-gray-800
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Start + End date --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Start date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date"
                           value="{{ old('start_date', $financialYear?->start_date?->format('Y-m-d')) }}"
                           class="w-full h-10 px-3 rounded-lg border text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('start_date') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    @error('start_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        End date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="end_date"
                           value="{{ old('end_date', $financialYear?->end_date?->format('Y-m-d')) }}"
                           class="w-full h-10 px-3 rounded-lg border text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('end_date') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    @error('end_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Info note --}}
            <div style="padding:12px 14px;background:#eff6ff;border:1px solid #bfdbfe;
                        border-radius:8px">
                <p style="font-size:12px;color:#1e40af">
                    <strong>Note:</strong> Once a financial year is closed,
                    all transactions within that period are locked and cannot
                    be modified. Make sure all entries are correct before closing.
                </p>
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
            {{ $financialYear ? 'Save changes' : 'Create financial year' }}
        </button>
        <a href="{{ route('financial-years.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                  font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>
    </div>

</div>
