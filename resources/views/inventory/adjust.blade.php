{{-- resources/views/inventory/adjust.blade.php --}}
@extends('layouts.app')
@section('title', 'Adjust Stock')
@section('header', 'Adjust Stock')
@section('subheader', $product->name . ' · SKU: ' . $product->sku)

@section('content')
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

        {{-- Adjustment form --}}
        <form id="main-form" method="POST"
              action="{{ route('inventory.store-adjustment', $product) }}">
            @csrf

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                <div class="p-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
                        Stock adjustment
                    </p>
                    <div class="space-y-4">

                        {{-- Branch --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                Branch <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="branch_id" id="branch-select"
                                        class="w-full h-10 px-3 pr-8 rounded-lg border border-gray-300
                                       bg-white text-sm text-gray-800 focus:outline-none
                                       focus:ring-2 focus:ring-blue-500 appearance-none">
                                    <option value="">— Select branch —</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                            </div>
                            @error('branch_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Adjustment type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-3">
                                Adjustment type <span class="text-red-500">*</span>
                            </label>
{{--                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">--}}

{{--                                <label style="display:flex;flex-direction:column;align-items:center;--}}
{{--                                      padding:14px 10px;border:1px solid #e5e7eb;border-radius:8px;--}}
{{--                                      cursor:pointer;text-align:center;transition:all .15s"--}}
{{--                                       id="type-add">--}}
{{--                                    <input type="radio" name="type" value="add"--}}
{{--                                           class="sr-only" checked--}}
{{--                                           onchange="selectType('add')">--}}
{{--                                    <div style="width:36px;height:36px;border-radius:8px;background:#dcfce7;--}}
{{--                                        display:flex;align-items:center;justify-content:center;--}}
{{--                                        margin-bottom:8px">--}}
{{--                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>--}}
{{--                                        </svg>--}}
{{--                                    </div>--}}
{{--                                    <p class="text-sm font-medium text-gray-700">Add</p>--}}
{{--                                    <p class="text-xs text-gray-400 mt-0.5">Increase stock</p>--}}
{{--                                </label>--}}

{{--                                <label style="display:flex;flex-direction:column;align-items:center;--}}
{{--                                      padding:14px 10px;border:1px solid #e5e7eb;border-radius:8px;--}}
{{--                                      cursor:pointer;text-align:center;transition:all .15s"--}}
{{--                                       id="type-subtract">--}}
{{--                                    <input type="radio" name="type" value="subtract"--}}
{{--                                           class="sr-only"--}}
{{--                                           onchange="selectType('subtract')">--}}
{{--                                    <div style="width:36px;height:36px;border-radius:8px;background:#fee2e2;--}}
{{--                                        display:flex;align-items:center;justify-content:center;--}}
{{--                                        margin-bottom:8px">--}}
{{--                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>--}}
{{--                                        </svg>--}}
{{--                                    </div>--}}
{{--                                    <p class="text-sm font-medium text-gray-700">Subtract</p>--}}
{{--                                    <p class="text-xs text-gray-400 mt-0.5">Decrease stock</p>--}}
{{--                                </label>--}}

{{--                                <label style="display:flex;flex-direction:column;align-items:center;--}}
{{--                                      padding:14px 10px;border:1px solid #e5e7eb;border-radius:8px;--}}
{{--                                      cursor:pointer;text-align:center;transition:all .15s"--}}
{{--                                       id="type-set">--}}
{{--                                    <input type="radio" name="type" value="set"--}}
{{--                                           class="sr-only"--}}
{{--                                           onchange="selectType('set')">--}}
{{--                                    <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;--}}
{{--                                        display:flex;align-items:center;justify-content:center;--}}
{{--                                        margin-bottom:8px">--}}
{{--                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"--}}
{{--                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>--}}
{{--                                        </svg>--}}
{{--                                    </div>--}}
{{--                                    <p class="text-sm font-medium text-gray-700">Set exact</p>--}}
{{--                                    <p class="text-xs text-gray-400 mt-0.5">Override quantity</p>--}}
{{--                                </label>--}}

{{--                            </div>--}}
                            {{-- Add this 4th card inside the adjustment type grid --}}
                            {{-- Change grid to repeat(4,1fr) --}}
                            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">

                                {{-- Add (existing) --}}
                                <label style="display:flex;flex-direction:column;align-items:center;
                  padding:14px 10px;border:1px solid #e5e7eb;border-radius:8px;
                  cursor:pointer;text-align:center;transition:all .15s"
                                       id="type-add">
                                    <input type="radio" name="type" value="add"
                                           class="sr-only" checked
                                           onchange="selectType('add')">
                                    <div style="width:36px;height:36px;border-radius:8px;background:#dcfce7;
                    display:flex;align-items:center;justify-content:center;margin-bottom:8px">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700">Add</p>
                                    <p class="text-xs text-gray-400 mt-0.5">General increase</p>
                                </label>

                                {{-- Restock (new) --}}
                                <label style="display:flex;flex-direction:column;align-items:center;
                  padding:14px 10px;border:1px solid #e5e7eb;border-radius:8px;
                  cursor:pointer;text-align:center;transition:all .15s"
                                       id="type-restock">
                                    <input type="radio" name="type" value="restock"
                                           class="sr-only"
                                           onchange="selectType('restock')">
                                    <div style="width:36px;height:36px;border-radius:8px;background:#e0f2fe;
                    display:flex;align-items:center;justify-content:center;margin-bottom:8px">
                                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700">Restock</p>
                                    <p class="text-xs text-gray-400 mt-0.5">From supplier</p>
                                </label>

                                {{-- Subtract (existing) --}}
                                <label style="display:flex;flex-direction:column;align-items:center;
                  padding:14px 10px;border:1px solid #e5e7eb;border-radius:8px;
                  cursor:pointer;text-align:center;transition:all .15s"
                                       id="type-subtract">
                                    <input type="radio" name="type" value="subtract"
                                           class="sr-only"
                                           onchange="selectType('subtract')">
                                    <div style="width:36px;height:36px;border-radius:8px;background:#fee2e2;
                    display:flex;align-items:center;justify-content:center;margin-bottom:8px">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700">Subtract</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Decrease stock</p>
                                </label>

                                {{-- Set exact (existing) --}}
                                <label style="display:flex;flex-direction:column;align-items:center;
                  padding:14px 10px;border:1px solid #e5e7eb;border-radius:8px;
                  cursor:pointer;text-align:center;transition:all .15s"
                                       id="type-set">
                                    <input type="radio" name="type" value="set"
                                           class="sr-only"
                                           onchange="selectType('set')">
                                    <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;
                    display:flex;align-items:center;justify-content:center;margin-bottom:8px">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700">Set exact</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Override quantity</p>
                                </label>

                            </div>
                            @error('type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Quantity --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="quantity" min="0" step="0.01"
                                   value="{{ old('quantity') }}"
                                   placeholder="Enter quantity"
                                   class="w-full h-10 px-3 rounded-lg border text-sm text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  {{ $errors->has('quantity') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white' }}">
                            @error('quantity')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Add this after the quantity field --}}
                        <div id="restock-fields" style="display:none">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Supplier name
                                    </label>
                                    <input type="text" name="supplier_name"
                                           value="{{ old('supplier_name') }}"
                                           placeholder="e.g. ABC Distributors"
                                           class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none focus:ring-2
                          focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                        Supplier invoice / reference
                                    </label>
                                    <input type="text" name="supplier_reference"
                                           value="{{ old('supplier_reference') }}"
                                           placeholder="e.g. INV-2024-001"
                                           class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none focus:ring-2
                          focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1.5">
                                Reason / notes
                                <span class="text-xs text-gray-400 font-normal ml-1">(optional)</span>
                            </label>
                            <textarea name="notes" rows="3"
                                      placeholder="e.g. Physical count correction, damaged goods removal…"
                                      class="w-full px-3 py-2.5 rounded-lg border border-gray-300
                                     bg-white text-sm text-gray-800 resize-none
                                     focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100"
                     style="display:flex;align-items:center;gap:12px">
                    <button type="submit" form="main-form"
                            class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                           font-medium rounded-lg transition-colors focus:outline-none
                           focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Apply adjustment
                    </button>
                    <a href="{{ route('inventory.index') }}"
                       class="h-10 px-5 bg-white hover:bg-gray-50 text-gray-600 text-sm
                      font-medium rounded-lg border border-gray-300 transition-colors"
                       style="display:inline-flex;align-items:center">
                        Cancel
                    </a>
                </div>

            </div>
        </form>

        {{-- Current stock per branch --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Current stock levels</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $product->name }}</p>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($currentStock as $stock)
                    @php
                        $isLow = $stock->quantity <= $product->reorder_level && $stock->quantity > 0;
                        $isOut = $stock->quantity <= 0;
                    @endphp
                    <div class="px-5 py-4"
                         style="display:flex;align-items:center;justify-content:space-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $stock->branch->name }}</p>
                            <p class="text-xs text-gray-400">
                                Reorder at: {{ $product->reorder_level }} {{ $product->unit }}
                            </p>
                        </div>
                        <div style="text-align:right">
                            <p class="text-lg font-semibold
                              {{ $isOut ? 'text-red-600' : ($isLow ? 'text-amber-600' : 'text-gray-800') }}">
                                {{ number_format($stock->quantity, 2) }}
                                <span class="text-xs font-normal text-gray-400">{{ $product->unit }}</span>
                            </p>
                            @if($isOut)
                                <p class="text-xs text-red-500">Out of stock</p>
                            @elseif($isLow)
                                <p class="text-xs text-amber-500">Low stock</p>
                            @else
                                <p class="text-xs text-green-500">In stock</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">
                        No stock records yet.
                    </div>
                @endforelse
            </div>

            {{-- Product info footer --}}
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                <dl style="display:grid;grid-template-columns:1fr 1fr;gap:12px"
                    class="text-sm">
                    <div>
                        <dt class="text-xs text-gray-400">Cost price</dt>
                        <dd class="font-medium text-gray-700 mt-0.5">
                            GHS {{ number_format($product->cost_price, 2) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400">Selling price</dt>
                        <dd class="font-medium text-gray-700 mt-0.5">
                            GHS {{ number_format($product->selling_price, 2) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400">Unit</dt>
                        <dd class="font-medium text-gray-700 mt-0.5">{{ ucfirst($product->unit) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400">SKU</dt>
                        <dd class="font-mono text-gray-700 mt-0.5">{{ $product->sku }}</dd>
                    </div>
                </dl>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function selectType(type) {
                ['add', 'restock', 'subtract', 'set'].forEach(t => {
                    const el = document.getElementById('type-' + t);
                    if (!el) return;
                    el.style.borderColor = t === type ? '#3b82f6' : '#e5e7eb';
                    el.style.background  = t === type ? '#eff6ff' : '#fff';
                });

                // Show supplier fields only for restock
                const restockFields = document.getElementById('restock-fields');
                if (restockFields) {
                restockFields.style.display = type === 'restock' ? 'block' : 'none';
                }
            }

            // Default on load
            selectType('add');
        </script>
    @endpush

@endsection
