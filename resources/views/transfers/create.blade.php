{{-- resources/views/transfers/create.blade.php --}}
@extends('layouts.app')
@section('title', 'New Transfer')
@section('header', 'New Stock Transfer')
@section('subheader', 'Request stock movement between branches')

@section('content')

    <form id="main-form" method="POST" action="{{ route('transfers.store') }}">
        @csrf
        @include('transfers._form', ['transfer' => null])
    </form>

@endsection
