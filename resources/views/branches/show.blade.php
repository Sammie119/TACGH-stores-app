{{-- resources/views/branches/show.blade.php --}}
@extends('layouts.app')

@section('title', $branch->name)
@section('header', $branch->name)
@section('subheader', 'Branch details and summary')

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Branch info --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Branch logo --}}
            @if($branch->logo)
                <div style="margin-bottom:16px;text-align:center">
                    <img src="{{ branch_logo_url($branch->logo) }}"
                         alt="{{ $branch->name }}"
                         style="height:72px;max-width:200px;object-fit:contain;
                margin:0 auto;display:block;border-radius:8px;
                border:1px solid #e5e7eb;background:#f9fafb;padding:8px">
                </div>
            @endif

            {{-- Details card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-semibold text-gray-700">Branch details</h2>
                    @can('edit branches')
                        <a href="{{ route('branches.edit', $branch) }}"
                           class="text-sm text-blue-600 hover:underline">Edit</a>
                    @endcan
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wide mb-1">Name</dt>
                        <dd class="font-medium text-gray-800">{{ $branch->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wide mb-1">Code</dt>
                        <dd class="font-mono text-gray-800">{{ $branch->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wide mb-1">Phone</dt>
                        <dd class="text-gray-800">{{ $branch->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wide mb-1">Email</dt>
                        <dd class="text-gray-800">{{ $branch->email ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 text-xs uppercase tracking-wide mb-1">Address</dt>
                        <dd class="text-gray-800">{{ $branch->address ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wide mb-1">Manager</dt>
                        <dd class="text-gray-800">{{ $branch->manager?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 text-xs uppercase tracking-wide mb-1">Status</dt>
                        <dd>
                            @if($branch->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full
                                         text-xs font-medium bg-green-100 text-green-700">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full
                                         text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Staff at this branch --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-700">
                        Staff ({{ $branch->users->count() }})
                    </h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($branch->users as $user)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center
                                    justify-center text-xs font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">
                        {{ $user->getRoleNames()->implode(', ') }}
                    </span>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">
                            No staff assigned to this branch yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Stats sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Total sales</p>
                <p class="text-2xl font-semibold text-gray-800 mt-1">
                    GHS {{ number_format($totalSales, 2) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">All time completed sales</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Stock value</p>
                <p class="text-2xl font-semibold text-gray-800 mt-1">
                    GHS {{ number_format($totalStockValue, 2) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">At cost price</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Created</p>
                <p class="text-sm font-medium text-gray-800 mt-1">
                    {{ $branch->created_at->format('d M Y') }}
                </p>
            </div>
        </div>

    </div>
@endsection
