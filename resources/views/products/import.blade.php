{{-- resources/views/products/import.blade.php --}}
@extends('layouts.app')
@section('title', 'Import Products')
@section('header', 'Import Products')
@section('subheader', 'Bulk upload products from Excel or CSV')

@section('content')

    <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:20px;align-items:start">

        {{-- Upload form --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

            <div class="p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">
                    Upload file
                </p>

                <form method="POST"
                      action="{{ route('products.import.preview') }}"
                      enctype="multipart/form-data"
                      id="upload-form">
                    @csrf

                    {{-- Drop zone --}}
                    <div id="drop-zone"
                         onclick="document.getElementById('file-input').click()"
                         style="border:2px dashed #d1d5db;border-radius:12px;
                            padding:40px 20px;text-align:center;cursor:pointer;
                            transition:all .2s;background:#fafafa"
                         ondragover="event.preventDefault();
                                 this.style.borderColor='#2563eb';
                                 this.style.background='#eff6ff'"
                         ondragleave="this.style.borderColor='#d1d5db';
                                  this.style.background='#fafafa'"
                         ondrop="handleDrop(event)">
                        <div id="drop-content">
                            <svg style="width:40px;height:40px;color:#d1d5db;
                                    margin:0 auto 12px;display:block"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p style="font-size:14px;font-weight:500;color:#374151;
                                  margin-bottom:4px">
                                Drop your file here or click to browse
                            </p>
                            <p style="font-size:12px;color:#9ca3af">
                                Supports .xlsx, .xls, .csv — max 5MB
                            </p>
                        </div>
                        <div id="file-selected" style="display:none">
                            <svg style="width:40px;height:40px;color:#16a34a;
                                    margin:0 auto 12px;display:block"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="1.5"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p id="file-name"
                               style="font-size:14px;font-weight:500;color:#166534">
                            </p>
                            <p style="font-size:12px;color:#16a34a;margin-top:4px">
                                Ready to preview
                            </p>
                        </div>
                    </div>

                    <input type="file"
                           id="file-input"
                           name="file"
                           accept=".xlsx,.xls,.csv"
                           style="display:none"
                           onchange="handleFileSelect(this)">

                    @error('file')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                    <div style="margin-top:16px;display:flex;gap:10px">
                        <button type="submit"
                                id="preview-btn"
                                disabled
                                style="height:40px;padding:0 20px;background:#2563eb;
                                   color:#fff;border:none;border-radius:8px;
                                   font-size:14px;font-weight:500;cursor:pointer;
                                   opacity:.4;transition:opacity .2s"
                                onmouseover="if(!this.disabled) this.style.background='#1d4ed8'"
                                onmouseout="this.style.background='#2563eb'">
                            Preview import
                        </button>
                        <button type="button"
                                onclick="document.getElementById('file-input').value='';
                                     resetDropZone()"
                                style="height:40px;padding:0 16px;background:#fff;
                                   border:1px solid #d1d5db;border-radius:8px;
                                   font-size:13px;color:#374151;cursor:pointer">
                            Clear
                        </button>
                    </div>

                </form>
            </div>

            {{-- Requirements --}}
            <div style="padding:16px 24px;background:#f9fafb;border-top:1px solid #f3f4f6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">
                    Required columns
                </p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
                    @foreach([
                        ['name',          'Product name',      true],
                        ['sku',           'Unique SKU code',   true],
                        ['unit',          'Unit (piece, kg…)', true],
                        ['cost_price',    'Cost price',        true],
                        ['selling_price', 'Selling price',     true],
                        ['category',      'Category name',     false],
                        ['reorder_level', 'Reorder level',     false],
                        ['initial_stock', 'Opening stock qty', false],
                        ['branch',        'Branch name',       false],
                        ['description',   'Description',       false],
                    ] as [$col, $desc, $required])
                        <div style="display:flex;align-items:flex-start;gap:6px">
                    <span style="font-size:10px;font-family:monospace;
                                 background:{{ $required ? '#eff6ff' : '#f3f4f6' }};
                                 color:{{ $required ? '#1d4ed8' : '#374151' }};
                                 padding:1px 6px;border-radius:4px;
                                 flex-shrink:0;margin-top:1px">
                        {{ $col }}
                    </span>
                            <span style="font-size:11px;color:#6b7280">
                        {{ $desc }}
                                @if($required)
                                    <span style="color:#dc2626">*</span>
                                @endif
                    </span>
                        </div>
                    @endforeach
                </div>
                <p style="font-size:10px;color:#9ca3af;margin-top:8px">
                    <span style="color:#dc2626">*</span> Required fields
                </p>
            </div>

        </div>

        {{-- Instructions + template --}}
        <div class="space-y-4">

            {{-- Download template --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Get started
                </p>
                <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                    Download the template, fill it in with your products,
                    then upload it here. The system will preview changes
                    before applying them.
                </p>
                <a href="{{ route('products.import.template') }}"
                   class="w-full h-10 bg-green-600 hover:bg-green-700 text-white
                      text-sm font-medium rounded-lg transition-colors"
                   style="display:flex;align-items:center;justify-content:center;gap:6px">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download CSV template
                </a>
            </div>

            {{-- How it works --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    How it works
                </p>
                <div class="space-y-3">
                    @foreach([
                        ['1', '#2563eb', 'Upload your file',
                         'Select your Excel or CSV file above'],
                        ['2', '#7c3aed', 'Preview changes',
                         'Review what will be created or updated before committing'],
                        ['3', '#16a34a', 'Confirm import',
                         'Products are created, existing ones are updated by SKU'],
                        ['4', '#d97706', 'Stock is set',
                         'Initial stock is distributed to the specified branch or all branches'],
                    ] as [$step, $color, $title, $desc])
                        <div style="display:flex;align-items:flex-start;gap:10px">
                            <div style="width:24px;height:24px;border-radius:50%;
                                background:{{ $color }};color:#fff;font-size:11px;
                                font-weight:700;display:flex;align-items:center;
                                justify-content:center;flex-shrink:0;margin-top:1px">
                                {{ $step }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $title }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tips --}}
            <div style="padding:14px 16px;background:#fef3c7;border:1px solid #fde68a;
                    border-radius:10px">
                <p class="text-xs font-semibold text-amber-700 mb-2">
                    Tips for a clean import
                </p>
                <ul style="list-style:none;padding:0;margin:0;
                       display:flex;flex-direction:column;gap:4px">
                    @foreach([
                        'SKUs must be unique — duplicates will be updated, not skipped',
                        'Category names that don\'t exist will be created automatically',
                        'Leave branch blank to set opening stock across all branches',
                        'Prices must be numbers without currency symbols',
                        'Keep the first row as column headers',
                    ] as $tip)
                        <li style="font-size:11px;color:#92400e;
                           display:flex;align-items:flex-start;gap:5px">
                            <span style="color:#d97706;flex-shrink:0">→</span>
                            {{ $tip }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Back to products --}}
            <a href="{{ route('products.index') }}"
               class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
               style="display:flex;align-items:center;justify-content:center">
                ← Back to products
            </a>

        </div>
    </div>

    @push('scripts')
        <script>
            function handleFileSelect(input) {
                if (input.files && input.files[0]) {
                    showFileSelected(input.files[0].name);
                }
            }

            function handleDrop(event) {
                event.preventDefault();
                document.getElementById('drop-zone').style.borderColor = '#d1d5db';
                document.getElementById('drop-zone').style.background  = '#fafafa';

                const files = event.dataTransfer.files;
                if (files.length > 0) {
                    const file  = files[0];
                    const valid = ['xlsx','xls','csv'].some(ext =>
                        file.name.toLowerCase().endsWith('.' + ext));

                    if (!valid) {
                        alert('Please upload an Excel (.xlsx, .xls) or CSV file.');
                        return;
                    }

                    const dt = new DataTransfer();
                    dt.items.add(file);
                    document.getElementById('file-input').files = dt.files;
                    showFileSelected(file.name);
                }
            }

            function showFileSelected(name) {
                document.getElementById('drop-content').style.display   = 'none';
                document.getElementById('file-selected').style.display  = 'block';
                document.getElementById('file-name').textContent        = name;

                const btn     = document.getElementById('preview-btn');
                btn.disabled  = false;
                btn.style.opacity = '1';
                btn.style.cursor  = 'pointer';

                document.getElementById('drop-zone').style.borderColor = '#16a34a';
                document.getElementById('drop-zone').style.background  = '#f0fdf4';
            }

            function resetDropZone() {
                document.getElementById('drop-content').style.display   = 'block';
                document.getElementById('file-selected').style.display  = 'none';
                document.getElementById('drop-zone').style.borderColor  = '#d1d5db';
                document.getElementById('drop-zone').style.background   = '#fafafa';

                const btn     = document.getElementById('preview-btn');
                btn.disabled  = true;
                btn.style.opacity = '.4';
            }

            // Show loading on submit
            document.getElementById('upload-form').addEventListener('submit', function () {
                const btn = document.getElementById('preview-btn');
                btn.textContent = 'Analysing file…';
                btn.disabled    = true;
                btn.style.opacity = '.7';
            });
        </script>
    @endpush

@endsection
