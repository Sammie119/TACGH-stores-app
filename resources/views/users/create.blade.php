{{-- resources/views/users/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add User')
@section('header', 'Add User')
@section('subheader', 'Create a new system user')

@section('content')
    <form id="main-form" method="POST" action="{{ route('users.store') }}">
        @csrf
        @include('users._form', ['user' => null])
    </form>
@endsection
