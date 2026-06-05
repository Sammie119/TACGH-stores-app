{{-- resources/views/transfers/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Transfer')
@section('header', 'Edit Transfer')
@section('subheader', $transfer->reference_no)

@section('content')

    {{-- Delete form outside main form --}}
    @can('create transfers')
        <form id="delete-form" method="POST"
              action="{{ route('transfers.destroy', $transfer) }}"
              onsubmit="return confirm('Delete this transfer request?')">
            @csrf @method('DELETE')
        </form>
    @endcan

    <form id="main-form" method="POST"
          action="{{ route('transfers.update', $transfer) }}">
        @csrf @method('PUT')
        @include('transfers._form', ['transfer' => $transfer])
    </form>

@endsection
