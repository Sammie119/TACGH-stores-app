{{-- resources/views/customers/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Customer')
@section('header', 'Edit Customer')
@section('subheader', $customer->name)

@section('content')

    <form id="delete-form" method="POST"
          action="{{ route('customers.destroy', $customer) }}"
          onsubmit="return confirm('Delete {{ addslashes($customer->name) }}?')">
        @csrf @method('DELETE')
    </form>

    <form id="main-form" method="POST"
          action="{{ route('customers.update', $customer) }}">
        @csrf @method('PUT')
        @include('customers._form', ['customer' => $customer])
    </form>

@endsection
