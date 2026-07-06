{{-- resources/views/consignments/create.blade.php --}}
@extends('layouts.app')
@section('title', 'New Consignment')
@section('header', 'New Consignment')
@section('subheader', 'Dispatch products for consignment sale')

@section('content')

    <form id="main-form" method="POST" action="{{ route('consignments.store') }}"
          onsubmit="return validateConsignmentForm(event)">
        @csrf
        @include('consignments._form')
    </form>

@endsection
