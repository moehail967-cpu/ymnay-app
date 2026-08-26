@extends('landlord.admin.admin-master')

@section('title') {{ __('Plugin Hook Log') }} @endsection
@section('page-title') {{ __('Plugin Hook Log') }} @endsection

@section('style')
<style>
.duration-badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 600;
}
.duration-fast  { background: #d1fae5; color: #065f46; }
.duration-mid   { background: #fef3c7; color: #92400e; }
.duration-slow  { background: #fee2e2; color: #991b1b; }
</style>
@endsection

@section('section')
<div class="p-6 space-y-5">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">{{ __('Plugin Hook Log') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Recent hook executions recorded by the plugin system.') }}</p>
        </div>
        <a href="{{ route('landlord.admin.plugins.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800">
            <i class="mdi mdi-arrow-left text-base"></i> {{ __('Back to Plugins') }}
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('landlord.admin.plugins.hookLog') }}"
          class="bg-white border border-gray-200 rounded-xl p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[160px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Plugin') }}</label>
            <select name="plugin"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">{{ __('All plugins') }}</option>
                @foreach($pluginIds as $pid)
                    <option value="{{ $pid }}" @selected($plugin === $pid)>{{ $pid }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Hook name') }}</label>
            <input type="text" name="hook" value="{{ $hook }}" placeholder="{{ __('e.g. nazmart:order_completed') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-indigo-700">
                <i class="mdi mdi-filter-outline"></i> {{ __('Filter') }}
            </button>
            @if($plugin || $hook)
                <a href="{{ route('landlord.admin.plugins.hookLog') }}"
                   class="inline-flex items-center gap-1.5 border border-gray-300 text-gray-600 text-sm px-4 py-2 rounded-lg hover:bg-gray-50">
                    <i class="mdi mdi-close"></i> {{ __('Clear') }}
                </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        @if($logs->total() === 0)
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <i class="mdi mdi-history text-5xl mb-3"></i>
                <p class="text-sm">{{ __('No hook log entries found.') }}</p>
                <p class="text-xs mt-1">{{ __('Hook calls are logged when they exceed the configured threshold.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">#</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Hook') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Plugin') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Duration') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Tenant') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Time') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($logs as $entry)
                        @php
                            $ms = round($entry->duration_ms ?? 0, 2);
                            $durationClass = $ms < 10 ? 'duration-fast' : ($ms < 50 ? 'duration-mid' : 'duration-slow');
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $entry->id }}</td>
                            <td class="px-4 py-3">
                                <code class="text-xs bg-gray-100 text-indigo-700 px-2 py-0.5 rounded">
                                    {{ $entry->hook }}
                                </code>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-700 bg-gray-100 px-2 py-0.5 rounded-full">
                                    <i class="mdi mdi-puzzle-outline text-indigo-500"></i>
                                    {{ $entry->plugin_id ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="duration-badge {{ $durationClass }}">
                                    {{ $ms }} ms
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $entry->tenant_id ?? __('landlord') }}
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($entry->created_at)->diffForHumans() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
            <div class="border-t border-gray-100 px-4 py-3 flex items-center justify-between">
                <p class="text-xs text-gray-500">
                    {{ __('Showing') }} {{ $logs->firstItem() }}–{{ $logs->lastItem() }}
                    {{ __('of') }} {{ $logs->total() }} {{ __('entries') }}
                </p>
                <div class="text-sm">
                    {{ $logs->links() }}
                </div>
            </div>
            @endif
        @endif
    </div>

</div>
@endsection
