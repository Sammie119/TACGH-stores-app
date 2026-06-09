{{-- resources/views/deposits/_form.blade.php --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    {{-- Deposit details --}}
    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Deposit details
        </p>
        <div class="space-y-4">

            {{-- Branch + Date --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
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
                                    {{ old('branch_id', $deposit?->branch_id) == $branch->id ? 'selected' : '' }}>
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
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Deposit date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="deposit_date"
                           value="{{ old('deposit_date', $deposit?->deposit_date?->format('Y-m-d') ?? today()->format('Y-m-d')) }}"
                           class="w-full h-10 px-3 rounded-lg border text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('deposit_date') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                    @error('deposit_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Amount --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Amount deposited (GHS) <span class="text-red-500">*</span>
                </label>
                <div style="position:relative">
                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);
                                 font-size:13px;font-weight:600;color:#9ca3af;pointer-events:none;
                                 z-index:1">
                        GHS
                    </span>
                    <input type="number" name="amount" step="0.01" min="0.01"
                           value="{{ old('amount', $deposit?->amount) }}"
                           placeholder="0.00"
                           style="width:100%;height:40px;padding:0 12px 0 52px;
                      border:1px solid {{ $errors->has('amount') ? '#fca5a5' : '#d1d5db' }};
                      border-radius:8px;font-size:14px;color:#111827;
                      background:{{ $errors->has('amount') ? '#fef2f2' : '#fff' }};
                      box-sizing:border-box;outline:none"
                           onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
                           onblur="this.style.borderColor='{{ $errors->has('amount') ? '#fca5a5' : '#d1d5db' }}';this.style.boxShadow='none'">
                </div>
                @error('amount')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bank + Reference --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Bank name
                        <span class="text-xs text-gray-400 font-normal ml-1">
                            (optional)
                        </span>
                    </label>
                    <input type="text" name="bank_name"
                           value="{{ old('bank_name', $deposit?->bank_name) }}"
                           placeholder="e.g. GCB Bank"
                           class="w-full h-10 px-3 rounded-lg border border-gray-300
                                  bg-white text-sm text-gray-800 focus:outline-none
                                  focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Reference / teller no
                        <span class="text-xs text-gray-400 font-normal ml-1">
                            (optional)
                        </span>
                    </label>
                    <input type="text" name="reference_no"
                           value="{{ old('reference_no', $deposit?->reference_no) }}"
                           placeholder="e.g. TLR-20240501-001"
                           class="w-full h-10 px-3 rounded-lg border border-gray-300
                                  bg-white text-sm font-mono text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Slip image --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Pay-in slip image
                    {{ $deposit ? '' : '*' }}
                    <span class="text-xs text-gray-400 font-normal ml-1">
                        (JPG, PNG — max 4MB)
                    </span>
                </label>

                @if($deposit?->slip_image)
                    <div style="margin-bottom:10px">
                        <a href="{{ branch_logo_url($deposit->slip_image) }}"
                           target="_blank">
                            <img src="{{ branch_logo_url($deposit->slip_image) }}"
                                 alt="Current slip"
                                 style="max-height:120px;border-radius:8px;
                                    border:1px solid #e5e7eb;object-fit:cover">
                        </a>
                        <p class="text-xs text-gray-400 mt-1">
                            Click image to view full size.
                            Upload a new one below to replace.
                        </p>
                    </div>
                @endif

                <input type="file" name="slip_image" accept="image/*"
                       class="w-full text-sm text-gray-600
                              file:mr-3 file:py-2 file:px-4 file:rounded-lg
                              file:border-0 file:text-sm file:font-medium
                              file:bg-blue-50 file:text-blue-600
                              hover:file:bg-blue-100">
                @error('slip_image')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Notes
                    <span class="text-xs text-gray-400 font-normal ml-1">
                        (optional)
                    </span>
                </label>
                <textarea name="notes" rows="2"
                          placeholder="Any additional notes…"
                          class="w-full px-3 py-2.5 rounded-lg border border-gray-300
                                 bg-white text-sm text-gray-800 resize-none
                                 focus:outline-none focus:ring-2
                                 focus:ring-blue-500">{{ old('notes', $deposit?->notes) }}</textarea>
            </div>

        </div>
    </div>

    {{-- Danger zone (edit only) --}}
    @if($deposit && $deposit->status === 'pending')
        @can('create deposits')
            <div class="p-6 border-t border-red-100 bg-red-50">
                <p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-4">
                    Danger zone
                </p>
                <div class="bg-white border border-red-200 rounded-lg p-4"
                     style="display:flex;align-items:center;
                    justify-content:space-between;gap:16px">
                    <div>
                        <p class="text-sm font-medium text-red-700">Delete this deposit</p>
                        <p class="text-xs text-red-400 mt-0.5">
                            Soft deleted — can be restored later.
                        </p>
                    </div>
                    <button type="submit" form="delete-form"
                            class="h-9 px-4 bg-white border border-red-300 text-red-600
                           text-sm font-medium rounded-lg transition-colors
                           hover:bg-red-600 hover:text-white hover:border-red-600">
                        Delete deposit
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
            {{ $deposit ? 'Save changes' : 'Submit deposit' }}
        </button>
        <a href="{{ route('deposits.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                  font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>
        @if($deposit)
            <a href="{{ route('deposits.show', $deposit) }}"
               class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
               style="margin-left:auto">
                View deposit →
            </a>
        @endif
    </div>

</div>
