@extends('landlord.admin.admin-master')

@section('title') {{ __('Plugin Install Log') }} @endsection
@section('page-title') {{ __('Plugin Install Log') }} @endsection

@section('section')
<div class="p-6 space-y-5">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">{{ __('Plugin Install Log') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Audit trail of all plugin installs and updates.') }}</p>
        </div>
        <a href="{{ route('landlord.admin.plugins.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800">
            <i class="mdi mdi-arrow-left text-base"></i> {{ __('Back to Plugins') }}
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        @if($logs->total() === 0)
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <i class="mdi mdi-shield-check-outline text-5xl mb-3"></i>
                <p class="text-sm">{{ __('No install log entries yet.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">#</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Plugin') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Action') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Version') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Source') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Checksum') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('By') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('IP') }}</th>
                            <th class="px-4 py-3 text-left font-medium">{{ __('Time') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($logs as $entry)
                        @php
                            $actionColor = match($entry->action) {
                                'install' => 'bg-blue-100 text-blue-700',
                                'update'  => 'bg-amber-100 text-amber-700',
                                default   => 'bg-gray-100 text-gray-600',
                            };
                            $sourceColor = $entry->source === 'upload'
                                ? 'bg-purple-100 text-purple-700'
                                : 'bg-indigo-100 text-indigo-700';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $entry->id }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-700 bg-gray-100 px-2 py-0.5 rounded-full">
                                    <i class="mdi mdi-puzzle-outline text-indigo-500"></i>
                                    {{ $entry->plugin_id }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $actionColor }}">
                                    {{ ucfirst($entry->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 text-xs">
                                @if($entry->from_version)
                                    <span class="line-through text-gray-400">{{ $entry->from_version }}</span>
                                    <i class="mdi mdi-arrow-right text-gray-400 mx-0.5"></i>
                                @endif
                                {{ $entry->version }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $sourceColor }}">
                                    <i class="mdi mdi-{{ $entry->source === 'upload' ? 'upload' : 'cloud-download-outline' }} mr-1"></i>
                                    {{ ucfirst($entry->source) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($entry->checksum_verified)
                                    <i class="mdi mdi-shield-check text-green-500 text-lg" title="{{ __('SHA256 verified') }}"></i>
                                @else
                                    <i class="mdi mdi-shield-off-outline text-gray-300 text-lg" title="{{ __('No checksum') }}"></i>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $entry->installed_by ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs font-mono">
                                {{ $entry->ip_address ?? '—' }}
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
