{{-- resources/views/returns/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Return #' . $return->id)
@section('header', 'Return Details')
@section('subheader', 'Return #' . $return->id)

@section('content')

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

        {{-- Return info --}}
        <div class="space-y-4">
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Return information
                </p>
                @php
                    $typeColors = [
                        'refund'   => 'background:#dbeafe;color:#1e40af',
                        'exchange' => 'background:#f3e8ff;color:#6b21a8',
                        'damaged'  => 'background:#fee2e2;color:#991b1b',
                    ];
                    $statusColors = [
                        'pending'  => 'background:#fef3c7;color:#92400e',
                        'approved' => 'background:#dcfce7;color:#166534',
                        'rejected' => 'background:#fee2e2;color:#991b1b',
                    ];
                @endphp
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Invoice</dt>
                        <dd class="font-mono font-semibold text-blue-600">
                            {{ $return->sale?->invoice_no ?? '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Product</dt>
                        <dd class="font-medium text-gray-800">
                            {{ $return->product?->name ?? '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Branch</dt>
                        <dd class="text-gray-700">{{ $return->branch?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Quantity</dt>
                        <dd class="text-gray-700">
                            {{ number_format($return->quantity, 0) }}
                            {{ $return->product?->unit }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Refund amount</dt>
                        <dd class="font-semibold text-gray-800">
                            GHS {{ number_format($return->refund_amount, 2) }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Type</dt>
                        <dd>
                        <span style="padding:2px 10px;border-radius:20px;font-size:11px;
                                     font-weight:500;
                                     {{ $typeColors[$return->type] ?? '' }}">
                            {{ ucfirst($return->type) }}
                        </span>
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                        <span style="padding:2px 10px;border-radius:20px;font-size:11px;
                                     font-weight:500;
                                     {{ $statusColors[$return->status] ?? '' }}">
                            {{ ucfirst($return->status) }}
                        </span>
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Processed by</dt>
                        <dd class="text-gray-700">
                            {{ $return->processedBy?->name ?? '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Date</dt>
                        <dd class="text-gray-700">
                            {{ $return->created_at->format('d M Y H:i') }}
                        </dd>
                    </div>
                    @if($return->reason)
                        <div>
                            <dt class="text-gray-500 mb-1">Reason</dt>
                            <dd class="text-gray-700 text-xs bg-gray-50 p-3
                               rounded-lg leading-relaxed">
                                {{ $return->reason }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Actions --}}
            @if($return->status === 'pending')
                <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">
                        Actions
                    </p>
                    @can('approve returns')
                        <form method="POST" action="{{ route('returns.approve', $return) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="w-full h-10 bg-green-600 hover:bg-green-700 text-white
                               text-sm font-medium rounded-lg transition-colors">
                                ✓ Approve return
                            </button>
                        </form>
                        <form method="POST" action="{{ route('returns.reject', $return) }}"
                              onsubmit="return confirm('Reject this return?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="w-full h-10 bg-white border border-red-300 text-red-600
                               hover:bg-red-600 hover:text-white text-sm font-medium
                               rounded-lg transition-colors">
                                ✗ Reject return
                            </button>
                        </form>
                    @endcan
                </div>
            @endif

            {{-- Return receipt PDF button --}}
            @if($return->status === 'approved')
                <a href="{{ route('pdf.return-receipt', $return) }}"
                   target="_blank"
                   class="w-full h-10 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors"
                   style="display:flex;align-items:center;justify-content:center;gap:6px">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Print return receipt
                </a>
            @endif

            <a href="{{ route('returns.index') }}"
               class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
               style="display:flex;align-items:center;justify-content:center">
                ← Back to returns
            </a>
        </div>

        {{-- Original sale info --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Original sale</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $return->sale?->invoice_no }}
                </p>
            </div>
            <div class="p-5">
                @if($return->sale)
                    <dl class="space-y-3 text-sm">
                        <div style="display:flex;justify-content:space-between">
                            <dt class="text-gray-500">Sale date</dt>
                            <dd class="text-gray-700">
                                {{ $return->sale->created_at->format('d M Y H:i') }}
                            </dd>
                        </div>
                        <div style="display:flex;justify-content:space-between">
                            <dt class="text-gray-500">Total amount</dt>
                            <dd class="font-medium text-gray-800">
                                GHS {{ number_format($return->sale->total_amount, 2) }}
                            </dd>
                        </div>
                        <div style="display:flex;justify-content:space-between">
                            <dt class="text-gray-500">Payment method</dt>
                            <dd class="text-gray-700">
                                {{ ucfirst($return->sale->payment_method) }}
                            </dd>
                        </div>
                        <div style="display:flex;justify-content:space-between">
                            <dt class="text-gray-500">Sale status</dt>
                            <dd class="text-gray-700">
                                {{ ucfirst($return->sale->status) }}
                            </dd>
                        </div>
                    </dl>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('sales.show', $return->sale) }}"
                           class="text-sm text-blue-600 hover:underline">
                            View full sale details →
                        </a>
                    </div>
                @else
                    <p class="text-sm text-gray-400">Sale not found.</p>
                @endif
            </div>
        </div>

    </div>

@endsection
