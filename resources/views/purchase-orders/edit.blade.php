{{-- resources/views/purchase-orders/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Purchase Order')
@section('header', 'Edit Purchase Order')
@section('subheader', $purchaseOrder->po_number)

@section('content')

    <form id="delete-form" method="POST"
          action="{{ route('purchase-orders.destroy', $purchaseOrder) }}"
          onsubmit="return confirm('Delete this purchase order?')">
        @csrf @method('DELETE')
    </form>

    <form id="main-form" method="POST"
          action="{{ route('purchase-orders.update', $purchaseOrder) }}">
        @csrf @method('PUT')
        @include('purchase-orders._form', ['purchaseOrder' => $purchaseOrder])
    </form>

@endsection
