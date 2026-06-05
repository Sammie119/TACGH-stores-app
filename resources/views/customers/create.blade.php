{{-- resources/views/customers/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add Customer')
@section('header', 'Add Customer')
@section('subheader', 'Create a new customer record')

@section('content')
    <form id="main-form" method="POST" action="{{ route('customers.store') }}">
        @csrf
        @include('customers._form', ['customer' => null])
    </form>
@endsection
