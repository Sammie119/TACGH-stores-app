{{-- resources/views/suppliers/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Supplier')
@section('header', 'Edit Supplier')
@section('subheader', $supplier->name)

@section('content')

    <form id="delete-form" method="POST"
          action="{{ route('suppliers.destroy', $supplier) }}"
          onsubmit="return confirm('Delete {{ addslashes($supplier->name) }}?')">
        @csrf @method('DELETE')
    </form>

    <form id="main-form" method="POST"
          action="{{ route('suppliers.update', $supplier) }}">
        @csrf @method('PUT')
        @include('suppliers._form', ['supplier' => $supplier])
    </form>

@endsection
