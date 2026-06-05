{{-- resources/views/coming-soon.blade.php --}}
@extends('layouts.app')

@section('title', 'Coming Soon')
@section('header', 'Coming Soon')

@section('content')
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-700">This module is being built</h2>
        <p class="text-gray-400 mt-2 text-sm">Check back soon — we're working through each module step by step.</p>
        <a href="{{ route('dashboard') }}"
           class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm
              font-medium rounded-lg hover:bg-blue-700 transition-colors">
            ← Back to dashboard
        </a>
    </div>
@endsection
