{{-- resources/views/financial-years/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Financial Year')
@section('header', 'Edit Financial Year')
@section('subheader', $financialYear->name)

@section('content')
    <form id="main-form" method="POST"
          action="{{ route('financial-years.update', $financialYear) }}">
        @csrf @method('PUT')
        @include('financial-years._form', ['financialYear' => $financialYear])
    </form>
@endsection
