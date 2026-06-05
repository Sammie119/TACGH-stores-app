{{-- resources/views/deposits/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Upload Deposit Slip')
@section('header', 'Upload Deposit Slip')
@section('subheader', 'Submit a bank pay-in slip for verification')

@section('content')
    <form id="main-form" method="POST"
          action="{{ route('deposits.store') }}"
          enctype="multipart/form-data">
        @csrf
        @include('deposits._form', ['deposit' => null])
    </form>
@endsection
