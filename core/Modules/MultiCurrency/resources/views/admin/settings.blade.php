@extends('tenant.admin.admin-master')

@section('title') {{ __('Multi-Currency Settings') }} @endsection

@section('content')
<div class="col-12">

    <div class="flex justify-end mb-4">
        <a href="{{ route('tenant.admin.multi-currency.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
            <i class="mdi mdi-arrow-left text-base"></i>
            {{ __('Currencies') }}
        </a>
    </div>

    <form action="{{ route('tenant.admin.multi-currency.settings.save') }}" method="POST">
    @csrf

    {{-- ── Exchange Rate API ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 mb-5">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-swap-horizontal text-emerald-500 text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark">{{ __('Exchange Rate API') }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Automatically fetch live exchange rates on a daily schedule or on demand.') }}</p>
            </div>
        </div>

        <div class="p-5 space-y-5">
            {{-- Provider info --}}
            <div class="flex gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <div class="w-9 h-9 rounded-lg bg-white border border-gray-200 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-earth text-indigo-500 text-lg"></i>
                </div>
                <div class="flex-1 text-xs text-gray-600 space-y-1">
                    <p class="font-semibold text-dark">ExchangeRate-API <span class="font-normal text-gray-400">(exchangerate-api.com)</span></p>
                    <p>{{ __('Free plan: 1,500 requests/month. Rates are fetched in USD base and applied to all enabled currencies.') }}</p>
                    <p>{{ __('Sign up for free and copy your API key below.') }}</p>
                </div>
            </div>

            {{-- API key field --}}
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-dark">{{ __('API Key') }}</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="rate_api_key" id="rate_api_key"
                           value="{{ $rateApiKey }}"
                           placeholder="{{ __('Paste your ExchangeRate-API key here…') }}"
                           class="flex-1 px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400 font-mono">
                    <button type="button" id="btn-test-api"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition flex-shrink-0">
                        <i class="mdi mdi-lightning-bolt text-sm"></i>
                        {{ __('Test Key') }}
                    </button>
                </div>
                <p id="api-test-result" class="text-xs hidden"></p>
                <p class="text-[11px] text-gray-400">
                    {{ __('Rates refresh automatically every day at midnight. You can also trigger a manual refresh from the Currencies page.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- ── IP Geo-Detection ─────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 mb-5">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-map-marker-outline text-blue-500 text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark">{{ __('IP Geo-Detection') }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Automatically switch currency based on the visitor\'s country.') }}</p>
            </div>
        </div>

        <div class="p-5">
            <div class="flex items-start justify-between gap-6">
                <div class="flex-1">
                    <p class="text-sm font-semibold text-dark">{{ __('Auto-detect currency from visitor IP') }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ __('On the visitor\'s first page load, their IP address is looked up in the offline GeoLite2 database to determine their country. The matching currency is then pre-selected for them.') }}
                    </p>

                    {{-- MMDB status badge --}}
                    <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold
                        {{ $mmdbExists ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                        <i class="mdi {{ $mmdbExists ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline' }} text-base"></i>
                        {{ $mmdbExists ? __('GeoLite2 database found — geo-detection ready') : __('GeoLite2 database missing — upload it below to enable this feature') }}
                    </div>
                </div>

                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-0.5">
                    <input type="checkbox" name="geo_detect" value="1" class="sr-only peer"
                           {{ $geoDetect === '1' ? 'checked' : '' }}
                           {{ !$mmdbExists ? 'disabled' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                peer-checked:bg-indigo-500 peer-disabled:opacity-40
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all
                                peer-checked:after:translate-x-5"></div>
                </label>
            </div>
        </div>
    </div>

    {{-- Save button --}}
    <div class="flex justify-end mb-5">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
            <i class="mdi mdi-content-save-outline text-base"></i>
            {{ __('Save Settings') }}
        </button>
    </div>

    </form>

    {{-- ── GeoLite2 Database Status ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 mb-5">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-database-outline text-violet-500 text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark">{{ __('GeoLite2 Database') }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ __('Offline IP database used for auto-detecting visitor currency.') }}</p>
            </div>
        </div>

        <div class="p-5 flex items-center gap-4">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold flex-shrink-0
                {{ $mmdbExists ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                <i class="mdi {{ $mmdbExists ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline' }} text-base"></i>
                {{ $mmdbExists ? __('Database ready') : __('Database not installed') }}
            </div>
            @if(!$mmdbExists)
            <p class="text-xs text-gray-500">
                {{ __('The GeoLite2 database has not been installed yet. Please contact your platform administrator to set it up.') }}
            </p>
            @else
            <p class="text-xs text-gray-400 font-mono truncate">{{ $mmdbPath }}</p>
            @endif
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(session('success')) toastr.success("{{ session('success') }}"); @endif
    @if(session('error'))   toastr.error("{{ session('error') }}");     @endif

    // ── Test API key ──────────────────────────────────────────────────────────
    document.getElementById('btn-test-api').addEventListener('click', function () {
        var key    = document.getElementById('rate_api_key').value.trim();
        var result = document.getElementById('api-test-result');

        if (!key) {
            result.className = 'text-xs text-amber-600 mt-1';
            result.textContent = '{{ __("Please enter an API key first.") }}';
            result.classList.remove('hidden');
            return;
        }

        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin text-sm"></i> {{ __("Testing…") }}';

        fetch('https://v6.exchangerate-api.com/v6/' + key + '/latest/USD')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                btn.disabled = false;
                btn.innerHTML = '<i class="mdi mdi-lightning-bolt text-sm"></i> {{ __("Test Key") }}';
                result.classList.remove('hidden');
                if (data.result === 'success') {
                    result.className = 'text-xs text-green-600 mt-1';
                    result.textContent = '{{ __("Key is valid! Next update: ") }}' + (data.time_next_update_utc || '—');
                } else {
                    result.className = 'text-xs text-red-500 mt-1';
                    result.textContent = '{{ __("Invalid key or quota exceeded: ") }}' + (data['error-type'] || 'unknown error');
                }
            })
            .catch(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="mdi mdi-lightning-bolt text-sm"></i> {{ __("Test Key") }}';
                result.className = 'text-xs text-red-500 mt-1';
                result.textContent = '{{ __("Could not reach ExchangeRate-API. Check your connection.") }}';
                result.classList.remove('hidden');
            });
    });
});
</script>
@endsection
