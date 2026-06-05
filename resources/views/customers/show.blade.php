{{-- resources/views/customers/show.blade.php --}}
@extends('layouts.app')
@section('title', $customer->name)
@section('header', $customer->name)
@section('subheader', 'Customer profile')

@section('content')

    <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start">

        {{-- Left: profile --}}
        <div class="space-y-4">

            <div class="bg-white border border-gray-200 rounded-xl p-5 text-center">
                <div style="width:60px;height:60px;border-radius:50%;background:#ede9fe;
                        color:#5b21b6;display:flex;align-items:center;
                        justify-content:center;font-size:22px;font-weight:700;
                        margin:0 auto 12px">
                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                </div>
                <p class="font-semibold text-gray-800 text-base">{{ $customer->name }}</p>
                @if($customer->email)
                    <p class="text-sm text-gray-400 mt-0.5">{{ $customer->email }}</p>
                @endif
                @if($customer->balance > 0)
                    <div style="margin-top:10px;padding:6px 12px;background:#fee2e2;
                        border-radius:20px;display:inline-block">
                        <p style="font-size:12px;font-weight:600;color:#991b1b">
                            Owes GHS {{ number_format($customer->balance, 2) }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase
                       tracking-widest mb-4">Details</p>
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="text-gray-700">{{ $customer->phone ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-700">{{ $customer->email ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Total spent</dt>
                        <dd class="font-semibold text-gray-800">
                            GHS {{ number_format($totalSales, 2) }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Total orders</dt>
                        <dd class="font-semibold text-gray-800">{{ $totalOrders }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Member since</dt>
                        <dd class="text-gray-700">
                            {{ $customer->created_at->format('d M Y') }}
                        </dd>
                    </div>
                </dl>
            </div>

            <a href="{{ route('customers.edit', $customer) }}"
               class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white text-sm
                  font-medium rounded-lg transition-colors"
               style="display:flex;align-items:center;justify-content:center">
                Edit customer
            </a>
        </div>

        {{-- Right: recent sales --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Recent sales</p>
            </div>
            <table class="w-full text-sm" style="border-collapse:collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                               text-gray-500 uppercase">Invoice</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                               text-gray-500 uppercase">Branch</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold
                               text-gray-500 uppercase">Amount</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                               text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-2.5 text-left text-xs font-semibold
                               text-gray-500 uppercase">Date</th>
                </tr>
                </thead>
                <tbody>
                @forelse($recentSales as $sale)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('sales.show', $sale) }}"
                               class="font-mono text-sm text-blue-600 hover:underline">
                                {{ $sale->invoice_no }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ $sale->branch?->name }}
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-gray-800">
                            GHS {{ number_format($sale->total_amount, 2) }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $sc = [
                                    'completed' => 'background:#dcfce7;color:#166534',
                                    'partial'   => 'background:#fef3c7;color:#92400e',
                                    'credit'    => 'background:#fee2e2;color:#991b1b',
                                    'cancelled' => 'background:#f3f4f6;color:#374151',
                                ][$sale->status] ?? '';
                            @endphp
                            <span style="padding:2px 8px;border-radius:20px;
                                     font-size:11px;font-weight:500;{{ $sc }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">
                            {{ $sale->created_at->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            class="px-5 py-10 text-center text-gray-400">
                            No sales yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection
