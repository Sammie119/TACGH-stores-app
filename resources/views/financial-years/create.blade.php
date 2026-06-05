{{-- resources/views/financial-years/create.blade.php --}}
@extends('layouts.app')
@section('title', 'New Financial Year')
@section('header', 'New Financial Year')
@section('subheader', 'Create a new accounting period')

@section('content')
    <form id="main-form" method="POST"
          action="{{ route('financial-years.store') }}">
        @csrf
        @include('financial-years._form', ['financialYear' => null])
    </form>
@endsection
