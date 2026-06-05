{{-- resources/views/stock-takes/create.blade.php --}}
@extends('layouts.app')
@section('title', 'New Stock Take')
@section('header', 'New Stock Take')
@section('subheader', 'Start a physical inventory count')

@section('content')
    <form id="main-form" method="POST" action="{{ route('stock-takes.store') }}">
        @csrf
        @include('stock-takes._form')
    </form>
@endsection
