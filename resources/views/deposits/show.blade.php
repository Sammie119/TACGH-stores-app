{{-- resources/views/deposits/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Deposit Details')
@section('header', 'Deposit Details')
@section('subheader', 'GHS ' . number_format($deposit->amount, 2))

@section('content')

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

        {{-- Left: info + actions --}}
        <div class="space-y-4">

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    Deposit information
                </p>
                @php
                    $statusColors = [
                        'pending'  => 'background:#fef3c7;color:#92400e',
                        'verified' => 'background:#dcfce7;color:#166534',
                        'rejected' => 'background:#fee2e2;color:#991b1b',
                    ];
                @endphp
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Branch</dt>
                        <dd class="font-medium text-gray-800">
                            {{ $deposit->branch?->name ?? '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Amount</dt>
                        <dd class="font-semibold text-gray-800 text-base">
                            GHS {{ number_format($deposit->amount, 2) }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Bank</dt>
                        <dd class="text-gray-700">{{ $deposit->bank_name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Reference</dt>
                        <dd class="font-mono text-sm text-gray-700">
                            {{ $deposit->reference_no ?? '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Deposit date</dt>
                        <dd class="text-gray-700">
                            {{ \Carbon\Carbon::parse($deposit->deposit_date)->format('d M Y') }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Submitted by</dt>
                        <dd class="text-gray-700">
                            {{ $deposit->depositedBy?->name ?? '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Verified by</dt>
                        <dd class="text-gray-700">
                            {{ $deposit->verifiedBy?->name ?? '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                        <span style="padding:2px 10px;border-radius:20px;font-size:11px;
                                     font-weight:500;
                                     {{ $statusColors[$deposit->status] ?? '' }}">
                            {{ ucfirst($deposit->status) }}
                        </span>
                        </dd>
                    </div>
                    @if($deposit->notes)
                        <div>
                            <dt class="text-gray-500 mb-1">Notes</dt>
                            <dd class="text-gray-700 text-xs bg-gray-50 p-3 rounded-lg">
                                {{ $deposit->notes }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Actions --}}
            @if($deposit->status === 'pending')
                <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">
                        Actions
                    </p>
                    @can('verify deposits')
                        <form method="POST" action="{{ route('deposits.verify', $deposit) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="w-full h-10 bg-green-600 hover:bg-green-700 text-white
                               text-sm font-medium rounded-lg transition-colors">
                                ✓ Verify deposit
                            </button>
                        </form>
                        <form method="POST" action="{{ route('deposits.reject', $deposit) }}"
                              onsubmit="return confirm('Reject this deposit?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="w-full h-10 bg-white border border-red-300 text-red-600
                               hover:bg-red-600 hover:text-white text-sm font-medium
                               rounded-lg transition-colors">
                                ✗ Reject deposit
                            </button>
                        </form>
                    @endcan
                    @can('create deposits')
                        <a href="{{ route('deposits.edit', $deposit) }}"
                           class="w-full h-10 bg-gray-100 hover:bg-gray-200 text-gray-700
                      text-sm font-medium rounded-lg transition-colors"
                           style="display:flex;align-items:center;justify-content:center">
                            Edit deposit
                        </a>
                    @endcan
                </div>
            @endif

            <a href="{{ route('deposits.index') }}"
               class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
               style="display:flex;align-items:center;justify-content:center">
                ← Back to deposits
            </a>
        </div>

        {{-- Right: slip image --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Pay-in slip</p>
            </div>
            <div class="p-5">
                @if($deposit->slip_image)
                    <a href="{{ Storage::url($deposit->slip_image) }}"
                       target="_blank">
                        <img src="{{ Storage::url($deposit->slip_image) }}"
                             alt="Pay-in slip"
                             style="width:100%;border-radius:8px;border:1px solid #e5e7eb;
                            object-fit:contain;max-height:500px">
                    </a>
                    <p class="text-xs text-gray-400 mt-2 text-center">
                        Click image to view full size
                    </p>
                @else
                    <div style="padding:48px;text-align:center;color:#d1d5db">
                        <p class="text-sm">No slip image uploaded</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection
