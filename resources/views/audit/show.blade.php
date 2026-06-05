{{-- resources/views/audit/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Activity #' . $log->id)
@section('header', 'Activity Details')
@section('subheader', 'Event #' . $log->id)

@section('content')

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

        {{-- Left: event info --}}
        <div class="space-y-4">

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase
                       tracking-widest mb-4">
                    Event information
                </p>

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

                <dl class="space-y-3 text-sm">
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Event ID</dt>
                        <dd class="font-mono text-gray-700">#{{ $log->id }}</dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Date & time</dt>
                        <dd class="text-gray-700">
                            {{ $log->created_at->format('d M Y H:i:s') }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Module</dt>
                        <dd>
                        <span style="padding:2px 10px;border-radius:20px;
                                     font-size:11px;font-weight:500;
                                     {{ $moduleStyle }}">
                            {{ ucfirst($log->log_name ?? 'general') }}
                        </span>
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Description</dt>
                        <dd class="text-gray-700 text-right">
                            {{ $log->description }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Subject type</dt>
                        <dd class="text-gray-700">
                            {{ $log->subject_type
                                ? class_basename($log->subject_type)
                                : '—' }}
                        </dd>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px">
                        <dt class="text-gray-500">Subject ID</dt>
                        <dd class="font-mono text-gray-700">
                            {{ $log->subject_id ?? '—' }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Performed by --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase
                       tracking-widest mb-4">
                    Performed by
                </p>
                @if($log->causer)
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:44px;height:44px;border-radius:50%;
                            background:#dbeafe;color:#1d4ed8;display:flex;
                            align-items:center;justify-content:center;
                            font-size:16px;font-weight:700;flex-shrink:0">
                            {{ strtoupper(substr($log->causer->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">
                                {{ $log->causer->name }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $log->causer->email }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $log->causer->getRoleNames()->implode(', ') }}
                            </p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400">System / automated action</p>
                @endif
            </div>

            <a href="{{ route('audit.index') }}"
               class="w-full h-10 bg-white border border-gray-300 hover:bg-gray-50
                  text-gray-600 text-sm font-medium rounded-lg transition-colors"
               style="display:flex;align-items:center;justify-content:center">
                ← Back to audit log
            </a>

        </div>

        {{-- Right: changed properties --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <p class="font-semibold text-gray-700">Changed properties</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    Before and after values recorded at the time of the event
                </p>
            </div>

            @php
                $properties = $log->properties ?? collect();
                $old        = $properties->get('old', []);
                $attributes = $properties->get('attributes', []);
            @endphp

            @if($properties->isEmpty() || ($properties->count() === 0))
                <div class="px-5 py-10 text-center text-sm text-gray-400">
                    No property changes recorded for this event.
                </div>

            @elseif(!empty($attributes) || !empty($old))

                <table class="w-full text-sm" style="border-collapse:collapse">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                               text-gray-500 uppercase tracking-wide">
                            Field
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                               text-gray-500 uppercase tracking-wide">
                            Before
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold
                               text-gray-500 uppercase tracking-wide">
                            After
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($attributes as $field => $newValue)
                        @php
                            $oldValue = $old[$field] ?? null;
                            $changed  = $oldValue !== $newValue;
                        @endphp
                        <tr class="border-b border-gray-50
                           {{ $changed ? 'bg-amber-50' : '' }}">
                            <td class="px-5 py-3 font-mono text-xs text-gray-600">
                                {{ $field }}
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">
                                @if($oldValue === null)
                                    <span class="text-gray-300">—</span>
                                @elseif(is_bool($oldValue))
                                    {{ $oldValue ? 'true' : 'false' }}
                                @else
                                    {{ Str::limit((string) $oldValue, 60) }}
                                @endif
                            </td>
                            <td class="px-5 py-3 text-xs
                               {{ $changed ? 'font-medium text-gray-800' : 'text-gray-500' }}">
                                @if($newValue === null)
                                    <span class="text-gray-300">—</span>
                                @elseif(is_bool($newValue))
                                    {{ $newValue ? 'true' : 'false' }}
                                @else
                                    {{ Str::limit((string) $newValue, 60) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            @else

                {{-- Show raw properties if not old/attributes format --}}
                <div class="p-5">
            <pre style="font-size:12px;color:#374151;background:#f9fafb;
                        padding:14px;border-radius:8px;overflow-x:auto;
                        white-space:pre-wrap;word-break:break-all;
                        border:1px solid #e5e7eb">{{ json_encode($properties->toArray(), JSON_PRETTY_PRINT) }}</pre>
                </div>

            @endif

        </div>

    </div>

@endsection
