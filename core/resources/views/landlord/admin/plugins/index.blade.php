@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Plugins')}} @endsection

@section('style')
<style>
    .plugin-card { transition: box-shadow .15s, border-color .15s; }
    .plugin-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
    .update-badge { animation: pulse-badge 2s infinite; }
    @keyframes pulse-badge { 0%,100%{opacity:1} 50%{opacity:.7} }

    /* ── Plugin filter tabs ── */
    .pf-tab-btn {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.4rem 0.875rem; border-radius: 9999px; font-size: 0.75rem;
        font-weight: 600; cursor: pointer; border: 1px solid var(--color-border-main, #e5e7eb);
        background: var(--color-bg-surface, #fff); color: var(--color-text-muted, #6b7280);
        transition: all .18s;
    }
    .pf-tab-btn:hover { border-color: var(--color-primary, #1a5c4e); color: var(--color-primary, #1a5c4e); }
    .pf-tab-btn.active { background: var(--color-primary, #1a5c4e); color: #fff; border-color: var(--color-primary, #1a5c4e); }
    .pf-tab-count {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 1.1rem; height: 1.1rem; padding: 0 0.25rem; border-radius: 9999px;
        font-size: 0.6rem; font-weight: 700;
        background: rgba(255,255,255,.2); color: inherit;
    }
    .pf-tab-btn:not(.active) .pf-tab-count {
        background: var(--color-bg-secondary, #f3f4f6); color: var(--color-text-subtle, #9ca3af);
    }
</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Page Header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-emerald-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="mdi mdi-puzzle-outline text-white text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-dark font-urbanist">{{__('Plugins')}}</h3>
                <p class="text-xs text-muted">{{__('Manage installed plugins and extensions')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="flex items-center gap-1 text-xs text-muted">
                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                {{ count(array_filter($plugins, fn($p) => $p['active'])) }} {{__('active')}}
                <span class="text-main mx-0.5">/</span>
                {{ count($plugins) }} {{__('total')}}
            </span>
            <a href="{{ route('landlord.admin.plugins.coreFeatures') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
                <i class="mdi mdi-shield-check-outline text-sm"></i>
                {{__('Core Features')}}
            </a>
            <a href="{{ route('landlord.admin.plugins.tenantPlugins') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
                <i class="mdi mdi-store-outline text-sm"></i>
                {{__('Tenant Plugins')}}
            </a>
            <a href="{{ route('landlord.admin.plugins.hookLog') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
                <i class="mdi mdi-history text-sm"></i>
                {{__('Hook Log')}}
            </a>
            <a href="{{ route('landlord.admin.plugins.installLog') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
                <i class="mdi mdi-shield-check-outline text-sm"></i>
                {{__('Install Log')}}
            </a>
            <button id="btn-check-all-updates"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
                <i class="mdi mdi-refresh text-sm"></i>
                {{__('Check Updates')}}
            </button>
            <button id="btn-open-install"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-primary/90 transition-colors shadow-sm">
                <i class="mdi mdi-upload text-sm"></i>
                {{__('Install Plugin')}}
            </button>
        </div>
    </div>
</div>

@php
    // Only landlord + both plugins appear here; core → Core Features; tenant → Tenant Plugins
    $countAll      = count($plugins);
    $countBoth     = count(array_filter($plugins, fn($p) => ($p['manifest']->type ?? '') === 'both'));
    $countLandlord = count(array_filter($plugins, fn($p) => ($p['manifest']->type ?? '') === 'landlord'));
@endphp

@if(empty($plugins))
    <div class="bg-surface rounded-xl border border-main p-12 text-center">
        <i class="mdi mdi-puzzle-outline text-5xl text-subtle mb-3 block"></i>
        <p class="text-base font-semibold text-dark mb-1">{{__('No plugins installed')}}</p>
        <p class="text-sm text-muted mb-4">{{__('Upload a plugin ZIP file to get started.')}}</p>
        <button id="btn-open-install-empty"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors">
            <i class="mdi mdi-upload text-base"></i>
            {{__('Install Plugin')}}
        </button>
    </div>
@else
    {{-- ── Filter Tabs ──────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-2 mb-5" id="plugin-filter-tabs">
        <button class="pf-tab-btn active" data-filter="all">
            {{__('All')}} <span class="pf-tab-count">{{$countAll}}</span>
        </button>
        <button class="pf-tab-btn" data-filter="landlord">
            <i class="mdi mdi-office-building text-xs"></i> {{__('Landlord')}} <span class="pf-tab-count">{{$countLandlord}}</span>
        </button>
        <button class="pf-tab-btn" data-filter="both">
            <i class="mdi mdi-swap-horizontal text-xs"></i> {{__('Both')}} <span class="pf-tab-count">{{$countBoth}}</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($plugins as $item)
            @php
                $manifest      = $item['manifest'];
                $active        = $item['active'];
                $licenseStatus = $item['license_status'];
                $isPaid        = $manifest->pricing === 'paid';
                $licensed      = $licenseStatus === 'active';
            @endphp

            <div class="bg-surface rounded-xl border border-main shadow-main flex flex-col overflow-hidden plugin-card {{ ($manifest->type ?? '') === 'tenant' ? 'border-sky-200/60' : '' }}"
                 data-plugin-id="{{ $manifest->id }}"
                 data-version="{{ $manifest->version }}"
                 data-has-update-server="{{ $manifest->updateServer ? 'true' : 'false' }}"
                 data-type="{{ $manifest->type ?? 'landlord' }}">

                {{-- Card Header --}}
                <div class="px-5 pt-5 pb-3 flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-puzzle text-primary text-xl"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
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
                    @if($manifest->type === 'tenant')
                        {{-- Tenant-only: landlord cannot toggle — show a clear lock indicator --}}
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-sky-50 border border-sky-200 text-sky-600 text-[10px] font-bold flex-shrink-0 cursor-default"
                              title="{{ __('Managed by each tenant individually') }}">
                            <i class="mdi mdi-lock-outline text-xs"></i>
                            {{ __('Tenant') }}
                        </span>
                    @else
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0"
                               title="{{ $active ? __('Deactivate') : __('Activate') }}">
                            <input type="checkbox"
                                   class="sr-only peer toggle-input"
                                   {{ $active ? 'checked' : '' }}
                                   data-plugin-id="{{ $manifest->id }}"
                                   data-toggle-url="{{ route('landlord.admin.plugins.toggle') }}">
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
                        {{ $manifest->type === 'both' ? 'bg-blue-50 text-blue-600' : ($manifest->type === 'tenant' ? 'bg-emerald-50 text-emerald-700' : 'bg-purple-50 text-purple-700') }}">
                        <i class="mdi {{ $manifest->type === 'both' ? 'mdi-swap-horizontal' : ($manifest->type === 'tenant' ? 'mdi-store' : 'mdi-shield-account') }} text-xs"></i>
                        {{ ucfirst($manifest->type) }}
                    </span>

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

                {{-- Footer actions --}}
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
                        {{-- Settings link (landlord/both plugins only; tenant-only plugins configure on their own page) --}}
                        @if($manifest->type !== 'tenant' && app(\App\PluginSystem\SettingsManager::class)->hasDefinitions($manifest->id))
                            <a href="{{ route('landlord.admin.plugin.settings.show', $manifest->id) }}"
                               class="text-xs font-semibold text-muted hover:text-primary px-2.5 py-1 rounded-lg border border-main hover:border-primary/40 flex items-center gap-1 transition-colors">
                                <i class="mdi mdi-cog-outline text-sm"></i>
                                {{__('Settings')}}
                            </a>
                        @endif

                        {{-- Update button (hidden until check reveals one) --}}
                        <button class="btn-install-update hidden text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 px-2.5 py-1 rounded-lg flex items-center gap-1 transition-colors"
                                data-plugin-id="{{ $manifest->id }}"
                                title="{{__('Install available update')}}">
                            <i class="mdi mdi-arrow-up-circle text-sm"></i>
                            {{__('Update')}}
                        </button>

                        {{-- License button for paid plugins --}}
                        @if($isPaid)
                            <button class="btn-license text-xs font-semibold text-muted hover:text-primary px-2.5 py-1 rounded-lg border border-main hover:border-primary/40 flex items-center gap-1 transition-colors"
                                    data-plugin-id="{{ $manifest->id }}"
                                    data-plugin-name="{{ $manifest->name }}"
                                    title="{{__('Manage license')}}">
                                <i class="mdi mdi-key-variant text-sm"></i>
                                {{__('License')}}
                            </button>
                        @endif

                        {{-- Purchase link for paid + unlicensed --}}
                        @if($isPaid && !$licensed && $manifest->purchaseUrl)
                            <a href="{{ $manifest->purchaseUrl }}" target="_blank"
                               class="text-xs font-semibold text-white bg-primary hover:bg-primary/90 px-2.5 py-1 rounded-lg flex items-center gap-1 transition-colors">
                                <i class="mdi mdi-cart-outline text-sm"></i>
                                {{__('Purchase')}}
                            </a>
                        @endif

                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- ── Install Plugin Modal ───────────────────────────────────────────── --}}
<div id="modal-install" class="fixed inset-0 z-[800] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="modal-install-backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-main">
            <h4 class="text-sm font-bold text-dark font-urbanist flex items-center gap-2">
                <i class="mdi mdi-upload text-primary"></i>
                {{__('Install Plugin')}}
            </h4>
            <button class="modal-close text-subtle hover:text-dark transition-colors text-xl leading-none">&times;</button>
        </div>
        <form id="form-install" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                <p class="text-xs text-muted">{{__('Upload a .zip file containing a valid plugin.json manifest.')}}</p>
                <div id="drop-zone"
                     class="border-2 border-dashed border-main rounded-xl p-8 text-center cursor-pointer hover:border-primary/50 transition-colors group">
                    <i class="mdi mdi-cloud-upload-outline text-4xl text-subtle group-hover:text-primary transition-colors mb-2 block"></i>
                    <p class="text-sm text-muted">{{__('Drop ZIP here or')}} <span class="text-primary font-semibold cursor-pointer underline">{{__('browse')}}</span></p>
                    <p class="text-xs text-subtle mt-1">{{__('Max 50 MB')}}</p>
                    <input type="file" name="plugin_zip" id="input-plugin-zip" accept=".zip" class="hidden">
                </div>
                <div id="selected-file" class="hidden flex items-center gap-2 px-3 py-2 bg-secondary rounded-lg border border-main">
                    <i class="mdi mdi-zip-box text-primary text-lg flex-shrink-0"></i>
                    <span id="selected-file-name" class="text-xs text-dark font-medium truncate flex-1"></span>
                    <button type="button" id="btn-clear-file" class="text-subtle hover:text-red-500 text-lg leading-none">&times;</button>
                </div>
            </div>
            <div class="px-6 pb-6 flex justify-end gap-2">
                <button type="button" class="modal-close px-4 py-2 text-sm font-semibold text-muted border border-main rounded-lg hover:bg-secondary transition-colors">
                    {{__('Cancel')}}
                </button>
                <button type="submit" id="btn-install-submit"
                        class="px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 disabled:opacity-60">
                    <i class="mdi mdi-upload text-base"></i>
                    {{__('Install')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Changelog Modal ────────────────────────────────────────────────── --}}
<div id="modal-changelog" class="fixed inset-0 z-[800] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="modal-changelog-backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-lg max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-main flex-shrink-0">
            <h4 class="text-sm font-bold text-dark font-urbanist flex items-center gap-2">
                <i class="mdi mdi-format-list-bulleted text-primary"></i>
                <span id="changelog-modal-title">{{__('Changelog')}}</span>
            </h4>
            <button class="modal-close text-subtle hover:text-dark transition-colors text-xl leading-none">&times;</button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <div class="flex items-center gap-3 mb-4 text-xs">
                <span class="px-2 py-1 bg-secondary border border-main rounded-lg text-muted">
                    {{__('Current:')}} <strong class="text-dark" id="changelog-current-version"></strong>
                </span>
                <i class="mdi mdi-arrow-right text-muted"></i>
                <span class="px-2 py-1 bg-amber-50 border border-amber-200 rounded-lg text-amber-700 font-semibold">
                    {{__('Available:')}} <strong id="changelog-new-version"></strong>
                </span>
            </div>
            <div id="changelog-body" class="prose prose-sm text-sm text-dark leading-relaxed whitespace-pre-wrap"></div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-2 flex-shrink-0 border-t border-main pt-4">
            <button type="button" class="modal-close px-4 py-2 text-sm font-semibold text-muted border border-main rounded-lg hover:bg-secondary transition-colors">
                {{__('Close')}}
            </button>
            <button id="btn-install-update-from-changelog"
                    class="px-4 py-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-lg flex items-center gap-2 transition-colors disabled:opacity-60">
                <i class="mdi mdi-arrow-up-circle text-base"></i>
                {{__('Install Update')}}
            </button>
        </div>
    </div>
</div>

{{-- ── Preview on Tenant Modal ─────────────────────────────────────────── --}}
<div id="modal-preview" class="fixed inset-0 z-[800] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="modal-preview-backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-main">
            <h4 class="text-sm font-bold text-dark font-urbanist flex items-center gap-2">
                <i class="mdi mdi-eye-outline text-indigo-500"></i>
                <span id="preview-modal-title">{{__('Preview on Tenant')}}</span>
            </h4>
            <button class="modal-close text-subtle hover:text-dark transition-colors text-xl leading-none">&times;</button>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-xs text-muted">
                {{__('Activate this plugin for a single tenant without a global rollout. The plugin will boot only for that tenant while remaining inactive everywhere else.')}}
            </p>
            <input type="hidden" id="preview-plugin-id">
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('Tenant ID')}}</label>
                <input type="number"
                       id="preview-tenant-id"
                       placeholder="{{ __('e.g. 5') }}"
                       class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary focus:outline-none focus:border-primary">
                <p class="text-xs text-muted mt-1">{{__('Find the tenant ID in the tenant management list.')}}</p>
            </div>
            <div id="preview-message" class="hidden text-xs font-semibold px-3 py-2 rounded-lg"></div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-2">
            <button type="button" class="modal-close px-4 py-2 text-sm font-semibold text-muted border border-main rounded-lg hover:bg-secondary transition-colors">
                {{__('Cancel')}}
            </button>
            <button id="btn-enable-preview"
                    class="px-4 py-2 text-sm font-semibold text-white bg-indigo-500 hover:bg-indigo-600 rounded-lg flex items-center gap-2 transition-colors disabled:opacity-60">
                <i class="mdi mdi-eye-check text-base"></i>
                {{__('Enable Preview')}}
            </button>
        </div>
    </div>
</div>

{{-- ── License Key Modal ──────────────────────────────────────────────── --}}
<div id="modal-license" class="fixed inset-0 z-[800] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="modal-license-backdrop"></div>
    <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-main">
            <h4 class="text-sm font-bold text-dark font-urbanist flex items-center gap-2">
                <i class="mdi mdi-key-variant text-amber-500"></i>
                <span id="license-modal-title">{{__('License Key')}}</span>
            </h4>
            <button class="modal-close text-subtle hover:text-dark transition-colors text-xl leading-none">&times;</button>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-xs text-muted">{{__('Enter your license key to activate full features for this plugin.')}}</p>
            <input type="hidden" id="license-plugin-id">
            <div>
                <label class="block text-xs font-semibold text-dark mb-1.5">{{__('License Key')}}</label>
                <input type="text" id="license-key-input"
                       placeholder="{{ __('XXXX-XXXX-XXXX-XXXX') }}"
                       class="w-full px-3 py-2.5 text-sm border border-main rounded-lg bg-secondary focus:outline-none focus:border-primary font-mono">
            </div>
            <div id="license-message" class="hidden text-xs font-semibold px-3 py-2 rounded-lg"></div>
        </div>
        <div class="px-6 pb-6 flex justify-end gap-2">
            <button type="button" class="modal-close px-4 py-2 text-sm font-semibold text-muted border border-main rounded-lg hover:bg-secondary transition-colors">
                {{__('Cancel')}}
            </button>
            <button id="btn-activate-license"
                    class="px-4 py-2 text-sm font-semibold text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition-colors flex items-center gap-2 disabled:opacity-60">
                <i class="mdi mdi-key-variant text-base"></i>
                {{__('Activate License')}}
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;

    // ── Filter tabs (B) ──────────────────────────────────────────────────────
    document.querySelectorAll('.pf-tab-btn').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.pf-tab-btn').forEach(function (t) { t.classList.remove('active'); });
            this.classList.add('active');
            var filter = this.dataset.filter;
            document.querySelectorAll('.plugin-card').forEach(function (card) {
                card.style.display = (filter === 'all' || card.dataset.type === filter) ? '' : 'none';
            });
        });
    });

    // ── Modal helpers ────────────────────────────────────────────────────────
    function openModal(id) {
        var m = document.getElementById(id);
        m.classList.remove('hidden');
        m.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        var m = document.getElementById(id);
        m.classList.add('hidden');
        m.classList.remove('flex');
        document.body.style.overflow = '';
    }
    document.querySelectorAll('.modal-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var m = this.closest('[id^="modal-"]');
            if (m) closeModal(m.id);
        });
    });
    ['modal-install-backdrop', 'modal-license-backdrop'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('click', function () { closeModal(el.closest('[id^="modal-"]').id); });
    });

    // ── Install button ───────────────────────────────────────────────────────
    ['btn-open-install', 'btn-open-install-empty'].forEach(function (id) {
        var btn = document.getElementById(id);
        if (btn) btn.addEventListener('click', function () { openModal('modal-install'); });
    });

    // ── File drop zone ───────────────────────────────────────────────────────
    var dropZone  = document.getElementById('drop-zone');
    var fileInput = document.getElementById('input-plugin-zip');
    var selFile   = document.getElementById('selected-file');
    var selName   = document.getElementById('selected-file-name');

    if (dropZone) {
        dropZone.addEventListener('click', function () { fileInput.click(); });
        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropZone.classList.add('border-primary/50');
        });
        dropZone.addEventListener('dragleave', function () {
            dropZone.classList.remove('border-primary/50');
        });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropZone.classList.remove('border-primary/50');
            if (e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                showSelectedFile(e.dataTransfer.files[0].name);
            }
        });
        fileInput.addEventListener('change', function () {
            if (fileInput.files[0]) showSelectedFile(fileInput.files[0].name);
        });
        document.getElementById('btn-clear-file').addEventListener('click', function () {
            fileInput.value = '';
            selFile.classList.add('hidden');
            dropZone.classList.remove('hidden');
        });
    }

    function showSelectedFile(name) {
        selName.textContent = name;
        selFile.classList.remove('hidden');
        dropZone.classList.add('hidden');
    }

    // ── Install form submit ──────────────────────────────────────────────────
    var installForm = document.getElementById('form-install');
    if (installForm) {
        installForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!fileInput || !fileInput.files[0]) {
                toastr.warning('{{ __("Please select a ZIP file first.") }}');
                return;
            }

            var btn = document.getElementById('btn-install-submit');
            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-base"></i> {{ __("Installing...") }}';

            var fd = new FormData(installForm);

            fetch('{{ route("landlord.admin.plugins.install") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                body: fd
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.status === 'success') {
                    toastr.success(data.message);
                    closeModal('modal-install');
                    setTimeout(function () { location.reload(); }, 1500);
                } else {
                    toastr.error(data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="mdi mdi-upload text-base"></i> {{ __("Install") }}';
                }
            })
            .catch(function () {
                toastr.error('{{ __("Upload failed. Please try again.") }}');
                btn.disabled = false;
                btn.innerHTML = '<i class="mdi mdi-upload text-base"></i> {{ __("Install") }}';
            });
        });
    }

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

        return fetch('{{ route("landlord.admin.plugins.checkUpdate") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ plugin_id: pluginId })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.status === 'update_available') {
                showUpdateOnCard(card, data.version, data.download_url);
            }
        })
        .catch(function () {});
    }

    function showUpdateOnCard(card, version, downloadUrl) {
        var indicator = card.querySelector('.update-indicator');
        var versionEl = card.querySelector('.update-version');
        var updateBtn = card.querySelector('.btn-install-update');

        if (indicator) {
            indicator.classList.remove('hidden');
            if (versionEl) versionEl.textContent = 'v' + version;
        }
        if (updateBtn) {
            updateBtn.classList.remove('hidden');
            updateBtn.dataset.downloadUrl = downloadUrl;
        }
    }

    // ── Install update buttons ───────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-install-update');
        if (!btn) return;

        var pluginId    = btn.dataset.pluginId;
        var downloadUrl = btn.dataset.downloadUrl;

        if (!downloadUrl) {
            toastr.error('{{ __("Download URL not available.") }}');
            return;
        }

        if (!confirm('{{ __("Install update for this plugin? The page will reload after.") }}')) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-sm"></i>';

        fetch('{{ route("landlord.admin.plugins.update") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ plugin_id: pluginId, download_url: downloadUrl })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.status === 'success') {
                toastr.success(data.message);
                setTimeout(function () { location.reload(); }, 1500);
            } else {
                toastr.error(data.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="mdi mdi-arrow-up-circle text-sm"></i> {{ __("Update") }}';
            }
        })
        .catch(function () {
            toastr.error('{{ __("Update failed. Please try again.") }}');
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-arrow-up-circle text-sm"></i> {{ __("Update") }}';
        });
    });

    // ── License modal ────────────────────────────────────────────────────────
    document.querySelectorAll('.btn-license').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('license-plugin-id').value     = this.dataset.pluginId;
            document.getElementById('license-modal-title').textContent = this.dataset.pluginName + ' — {{ __("License Key") }}';
            document.getElementById('license-key-input').value     = '';
            document.getElementById('license-message').classList.add('hidden');
            openModal('modal-license');
        });
    });

    document.getElementById('btn-activate-license').addEventListener('click', function () {
        var pluginId = document.getElementById('license-plugin-id').value;
        var key      = document.getElementById('license-key-input').value.trim();
        var msgEl    = document.getElementById('license-message');
        var btn      = this;

        if (!key) {
            toastr.warning('{{ __("Please enter a license key.") }}');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-base"></i> {{ __("Activating...") }}';
        msgEl.classList.add('hidden');

        fetch('{{ route("landlord.admin.plugins.activateLicense") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ plugin_id: pluginId, license_key: key })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            msgEl.classList.remove('hidden', 'text-green-700', 'bg-green-50', 'text-red-600', 'bg-red-50');
            if (data.status === 'success') {
                msgEl.classList.add('text-green-700', 'bg-green-50');
                msgEl.textContent = data.message;
                toastr.success(data.message);
                setTimeout(function () { location.reload(); }, 1800);
            } else {
                msgEl.classList.add('text-red-600', 'bg-red-50');
                msgEl.textContent = data.message;
                btn.disabled = false;
                btn.innerHTML = '<i class="mdi mdi-key-variant text-base"></i> {{ __("Activate License") }}';
            }
        })
        .catch(function () {
            toastr.error('{{ __("An error occurred. Please try again.") }}');
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-key-variant text-base"></i> {{ __("Activate License") }}';
        });
    });

    // ── Changelog modal ──────────────────────────────────────────────────────
    // Attach backdrop close for new modals
    ['modal-changelog-backdrop', 'modal-preview-backdrop'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('click', function () { closeModal(el.closest('[id^="modal-"]').id); });
    });

    var _changelogDownloadUrl = null;
    var _changelogPluginId    = null;

    function showUpdateOnCard(card, version, downloadUrl) {
        var indicator = card.querySelector('.update-indicator');
        var versionEl = card.querySelector('.update-version');
        var updateBtn = card.querySelector('.btn-install-update');

        if (indicator) {
            indicator.classList.remove('hidden');
            if (versionEl) versionEl.textContent = 'v' + version;
        }
        if (updateBtn) {
            updateBtn.classList.remove('hidden');
            updateBtn.dataset.downloadUrl = downloadUrl;
        }
    }

    document.addEventListener('click', function (e) {
        // "View Changelog" — update badge click opens changelog modal
        var badge = e.target.closest('.update-indicator');
        if (badge) {
            var card    = badge.closest('.plugin-card');
            var btn     = card.querySelector('.btn-install-update');
            var verEl   = badge.querySelector('.update-version');
            var nameEl  = card.querySelector('.plugin-card p.font-bold') || { textContent: '' };

            _changelogDownloadUrl = btn ? btn.dataset.downloadUrl : null;
            _changelogPluginId    = card ? card.dataset.pluginId : null;

            document.getElementById('changelog-modal-title').textContent   = (nameEl.textContent || '').trim() + ' — {{ __("Changelog") }}';
            document.getElementById('changelog-current-version').textContent = 'v' + (card.dataset.version || '?');
            document.getElementById('changelog-new-version').textContent    = verEl ? verEl.textContent : '';
            document.getElementById('changelog-body').textContent           = '{{ __("Changelog not available for this update.") }}';

            // Try to fetch changelog if available
            if (_changelogPluginId) {
                fetch('{{ route("landlord.admin.plugins.checkUpdate") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ plugin_id: _changelogPluginId })
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.changelog) {
                        document.getElementById('changelog-body').textContent = data.changelog;
                    }
                })
                .catch(function () {});
            }

            openModal('modal-changelog');
        }
    });

    document.getElementById('btn-install-update-from-changelog').addEventListener('click', function () {
        if (!_changelogDownloadUrl || !_changelogPluginId) return;

        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-base"></i>';

        fetch('{{ route("landlord.admin.plugins.update") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ plugin_id: _changelogPluginId, download_url: _changelogDownloadUrl })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.status === 'success') {
                toastr.success(data.message);
                closeModal('modal-changelog');
                setTimeout(function () { location.reload(); }, 1500);
            } else {
                toastr.error(data.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="mdi mdi-arrow-up-circle text-base"></i> {{ __("Install Update") }}';
            }
        });
    });

    // ── Preview on tenant modal ───────────────────────────────────────────────
    document.querySelectorAll('.btn-preview').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('preview-plugin-id').value = this.dataset.pluginId;
            document.getElementById('preview-modal-title').textContent = this.dataset.pluginName + ' — {{ __("Preview on Tenant") }}';
            document.getElementById('preview-tenant-id').value = '';
            document.getElementById('preview-message').classList.add('hidden');
            openModal('modal-preview');
        });
    });

    document.getElementById('btn-enable-preview').addEventListener('click', function () {
        var pluginId = document.getElementById('preview-plugin-id').value;
        var tenantId = document.getElementById('preview-tenant-id').value.trim();
        var msgEl    = document.getElementById('preview-message');
        var btn      = this;

        if (!tenantId) {
            toastr.warning('{{ __("Please enter a tenant ID.") }}');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-base"></i>';

        fetch('{{ route("landlord.admin.plugins.tenantOverride") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ plugin_id: pluginId, tenant_id: tenantId, status: 'active', is_preview: true })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            msgEl.classList.remove('hidden', 'text-green-700', 'bg-green-50', 'text-red-600', 'bg-red-50');
            if (data.status === 'success') {
                msgEl.classList.add('text-green-700', 'bg-green-50');
                msgEl.textContent = data.message;
                toastr.success(data.message);
                setTimeout(function () { closeModal('modal-preview'); }, 1500);
            } else {
                msgEl.classList.add('text-red-600', 'bg-red-50');
                msgEl.textContent = data.message;
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-eye-check text-base"></i> {{ __("Enable Preview") }}';
        })
        .catch(function () {
            toastr.error('{{ __("An error occurred.") }}');
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-eye-check text-base"></i> {{ __("Enable Preview") }}';
        });
    });
})();
</script>
@endsection
