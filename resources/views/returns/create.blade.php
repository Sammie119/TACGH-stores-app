{{-- resources/views/returns/create.blade.php --}}
@extends('layouts.app')
@section('title', 'New Return')
@section('header', 'New Return')
@section('subheader', 'Process a product return')

@section('content')

    {{-- Search form — standalone, no connection to main-form --}}
    <form id="search-form"
          method="GET"
          action="{{ route('returns.create') }}">
    </form>

    {{-- Main return submission form --}}
    @if($sale)
        <form id="main-form"
              method="POST"
              action="{{ route('returns.store') }}">
            @csrf
            <input type="hidden" name="sale_id" value="{{ $sale->id }}">
        </form>
    @endif

    {{-- The visible layout --}}
    @include('returns._form', [
        'sale'      => $sale,
        'saleItems' => $saleItems,
        'invoiceNo' => $invoiceNo,
    ])

@endsection
