{{-- resources/views/products/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Product')
@section('header', 'Edit Product')
@section('subheader', $product->name)

@section('content')

    {{-- Delete form outside main form --}}
    @can('delete products')
        <form id="delete-form"
              method="POST"
              action="{{ route('products.destroy', $product) }}"
              onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This can be restored later.')">
            @csrf @method('DELETE')
        </form>
    @endcan

    <form id="main-form"
          method="POST"
          action="{{ route('products.update', $product) }}"
          enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('products._form', ['product' => $product])
    </form>

@endsection
