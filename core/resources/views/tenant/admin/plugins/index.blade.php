@extends('tenant.admin.admin-master')
@section('title') {{__('Plugins')}} @endsection

@section('style')
<style>
    .plugin-card { transition: box-shadow .15s; }
    .plugin-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
    .update-badge { animation: pulse-badge 2s infinite; }
    @keyframes pulse-badge { 0%,100%{opacity:1} 50%{opacity:.7} }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-emerald-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="mdi mdi-puzzle-outline text-white text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-dark font-urbanist">{{__('Plugins')}}</h3>
                <p class="text-xs text-muted">{{__('Activate, update and purchase extensions for your store')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="flex items-center gap-1 text-xs text-muted">
                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                {{ count(array_filter($plugins, fn($p) => $p['active'])) }} {{__('active')}}
                <span class="text-main mx-0.5">/</span>
                {{ count($plugins) }} {{__('total')}}
            </span>
            <button id="btn-check-all-updates"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
                <i class="mdi mdi-refresh text-sm"></i>
                {{__('Check Updates')}}
            </button>
        </div>
    </div>
</div>

@if(empty($plugins))
    <div class="bg-surface rounded-xl border border-main p-12 text-center">
        <i class="mdi mdi-puzzle-outline text-5xl text-subtle mb-3 block"></i>
        <p class="text-base font-semibold text-dark mb-1">{{__('No plugins available')}}</p>
        <p class="text-sm text-muted">{{__('Your platform administrator has not installed any plugins for your store yet.')}}</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($plugins as $item)
            @php
                $manifest      = $item['manifest'];
                $active        = $item['active'];
                $licenseStatus = $item['license_status'];
                $isPaid        = $manifest->pricing === 'paid';
                $licensed      = $licenseStatus === 'active';
                $isCorePlugin  = in_array($manifest->id, ['widget-builder', 'page-builder', 'menu-builder', 'integrations', 'commission-manage']);
            @endphp

            <div class="bg-surface rounded-xl border border-main shadow-main flex flex-col overflow-hidden plugin-card"
                 data-plugin-id="{{ $manifest->id }}"
                 data-version="{{ $manifest->version }}"
                 data-has-update-server="{{ $manifest->updateServer ? 'true' : 'false' }}">

                {{-- Card Header --}}
                <div class="px-5 pt-5 pb-3 flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-puzzle text-primary text-xl"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <p class="text-sm font-bold text-dark font-urbanist truncate">{{ $manifest->name }}</p>
                                <span class="update-indicator hidden">
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold update-badge">
                                        <i class="mdi mdi-arrow-up-circle text-xs"></i>
                                        <span class="update-version"></span>
                                    </span>
                                </span>
                            </div>
                            <p class="text-xs text-subtle font-mono">{{ $manifest->id }}</p>
                        </div>
                    </div>

                    {{-- Active toggle --}}
                    @if($isCorePlugin)
                        {{-- Core system plugins cannot be disabled by tenants --}}
                        <div class="relative flex-shrink-0 group" title="{{ __('Core system plugin — always active') }}">
                            <div class="w-10 h-5 bg-primary/40 rounded-full opacity-60 cursor-not-allowed
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:rounded-full after:h-4 after:w-4"></div>
                            <span class="absolute -bottom-7 left-1/2 -translate-x-1/2 whitespace-nowrap bg-dark text-white text-[10px] px-2 py-1 rounded shadow opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                                {{ __('Always active') }}
                            </span>
                        </div>
                    @else
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0"
                               title="{{ $active ? __('Deactivate') : __('Activate') }}">
                            <input type="checkbox"
                                   class="sr-only peer toggle-input"
                                   {{ $active ? 'checked' : '' }}
                                   data-plugin-id="{{ $manifest->id }}"
                                   data-toggle-url="{{ route('tenant.admin.plugins.toggle') }}">
                            <div class="relative w-10 h-5 bg-gray-200 rounded-full peer
                                        peer-checked:bg-primary
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                        peer-checked:after:translate-x-5 transition-colors"></div>
                        </label>
                    @endif
                </div>

                {{-- Description --}}
                @if($manifest->description)
                    <p class="px-5 pb-3 text-xs text-muted leading-relaxed line-clamp-2">
                        {{ $manifest->description }}
                    </p>
                @endif

                {{-- Meta badges --}}
                <div class="px-5 pb-4 flex flex-wrap gap-1.5">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold
                        {{ $isPaid ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-500' }}">
                        <i class="mdi {{ $isPaid ? 'mdi-currency-usd' : 'mdi-gift-outline' }} text-xs"></i>
                        {{ ucfirst($manifest->pricing) }}
                    </span>

                    @if($isPaid)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold
                            {{ $licensed ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                            <i class="mdi {{ $licensed ? 'mdi-key-variant' : 'mdi-key-remove' }} text-xs"></i>
                            {{ $licensed ? __('Licensed') : __('Unlicensed') }}
                        </span>
                    @endif

                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold status-badge
                        {{ $active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-green-500' : 'bg-gray-400' }} status-dot"></span>
                        <span class="status-text">{{ $active ? __('Active') : __('Inactive') }}</span>
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

                    <div class="flex items-center gap-1.5">
                        {{-- Settings link --}}
                        @php
                            $settingsUrl     = $item['settings_url'] ?? null;
                            $hasGenericSettings = !$manifest->settingsRoute
                                && app(\App\PluginSystem\SettingsManager::class)->hasDefinitions($manifest->id);
                        @endphp
                        @if($settingsUrl)
                            <a href="{{ $settingsUrl }}"
                               class="text-xs font-semibold text-muted hover:text-primary px-2.5 py-1 rounded-lg border border-main hover:border-primary/40 flex items-center gap-1 transition-colors">
                                <i class="mdi mdi-cog-outline text-sm"></i>
                                {{__('Settings')}}
                            </a>
                        @elseif($hasGenericSettings)
                            <a href="{{ route('tenant.admin.plugin.settings.show', $manifest->id) }}"
                               class="text-xs font-semibold text-muted hover:text-primary px-2.5 py-1 rounded-lg border border-main hover:border-primary/40 flex items-center gap-1 transition-colors">
                                <i class="mdi mdi-cog-outline text-sm"></i>
                                {{__('Settings')}}
                            </a>
                        @endif

                        {{-- Update badge button (shown after check) --}}
                        <span class="btn-update-notify hidden text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg flex items-center gap-1">
                            <i class="mdi mdi-arrow-up-circle text-sm"></i>
                            {{__('Update via admin panel')}}
                        </span>

                        {{-- Purchase button for paid + unlicensed --}}
                        @if($isPaid && !$licensed && $manifest->purchaseUrl)
                            <a href="{{ $manifest->purchaseUrl }}" target="_blank"
                               class="text-xs font-semibold text-white bg-primary hover:bg-primary/90 px-2.5 py-1 rounded-lg flex items-center gap-1 transition-colors">
                                <i class="mdi mdi-cart-outline text-sm"></i>
                                {{__('Purchase')}}
                            </a>
                        @elseif($isPaid && !$licensed)
                            <span class="text-xs text-red-500 font-medium flex items-center gap-1">
                                <i class="mdi mdi-alert-circle-outline text-sm"></i>
                                {{__('Contact admin')}}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection

@section('scripts')
<script>
(function () {
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;

    // ── Toggle ───────────────────────────────────────────────────────────────
    document.querySelectorAll('.toggle-input').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            var pluginId  = this.dataset.pluginId;
            var url       = this.dataset.toggleUrl;
            var card      = this.closest('.plugin-card');
            var badge     = card.querySelector('.status-badge');
            var dot       = card.querySelector('.status-dot');
            var text      = card.querySelector('.status-text');
            var isChecked = this.checked;

            this.disabled = true;

            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ plugin_id: pluginId })
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.status === 'error') {
                    checkbox.checked = !isChecked;
                    toastr.error(data.message);
                    return;
                }
                var active = data.status === 'active';
                badge.className = badge.className.replace(/bg-\S+\s+text-\S+/, '');
                if (active) {
                    badge.classList.add('bg-green-50', 'text-green-700');
                    dot.className = dot.className.replace('bg-gray-400', 'bg-green-500');
                } else {
                    badge.classList.add('bg-gray-100', 'text-gray-400');
                    dot.className = dot.className.replace('bg-green-500', 'bg-gray-400');
                }
                text.textContent = active ? '{{ __("Active") }}' : '{{ __("Inactive") }}';
                toastr.success(data.message);
            })
            .catch(function () {
                checkbox.checked = !isChecked;
                toastr.error('{{ __("An error occurred. Please try again.") }}');
            })
            .finally(function () { checkbox.disabled = false; });
        });
    });

    // ── Check all updates ────────────────────────────────────────────────────
    document.getElementById('btn-check-all-updates').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-sm"></i> {{ __("Checking...") }}';

        var cards = document.querySelectorAll('.plugin-card[data-has-update-server="true"]');
        var promises = Array.from(cards).map(function (card) {
            return checkUpdateForCard(card);
        });

        Promise.all(promises).then(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-refresh text-sm"></i> {{ __("Check Updates") }}';
            toastr.info('{{ __("Update check complete.") }}');
        });
    });

    function checkUpdateForCard(card) {
        var pluginId = card.dataset.pluginId;

        return fetch('{{ route("tenant.admin.plugins.checkUpdate") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ plugin_id: pluginId })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.status === 'update_available') {
                var indicator = card.querySelector('.update-indicator');
                var versionEl = card.querySelector('.update-version');
                var notifyBtn = card.querySelector('.btn-update-notify');

                if (indicator) {
                    indicator.classList.remove('hidden');
                    if (versionEl) versionEl.textContent = 'v' + data.version;
                }
                if (notifyBtn) {
                    notifyBtn.classList.remove('hidden');
                }
            }
        })
        .catch(function () {});
    }
})();
</script>
@endsection
