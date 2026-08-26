@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Core Features')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Page Header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="mdi mdi-shield-check text-white text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-dark font-urbanist">{{__('Core Features')}}</h3>
                <p class="text-xs text-muted">{{__('Built-in platform capabilities — always active, cannot be disabled')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                <i class="mdi mdi-lock-check text-sm"></i>
                {{ count($plugins) }} {{__('Always Active')}}
            </span>
            <a href="{{ route('landlord.admin.plugins.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
                <i class="mdi mdi-puzzle-outline text-sm"></i>
                {{__('All Plugins')}}
            </a>
        </div>
    </div>
</div>

{{-- Info banner --}}
<div class="flex items-start gap-3 px-4 py-3 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 text-xs mb-6">
    <i class="mdi mdi-information-outline text-base flex-shrink-0 mt-0.5"></i>
    <p>{{__('Core features are fundamental parts of the platform. They run automatically and cannot be toggled off. To manage optional plugins, visit the')}}
        <a href="{{ route('landlord.admin.plugins.index') }}" class="font-semibold underline underline-offset-2 hover:text-sky-600">{{__('Plugins page')}}</a>.
    </p>
</div>

@if(empty($plugins))
    <div class="bg-surface rounded-xl border border-main p-12 text-center">
        <i class="mdi mdi-shield-off-outline text-5xl text-subtle mb-3 block"></i>
        <p class="text-base font-semibold text-dark mb-1">{{__('No core features found')}}</p>
        <p class="text-sm text-muted">{{__('Core plugin manifests may not be loaded yet.')}}</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($plugins as $item)
            @php
                $manifest = $item['manifest'];
            @endphp

            <div class="bg-surface rounded-xl border border-emerald-100 shadow-main flex flex-col overflow-hidden">

                {{-- Card Header --}}
                <div class="px-5 pt-5 pb-3 flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-shield-check text-emerald-600 text-xl"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-dark font-urbanist truncate">{{ $manifest->name }}</p>
                            <p class="text-xs text-subtle font-mono">{{ $manifest->id }}</p>
                        </div>
                    </div>

                    {{-- Always Active badge --}}
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold flex-shrink-0 cursor-default whitespace-nowrap">
                        <i class="mdi mdi-lock-check text-xs"></i>
                        {{__('Always Active')}}
                    </span>
                </div>

                {{-- Description --}}
                @if($manifest->description)
                    <p class="px-5 pb-3 text-xs text-muted leading-relaxed line-clamp-2">
                        {{ $manifest->description }}
                    </p>
                @else
                    <p class="px-5 pb-3 text-xs text-subtle italic">
                        {{__('A core platform capability with no configurable options.')}}
                    </p>
                @endif

                {{-- Meta badges --}}
                <div class="px-5 pb-4 flex flex-wrap gap-1.5">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold
                        {{ $manifest->type === 'both' ? 'bg-blue-50 text-blue-600' : ($manifest->type === 'tenant' ? 'bg-emerald-50 text-emerald-700' : 'bg-purple-50 text-purple-700') }}">
                        <i class="mdi {{ $manifest->type === 'both' ? 'mdi-swap-horizontal' : ($manifest->type === 'tenant' ? 'mdi-store' : 'mdi-shield-account') }} text-xs"></i>
                        {{ ucfirst($manifest->type) }}
                    </span>

                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-500">
                        <i class="mdi mdi-gift-outline text-xs"></i>
                        {{__('Built-in')}}
                    </span>

                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{__('Active')}}
                    </span>
                </div>

                {{-- Footer --}}
                <div class="mt-auto px-5 py-3 bg-secondary border-t border-main flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 text-xs text-subtle">
                        <i class="mdi mdi-tag-outline text-xs"></i>
                        <span>v{{ $manifest->version }}</span>
                        @if($manifest->author)
                            <span class="text-main mx-0.5">·</span>
                            <span>{{ $manifest->author }}</span>
                        @endif
                    </div>

                    @if(app(\App\PluginSystem\SettingsManager::class)->hasDefinitions($manifest->id))
                        <a href="{{ route('landlord.admin.plugin.settings.show', $manifest->id) }}"
                           class="text-xs font-semibold text-muted hover:text-primary px-2.5 py-1 rounded-lg border border-main hover:border-primary/40 flex items-center gap-1 transition-colors">
                            <i class="mdi mdi-cog-outline text-sm"></i>
                            {{__('Settings')}}
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
