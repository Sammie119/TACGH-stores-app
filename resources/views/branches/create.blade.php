{{-- resources/views/branches/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add Branch')
@section('header', 'Add Branch')
@section('subheader', 'Create a new store or branch')

@section('content')
    <form id="main-form" method="POST"
          action="{{ route('branches.store') }}"
          enctype="multipart/form-data">
        @csrf
        @include('branches._form', ['branch' => null])
    </form>
@endsection
