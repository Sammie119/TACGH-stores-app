{{-- resources/views/users/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit User')
@section('header', 'Edit User')
@section('subheader', $user->name)

@section('content')

    {{-- Delete form lives OUTSIDE main form --}}
    @can('delete users')
        @if($user->id !== auth()->id())
            <form id="delete-form" method="POST" action="{{ route('users.destroy', $user) }}"
                  onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This can be restored later.')">
                @csrf @method('DELETE')
            </form>
        @endif
    @endcan

    <form id="main-form" method="POST" action="{{ route('users.update', $user) }}">
        @csrf @method('PUT')
        @include('users._form', ['user' => $user])
    </form>

@endsection
