{{-- resources/views/deposits/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Deposit')
@section('header', 'Edit Deposit')
@section('subheader', 'GHS ' . number_format($deposit->amount, 2))

@section('content')

    @can('create deposits')
        <form id="delete-form" method="POST"
              action="{{ route('deposits.destroy', $deposit) }}"
              onsubmit="return confirm('Delete this deposit?')">
            @csrf @method('DELETE')
        </form>
    @endcan

    <form id="main-form" method="POST"
          action="{{ route('deposits.update', $deposit) }}"
          enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('deposits._form', ['deposit' => $deposit])
    </form>

@endsection
