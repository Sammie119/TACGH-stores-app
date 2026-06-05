{{-- resources/views/suppliers/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Suppliers')
@section('header', 'Suppliers')
@section('subheader', 'Manage your suppliers')

@section('content')

    {{-- Summary --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total suppliers
            </p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">
                {{ number_format($suppliers->total()) }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">
                Total owed to suppliers
            </p>
            <p class="text-2xl font-semibold mt-1
                  {{ $totalOwed > 0 ? 'text-red-600' : 'text-gray-800' }}">
                GHS {{ number_format($totalOwed, 2) }}
            </p>
        </div>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px"
              method="GET" action="{{ route('suppliers.index') }}">
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
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search suppliers…"
                       class="h-9 pl-9 pr-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none
                          focus:ring-2 focus:ring-blue-500"
                       style="width:220px">
            </div>
            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                       text-sm font-medium rounded-lg hover:bg-gray-50
                       transition-colors">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('suppliers.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500">Clear</a>
            @endif
        </form>

        @can('create suppliers')
            <a href="{{ route('suppliers.create') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
              font-medium rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                Add supplier
            </a>
        @endcan
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('suppliers.index')"
    />

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Supplier</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Contact</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Phone</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Orders</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Balance owed</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($suppliers as $supplier)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $supplier->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:8px;
                                    background:#e0f2fe;color:#0369a1;display:flex;
                                    align-items:center;justify-content:center;
                                    font-size:12px;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($supplier->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ $supplier->name }}
                                </p>
                                <p class="text-xs text-gray-400 font-mono">
                                    {{ $supplier->code }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $supplier->contact_person ?? '—' }}
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $supplier->phone ?? '—' }}
                    </td>

                    <td class="px-5 py-3">
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#eff6ff;color:#1d4ed8">
                        {{ $supplier->purchase_orders_count }}
                    </span>
                    </td>

                    <td class="px-5 py-3">
                        @if($supplier->balance > 0)
                            <span class="font-semibold text-red-600">
                        GHS {{ number_format($supplier->balance, 2) }}
                    </span>
                        @else
                            <span class="text-gray-400 text-xs">Settled</span>
                        @endif
                    </td>

                    <td class="px-5 py-3">
                        @if($supplier->trashed())
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                 font-weight:500;background:#fee2e2;color:#991b1b">
                        Deleted
                    </span>
                        @elseif($supplier->is_active)
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
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($supplier->trashed())
                                @can('create suppliers')
                                    <form method="POST"
                                          action="{{ route('suppliers.restore', $supplier->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-green-600
                                               hover:underline font-medium">
                                            Restore
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('suppliers.force-delete', $supplier->id) }}"
                                          onsubmit="return confirm('Permanently delete?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600
                                               hover:underline font-medium">
                                            Delete permanently
                                        </button>
                                    </form>
                                @endcan
                            @else
                                <a href="{{ route('suppliers.show', $supplier) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">
                                    View
                                </a>
                                @can('edit suppliers')
                                    <a href="{{ route('suppliers.edit', $supplier) }}"
                                       class="text-xs text-gray-600 hover:underline font-medium">
                                        Edit
                                    </a>
                                @endcan
                                @can('delete suppliers')
                                    <form method="POST"
                                          action="{{ route('suppliers.destroy', $supplier) }}"
                                          onsubmit="return confirm('Delete this supplier?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-500
                                               hover:underline font-medium">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7"
                        class="px-5 py-12 text-center text-gray-400">
                        No suppliers found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        @if($suppliers->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

@endsection
