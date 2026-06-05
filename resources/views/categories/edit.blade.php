{{-- resources/views/categories/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Category')
@section('header', 'Edit Category')
@section('subheader', $category->name)

@section('content')

    {{-- Delete form sits OUTSIDE the main form --}}
    @can('delete products')
        @if($category)
            <form id="delete-form"
                  method="POST" action="{{ route('categories.destroy', $category) }}"
                  onsubmit="return confirm('Delete {{ addslashes($category->name) }}?')">
                @csrf @method('DELETE')
            </form>
        @endif
    @endcan

    <form id="main-form" method="POST" action="{{ route('categories.update', $category) }}">
        @csrf @method('PUT')
        @include('categories._form', ['category' => $category])
    </form>
@endsection
