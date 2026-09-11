@extends('tenant.admin.admin-master')

@section('title') {{ __('Multi-Currency') }} @endsection

@section('content')
<div class="col-12">

    <div class="flex justify-end mb-4">
        <a href="{{ route('tenant.admin.multi-currency.settings') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
            <i class="mdi mdi-cog-outline text-base"></i>
            {{ __('Settings') }}
        </a>
    </div>

    {{-- ── Currency cards ───────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-currency-usd text-emerald-500 text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark">{{ __('Available Currencies') }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5" id="mc-enabled-count">
                        {{ $currencies->where('enabled', true)->count() }} {{ __('of') }} {{ $currencies->count() }} {{ __('enabled') }}
                    </p>
                </div>
            </div>
            <button type="button" id="mc-refresh-rates"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition"
                    {{ !$rateApiKey ? 'disabled title="' . __('Add an ExchangeRate-API key in Settings to use this') . '"' : '' }}>
                <i class="mdi mdi-refresh text-sm"></i>
                {{ __('Refresh All Rates') }}
            </button>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3" id="mc-grid">
            @forelse($currencies as $currency)
            <div class="mc-card rounded-xl border-2 p-4 flex flex-col gap-3 transition-all
                        {{ $currency->is_default ? 'border-indigo-400 bg-indigo-50/40' :
                           ($currency->enabled   ? 'border-emerald-300 bg-emerald-50/20' : 'border-gray-200 bg-white') }}"
                 data-code="{{ $currency->code }}"
                 data-enabled="{{ $currency->enabled ? '1' : '0' }}"
                 data-default="{{ $currency->is_default ? '1' : '0' }}">

                {{-- Symbol + name --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="mc-symbol w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold
                                    {{ $currency->is_default ? 'bg-indigo-100 text-indigo-700' :
                                       ($currency->enabled   ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500') }}">
                            {{ $currency->symbol }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-dark leading-tight">{{ $currency->code }}</p>
                            <p class="text-xs text-gray-500 leading-tight">{{ $currency->name }}</p>
                        </div>
                    </div>
                    @if($currency->is_default)
                        <span class="mc-default-badge inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 uppercase tracking-wide">
                            <i class="mdi mdi-star text-xs"></i> {{ __('Default') }}
                        </span>
                    @endif
                </div>

                {{-- Editable rate (only when currency is enabled) --}}
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="text-gray-500">1 {{ $currency->base_code }} =</span>
                        @if($currency->rate_updated_at)
                            <span class="text-[10px] text-gray-400">{{ $currency->rate_updated_at->diffForHumans() }}</span>
                        @endif
                    </div>
                    @if($currency->enabled)
                    <div class="flex items-center gap-1.5">
                        <input type="number" step="0.000001" min="0.000001"
                               value="{{ $currency->rate }}"
                               class="mc-rate-input flex-1 px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400 font-mono"
                               data-code="{{ $currency->code }}">
                        <span class="text-xs text-gray-500 font-semibold">{{ $currency->code }}</span>
                        <button type="button"
                                class="mc-save-rate px-2 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition text-xs font-semibold flex-shrink-0"
                                data-code="{{ $currency->code }}"
                                title="{{ __('Save rate') }}">
                            <i class="mdi mdi-content-save-outline text-sm"></i>
                        </button>
                    </div>
                    @else
                    <div class="flex items-center gap-1.5 mc-rate-display">
                        <span class="flex-1 px-2 py-1.5 text-xs border border-gray-100 rounded-lg bg-gray-50 font-mono text-gray-400">{{ number_format($currency->rate, 4) }}</span>
                        <span class="text-xs text-gray-400 font-semibold">{{ $currency->code }}</span>
                        <span class="px-2 py-1.5 text-xs text-gray-300 flex-shrink-0"><i class="mdi mdi-lock-outline text-sm"></i></span>
                    </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-1 border-t border-gray-100 mt-auto">
                    @if(!$currency->is_default)
                    <button type="button"
                            class="mc-toggle flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition
                                   {{ $currency->enabled ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            data-code="{{ $currency->code }}">
                        <i class="mdi {{ $currency->enabled ? 'mdi-close-circle-outline' : 'mdi-plus-circle-outline' }} text-sm mc-toggle-icon"></i>
                        <span class="mc-toggle-label">{{ $currency->enabled ? __('Disable') : __('Enable') }}</span>
                    </button>
                    @endif

                    @if($currency->enabled && !$currency->is_default)
                    <button type="button"
                            class="mc-set-default inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition"
                            data-code="{{ $currency->code }}"
                            title="{{ __('Set as default currency') }}">
                        <i class="mdi mdi-star-outline text-sm"></i>
                        {{ __('Default') }}
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-4 py-10 text-center text-sm text-gray-400">
                <i class="mdi mdi-currency-usd-off text-3xl block mb-2 text-gray-300"></i>
                {{ __('No currencies available. Please contact your platform administrator.') }}
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Info banner ──────────────────────────────────────────────────────── --}}
    <div class="mt-4 bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3">
        <i class="mdi mdi-information-outline text-blue-400 text-xl flex-shrink-0 mt-0.5"></i>
        <div class="text-xs text-blue-700 space-y-1">
            <p><strong>{{ __('How it works:') }}</strong> {{ __('Visitors use the currency switcher in the bottom-right corner of your store. Prices are converted in real-time using the rates above.') }}</p>
            <p>{{ __('The switcher only appears when 2 or more currencies are enabled.') }}</p>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
(function () {
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;

    function post(url, data, cb) {
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(data)
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
        .then(function (res) { cb(res.ok, res.data); })
        .catch(function () { toastr.error('{{ __("An unexpected error occurred.") }}'); });
    }

    // ── Toggle enable / disable ───────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.mc-toggle');
        if (!btn) return;

        var code = btn.dataset.code;
        btn.disabled = true;

        post('{{ route("tenant.admin.multi-currency.toggle") }}', { code: code }, function (ok, data) {
            btn.disabled = false;
            if (!ok) { toastr.error(data.error); return; }

            var card    = btn.closest('.mc-card');
            var enabled = data.enabled;

            card.dataset.enabled = enabled ? '1' : '0';

            // Card border + bg
            card.className = card.className
                .replace(/border-indigo-400|bg-indigo-50\/40|border-emerald-300|bg-emerald-50\/20|border-gray-200|bg-white/g, '')
                .trim();
            card.classList.add(...(enabled
                ? ['border-emerald-300', 'bg-emerald-50/20']
                : ['border-gray-200', 'bg-white']));

            // Symbol badge colour
            var sym = card.querySelector('.mc-symbol');
            sym.className = sym.className
                .replace(/bg-indigo-100|text-indigo-700|bg-emerald-100|text-emerald-700|bg-gray-100|text-gray-500/g, '')
                .trim();
            sym.classList.add(...(enabled
                ? ['bg-emerald-100', 'text-emerald-700']
                : ['bg-gray-100', 'text-gray-500']));

            // Toggle button state
            btn.className = btn.className
                .replace(/bg-red-50|text-red-600|hover:bg-red-100|bg-gray-100|text-gray-700|hover:bg-gray-200/g, '')
                .trim();
            btn.classList.add(...(enabled
                ? ['bg-red-50', 'text-red-600', 'hover:bg-red-100']
                : ['bg-gray-100', 'text-gray-700', 'hover:bg-gray-200']));

            var icon  = btn.querySelector('.mc-toggle-icon');
            var label = btn.querySelector('.mc-toggle-label');
            icon.className  = 'mdi ' + (enabled ? 'mdi-close-circle-outline' : 'mdi-plus-circle-outline') + ' text-sm mc-toggle-icon';
            label.textContent = enabled ? '{{ __("Disable") }}' : '{{ __("Enable") }}';

            // Show/hide Set Default button
            var defBtn = card.querySelector('.mc-set-default');
            if (defBtn) defBtn.style.display = enabled ? '' : 'none';

            // Swap rate field between editable and locked display
            var rateWrap = card.querySelector('.mc-rate-input')?.parentElement
                        || card.querySelector('.mc-rate-display');
            if (rateWrap) {
                var currentRate = card.querySelector('.mc-rate-input')?.value
                               || card.querySelector('.mc-rate-display span')?.textContent?.trim()
                               || '0';
                var code = card.dataset.code;
                if (enabled) {
                    rateWrap.outerHTML =
                        '<div class="flex items-center gap-1.5">' +
                        '<input type="number" step="0.000001" min="0.000001" value="' + currentRate + '" ' +
                        'class="mc-rate-input flex-1 px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400 font-mono" data-code="' + code + '">' +
                        '<span class="text-xs text-gray-500 font-semibold">' + code + '</span>' +
                        '<button type="button" class="mc-save-rate px-2 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition text-xs font-semibold flex-shrink-0" data-code="' + code + '" title="{{ __("Save rate") }}">' +
                        '<i class="mdi mdi-content-save-outline text-sm"></i></button></div>';
                } else {
                    rateWrap.outerHTML =
                        '<div class="flex items-center gap-1.5 mc-rate-display">' +
                        '<span class="flex-1 px-2 py-1.5 text-xs border border-gray-100 rounded-lg bg-gray-50 font-mono text-gray-400">' + currentRate + '</span>' +
                        '<span class="text-xs text-gray-400 font-semibold">' + code + '</span>' +
                        '<span class="px-2 py-1.5 text-xs text-gray-300 flex-shrink-0"><i class="mdi mdi-lock-outline text-sm"></i></span></div>';
                }
            }

            updateCount();
            toastr.success(data.message);
        });
    });

    // ── Set default ───────────────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.mc-set-default');
        if (!btn) return;

        var code = btn.dataset.code;
        btn.disabled = true;

        post('{{ route("tenant.admin.multi-currency.set-default") }}', { code: code }, function (ok, data) {
            btn.disabled = false;
            if (!ok) { toastr.error(data.error); return; }

            // Clear all default styles
            document.querySelectorAll('.mc-card').forEach(function (c) {
                c.dataset.default = '0';
                c.className = c.className
                    .replace(/border-indigo-400|bg-indigo-50\/40/g, '').trim();
                var isEnabled = c.dataset.enabled === '1';
                if (isEnabled && !c.classList.contains('border-emerald-300')) {
                    c.classList.add('border-emerald-300', 'bg-emerald-50/20');
                }
                var badge = c.querySelector('.mc-default-badge');
                if (badge) badge.remove();
                // Re-show toggle button for previously-default card
                var tgl = c.querySelector('.mc-toggle');
                if (tgl) tgl.style.display = '';
            });

            // Apply to new default card
            var card = document.querySelector('.mc-card[data-code="' + code + '"]');
            card.dataset.default = '1';
            card.className = card.className
                .replace(/border-emerald-300|bg-emerald-50\/20|border-gray-200|bg-white/g, '').trim();
            card.classList.add('border-indigo-400', 'bg-indigo-50/40');

            // Add default badge
            var nameWrap = card.querySelector('.mc-symbol').parentElement.parentElement;
            var badge = document.createElement('span');
            badge.className = 'mc-default-badge inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 uppercase tracking-wide';
            badge.innerHTML = '<i class="mdi mdi-star text-xs"></i> {{ __("Default") }}';
            nameWrap.appendChild(badge);

            // Hide toggle on this card (default cannot be disabled)
            var tgl = card.querySelector('.mc-toggle');
            if (tgl) tgl.style.display = 'none';
            // Hide Set Default on this card
            btn.style.display = 'none';

            toastr.success(data.message);
        });
    });

    // ── Save individual rate ──────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.mc-save-rate');
        if (!btn) return;

        var code  = btn.dataset.code;
        var input = document.querySelector('.mc-rate-input[data-code="' + code + '"]');
        var rate  = parseFloat(input.value);

        if (!rate || rate <= 0) { toastr.error('{{ __("Enter a valid positive rate.") }}'); return; }

        btn.disabled = true;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-sm"></i>';

        post('{{ route("tenant.admin.multi-currency.update-rate") }}', { code: code, rate: rate }, function (ok, data) {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-content-save-outline text-sm"></i>';
            if (!ok) { toastr.error(data.error); return; }
            toastr.success(data.message);
        });
    });

    // ── Refresh all rates ─────────────────────────────────────────────────────
    var refreshBtn = document.getElementById('mc-refresh-rates');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-sm"></i> {{ __("Refreshing...") }}';

            post('{{ route("tenant.admin.multi-currency.refresh-rates") }}', {}, function (ok, data) {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = '<i class="mdi mdi-refresh text-sm"></i> {{ __("Refresh All Rates") }}';
                if (!ok) { toastr.error(data.error); return; }
                toastr.success(data.message);
                // Reload to show updated rates on inputs
                setTimeout(function () { location.reload(); }, 1200);
            });
        });
    }

    // ── Update enabled count label ────────────────────────────────────────────
    function updateCount() {
        var total   = document.querySelectorAll('.mc-card').length;
        var enabled = document.querySelectorAll('.mc-card[data-enabled="1"]').length;
        var el = document.getElementById('mc-enabled-count');
        if (el) el.textContent = enabled + ' {{ __("of") }} ' + total + ' {{ __("enabled") }}';
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success')) toastr.success("{{ session('success') }}"); @endif
        @if(session('error'))   toastr.error("{{ session('error') }}");     @endif
    });
})();
</script>
@endsection
