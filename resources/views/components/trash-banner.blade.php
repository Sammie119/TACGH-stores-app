{{-- resources/views/components/trash-banner.blade.php --}}
@props(['count', 'showingTrashed' => false, 'route'])

@if($count > 0 || $showingTrashed)
    <div class="mb-4 flex items-center justify-between px-4 py-3 rounded-lg
            {{ $showingTrashed ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50 border border-gray-200' }}">
        <div class="flex items-center gap-2 text-sm">
            @if($showingTrashed)
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span class="text-amber-700 font-medium">Showing deleted records</span>
            @else
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span class="text-gray-600">
                <span class="font-medium text-red-600">{{ $count }}</span>
                deleted {{ Str::plural('record', $count) }} hidden
            </span>
            @endif
        </div>
        <a href="{{ $showingTrashed ? $route : $route . '?trashed=1' }}"
           class="text-sm font-medium
              {{ $showingTrashed ? 'text-amber-700 hover:text-amber-900' : 'text-gray-500 hover:text-red-600' }}
              transition-colors">
            {{ $showingTrashed ? '← Show active records' : 'Show deleted records' }}
        </a>
    </div>
@endif
