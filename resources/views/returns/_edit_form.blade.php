{{-- resources/views/returns/_edit_form.blade.php --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    {{-- Sale info banner --}}
    <div style="padding:16px 24px;background:#f0fdf4;border-bottom:1px solid #bbf7d0">
        <p class="text-xs font-semibold text-green-700 uppercase tracking-widest mb-2">
            Original sale
        </p>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px"
             class="text-sm">
            <div>
                <p class="text-xs text-green-600">Invoice</p>
                <p class="font-mono font-semibold text-green-800">
                    {{ $return->sale?->invoice_no }}
                </p>
            </div>
            <div>
                <p class="text-xs text-green-600">Product</p>
                <p class="font-medium text-green-800">
                    {{ $return->product?->name }}
                </p>
            </div>
            <div>
                <p class="text-xs text-green-600">Branch</p>
                <p class="font-medium text-green-800">
                    {{ $return->branch?->name }}
                </p>
            </div>
            <div>
                <p class="text-xs text-green-600">Refund amount</p>
                <p class="font-semibold text-green-800">
                    GHS {{ number_format($return->refund_amount, 2) }}
                </p>
            </div>
        </div>
    </div>

    {{-- Editable fields --}}
    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Return details
        </p>
        <div class="space-y-4">

            {{-- Quantity + Type --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="quantity"
                           value="{{ old('quantity', $return->quantity) }}"
                           min="0.01" step="0.01"
                           class="w-full h-10 px-3 rounded-lg border text-sm
                                  text-gray-800 focus:outline-none
                                  focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('quantity') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    @error('quantity')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Return type <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="type"
                                class="w-full h-10 px-3 pr-8 rounded-lg border
                                       border-gray-300 bg-white text-sm text-gray-800
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500 appearance-none">
                            <option value="refund"
                                {{ old('type', $return->type) === 'refund' ? 'selected' : '' }}>
                                Refund — stock returned
                            </option>
                            <option value="exchange"
                                {{ old('type', $return->type) === 'exchange' ? 'selected' : '' }}>
                                Exchange — swap product
                            </option>
                            <option value="damaged"
                                {{ old('type', $return->type) === 'damaged' ? 'selected' : '' }}>
                                Damaged — write off
                            </option>
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
                    @error('type')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Reason --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Reason
                    <span class="text-xs text-gray-400 font-normal ml-1">
                        (optional)
                    </span>
                </label>
                <textarea name="reason" rows="4"
                          placeholder="Describe the reason for return…"
                          class="w-full px-3 py-2.5 rounded-lg border border-gray-300
                                 bg-white text-sm text-gray-800 resize-none
                                 focus:outline-none focus:ring-2
                                 focus:ring-blue-500">{{ old('reason', $return->reason) }}</textarea>
            </div>

        </div>
    </div>

    {{-- Danger zone --}}
    @if($return->status === 'pending')
        @can('create returns')
            <div class="p-6 border-t border-red-100 bg-red-50">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-4">
                    Danger zone
                </p>
                <div class="bg-white border border-red-200 rounded-lg p-4"
                     style="display:flex;align-items:center;
                    justify-content:space-between;gap:16px">
                    <div>
                        <p class="text-sm font-medium text-red-700">
                            Delete this return request
                        </p>
                        <p class="text-xs text-red-400 mt-0.5">
                            Soft deleted — can be restored later.
                        </p>
                    </div>
                    <button type="submit" form="delete-form"
                            class="h-9 px-4 bg-white border border-red-300 text-red-600
                           text-sm font-medium rounded-lg transition-colors
                           hover:bg-red-600 hover:text-white hover:border-red-600">
                        Delete return
                    </button>
                </div>
            </div>
        @endcan
    @endif

    {{-- Footer --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
         style="display:flex;align-items:center;gap:12px">
        <button type="submit" form="main-form"
                class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors focus:outline-none
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Save changes
        </button>
        <a href="{{ route('returns.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                  font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>
        <a href="{{ route('returns.show', $return) }}"
           class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
           style="margin-left:auto">
            View return →
        </a>
    </div>

</div>
