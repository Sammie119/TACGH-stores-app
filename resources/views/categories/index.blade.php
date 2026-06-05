{{-- resources/views/categories/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Categories')
@section('header', 'Product Categories')
@section('subheader', 'Organise products into categories')

@section('content')

    <div style="display:flex;align-items:center;justify-content:space-between;
            gap:12px;margin-bottom:16px;flex-wrap:wrap">

        <form style="display:flex;align-items:center;gap:10px" method="GET"
              action="{{ route('categories.index') }}">
            <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search categories…"
                       class="h-9 pl-9 pr-3 rounded-lg border border-gray-300 bg-white
                          text-sm text-gray-800 focus:outline-none focus:ring-2
                          focus:ring-blue-500"
                       style="width:220px">
            </div>
            <button type="submit"
                    class="h-9 px-4 bg-white border border-gray-300 text-gray-600
                       text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('categories.index') }}"
                   class="text-sm text-gray-400 hover:text-red-500 transition-colors">Clear</a>
            @endif
        </form>

        @can('create products')
            <a href="{{ route('categories.create') }}"
               class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium
              rounded-lg transition-colors"
               style="display:inline-flex;align-items:center;gap:6px;white-space:nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add category
            </a>
        @endcan
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('categories.index')"
    />

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Category
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Parent
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Products
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Status
                </th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody>
            @forelse($categories as $category)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors
                       {{ $category->trashed() ? 'opacity-60 bg-red-50' : '' }}">

                    <td class="px-5 py-3">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:8px;background:#f0fdf4;
                                    color:#16a34a;display:flex;align-items:center;
                                    justify-content:center;font-size:13px;font-weight:600;
                                    flex-shrink:0">
                                {{ strtoupper(substr($category->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $category->name }}</p>
                                @if($category->description)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ Str::limit($category->description, 50) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td class="px-5 py-3 text-gray-600">
                        {{ $category->parent?->name ?? '—' }}
                    </td>

                    <td class="px-5 py-3">
                    <span style="display:inline-flex;align-items:center;padding:2px 8px;
                                 border-radius:20px;font-size:11px;font-weight:500;
                                 background:#eff6ff;color:#1d4ed8">
                        {{ $category->products_count }} products
                    </span>
                    </td>

                    <td class="px-5 py-3">
                        @if($category->trashed())
                            <span style="padding:2px 8px;border-radius:20px;font-size:11px;
                                     font-weight:500;background:#fee2e2;color:#991b1b">
                            Deleted
                        </span>
                        @elseif($category->is_active)
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
                        <div style="display:flex;align-items:center;gap:12px">
                            @if($category->trashed())
                                @can('create products')
                                    <form method="POST"
                                          action="{{ route('categories.restore', $category->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-green-600 hover:underline font-medium">
                                            Restore
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('categories.force-delete', $category->id) }}"
                                          onsubmit="return confirm('Permanently delete this category?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:underline font-medium">
                                            Delete permanently
                                        </button>
                                    </form>
                                @endcan
                            @else
                                @can('edit products')
                                    <a href="{{ route('categories.edit', $category) }}"
                                       class="text-xs text-blue-600 hover:underline font-medium">Edit</a>
                                @endcan
                                @can('delete products')
                                    <form method="POST"
                                          action="{{ route('categories.destroy', $category) }}"
                                          onsubmit="return confirm('Delete this category?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-500 hover:underline font-medium">
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
                    <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                        No categories found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($categories->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection
