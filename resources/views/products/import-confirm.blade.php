{{-- resources/views/products/import-confirm.blade.php --}}
@extends('layouts.app')
@section('title', 'Confirm Import')
@section('header', 'Preview Import')
@section('subheader', 'Review changes before applying')

@section('content')

    {{-- Summary banner --}}
    @php
        $createCount = collect($imported)->where('action', 'created')->count();
        $updateCount = collect($imported)->where('action', 'updated')->count();
        $errorCount  = count($rowErrors);
    @endphp

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="width:36px;height:36px;border-radius:8px;background:#dcfce7;
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                        Will create
                    </p>
                    <p class="text-2xl font-bold text-green-600">{{ $createCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="width:36px;height:36px;border-radius:8px;background:#dbeafe;
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                        Will update
                    </p>
                    <p class="text-2xl font-bold text-blue-600">{{ $updateCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="width:36px;height:36px;border-radius:8px;
                        background:{{ $errorCount > 0 ? '#fee2e2' : '#f3f4f6' }};
                        display:flex;align-items:center;justify-content:center;
                        flex-shrink:0">
                    <svg class="w-5 h-5 {{ $errorCount > 0 ? 'text-red-600' : 'text-gray-400' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                        Rows with errors
                    </p>
                    <p class="text-2xl font-bold
                          {{ $errorCount > 0 ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $errorCount }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Errors --}}
    @if(count($errors) > 0)
        <div class="bg-white border border-red-200 rounded-xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-red-100 bg-red-50">
                <p class="font-semibold text-red-700">
                    Rows with errors — these will be skipped
                </p>
                <p class="text-xs text-red-500 mt-0.5">
                    Fix these rows in your file and re-upload to import them
                </p>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Row</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Name</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">SKU</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Errors</th>
                </tr>
                </thead>
                <tbody>
                @foreach($errors as $error)
                    <tr class="border-b border-gray-50 bg-red-50">
                        <td class="px-5 py-3 text-red-600 font-mono font-medium">
                            Row {{ $error['row'] }}
                        </td>
                        <td class="px-5 py-3 text-gray-700">
                            {{ $error['name'] }}
                        </td>
                        <td class="px-5 py-3 font-mono text-gray-600">
                            {{ $error['sku'] }}
                        </td>
                        <td class="px-5 py-3">
                            <ul style="list-style:none;padding:0;margin:0">
                                @foreach($error['errors'] as $msg)
                                    <li style="font-size:12px;color:#dc2626;
                                   display:flex;align-items:flex-start;gap:4px">
                                        <span>•</span> {{ $msg }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Preview table --}}
    @if(count($imported) > 0)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-gray-100"
                 style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p class="font-semibold text-gray-700">Products to import</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ count($imported) }} product(s) ready
                    </p>
                </div>
                <div style="display:flex;gap:8px">
            <span style="padding:3px 10px;border-radius:20px;font-size:11px;
                         font-weight:500;background:#dcfce7;color:#166534">
                {{ $createCount }} new
            </span>
                    @if($updateCount > 0)
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;
                         font-weight:500;background:#dbeafe;color:#1e40af">
                {{ $updateCount }} update
            </span>
                    @endif
                </div>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Row</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Product name</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">SKU</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                           text-gray-500 uppercase">Opening stock</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Branch</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                           text-gray-500 uppercase">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($imported as $item)
                    <tr class="border-b border-gray-50 hover:bg-gray-50
                       {{ $item['action'] === 'updated' ? 'bg-blue-50' : '' }}">
                        <td class="px-5 py-3 font-mono text-xs text-gray-400">
                            {{ $item['row'] }}
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $item['name'] }}
                        </td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">
                            {{ $item['sku'] }}
                        </td>
                        <td class="px-5 py-3 text-right text-gray-700">
                            {{ $item['stock'] > 0
                                ? number_format($item['stock'], 0)
                                : '—' }}
                        </td>
                        <td class="px-5 py-3 text-gray-600 text-xs">
                            {{ $item['branch'] ?? 'All branches' }}
                        </td>
                        <td class="px-5 py-3">
                            @if($item['action'] === 'created')
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#dcfce7;color:#166534">
                        Create
                    </span>
                            @else
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#dbeafe;color:#1e40af">
                        Update
                    </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Action buttons --}}
    <div style="display:flex;align-items:center;gap:12px">

        @if(count($imported) > 0)
            <form method="POST"
                  action="{{ route('products.import.process') }}"
                  id="import-form">
                @csrf
                <button type="button"
                        id="confirm-btn"
                        onclick="confirmImport()"
                        class="h-10 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors focus:outline-none
                       focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        style="cursor:pointer;border:none">
                    ✓ Confirm &amp; import {{ count($imported) }} product(s)
                </button>
            </form>
        @endif

        <a href="{{ route('products.import') }}"
           class="h-10 px-5 bg-white border border-gray-300 hover:bg-gray-50
              text-gray-600 text-sm font-medium rounded-lg transition-colors"
           style="display:inline-flex;align-items:center">
            ← Upload different file
        </a>

        <a href="{{ route('products.index') }}"
           class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
           style="margin-left:auto">
            Cancel
        </a>

    </div>

    @push('scripts')
        <script>
            function confirmImport() {
                const btn  = document.getElementById('confirm-btn');
                const form = document.getElementById('import-form');

                // Update button text first
                btn.textContent   = 'Importing…';
                btn.style.opacity = '0.7';
                btn.style.cursor  = 'not-allowed';

                // Submit after brief delay so browser renders the text change
                setTimeout(function () {
                    form.submit();
                }, 100);
            }
        </script>
    @endpush

@endsection
