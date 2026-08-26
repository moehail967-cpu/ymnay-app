<?php

namespace Modules\MultiCurrency\Http\Controllers\Admin;

use App\PluginSystem\PluginManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\MultiCurrency\Models\Currency;
use Modules\MultiCurrency\Models\TenantCurrency;

class TenantCurrencyController extends Controller
{
    private function plugin(): mixed
    {
        return app(PluginManager::class)->get('multi-currency');
    }

    public function index()
    {
        $this->ensureCurrenciesSeeded();
        $this->ensureDefaultCurrency();

        $allCurrencies    = Currency::allActive();
        $enabledCodes     = TenantCurrency::enabled()->pluck('is_default', 'currency_code');
        $defaultCode      = TenantCurrency::getDefault()?->currency_code;
        $rateApiKey = $this->plugin()?->get_option('rate_api_key', '') ?? '';

        $currencies = $allCurrencies->map(function ($currency) use ($enabledCodes, $defaultCode) {
            $currency->enabled    = $enabledCodes->has($currency->code);
            $currency->is_default = $currency->code === $defaultCode;
            return $currency;
        });

        return view('multi-currency::admin.index', compact('currencies', 'rateApiKey'));
    }

    private function ensureCurrenciesSeeded(): void
    {
        if (Currency::count() > 0) {
            return;
        }

        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar',         'symbol' => '$',   'rate' => 1.000000],
            ['code' => 'EUR', 'name' => 'Euro',               'symbol' => '€',   'rate' => 0.920000],
            ['code' => 'GBP', 'name' => 'British Pound',      'symbol' => '£',   'rate' => 0.790000],
            ['code' => 'BDT', 'name' => 'Bangladeshi Taka',   'symbol' => '৳',   'rate' => 110.000000],
            ['code' => 'INR', 'name' => 'Indian Rupee',       'symbol' => '₹',   'rate' => 83.000000],
            ['code' => 'AED', 'name' => 'UAE Dirham',         'symbol' => 'AED', 'rate' => 3.670000],
            ['code' => 'AUD', 'name' => 'Australian Dollar',  'symbol' => 'A$',  'rate' => 1.520000],
            ['code' => 'CAD', 'name' => 'Canadian Dollar',    'symbol' => 'C$',  'rate' => 1.360000],
            ['code' => 'JPY', 'name' => 'Japanese Yen',       'symbol' => '¥',   'rate' => 149.000000],
            ['code' => 'SGD', 'name' => 'Singapore Dollar',   'symbol' => 'S$',  'rate' => 1.340000],
            ['code' => 'MYR', 'name' => 'Malaysian Ringgit',  'symbol' => 'RM',  'rate' => 4.680000],
            ['code' => 'SAR', 'name' => 'Saudi Riyal',        'symbol' => '﷼',   'rate' => 3.750000],
            ['code' => 'PKR', 'name' => 'Pakistani Rupee',    'symbol' => '₨',   'rate' => 278.000000],
            ['code' => 'NGN', 'name' => 'Nigerian Naira',     'symbol' => '₦',   'rate' => 1500.000000],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['code' => $currency['code']],
                array_merge($currency, [
                    'is_active'       => true,
                    'base_code'       => 'USD',
                    'rate_updated_at' => now(),
                ])
            );
        }
    }

    private function ensureDefaultCurrency(): void
    {
        if (TenantCurrency::count() > 0) {
            return;
        }

        // Use the store's configured currency as the initial default
        $storeCode = strtoupper(trim(get_static_option('site_global_currency') ?? 'USD'));

        // Fall back to USD if the store currency isn't in our list
        if (!Currency::findByCode($storeCode)) {
            $storeCode = 'USD';
        }

        TenantCurrency::create([
            'currency_code' => $storeCode,
            'is_default'    => true,
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $code = strtoupper(trim($request->input('code', '')));

        if (!Currency::findByCode($code)) {
            return response()->json(['error' => __('Invalid currency code.')], 422);
        }

        if (TenantCurrency::isEnabled($code)) {
            $current = TenantCurrency::where('currency_code', $code)->first();
            if ($current?->is_default) {
                $next = TenantCurrency::where('currency_code', '!=', $code)->first();
                if ($next) {
                    $next->update(['is_default' => true]);
                } else {
                    return response()->json(['error' => __('Cannot disable the only active currency.')], 422);
                }
            }
            TenantCurrency::where('currency_code', $code)->delete();
            $enabled = false;
        } else {
            $isFirst = TenantCurrency::count() === 0;
            TenantCurrency::create([
                'currency_code' => $code,
                'is_default'    => $isFirst,
            ]);
            $enabled = true;
        }

        $this->clearCache();

        return response()->json(['enabled' => $enabled, 'message' => __('Currency updated.')]);
    }

    public function setDefault(Request $request): JsonResponse
    {
        $code = strtoupper(trim($request->input('code', '')));

        if (!TenantCurrency::isEnabled($code)) {
            return response()->json(['error' => __('Enable this currency first.')], 422);
        }

        TenantCurrency::clearDefault();
        TenantCurrency::where('currency_code', $code)->update(['is_default' => true]);
        session(['mc_currency' => $code]);
        $this->clearCache();

        return response()->json(['message' => __('Default currency updated.')]);
    }

    public function updateRate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:3',
            'rate' => 'required|numeric|min:0.000001',
        ]);

        $code = strtoupper($request->input('code'));
        $rate = (float) $request->input('rate');

        $updated = Currency::where('code', $code)->update([
            'rate'             => $rate,
            'rate_updated_at'  => now(),
        ]);

        if (!$updated) {
            return response()->json(['error' => __('Currency not found.')], 422);
        }

        Cache::forget("mc_rate_{$code}");
        Cache::forget('mc_currencies_list');

        return response()->json(['message' => __('Rate updated.'), 'rate' => number_format($rate, 4)]);
    }

    public function refreshRates(Request $request): JsonResponse
    {
        $plugin = $this->plugin();
        $apiKey = $plugin?->get_option('rate_api_key', '');

        if (!$apiKey) {
            return response()->json(['error' => __('No API key configured. Add your ExchangeRate-API key in Settings.')], 422);
        }

        try {
            $response = Http::timeout(10)->get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD");

            if (!$response->ok()) {
                return response()->json(['error' => __('API request failed. Check your API key.')], 422);
            }

            $rates = $response->json('conversion_rates', []);
            $updated = 0;

            foreach ($rates as $code => $rate) {
                $rows = Currency::where('code', $code)->update([
                    'rate'            => $rate,
                    'rate_updated_at' => now(),
                ]);
                if ($rows) {
                    Cache::forget("mc_rate_{$code}");
                    $updated++;
                }
            }

            Cache::forget('mc_currencies_list');

            return response()->json(['message' => __(':count rates refreshed from ExchangeRate-API.', ['count' => $updated])]);
        } catch (\Throwable $e) {
            Log::channel('plugin')->error("[multi-currency] refreshRates: {$e->getMessage()}");
            return response()->json(['error' => __('Rate refresh failed: ') . $e->getMessage()], 500);
        }
    }

    public function showSettings()
    {
        $plugin     = $this->plugin();
        $geoDetect  = $plugin?->get_option('geo_detect', '0');
        $rateApiKey = $plugin?->get_option('rate_api_key', '') ?? '';
        $mmdbPath   = storage_path('app/geoip/GeoLite2-Country.mmdb');
        $mmdbExists = file_exists($mmdbPath);

        return view('multi-currency::admin.settings', compact(
            'geoDetect', 'rateApiKey', 'mmdbExists', 'mmdbPath'
        ));
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $plugin = $this->plugin();
        if (!$plugin) {
            return back()->with('error', __('Plugin not available.'));
        }

        $plugin->update_option('geo_detect', $request->boolean('geo_detect') ? '1' : '0');
        $plugin->update_option('rate_api_key', trim($request->input('rate_api_key', '')));

        return redirect()->route('tenant.admin.multi-currency.settings')
            ->with('success', __('Settings saved.'));
    }

private function clearCache(): void
    {
        Cache::forget('mc_tenant_currencies');
        Cache::forget('mc_currencies_list');
    }
}
