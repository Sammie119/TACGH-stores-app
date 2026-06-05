{{-- resources/views/suppliers/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add Supplier')
@section('header', 'Add Supplier')
@section('subheader', 'Register a new supplier')

@section('content')
    <form id="main-form" method="POST" action="{{ route('suppliers.store') }}">
        @csrf
        @include('suppliers._form', ['supplier' => null])
    </form>
@endsection
