@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Cache Settings')}} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-broom text-warning text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Cache Settings')}}</h3>
            <p class="text-xs text-muted">{{__('Clear various application caches')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @php
                $cacheTypes = [
                    ['type' => 'route', 'label' => 'Clear Route Cache', 'icon' => 'la-route', 'color' => 'primary'],
                    ['type' => 'view', 'label' => 'Clear View Cache', 'icon' => 'la-eye', 'color' => 'info'],
                    ['type' => 'config', 'label' => 'Clear Config Cache', 'icon' => 'la-cog', 'color' => 'warning'],
                    ['type' => 'event', 'label' => 'Clear Event Cache', 'icon' => 'la-bolt', 'color' => 'success'],
                    ['type' => 'all', 'label' => 'Clear All Cache', 'icon' => 'la-trash-alt', 'color' => 'danger'],
                ];
            @endphp

            @foreach($cacheTypes as $cache)
                <form action="{{route(route_prefix().'admin.general.cache.settings')}}" method="post">
                    @csrf
                    <input type="hidden" name="type" value="{{$cache['type']}}">
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl bg-secondary border border-main hover:border-sidebar-hover hover:shadow-sm transition group">
                        <div class="w-8 h-8 rounded-lg bg-{{$cache['color']}}-soft flex items-center justify-center flex-shrink-0">
                            <i class="las {{$cache['icon']}} text-{{$cache['color']}} text-sm"></i>
                        </div>
                        <span class="text-xs font-semibold text-muted group-hover:text-dark transition">{{__($cache['label'])}}</span>
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>

@endsection
