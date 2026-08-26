@php
    $route_name = 'landlord';
@endphp

@extends($route_name.'.admin.admin-master')
@section('title') {{__('User Activity Logs')}} @endsection

@section('style')
    <style>.hover\:text-white:hover { color: #fff !important; }</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

@php
    $totalCount   = $activities->total();
    $createdCount = $activities->getCollection()->where('description', 'created')->count();
    $updatedCount = $activities->getCollection()->where('description', 'updated')->count();
    $deletedCount = $activities->getCollection()->where('description', 'deleted')->count();
@endphp

{{-- ── Stats Row ── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
    @php
        $stats = [
            ['label' => __('Total Logs'),  'value' => $totalCount,   'icon' => 'mdi-history',              'color' => 'text-primary', 'bg' => 'bg-primary-soft'],
            ['label' => __('Created'),     'value' => $createdCount, 'icon' => 'mdi-plus-circle-outline',  'color' => 'text-success', 'bg' => 'bg-success-soft'],
            ['label' => __('Updated'),     'value' => $updatedCount, 'icon' => 'mdi-pencil-outline',       'color' => 'text-info',    'bg' => 'bg-info-soft'],
            ['label' => __('Deleted'),     'value' => $deletedCount, 'icon' => 'mdi-delete-outline',       'color' => 'text-danger',  'bg' => 'bg-danger-soft'],
        ];
    @endphp
    @foreach($stats as $stat)
        <div class="bg-surface rounded-xl border border-main shadow-main px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg {{ $stat['bg'] }} flex items-center justify-center shrink-0">
                <i class="mdi {{ $stat['icon'] }} {{ $stat['color'] }} text-xl"></i>
            </div>
            <div>
                <p class="text-xl font-black text-dark leading-none">{{ $stat['value'] }}</p>
                <p class="text-[11px] text-muted mt-1">{{ $stat['label'] }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- ── Main Card ── --}}
<div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">

    {{-- Card Header --}}
    <div class="px-5 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center shrink-0">
                <i class="mdi mdi-history text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Activity Log')}}</h3>
                <p class="text-xs text-muted">
                    {{ $totalCount > 0 ? __('Showing') . ' ' . $activities->firstItem() . '–' . $activities->lastItem() . ' ' . __('of') . ' ' . $totalCount . ' ' . __('entries') : __('No entries found') }}
                </p>
            </div>
        </div>
        <a href="{{route('landlord.admin.tenant')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary-soft border border-main text-primary text-sm font-semibold hover:bg-primary hover:text-white hover:border-primary transition whitespace-nowrap">
            <i class="mdi mdi-arrow-left text-base"></i>
            {{__('All Users')}}
        </a>
    </div>

    {{-- Activity List --}}
    <div class="divide-y divide-main">
        @forelse($activities as $activity)
            @php
                $ex       = explode("App\Models\\", $activity->subject_type) ?? [];
                $section  = $ex[1] ?? '';
                $userName = optional($activity->causer)->name ?? optional(Auth::guard('web')->user())->name;
                $desc     = $activity->description;

                $iconMap = [
                    'created' => ['mdi-plus-circle-outline', 'text-success', 'bg-success-soft', 'bg-green-50 text-green-700 border-green-200'],
                    'updated' => ['mdi-pencil-outline',      'text-info',    'bg-info-soft',    'bg-blue-50 text-blue-700 border-blue-200'],
                    'deleted' => ['mdi-delete-outline',      'text-danger',  'bg-danger-soft',  'bg-red-50 text-red-600 border-red-200'],
                ];
                $icon = $iconMap[$desc] ?? ['mdi-information-outline', 'text-primary', 'bg-primary-soft', 'bg-primary-soft text-primary border-primary'];
            @endphp

            <div class="px-5 sm:px-6 py-4 flex items-start gap-4 hover:bg-secondary transition-colors">

                {{-- Action Icon --}}
                <div class="w-9 h-9 rounded-lg {{ $icon[2] }} flex items-center justify-center shrink-0 mt-0.5">
                    <i class="mdi {{ $icon[0] }} {{ $icon[1] }} text-base"></i>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-dark leading-snug">
                        <span class="font-semibold">{{ $userName }}</span>
                        <span class="text-muted mx-1">{{ $desc }}</span>
                        @if($section)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-secondary border border-main text-[10px] font-bold text-dark uppercase">
                                {{ $section }}
                            </span>
                        @endif
                    </p>
                    @if($activity->properties && $activity->properties->isNotEmpty())
                        <p class="text-[11px] text-muted mt-0.5 truncate max-w-md">
                            {{ implode(', ', array_keys($activity->properties->toArray())) }}
                        </p>
                    @endif
                    <p class="text-[11px] text-muted mt-1 flex items-center gap-1">
                        <i class="mdi mdi-clock-outline text-xs"></i>
                        {{ $activity->updated_at->diffForHumans() }}
                        <span class="text-muted/50 mx-1">·</span>
                        {{ $activity->updated_at->format('d M Y, h:i A') }}
                    </p>
                </div>

                {{-- Action Badge --}}
                <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $icon[3] }} capitalize">
                    {{ $desc }}
                </span>

            </div>
        @empty
            <div class="px-6 py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-secondary border border-main flex items-center justify-center mx-auto mb-4">
                    <i class="mdi mdi-history text-3xl text-muted"></i>
                </div>
                <p class="text-sm font-semibold text-dark mb-1">{{__('No activity yet')}}</p>
                <p class="text-xs text-muted">{{__('Actions performed by users will appear here.')}}</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($activities->hasPages())
        <div class="px-5 sm:px-6 py-4 border-t border-main">
            {{ $activities->links('vendor.pagination.landlord-tailwind') }}
        </div>
    @endif

</div>

@endsection
