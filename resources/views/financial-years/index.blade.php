{{-- resources/views/financial-years/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Financial Years')
@section('header', 'Financial Years')
@section('subheader', 'Manage accounting periods')

@section('content')

    {{-- Active year banner --}}
    @if($activeYear)
        <div style="display:flex;align-items:center;gap:10px;padding:14px 18px;
            background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;
            margin-bottom:20px">
            <div style="width:8px;height:8px;border-radius:50%;background:#16a34a;
                flex-shrink:0"></div>
            <div>
                <p style="font-size:13px;font-weight:600;color:#166534">
                    Active financial year: {{ $activeYear->name }}
                </p>
                <p style="font-size:12px;color:#16a34a;margin-top:1px">
                    {{ $activeYear->start_date->format('d M Y') }}
                    — {{ $activeYear->end_date->format('d M Y') }}
                </p>
            </div>
        </div>
    @else
        <div style="display:flex;align-items:center;gap:10px;padding:14px 18px;
            background:#fef2f2;border:1px solid #fecaca;border-radius:10px;
            margin-bottom:20px">
            <div style="width:8px;height:8px;border-radius:50%;background:#ef4444;
                flex-shrink:0"></div>
            <div>
                <p style="font-size:13px;font-weight:600;color:#dc2626">
                    No active financial year
                </p>
                <p style="font-size:12px;color:#ef4444;margin-top:1px">
                    Transactions cannot be processed until a financial year is activated.
                </p>
            </div>
        </div>
    @endif

    <div style="display:flex;align-items:center;justify-content:space-between;
            margin-bottom:16px">
        <div></div>
        @can('manage financial years')
            <a href="{{ route('financial-years.create') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
              font-medium rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                New financial year
            </a>
        @endcan
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('financial-years.index')"
    />

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Name</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Start date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">End date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($years as $year)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $year->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $year->name }}</p>
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $year->start_date->format('d M Y') }}
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $year->end_date->format('d M Y') }}
                    </td>

                    <td class="px-5 py-3">
                        @if($year->trashed())
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#fee2e2;color:#991b1b">
                            Deleted
                        </span>
                        @elseif($year->is_closed)
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#f3f4f6;color:#374151">
                            Closed
                        </span>
                        @elseif($year->is_active)
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#dcfce7;color:#166534">
                            Active
                        </span>
                        @else
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#fef3c7;color:#92400e">
                            Inactive
                        </span>
                        @endif
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:12px">
                            @if($year->trashed())
                                @can('manage financial years')
                                    <form method="POST"
                                          action="{{ route('financial-years.restore', $year->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-green-600
                                               hover:underline font-medium">
                                            Restore
                                        </button>
                                    </form>
                                @endcan
                            @else
                                @can('manage financial years')
                                    @if(!$year->is_active && !$year->is_closed)
                                        <form method="POST"
                                              action="{{ route('financial-years.activate', $year) }}">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-green-600
                                               hover:underline font-medium">
                                                Activate
                                            </button>
                                        </form>
                                    @endif

                                    @if(!$year->is_closed)
                                        <a href="{{ route('financial-years.edit', $year) }}"
                                           class="text-xs text-blue-600 hover:underline font-medium">
                                            Edit
                                        </a>
                                    @endif

                                    @if($year->is_active || (!$year->is_closed && !$year->is_active))
                                        <form method="POST"
                                              action="{{ route('financial-years.close', $year) }}"
                                              onsubmit="return confirm('Close {{ addslashes($year->name) }}? This cannot be undone and will lock all transactions.')">
                                            @csrf @method('PATCH')
                                            <button class="text-xs text-amber-600
                                               hover:underline font-medium">
                                                Close
                                            </button>
                                        </form>
                                    @endif

                                    @if(!$year->is_active && !$year->is_closed)
                                        <form method="POST"
                                              action="{{ route('financial-years.destroy', $year) }}"
                                              onsubmit="return confirm('Delete this financial year?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-500
                                               hover:underline font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                        No financial years found. Create one to start processing transactions.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

@endsection
