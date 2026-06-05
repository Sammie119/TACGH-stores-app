{{-- resources/views/purchase-orders/create.blade.php --}}
@extends('layouts.app')
@section('title', 'New Purchase Order')
@section('header', 'New Purchase Order')
@section('subheader', 'Create a purchase order for stock')

@section('content')
    <form id="main-form" method="POST" action="{{ route('purchase-orders.store') }}">
        @csrf
        @include('purchase-orders._form', ['purchaseOrder' => null])
    </form>
@endsection
