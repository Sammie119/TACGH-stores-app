{{-- resources/views/products/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add Product')
@section('header', 'Add Product')
@section('subheader', 'Add a new product to the catalogue')

@section('content')

    <form id="main-form"
          method="POST"
          action="{{ route('products.store') }}"
          enctype="multipart/form-data">
        @csrf
        @include('products._form', ['product' => null])
    </form>

@endsection
