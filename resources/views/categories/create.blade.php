{{-- resources/views/categories/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add Category')
@section('header', 'Add Category')
@section('subheader', 'Create a new product category')

@section('content')
    <form method="POST" action="{{ route('categories.store') }}">
        @csrf
        @include('categories._form', ['category' => null])
    </form>
@endsection
