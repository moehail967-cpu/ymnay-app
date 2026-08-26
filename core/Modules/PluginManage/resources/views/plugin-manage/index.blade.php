@extends('landlord.admin.admin-master')
@section('title') {{__('All Plugins')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

@php
    // Tenant-only plugins are managed via price plans — hide from landlord plugin page
    $visiblePlugins  = collect($pluginList)->filter(fn($p) => ($p->scope ?? null) !== 'tenant')->values();

    $totalPlugins    = $visiblePlugins->count();
    $activePlugins   = $visiblePlugins->where('status', true)->count();
    $inactivePlugins = $totalPlugins - $activePlugins;
    $corePlugins     = $visiblePlugins->filter(fn($p) => !$p->external)->count();
    $extPlugins      = $totalPlugins - $corePlugins;

    $countAll      = $totalPlugins;
    $countLandlord = $visiblePlugins->where('scope', 'landlord')->count();
    $countBoth     = $visiblePlugins->where('scope', 'both')->count();
@endphp

{{-- Hero Banner --}}
<div class="featured-card mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex items-center gap-4 flex-1 min-w-0">
            <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-puzzle text-2xl text-white/80"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-white/40">{{__('Plugin Manager')}}</p>
                <p class="text-2xl font-extrabold text-white leading-tight mt-0.5">{{$totalPlugins}} {{__('Plugins')}}</p>
            </div>
        </div>
        <a href="{{route('landlord.plugin.manage.new')}}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white/15 backdrop-blur-sm text-white text-sm font-semibold hover:bg-white/25 transition-all border border-white/10 self-start sm:self-center">
            <i class="mdi mdi-plus-circle-outline text-base"></i> {{__('Upload Plugin')}}
        </a>
    </div>
    <div class="flex items-center gap-6 sm:gap-8 mt-5 pt-4 border-t border-white/10">
        <div>
            <p class="text-[10px] text-white/40 uppercase tracking-wider font-bold">{{__('Active')}}</p>
            <p class="text-lg font-extrabold text-emerald-300 mt-0.5">{{$activePlugins}}</p>
        </div>
        <div class="w-px h-8 bg-white/10"></div>
        <div>
            <p class="text-[10px] text-white/40 uppercase tracking-wider font-bold">{{__('Inactive')}}</p>
            <p class="text-lg font-extrabold text-white/70 mt-0.5">{{$inactivePlugins}}</p>
        </div>
        <div class="w-px h-8 bg-white/10"></div>
        <div>
            <p class="text-[10px] text-white/40 uppercase tracking-wider font-bold">{{__('Core')}}</p>
            <p class="text-lg font-extrabold text-white mt-0.5">{{$corePlugins}}</p>
        </div>
        <div class="w-px h-8 bg-white/10"></div>
        <div>
            <p class="text-[10px] text-white/40 uppercase tracking-wider font-bold">{{__('External')}}</p>
            <p class="text-lg font-extrabold text-sky-300 mt-0.5">{{$extPlugins}}</p>
        </div>
    </div>
</div>

{{-- Filter Tabs --}}
<div class="plugin-filter-tabs mb-5">
    <button class="pf-tab active" data-filter="all">
        {{__('All')}} <span class="pf-count">{{$countAll}}</span>
    </button>
    <button class="pf-tab" data-filter="landlord">
        <i class="mdi mdi-office-building text-sm"></i> {{__('Landlord')}} <span class="pf-count">{{$countLandlord}}</span>
    </button>
    <button class="pf-tab" data-filter="both">
        <i class="mdi mdi-swap-horizontal text-sm"></i> {{__('Both')}} <span class="pf-count">{{$countBoth}}</span>
    </button>
</div>

{{-- Plugin Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5" id="pluginGrid">
    @foreach($visiblePlugins as $plugin)
    @php
        $isCore      = !$plugin->external;
        $isActive    = $plugin->status;
        $scope       = $plugin->scope ?? null;
        $isTenantOnly = $scope === 'tenant';
    @endphp
    <div class="plugin-card plugin-card-item {{ $isTenantOnly ? 'is-tenant-managed' : '' }}"
         data-plugin="{{$plugin->name}}"
         data-scope="{{ $scope ?? 'none' }}">

        {{-- Card Body --}}
        <div class="p-5 flex-1">
            {{-- Top row: icon + name + status --}}
            <div class="flex items-start gap-3.5">
                <div class="plugin-icon {{ $isCore ? 'bg-[var(--color-primary-soft)]' : 'bg-[var(--color-info-bg)]' }}">
                    <i class="mdi {{ $isCore ? 'mdi-shield-check' : 'mdi-package-variant' }} text-lg
                        {{ $isCore ? 'text-brand' : 'text-info' }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="plugin-name truncate">{{$plugin->name}}</h4>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        <span class="plugin-version">v{{$plugin->version}}</span>
                        <span class="plugin-category
                            {{ $isCore
                                ? 'bg-[var(--color-primary-soft)] text-[var(--color-text-brand)]'
                                : 'bg-[var(--color-info-bg)] text-[var(--color-info)]' }}">
                            {{$plugin->category}}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <p class="plugin-desc mt-3.5">
                @if(!empty($plugin->description))
                    {{$plugin->description}}
                @else
                    {{$plugin->name.' '.__('enhances platform features as a').' '.strtolower($plugin->category)}}
                @endif
            </p>

            {{-- Status + Scope badges (D) --}}
            <div class="mt-3.5 flex items-center gap-2 flex-wrap">
                @if($isActive)
                    <span class="plugin-status is-active bg-[var(--color-success-bg)] text-[var(--color-success)]">
                        <span class="status-dot"></span> {{__('Active')}}
                    </span>
                @else
                    <span class="plugin-status bg-gray-100 text-[var(--color-text-subtle)]">
                        <span class="status-dot bg-gray-400"></span> {{__('Inactive')}}
                    </span>
                @endif

                @if($scope === 'tenant')
                    <span class="plugin-scope-badge scope-tenant">
                        <i class="mdi mdi-account-group"></i> {{__('Tenant')}}
                    </span>
                @elseif($scope === 'landlord')
                    <span class="plugin-scope-badge scope-landlord">
                        <i class="mdi mdi-office-building"></i> {{__('Landlord')}}
                    </span>
                @elseif($scope === 'both')
                    <span class="plugin-scope-badge scope-both">
                        <i class="mdi mdi-swap-horizontal"></i> {{__('Both')}}
                    </span>
                @endif
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="plugin-footer">
            @if($plugin->external)
                <button type="button"
                        data-status="{{$plugin->status ? 1 : 0}}"
                        data-plugintype="{{$plugin->category}}"
                        data-plugin="{{$plugin->name}}"
                        class="pl_active_deactive plugin-btn {{ $isActive ? 'btn-deactivate' : 'btn-activate' }}">
                    <i class="mdi {{ $isActive ? 'mdi-pause-circle-outline' : 'mdi-play-circle-outline' }} text-sm"></i>
                    {{$isActive ? __('Deactivate') : __('Activate')}}
                </button>
                <button type="button"
                        data-plugintype="{{$plugin->category}}"
                        data-plugin="{{$plugin->name}}"
                        class="pl_delete plugin-btn btn-delete"
                        title="{{__('Delete')}}">
                    <i class="mdi mdi-delete-outline text-sm"></i>
                </button>
            @else
                <span class="plugin-core-label">
                    <i class="mdi mdi-lock-outline"></i> {{__('Core Plugin · Managed by System')}}
                </span>
            @endif
        </div>
    </div>
    @endforeach
</div>

@endsection

@section('scripts')
<script>
(function ($) {
    "use strict";

    // ── Filter tabs (B) ─────────────────────────────────────────────
    document.querySelectorAll('.pf-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.pf-tab').forEach(function (t) { t.classList.remove('active'); });
            this.classList.add('active');
            var filter = this.dataset.filter;
            document.querySelectorAll('.plugin-card-item').forEach(function (card) {
                if (filter === 'all' || card.dataset.scope === filter) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // ── Activate / Deactivate ────────────────────────────────────────
    $(document).on("click", ".pl_active_deactive", function (e) {
        e.preventDefault();
        var el = $(this);
        var allData = el.data();
        var swalDesc = '{{ __("It will disable the features you are enjoying from") }} ' + allData.plugin + ' {{ __("plugin!") }}';
        var swalBtnText = allData.status == 1 ? '{{ __("Yes, deactivate it!") }}' : '{{ __("Yes, activate it!") }}';
        var buttonText  = allData.status !== 1 ? '{{ __("Deactivate") }}' : '{{ __("Activate") }}';

        if (allData.plugintype === "Core Plugin") {
            swalDesc = allData.plugin + ' {{ __("is a core plugin, after deactivating it you might face issues or errors") }}';
        }
        if (allData.status == 0) {
            swalDesc = '{{ __("You are activating a new plugin..") }}';
        }

        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: swalDesc,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1a5c4e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: swalBtnText,
            cancelButtonText: '{{ __("Cancel") }}'
        }).then(function (result) {
            if (result.isConfirmed) {
                el.text(buttonText).attr('disabled', true).css('opacity', '0.6');
                $.ajax({
                    url: '{{ route("landlord.plugin.manage.status.change") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        plugin: allData.plugin,
                        status: allData.status
                    },
                    success: function () {
                        toastr.success('{{ __("Plugin status updated") }}');
                        setTimeout(function () { location.reload(); }, 600);
                    },
                    error: function () {
                        toastr.error('{{ __("Something went wrong") }}');
                        el.attr('disabled', false).css('opacity', '1');
                    }
                });
            }
        });
    });

    // ── Delete Plugin ────────────────────────────────────────────────
    $(document).on("click", ".pl_delete", function (e) {
        e.preventDefault();
        var el = $(this);
        var allData = el.data();

        if (allData.plugintype === "Core Plugin") {
            Swal.fire({
                icon: 'error',
                title: '{{ __("Oops...") }}',
                text: '{{ __("You cannot delete a core plugin") }}',
                timer: 3000,
                timerProgressBar: true
            });
            return;
        }

        Swal.fire({
            title: '{{ __("Delete") }} ' + allData.plugin + '?',
            text: '{{ __("You will not be able to restore this plugin!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then(function (result) {
            if (result.isConfirmed) {
                var card = el.closest('.plugin-card-item');
                card.css({ opacity: 0.4, pointerEvents: 'none', transform: 'scale(0.97)' });

                $.ajax({
                    url: '{{ route("landlord.plugin.manage.delete") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        plugin: allData.plugin
                    },
                    success: function () {
                        toastr.success('{{ __("Plugin deleted successfully") }}');
                        card.slideUp(300, function () { $(this).remove(); });
                    },
                    error: function () {
                        toastr.error('{{ __("Something went wrong") }}');
                        card.css({ opacity: 1, pointerEvents: 'auto', transform: 'none' });
                    }
                });
            }
        });
    });

})(jQuery);
</script>
@endsection
