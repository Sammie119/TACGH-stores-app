{{-- resources/views/customers/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Customers')
@section('header', 'Customers')
@section('subheader', 'Manage customer records')

@section('content')

    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px"
              method="GET" action="{{ route('customers.index') }}">
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
                       placeholder="Search name, phone, email…"
                       class="h-9 pl-9 pr-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none
                          focus:ring-2 focus:ring-blue-500"
                       style="width:240px">
            </div>
            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                       text-sm font-medium rounded-lg hover:bg-gray-50
                       transition-colors">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('customers.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500 transition-colors">
                    Clear
                </a>
            @endif
        </form>

        <a href="{{ route('customers.create') }}"
           class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm
              font-medium rounded-lg transition-colors"
           style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Add customer
        </a>
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('customers.index')"
    />

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Customer</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Phone</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Email</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Balance</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500
                           uppercase tracking-wide">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($customers as $customer)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $customer->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:50%;
                                    background:#ede9fe;color:#5b21b6;display:flex;
                                    align-items:center;justify-content:center;
                                    font-size:13px;font-weight:600;flex-shrink:0">
                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ $customer->name }}
                                </p>
                                @if($customer->address)
                                    <p class="text-xs text-gray-400">
                                        {{ Str::limit($customer->address, 40) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $customer->phone ?? '—' }}
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $customer->email ?? '—' }}
                    </td>

                    <td class="px-5 py-3">
                        @if($customer->balance > 0)
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#fee2e2;color:#991b1b">
                            Owes GHS {{ number_format($customer->balance, 2) }}
                        </span>
                        @else
                            <span class="text-xs text-gray-400">No balance</span>
                        @endif
                    </td>

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:12px">
                            @if($customer->trashed())
                                <form method="POST"
                                      action="{{ route('customers.restore', $customer->id) }}">
                                    @csrf @method('PATCH')
                                    <button class="text-xs text-green-600
                                               hover:underline font-medium">
                                        Restore
                                    </button>
                                </form>
                                <form method="POST"
                                      action="{{ route('customers.force-delete', $customer->id) }}"
                                      onsubmit="return confirm('Permanently delete {{ addslashes($customer->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-600
                                               hover:underline font-medium">
                                        Delete permanently
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('customers.show', $customer) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">
                                    View
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}"
                                   class="text-xs text-gray-600 hover:underline font-medium">
                                    Edit
                                </a>
                                <form method="POST"
                                      action="{{ route('customers.destroy', $customer) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($customer->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-500
                                               hover:underline font-medium">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                        No customers found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($customers->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

@endsection
