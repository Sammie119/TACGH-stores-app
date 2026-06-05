{{-- resources/views/users/show.blade.php --}}
@extends('layouts.app')
@section('title', $user->name)
@section('header', $user->name)
@section('subheader', $user->email)

@section('content')
    <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start">

        {{-- Left: Profile card --}}
        <div class="space-y-4">

            <div class="bg-white border border-gray-200 rounded-xl p-6 text-center">
                <div style="width:64px;height:64px;border-radius:50%;background:#dbeafe;
                        color:#1d4ed8;display:flex;align-items:center;justify-content:center;
                        font-size:22px;font-weight:700;margin:0 auto 12px">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <p class="font-semibold text-gray-800 text-base">{{ $user->name }}</p>
                <p class="text-sm text-gray-400 mt-0.5">{{ $user->email }}</p>
                <div style="display:flex;flex-wrap:wrap;gap:4px;justify-content:center;margin-top:10px">
                    @foreach($user->roles as $role)
                        <span style="padding:2px 10px;border-radius:20px;font-size:11px;
                             font-weight:500;background:#ede9fe;color:#5b21b6">
                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                </span>
                    @endforeach
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Details</p>
                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Branch</dt>
                        <dd class="font-medium text-gray-700">{{ $user->branch?->name ?? '—' }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            @if($user->is_active)
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                         font-weight:500;background:#dcfce7;color:#166534">
                                Active
                            </span>
                            @else
                                <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                         font-weight:500;background:#f3f4f6;color:#374151">
                                Inactive
                            </span>
                            @endif
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Joined</dt>
                        <dd class="font-medium text-gray-700">
                            {{ $user->created_at->format('d M Y') }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <dt class="text-gray-500">Total sales</dt>
                        <dd class="font-medium text-gray-700">{{ number_format($totalSales) }}</dd>
                    </div>
                </dl>
            </div>

            @can('edit users')
                <a href="{{ route('users.edit', $user) }}"
                   class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white text-sm
                  font-medium rounded-lg transition-colors"
                   style="display:flex;align-items:center;justify-content:center;gap:6px">
                    Edit user
                </a>
            @endcan

        </div>

        {{-- Right: Activity log --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Recent activity</p>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentActivity as $log)
                    <div class="px-5 py-3" style="display:flex;align-items:flex-start;gap:12px">
                        <div style="width:7px;height:7px;border-radius:50%;background:#93c5fd;
                            margin-top:5px;flex-shrink:0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700">{{ $log->description }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $log->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-gray-400">
                        No activity recorded yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
