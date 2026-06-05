{{-- resources/views/supplier-payments/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Record Payment')
@section('header', 'Record Supplier Payment')
@section('subheader', 'Pay a supplier for goods received')

@section('content')

    <form id="search-form"
          method="GET"
          action="{{ route('supplier-payments.create') }}">
    </form>

    <form id="main-form"
          method="POST"
          action="{{ route('supplier-payments.store') }}">
        @csrf
    </form>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

        {{-- Step 1: Select supplier --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
                    Step 1 — Select supplier
                </p>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                        Supplier
                    </label>
                    <div style="display:flex;gap:10px">
                        <div class="relative" style="flex:1">
                            <select name="supplier_id"
                                    form="search-form"
                                    class="w-full h-10 px-3 pr-8 rounded-lg border
                                       border-gray-300 bg-white text-sm text-gray-800
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500 appearance-none">
                                <option value="">— Select supplier —</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                        (owes GHS {{ number_format($supplier->balance, 2) }})
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
                        <button type="submit"
                                form="search-form"
                                class="h-10 px-4 bg-gray-800 hover:bg-gray-900 text-white
                                   text-sm font-medium rounded-lg transition-colors
                                   flex-shrink-0">
                            Load
                        </button>
                    </div>
                </div>

                @if($selectedSupplier)
                    <div style="margin-top:16px;padding:14px;background:#f0fdf4;
                        border:1px solid #bbf7d0;border-radius:8px">
                        <p class="text-sm font-semibold text-green-800 mb-2">
                            {{ $selectedSupplier->name }}
                        </p>
                        <dl class="space-y-1.5">
                            <div style="display:flex;justify-content:space-between;font-size:12px">
                                <dt style="color:#16a34a">Code</dt>
                                <dd style="font-family:monospace;font-weight:600;color:#166534">
                                    {{ $selectedSupplier->code }}
                                </dd>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:12px">
                                <dt style="color:#16a34a">Balance owed</dt>
                                <dd style="font-weight:700;color:#dc2626">
                                    GHS {{ number_format($selectedSupplier->balance, 2) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                @endif
            </div>
        </div>

        {{-- Step 2: Payment details --}}
        <div>
            @if($selectedSupplier)
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="p-6">
                        <p class="text-xs font-semibold text-gray-400 uppercase
                           tracking-widest mb-5">
                            Step 2 — Payment details
                        </p>

                        <input type="hidden" name="supplier_id"
                               form="main-form"
                               value="{{ $selectedSupplier->id }}">

                        <div class="space-y-4">

                            {{-- Purchase order --}}
                            @if($openOrders->count() > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Link to purchase order
                                        <span class="text-xs text-gray-400 font-normal ml-1">
                                (optional)
                            </span>
                                    </label>
                                    <div class="relative">
                                        <select name="purchase_order_id"
                                                form="main-form"
                                                class="w-full h-10 px-3 pr-8 rounded-lg border
                                           border-gray-300 bg-white text-sm text-gray-800
                                           focus:outline-none focus:ring-2
                                           focus:ring-blue-500 appearance-none">
                                            <option value="">— General payment —</option>
                                            @foreach($openOrders as $po)
                                                <option value="{{ $po->id }}"
                                                    {{ request('po_id') == $po->id ? 'selected' : '' }}>
                                                    {{ $po->po_number }}
                                                    (balance: GHS {{ number_format($po->balance_due, 2) }})
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
                            @endif

                            {{-- Amount --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                    Amount (GHS) <span class="text-red-500">*</span>
                                </label>
                                <div style="position:relative">
                            <span style="position:absolute;left:12px;top:50%;
                                         transform:translateY(-50%);font-size:13px;
                                         font-weight:600;color:#9ca3af;
                                         pointer-events:none">
                                GHS
                            </span>
                                    <input type="number"
                                           name="amount"
                                           form="main-form"
                                           step="0.01" min="0.01"
                                           value="{{ old('amount', $selectedSupplier->balance) }}"
                                           placeholder="0.00"
                                           style="width:100%;height:40px;padding:0 12px 0 52px;
                                          border:1px solid #d1d5db;border-radius:8px;
                                          font-size:14px;color:#111827;background:#fff;
                                          box-sizing:border-box;outline:none"
                                           onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
                                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                </div>
                                @error('amount')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Payment method + date --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Payment method <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="payment_method"
                                                form="main-form"
                                                class="w-full h-10 px-3 pr-8 rounded-lg border
                                               border-gray-300 bg-white text-sm text-gray-800
                                               focus:outline-none focus:ring-2
                                               focus:ring-blue-500 appearance-none">
                                            <option value="cash">Cash</option>
                                            <option value="momo">MoMo</option>
                                            <option value="bank">Bank transfer</option>
                                            <option value="cheque">Cheque</option>
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
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Payment date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                           name="payment_date"
                                           form="main-form"
                                           value="{{ today()->format('Y-m-d') }}"
                                           class="w-full h-10 px-3 rounded-lg border
                                          border-gray-300 bg-white text-sm text-gray-800
                                          focus:outline-none focus:ring-2
                                          focus:ring-blue-500">
                                </div>
                            </div>

                            {{-- Bank reference --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                    Bank / cheque reference
                                    <span class="text-xs text-gray-400 font-normal ml-1">
                                (optional)
                            </span>
                                </label>
                                <input type="text"
                                       name="bank_reference"
                                       form="main-form"
                                       value="{{ old('bank_reference') }}"
                                       placeholder="e.g. TXN-123456"
                                       class="w-full h-10 px-3 rounded-lg border border-gray-300
                                      bg-white text-sm font-mono text-gray-800
                                      focus:outline-none focus:ring-2
                                      focus:ring-blue-500">
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                    Notes
                                    <span class="text-xs text-gray-400 font-normal ml-1">
                                (optional)
                            </span>
                                </label>
                                <textarea name="notes"
                                          form="main-form"
                                          rows="2"
                                          placeholder="Any payment notes…"
                                          class="w-full px-3 py-2.5 rounded-lg border
                                         border-gray-300 bg-white text-sm text-gray-800
                                         resize-none focus:outline-none
                                         focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                            </div>

                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
                         style="display:flex;align-items:center;gap:12px">
                        <button type="submit"
                                form="main-form"
                                class="h-10 px-6 bg-green-600 hover:bg-green-700 text-white
                               text-sm font-medium rounded-lg transition-colors
                               focus:outline-none focus:ring-2 focus:ring-green-500
                               focus:ring-offset-2">
                            Record payment
                        </button>
                        <a href="{{ route('supplier-payments.index') }}"
                           class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                          font-medium rounded-lg border border-gray-300 transition-colors"
                           style="display:inline-flex;align-items:center">
                            Cancel
                        </a>
                    </div>
                </div>

            @else
                <div class="bg-white border border-gray-200 rounded-xl p-10 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="#e5e7eb"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <p class="text-sm text-gray-400 mt-1">
                        Select a supplier on the left to begin
                    </p>
                </div>
            @endif
        </div>

    </div>

@endsection
