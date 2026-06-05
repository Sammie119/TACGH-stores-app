{{-- resources/views/pdf/layouts/base.blade.php --}}
    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.5;
        }

        /* Page header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 2px solid #2563eb;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .doc-title {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            text-align: right;
        }

        .doc-meta {
            font-size: 10px;
            color: #6b7280;
            text-align: right;
            margin-top: 4px;
        }

        /* Info grid */
        .info-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-box {
            flex: 1;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 14px;
        }

        .info-label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #9ca3af;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 11px;
            color: #111827;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        thead tr {
            background: #1e3a5f;
            color: #fff;
        }

        thead th {
            padding: 8px 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        tbody tr {
            border-bottom: 1px solid #f3f4f6;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        tbody td {
            padding: 7px 10px;
            font-size: 10px;
            color: #374151;
        }

        tfoot tr {
            background: #f3f4f6;
            border-top: 2px solid #e5e7eb;
        }

        tfoot td {
            padding: 8px 10px;
            font-size: 11px;
            font-weight: bold;
            color: #111827;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-green  { background: #dcfce7; color: #166534; }
        .badge-amber  { background: #fef3c7; color: #92400e; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-blue   { background: #dbeafe; color: #1e40af; }
        .badge-gray   { background: #f3f4f6; color: #374151; }

        /* Footer */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #e5e7eb;
            padding: 8px 24px;
            font-size: 9px;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
            background: #fff;
        }

        /* Summary box */
        .summary-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 16px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 11px;
        }

        .summary-row:last-child { margin-bottom: 0; }

        .summary-total {
            border-top: 1px solid #bfdbfe;
            padding-top: 8px;
            margin-top: 8px;
            font-size: 13px;
            font-weight: bold;
            color: #1e40af;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-blue { color: #2563eb; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .text-gray { color: #6b7280; }
        .text-sm { font-size: 10px; }
        .monospace { font-family: 'Courier New', monospace; }
    </style>
    @yield('styles')
</head>
<body>

@section('header')
    <div class="page-header">
        <div>
            <div class="company-name">{{ setting('app_name', 'Stores Manager') }}</div>
            @if(isset($branch))
                <div style="font-size:11px;color:#6b7280;margin-top:2px">
                    {{ $branch->name }}
                    @if($branch->address) · {{ $branch->address }} @endif
                    @if($branch->phone) · {{ $branch->phone }} @endif
                </div>
            @endif
        </div>
        <div>
            <div class="doc-title">@yield('doc-title')</div>
            <div class="doc-meta">
                Generated: {{ now()->format('d M Y H:i') }}<br>
                By: {{ auth()->user()->name }}
            </div>
        </div>
    </div>
@show

@yield('content')

<div class="page-footer">
    <span>{{ setting('app_name', 'Stores Manager') }} — Confidential</span>
    <span>Generated {{ now()->format('d M Y H:i') }} by {{ auth()->user()->name }}</span>
    <span>Page <span class="pagenum"></span></span>
</div>

</body>
</html>
