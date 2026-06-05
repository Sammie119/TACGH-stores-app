{{-- resources/views/branches/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Branch')
@section('header', 'Edit Branch')
@section('subheader', $branch->name)

@section('content')

    @can('delete branches')
        <form id="delete-form" method="POST"
              action="{{ route('branches.destroy', $branch) }}"
              onsubmit="return confirm('Delete {{ addslashes($branch->name) }}?')">
            @csrf @method('DELETE')
        </form>
    @endcan

    <form id="main-form" method="POST"
          action="{{ route('branches.update', $branch) }}"
          enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('branches._form', ['branch' => $branch])
    </form>

@endsection
