{{-- resources/views/deposits/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Upload Deposit Slip')
@section('header', 'Upload Deposit Slip')
@section('subheader', 'Submit a bank pay-in slip for verification')

@section('content')

    {{-- Cashier's cash banking position breakdown --}}
    @php
        $myBalanceSales  = \App\Models\Sale::where('user_id', auth()->id())
                ->where('payment_method', 'cash')->where('status', 'completed')->sum('amount_paid')
            + \App\Models\Sale::where('user_id', auth()->id())
                ->where('payment_method', 'split')->where('split_method_1', 'cash')
                ->where('status', 'completed')->sum('split_amount_1')
            + \App\Models\Sale::where('user_id', auth()->id())
                ->where('payment_method', 'split')->where('split_method_2', 'cash')
                ->where('status', 'completed')->sum('split_amount_2');
        $myBalanceCons   = \App\Models\ConsignmentPayment::where('paid_by', auth()->id())
            ->where('payment_method', 'cash')->sum('amount');
        $myBalanceBanked = \App\Models\BankDeposit::where('deposited_by', auth()->id())
            ->where('status', 'verified')->sum('amount');
        $myBalance       = $myBalanceSales + $myBalanceCons - $myBalanceBanked;
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
            Your cash banking position
        </p>
        <div class="space-y-2 mb-4">
            <div style="display:flex;justify-content:space-between;align-items:center">
                <span class="text-sm text-gray-600">Cash from sales</span>
                <span class="text-sm font-medium text-blue-600">+ GHS {{ number_format($myBalanceSales, 2) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center">
                <span class="text-sm text-gray-600">Cash from consignment payments</span>
                <span class="text-sm font-medium text-orange-600">+ GHS {{ number_format($myBalanceCons, 2) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center">
                <span class="text-sm text-gray-600">Verified deposits (cleared)</span>
                <span class="text-sm font-medium text-green-600">− GHS {{ number_format($myBalanceBanked, 2) }}</span>
            </div>
        </div>
        <div style="border-top:1px solid #e5e7eb;padding-top:12px;
                    display:flex;justify-content:space-between;align-items:center">
            <span class="text-sm font-semibold text-gray-700">Outstanding to bank</span>
            <span class="text-xl font-bold {{ $myBalance > 0 ? 'text-red-600' : 'text-gray-500' }}">
                GHS {{ number_format($myBalance, 2) }}
            </span>
        </div>
        @if($myBalance <= 0)
            <p class="text-xs text-green-600 font-medium mt-1 text-right">Fully settled — no outstanding balance</p>
        @endif
    </div>

    <form id="main-form" method="POST"
          action="{{ route('deposits.store') }}"
          enctype="multipart/form-data">
        @csrf
        @include('deposits._form', ['deposit' => null])
    </form>
@endsection
