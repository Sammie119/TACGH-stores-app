{{-- resources/views/audit/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Audit Log')
@section('header', 'Audit Log')
@section('subheader', 'Full system activity trail')

@section('content')

    {{-- Summary --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total events
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                {{ number_format($totalCount) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">All time</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Today's activity
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                {{ number_format($todayCount) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">
                {{ now()->format('d M Y') }}
            </p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <form method="GET" action="{{ route('audit.index') }}"
              style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap">

            {{-- Search --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Search description
                </label>
                <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center
                             pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1.5"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="Search activity…"
                           class="h-9 pl-9 pr-3 rounded-lg border border-gray-300
                              bg-white text-sm text-gray-800 focus:outline-none
                              focus:ring-2 focus:ring-blue-500"
                           style="width:200px">
                </div>
            </div>

            {{-- User --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    User
                </label>
                <select name="user_id"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                           text-sm text-gray-700 focus:outline-none
                           focus:ring-2 focus:ring-blue-500"
                        style="min-width:160px">
                    <option value="">All users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Module --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Module
                </label>
                <select name="log_name"
                        class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                           text-sm text-gray-700 focus:outline-none
                           focus:ring-2 focus:ring-blue-500">
                    <option value="">All modules</option>
                    @foreach($logNames as $name)
                        <option value="{{ $name }}"
                            {{ request('log_name') === $name ? 'selected' : '' }}>
                            {{ ucfirst($name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date from --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    From
                </label>
                <input type="date" name="date_from"
                       value="{{ request('date_from') }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-700 focus:outline-none
                          focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Date to --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    To
                </label>
                <input type="date" name="date_to"
                       value="{{ request('date_to') }}"
                       class="h-9 px-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-700 focus:outline-none
                          focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
                       font-medium rounded-lg transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search','user_id','log_name','date_from','date_to']))
                <a href="{{ route('audit.index') }}"
                   class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
                   style="display:inline-flex;align-items:center">
                    Clear
                </a>
            @endif

        </form>
    </div>

    {{-- Log table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100"
             style="display:flex;align-items:center;justify-content:space-between">
            <p class="font-semibold text-gray-700">
                Activity log
                <span class="text-sm font-normal text-gray-400 ml-2">
                {{ $logs->total() }} events
            </span>
            </p>
            <p class="text-xs text-gray-400">
                Immutable — records cannot be modified
            </p>
        </div>

        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide"
                    style="width:160px">Date & time</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">User</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Module</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Description</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Subject</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                @php
                    $moduleColors = [
                        'sales'     => 'background:#fef3c7;color:#92400e',
                        'products'  => 'background:#dcfce7;color:#166534',
                        'transfers' => 'background:#dbeafe;color:#1e40af',
                        'returns'   => 'background:#f3e8ff;color:#6b21a8',
                        'banking'   => 'background:#fefce8;color:#854d0e',
                        'default'   => 'background:#f3f4f6;color:#374151',
                    ];
                    $moduleStyle = $moduleColors[$log->log_name]
                        ?? $moduleColors['default'];
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">

                    {{-- Date --}}
                    <td class="px-5 py-3 text-xs text-gray-500 whitespace-nowrap">
                        {{ $log->created_at->format('d M Y') }}<br>
                        <span class="text-gray-400">
                        {{ $log->created_at->format('H:i:s') }}
                    </span>
                    </td>

                    {{-- User --}}
                    <td class="px-5 py-3">
                        @if($log->causer)
                            <div style="display:flex;align-items:center;gap:8px">
                                <div style="width:28px;height:28px;border-radius:50%;
                                    background:#dbeafe;color:#1d4ed8;display:flex;
                                    align-items:center;justify-content:center;
                                    font-size:11px;font-weight:600;flex-shrink:0">
                                    {{ strtoupper(substr($log->causer->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">
                                        {{ $log->causer->name }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $log->causer->getRoleNames()->first() }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <span class="text-gray-400 text-xs">System</span>
                        @endif
                    </td>

                    {{-- Module --}}
                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;{{ $moduleStyle }}">
                        {{ ucfirst($log->log_name ?? 'general') }}
                    </span>
                    </td>

                    {{-- Description --}}
                    <td class="px-5 py-3 text-gray-700 text-sm">
                        {{ $log->description }}
                    </td>

                    {{-- Subject --}}
                    <td class="px-5 py-3">
                        @if($log->subject)
                            @php
                                $subjectClass = class_basename($log->subject_type);
                            @endphp
                            <p class="text-xs font-medium text-gray-600">
                                {{ $subjectClass }}
                            </p>
                            <p class="text-xs text-gray-400">
                                #{{ $log->subject_id }}
                            </p>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-3">
                        <a href="{{ route('audit.show', $log->id) }}"
                           class="text-xs text-blue-600 hover:underline font-medium">
                            Details
                        </a>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6"
                        class="px-5 py-12 text-center text-gray-400">
                        No activity recorded yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

@endsection
