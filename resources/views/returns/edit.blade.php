{{-- resources/views/returns/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Return')
@section('header', 'Edit Return')
@section('subheader', 'Return #' . $return->id . ' — ' . $return->sale?->invoice_no)

@section('content')

    {{-- Delete form outside main form --}}
    @can('create returns')
        @if($return->status === 'pending')
            <form id="delete-form" method="POST"
                  action="{{ route('returns.destroy', $return) }}"
                  onsubmit="return confirm('Delete this return request?')">
                @csrf @method('DELETE')
            </form>
        @endif
    @endcan

    <form id="main-form" method="POST"
          action="{{ route('returns.update', $return) }}">
        @csrf @method('PUT')
        @include('returns._edit_form', ['return' => $return])
    </form>

@endsection
