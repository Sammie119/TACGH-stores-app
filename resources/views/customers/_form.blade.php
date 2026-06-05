{{-- resources/views/customers/_form.blade.php --}}
<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

    <div class="p-6">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
            Customer details
        </p>
        <div class="space-y-4">

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Full name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center
                                 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.5"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    <input type="text" name="name"
                           value="{{ old('name', $customer?->name) }}"
                           placeholder="e.g. John Mensah"
                           class="w-full h-10 pl-9 pr-3 rounded-lg border text-sm
                                  text-gray-800 focus:outline-none
                                  focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                </div>
                @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone + Email --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Phone number
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center
                                     pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </span>
                        <input type="text" name="phone"
                               value="{{ old('phone', $customer?->phone) }}"
                               placeholder="e.g. 024-000-0001"
                               class="w-full h-10 pl-9 pr-3 rounded-lg border
                                      border-gray-300 bg-white text-sm text-gray-800
                                      focus:outline-none focus:ring-2
                                      focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Email address
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center
                                     pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input type="email" name="email"
                               value="{{ old('email', $customer?->email) }}"
                               placeholder="customer@example.com"
                               class="w-full h-10 pl-9 pr-3 rounded-lg border
                                      border-gray-300 bg-white text-sm text-gray-800
                                      focus:outline-none focus:ring-2
                                      focus:ring-blue-500">
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                    Address
                    <span class="text-xs text-gray-400 font-normal ml-1">
                        (optional)
                    </span>
                </label>
                <textarea name="address" rows="3"
                          placeholder="Customer address…"
                          class="w-full px-3 py-2.5 rounded-lg border border-gray-300
                                 bg-white text-sm text-gray-800 resize-none
                                 focus:outline-none focus:ring-2
                                 focus:ring-blue-500">{{ old('address', $customer?->address) }}</textarea>
            </div>

        </div>
    </div>

    {{-- Danger zone (edit only) --}}
    @if($customer)
        <div class="p-6 border-t border-red-100 bg-red-50">
            <p class="text-xs font-semibold text-red-400 uppercase tracking-widest mb-4">
                Danger zone
            </p>
            <div class="bg-white border border-red-200 rounded-lg p-4"
                 style="display:flex;align-items:center;
                    justify-content:space-between;gap:16px">
                <div>
                    <p class="text-sm font-medium text-red-700">Delete this customer</p>
                    <p class="text-xs text-red-400 mt-0.5">
                        Soft deleted — can be restored later.
                    </p>
                </div>
                <button type="submit" form="delete-form"
                        class="h-9 px-4 bg-white border border-red-300 text-red-600
                           text-sm font-medium rounded-lg transition-colors
                           hover:bg-red-600 hover:text-white hover:border-red-600">
                    Delete customer
                </button>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
         style="display:flex;align-items:center;gap:12px">
        <button type="submit" form="main-form"
                class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors focus:outline-none
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            {{ $customer ? 'Save changes' : 'Create customer' }}
        </button>
        <a href="{{ route('customers.index') }}"
           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                  font-medium rounded-lg border border-gray-300 transition-colors"
           style="display:inline-flex;align-items:center">
            Cancel
        </a>
        @if($customer)
            <a href="{{ route('customers.show', $customer) }}"
               class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
               style="margin-left:auto">
                View customer →
            </a>
        @endif
    </div>

</div>
