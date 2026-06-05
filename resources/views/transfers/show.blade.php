{{-- resources/views/transfers/show.blade.php --}}
@extends('layouts.app')
@section('title', $transfer->reference_no)
@section('header', 'Transfer ' . $transfer->reference_no)
@section('subheader', 'Created ' . $transfer->created_at->format('d M Y H:i'))

@section('content')

    {{-- Status timeline --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <div style="display:flex;align-items:center">
            @php
                $steps        = ['pending', 'approved', 'dispatched', 'received'];
                $currentIndex = array_search($transfer->status, $steps);
                $isRejected   = $transfer->status === 'rejected';
            @endphp

            @foreach($steps as $i => $step)
                @php $done = !$isRejected && $currentIndex !== false && $currentIndex >= $i; @endphp
                <div style="display:flex;align-items:center;flex:1">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:6px">
                        <div style="width:32px;height:32px;border-radius:50%;display:flex;
                            align-items:center;justify-content:center;font-size:12px;
                            font-weight:600;flex-shrink:0;
                            background:{{ $done ? '#2563eb' : '#f3f4f6' }};
                            color:{{ $done ? '#fff' : '#9ca3af' }}">
                            @if($done)
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <p style="font-size:11px;font-weight:500;white-space:nowrap;
                          color:{{ $done ? '#2563eb' : '#9ca3af' }}">
                            {{ ucfirst($step) }}
                        </p>
                    </div>
                    @if(!$loop->last)
                        <div style="flex:1;height:2px;margin:0 8px;margin-bottom:18px;
                        background:{{ ($done && $currentIndex !== false && $currentIndex > $i) ? '#2563eb' : '#e5e7eb' }}">
                        </div>
                    @endif
                </div>
            @endforeach

            @if($isRejected)
                <div style="margin-left:16px;margin-bottom:18px;padding:4px 12px;
                    border-radius:20px;background:#fee2e2;color:#991b1b;
                    font-size:12px;font-weight:500;white-space:nowrap">
                    Rejected
                </div>
            @endif
        </div>
    </div>

    {{-- Main grid --}}
    <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:20px;align-items:start">

        {{-- Left: info + actions --}}
        <div class="space-y-4">

            {{-- Transfer info --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Transfer info
                </p>
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Reference</dt>
                        <dd class="font-mono font-medium text-gray-800">{{ $transfer->reference_no }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">From</dt>
                        <dd class="font-medium text-gray-800">{{ $transfer->fromBranch?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">To</dt>
                        <dd class="font-medium text-gray-800">{{ $transfer->toBranch?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Status</dt>
                        <dd>
                            @php
                                $statusColors = [
                                    'pending'    => 'background:#fef3c7;color:#92400e',
                                    'approved'   => 'background:#dcfce7;color:#166534',
                                    'dispatched' => 'background:#dbeafe;color:#1e40af',
                                    'received'   => 'background:#f3f4f6;color:#374151',
                                    'rejected'   => 'background:#fee2e2;color:#991b1b',
                                ];
                                $style = $statusColors[$transfer->status] ?? 'background:#f3f4f6;color:#374151';
                            @endphp
                            <span style="padding:2px 10px;border-radius:20px;font-size:11px;
                                     font-weight:500;{{ $style }}">
                            {{ ucfirst($transfer->status) }}
                        </span>
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Requested by</dt>
                        <dd class="text-gray-700">{{ $transfer->requestedBy?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Approved by</dt>
                        <dd class="text-gray-700">{{ $transfer->approvedBy?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500 flex-shrink-0">Date</dt>
                        <dd class="text-gray-700">{{ $transfer->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    @if($transfer->notes)
                        <div>
                            <dt class="text-gray-500 mb-1">Notes</dt>
                            <dd class="text-gray-700 text-xs bg-gray-50 p-3 rounded-lg leading-relaxed">
                                {{ $transfer->notes }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Actions --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Actions
                </p>
                <div class="space-y-3">

                    @if($transfer->status === 'pending')

                        @can('approve transfers')
                            <form method="POST" action="{{ route('transfers.approve', $transfer) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full h-10 bg-green-600 hover:bg-green-700 text-white
                                       text-sm font-medium rounded-lg transition-colors">
                                    ✓ Approve transfer
                                </button>
                            </form>
                            <form method="POST" action="{{ route('transfers.reject', $transfer) }}"
                                  onsubmit="return confirm('Reject this transfer?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full h-10 bg-white border border-red-300 text-red-600
                                       hover:bg-red-600 hover:text-white text-sm font-medium
                                       rounded-lg transition-colors">
                                    ✗ Reject transfer
                                </button>
                            </form>
                        @endcan

                        @can('create transfers')
                            <a href="{{ route('transfers.edit', $transfer) }}"
                               class="w-full h-10 bg-gray-100 hover:bg-gray-200 text-gray-700
                              text-sm font-medium rounded-lg transition-colors"
                               style="display:flex;align-items:center;justify-content:center">
                                Edit transfer
                            </a>
                        @endcan

                    @endif

                    @if($transfer->status === 'approved')
                        @can('dispatch transfers')
                            <form method="POST" action="{{ route('transfers.dispatch', $transfer) }}"
                                  onsubmit="return confirm('Dispatch this transfer? Stock will be deducted from {{ addslashes($transfer->fromBranch?->name) }}.')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white
                                       text-sm font-medium rounded-lg transition-colors">
                                    Dispatch transfer
                                </button>
                            </form>
                        @endcan
                    @endif

                    @if($transfer->status === 'received')
                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-center">
                            <p class="text-sm font-medium text-green-700">Transfer completed</p>
                            <p class="text-xs text-green-500 mt-0.5">Stock has been updated</p>
                        </div>
                    @endif

                    @if($transfer->status === 'rejected')
                        <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-center">
                            <p class="text-sm font-medium text-red-700">Transfer rejected</p>
                            <p class="text-xs text-red-400 mt-0.5">No stock was moved</p>
                        </div>
                    @endif

                    {{-- Print PDF --}}
                    <a href="{{ route('pdf.stock-transfer', $transfer) }}"
                       target="_blank"
                       class="w-full h-10 bg-red-600 hover:bg-red-700 text-white text-sm
                        font-medium rounded-lg transition-colors"
                       style="display:flex;align-items:center;justify-content:center;gap:6px">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download PDF
                    </a>

                    <a href="{{ route('transfers.index') }}"
                       class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                          text-gray-600 text-sm font-medium rounded-lg transition-colors"
                       style="display:flex;align-items:center;justify-content:center">
                        ← Back to transfers
                    </a>

                </div>
            </div>

        </div>

        {{-- Right: items + receive form --}}
        <div class="space-y-4">

            {{-- Items table --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <p class="font-semibold text-gray-700">Transfer items</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $transfer->items->count() }} {{ Str::plural('item', $transfer->items->count()) }}
                    </p>
                </div>
                <table class="w-full text-sm" style="border-collapse:collapse">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Product
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Requested
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Received
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transfer->items as $item)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">
                                    {{ $item->product?->name ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-400 font-mono">
                                    {{ $item->product?->sku }}
                                </p>
                            </td>
                            <td class="px-5 py-3 text-gray-700">
                                {{ number_format($item->quantity_requested, 2) }}
                                <span class="text-xs text-gray-400 ml-1">
                                {{ $item->product?->unit }}
                            </span>
                            </td>
                            <td class="px-5 py-3">
                                @if($transfer->status === 'received')
                                    @php
                                        $short = $item->quantity_received < $item->quantity_requested;
                                    @endphp
                                    <span class="font-medium {{ $short ? 'text-amber-600' : 'text-green-600' }}">
                                    {{ number_format($item->quantity_received, 2) }}
                                </span>
                                    <span class="text-xs text-gray-400 ml-1">
                                    {{ $item->product?->unit }}
                                </span>
                                    @if($short)
                                        <p class="text-xs text-amber-500">Short by {{ number_format($item->quantity_requested - $item->quantity_received, 2) }}</p>
                                    @endif
                                @else
                                    <span class="text-gray-300 text-xs">Not yet received</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Receive form --}}
            @if($transfer->status === 'dispatched')
                @can('receive transfers')
                    <div id="receive" class="bg-white border border-blue-200 rounded-xl overflow-hidden">
                        <div class="px-5 py-4 border-b border-blue-100 bg-blue-50">
                            <p class="font-semibold text-blue-800">Confirm receipt</p>
                            <p class="text-xs text-blue-600 mt-0.5">
                                Enter actual quantities received at
                                <strong>{{ $transfer->toBranch?->name }}</strong>
                            </p>
                        </div>
                        <form method="POST" action="{{ route('transfers.receive', $transfer) }}">
                            @csrf @method('PATCH')
                            <div class="divide-y divide-gray-50">
                                @foreach($transfer->items as $item)
                                    <div class="px-5 py-4"
                                         style="display:flex;align-items:center;
                                    justify-content:space-between;gap:16px">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">
                                                {{ $item->product?->name }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                Requested:
                                                {{ number_format($item->quantity_requested, 2) }}
                                                {{ $item->product?->unit }}
                                            </p>
                                        </div>
                                        <div style="width:130px;flex-shrink:0">
                                            <label class="block text-xs text-gray-500 mb-1">
                                                Qty received
                                            </label>
                                            <input type="number"
                                                   name="received_quantities[{{ $item->id }}]"
                                                   value="{{ $item->quantity_requested }}"
                                                   min="0" step="0.01"
                                                   class="w-full h-10 px-3 rounded-lg border border-gray-300
                                              bg-white text-sm text-gray-800 focus:outline-none
                                              focus:ring-2 focus:ring-blue-500 text-right">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                                <button type="submit"
                                        class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white
                                       text-sm font-medium rounded-lg transition-colors
                                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Confirm receipt & update stock
                                </button>
                            </div>
                        </form>
                    </div>
                @endcan
            @endif

        </div>

    </div>

@endsection
