@extends('layouts.app')

@section('title', 'Branches')
@section('header', 'Branches')
@section('subheader', 'Manage all stores and branches')

@section('content')

    <div class="flex items-center justify-between mb-4">
        @can('create branches')
            <a href="{{ route('branches.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
              text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add branch
            </a>
        @endcan
    </div>

    <x-trash-banner
        :count="$trashedCount"
        :showing-trashed="$showingTrashed"
        :route="route('branches.index')"
    />

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm" style="border-collapse:collapse;min-width:600px">
            <thead>
            <tr class="bg-gray-50 text-left text-xs text-gray-500 uppercase tracking-wide border-b border-gray-200">
                <th class="px-5 py-3">Branch</th>
                <th class="px-5 py-3">Code</th>
                <th class="px-5 py-3">Manager</th>
                <th class="px-5 py-3">Phone</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($branches as $branch)
                <tr class="hover:bg-gray-50 transition-colors {{ $branch->trashed() ? 'opacity-60 bg-red-50' : '' }}">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if($branch->logo)
                                <img src="{{ branch_logo_url($branch->logo) }}"
                                     alt="{{ $branch->name }}"
                                     style="width:34px;height:34px;border-radius:8px;object-fit:contain;
                                     border:1px solid #e5e7eb;background:#fff;padding:2px;
                                     flex-shrink:0">
                            @else
                                <div style="width:34px;height:34px;border-radius:8px;background:#dbeafe;
                                    color:#1d4ed8;display:flex;align-items:center;
                                    justify-content:center;font-size:13px;font-weight:600;
                                    flex-shrink:0">
                                    {{ strtoupper(substr($branch->name, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-800">{{ $branch->name }}</p>
                                <p class="text-xs text-gray-400">{{ $branch->address }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 font-mono text-gray-600">{{ $branch->code }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $branch->manager?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $branch->phone ?? '—' }}</td>
                    <td class="px-5 py-3">
                        @if($branch->trashed())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
                                     font-medium bg-red-100 text-red-700">Deleted</span>
                        @elseif($branch->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
                                     font-medium bg-green-100 text-green-700">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
                                     font-medium bg-gray-100 text-gray-600">Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if($branch->trashed())
                                @can('create branches')
                                    <form method="POST"
                                          action="{{ route('branches.restore', $branch->id) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs text-green-600 hover:underline font-medium">
                                            Restore
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('branches.force-delete', $branch->id) }}"
                                          onsubmit="return confirm('Permanently delete this branch? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:underline font-medium">
                                            Delete permanently
                                        </button>
                                    </form>
                                @endcan
                            @else
                                <a href="{{ route('branches.show', $branch) }}"
                                   class="text-xs text-blue-600 hover:underline font-medium">View</a>
                                @can('edit branches')
                                    <a href="{{ route('branches.edit', $branch) }}"
                                       class="text-xs text-gray-600 hover:underline font-medium">Edit</a>
                                @endcan
                                @can('delete branches')
                                    <form method="POST"
                                          action="{{ route('branches.destroy', $branch) }}"
                                          onsubmit="return confirm('Delete this branch?')">
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
                    <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                        No branches found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($branches->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $branches->links() }}
            </div>
        @endif
    </div>
@endsection
