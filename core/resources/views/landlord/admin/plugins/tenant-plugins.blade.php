@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Tenant Plugins')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Page Header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="mdi mdi-store text-white text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-dark font-urbanist">{{__('Tenant Plugins')}}</h3>
                <p class="text-xs text-muted">{{__('Plugins available for tenants — assign them to price plans to control access')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-50 border border-sky-200 text-sky-700 text-xs font-semibold">
                <i class="mdi mdi-puzzle-outline text-sm"></i>
                {{ count($plugins) }} {{__('Plugins')}}
            </span>
            <a href="{{ route('landlord.admin.price.plan') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-primary/90 transition-colors shadow-sm">
                <i class="mdi mdi-tag-multiple-outline text-sm"></i>
                {{__('Manage Price Plans')}}
            </a>
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
    <p>{{__('Tenant plugins are managed by each tenant from their own admin panel. To control which plugins a tenant can access, assign them to a')}}
        <a href="{{ route('landlord.admin.price.plan') }}" class="font-semibold underline underline-offset-2 hover:text-sky-600">{{__('price plan')}}</a>.
        {{__('Tenants can then activate or deactivate their assigned plugins independently.')}}
    </p>
</div>

@if(empty($plugins))
    <div class="bg-surface rounded-xl border border-main p-12 text-center">
        <i class="mdi mdi-store-off-outline text-5xl text-subtle mb-3 block"></i>
        <p class="text-base font-semibold text-dark mb-1">{{__('No tenant plugins found')}}</p>
        <p class="text-sm text-muted">{{__('Upload a plugin with type "tenant" to see it here.')}}</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($plugins as $item)
            @php
                $manifest = $item['manifest'];
                $active   = $item['active'];
                $isPaid   = $manifest->pricing === 'paid';
            @endphp

            <div class="bg-surface rounded-xl border border-sky-100 shadow-main flex flex-col overflow-hidden">

                {{-- Card Header --}}
                <div class="px-5 pt-5 pb-3 flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-puzzle text-sky-500 text-xl"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-dark font-urbanist truncate">{{ $manifest->name }}</p>
                            <p class="text-xs text-subtle font-mono">{{ $manifest->id }}</p>
                        </div>
                    </div>

                    {{-- Tenant-managed badge --}}
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 border border-sky-200 text-sky-600 text-[10px] font-bold flex-shrink-0 cursor-default whitespace-nowrap">
                        <i class="mdi mdi-account-cog-outline text-xs"></i>
                        {{__('Tenant Managed')}}
                    </span>
                </div>

                {{-- Description --}}
                @if($manifest->description)
                    <p class="px-5 pb-3 text-xs text-muted leading-relaxed line-clamp-2">
                        {{ $manifest->description }}
                    </p>
                @else
                    <p class="px-5 pb-3 text-xs text-subtle italic">
                        {{__('Extends tenant store functionality.')}}
                    </p>
                @endif

                {{-- Meta badges --}}
                <div class="px-5 pb-4 flex flex-wrap gap-1.5">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-700">
                        <i class="mdi mdi-store text-xs"></i>
                        {{__('Tenant')}}
                    </span>

                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold
                        {{ $isPaid ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-500' }}">
                        <i class="mdi {{ $isPaid ? 'mdi-currency-usd' : 'mdi-gift-outline' }} text-xs"></i>
                        {{ ucfirst($manifest->pricing) }}
                    </span>

                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold
                        {{ $active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        {{ $active ? __('Globally Active') : __('Globally Inactive') }}
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

                    <a href="{{ route('landlord.admin.price.plan') }}"
                       class="text-xs font-semibold text-muted hover:text-primary px-2.5 py-1 rounded-lg border border-main hover:border-primary/40 flex items-center gap-1 transition-colors"
                       title="{{ __('Assign to a price plan') }}">
                        <i class="mdi mdi-tag-plus-outline text-sm"></i>
                        {{__('Assign to Plan')}}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
