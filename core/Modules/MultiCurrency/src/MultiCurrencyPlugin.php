<?php

namespace Modules\MultiCurrency\Src;

use App\PluginSystem\PluginBase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Modules\MultiCurrency\Http\Controllers\Admin\TenantCurrencyController;
use Modules\MultiCurrency\Http\Controllers\Frontend\CurrencySwitchController;
use Modules\MultiCurrency\Models\Currency;
use Modules\MultiCurrency\Models\TenantCurrency;
use Modules\MultiCurrency\Services\GeoDetector;

class MultiCurrencyPlugin extends PluginBase
{
    public function id(): string
    {
        return 'multi-currency';
    }

    // ── Boot ─────────────────────────────────────────────────────────────────

    public function boot(): void
    {
        // Convert product prices on render
        $this->add_filter('nazmart:product_price', [$this, 'convertPrice'], 20, 2);

        // Convert cart total
        $this->add_filter('nazmart:cart_total', [$this, 'convertPrice'], 20, 2);

        // Override currency symbol displayed next to converted prices
        $this->add_filter('nazmart:currency_symbol', [$this, 'getSelectedSymbol'], 10);

        // ── CartDiscount plugin hooks ─────────────────────────────────────────
        // Convert tier thresholds and flat amounts from base to selected currency
        $this->add_filter('cart_discount:threshold',  [$this, 'convertPrice'], 10, 1);
        $this->add_filter('cart_discount:flat_amount', [$this, 'convertPrice'], 10, 1);
        // Supply selected-currency symbol to the JS widget config
        $this->add_filter('cart_discount:symbol', [$this, 'getSelectedSymbol'], 10);

        // ── LoyaltyPoints plugin hooks ────────────────────────────────────────
        // Convert base-currency redemption discount to selected currency
        $this->add_filter('loyalty_points:redemption_discount', [$this, 'convertPrice'], 10, 1);
        // Supply selected-currency cart total for max-discount cap calculation
        $this->add_filter('loyalty_points:cart_total', [$this, 'convertPrice'], 10, 1);

        // Auto-detect currency from visitor IP on first visit (fires in <head>)
        $this->add_action('nazmart:frontend_head', [$this, 'maybeAutoDetect'], 5);

        // Inject switcher CSS into <head>, widget button into navbar icons row
        $this->add_action('nazmart:frontend_head_end', [$this, 'injectSwitcherStyles'], 10);
        $this->add_action('nazmart:navbar_icons',      [$this, 'injectSwitcherWidget'], 10);

        $this->add_menu([
            'id'      => 'multi-currency-tenant-menu',
            'label'   => __('Multi Currency'),
            'icon'    => 'mdi-currency-usd',
            'route'   => 'tenant.admin.multi-currency.index',
            'order'   => 85,
            'context' => 'tenant',
        ]);

        $this->add_submenu('multi-currency-tenant-menu', [
            'id'    => 'multi-currency-currencies',
            'label' => __('Currencies'),
            'route' => 'tenant.admin.multi-currency.index',
        ]);

        $this->add_submenu('multi-currency-tenant-menu', [
            'id'    => 'multi-currency-settings',
            'label' => __('Settings'),
            'route' => 'tenant.admin.multi-currency.settings',
        ]);

        $this->register_settings([
            ['key' => 'geo_detect', 'label' => 'Auto-detect currency from visitor IP', 'type' => 'toggle', 'default' => '0'],
        ]);

        // Scheduled daily rate refresh
        $this->schedule('daily', [$this, 'refreshRates']);
    }

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function on_activate(): void
    {
        $this->run_migrations();

        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar',       'symbol' => '$',   'rate' => 1.000000],
            ['code' => 'EUR', 'name' => 'Euro',             'symbol' => '€',   'rate' => 0.920000],
            ['code' => 'GBP', 'name' => 'British Pound',    'symbol' => '£',   'rate' => 0.790000],
            ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'symbol' => '৳',   'rate' => 110.000000],
            ['code' => 'INR', 'name' => 'Indian Rupee',     'symbol' => '₹',   'rate' => 83.000000],
            ['code' => 'AED', 'name' => 'UAE Dirham',       'symbol' => 'AED', 'rate' => 3.670000],
            ['code' => 'AUD', 'name' => 'Australian Dollar','symbol' => 'A$',  'rate' => 1.520000],
            ['code' => 'CAD', 'name' => 'Canadian Dollar',  'symbol' => 'C$',  'rate' => 1.360000],
            ['code' => 'JPY', 'name' => 'Japanese Yen',     'symbol' => '¥',   'rate' => 149.000000],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$',  'rate' => 1.340000],
            ['code' => 'MYR', 'name' => 'Malaysian Ringgit','symbol' => 'RM',  'rate' => 4.680000],
            ['code' => 'SAR', 'name' => 'Saudi Riyal',      'symbol' => '﷼',   'rate' => 3.750000],
            ['code' => 'PKR', 'name' => 'Pakistani Rupee',  'symbol' => '₨',   'rate' => 278.000000],
            ['code' => 'NGN', 'name' => 'Nigerian Naira',   'symbol' => '₦',   'rate' => 1500.000000],
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

        $this->update_option('geo_detect', '0');

        // Seed a default selection so the switcher widget is visible immediately
        if (TenantCurrency::count() === 0) {
            TenantCurrency::firstOrCreate(['currency_code' => 'USD'], ['is_default' => true]);
            TenantCurrency::firstOrCreate(['currency_code' => 'EUR'], ['is_default' => false]);
            TenantCurrency::firstOrCreate(['currency_code' => 'GBP'], ['is_default' => false]);
        }
    }

    public function on_deactivate(): void
    {
        $tenantKey = 'mc_currencies_' . (function_exists('tenant') && tenant() ? tenant()->id : 'default');
        Cache::forget($tenantKey);
        Cache::forget('mc_currencies_list');
    }

    public function on_update(string $from_version): void
    {
        $this->run_migrations();
    }

    // ── Routes — always registered even when plugin is inactive ──────────────

    public function routes(): void
    {
        $this->register_web_routes(function () {
            Route::middleware([
                    'web',
                    \App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware::class,
                    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
                    'auth:admin',
                    'tenant_admin_glvar',
                    'package_expire',
                    'tenantAdminPanelMailVerify',
                    'tenant_status',
                    'set_lang',
                ])
                ->prefix('admin-home/multi-currency')
                ->name('tenant.admin.multi-currency.')
                ->group(function () {
                    Route::get('/', [TenantCurrencyController::class, 'index'])->name('index');
                    Route::post('/toggle', [TenantCurrencyController::class, 'toggle'])->name('toggle');
                    Route::post('/set-default', [TenantCurrencyController::class, 'setDefault'])->name('set-default');
                    Route::get('/settings', [TenantCurrencyController::class, 'showSettings'])->name('settings');
                    Route::post('/settings', [TenantCurrencyController::class, 'saveSettings'])->name('settings.save');
                    Route::post('/update-rate', [TenantCurrencyController::class, 'updateRate'])->name('update-rate');
                    Route::post('/refresh-rates', [TenantCurrencyController::class, 'refreshRates'])->name('refresh-rates');
                });

            // Frontend switch endpoint — needs tenant context to query tenant DB
            if (\App\PluginSystem\PluginManager::isActive('multi-currency')) {
                Route::middleware([
                        'web',
                        \App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware::class,
                        \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
                        'tenant_glvar',
                        'set_lang',
                        'package_expire',
                    ])
                    ->post('/mc-switch', [CurrencySwitchController::class, 'switch'])
                    ->name('tenant.mc.switch');
            }
        });
    }

    // ── Filter callbacks ──────────────────────────────────────────────────────

    public function convertPrice(float $price, mixed $context = null): float
    {
        $selected = $this->selectedCurrency();
        if (!$selected) {
            return $price;
        }

        $baseCurrency = TenantCurrency::getDefault()?->currency_code ?? 'USD';
        if ($selected->code === $baseCurrency) {
            return $price;
        }

        return round($price * (float) $selected->rate, 2);
    }

    public function getSelectedSymbol(?array $default): ?array
    {
        $selected = $this->selectedCurrency();
        if (!$selected) {
            return null;
        }
        return ['symbol' => $selected->symbol, 'code' => $selected->code];
    }

    // ── Geo-detection ─────────────────────────────────────────────────────────

    public function maybeAutoDetect(): void
    {
        // Only run once per visitor session
        if (session()->has('mc_currency')) {
            return;
        }

        if ($this->get_option('geo_detect', '0') !== '1') {
            return;
        }

        $detector = new GeoDetector();
        if (!$detector->isAvailable()) {
            return;
        }

        $ip   = request()->ip();
        $code = $detector->detect($ip);

        if (!$code || !TenantCurrency::isEnabled($code)) {
            // Fall back to tenant default
            $default = TenantCurrency::getDefault();
            if ($default) {
                session(['mc_currency' => $default->currency_code]);
            }
            return;
        }

        session(['mc_currency' => $code]);

        // Emit one-time JS reload so this page renders in the detected currency.
        // localStorage flag prevents infinite reload loops.
        echo <<<JS
        <script>
        (function(){
            if(!localStorage.getItem('mc_geo_done')){
                localStorage.setItem('mc_geo_done','1');
                location.reload();
            }
        })();
        </script>
        JS;
    }

    // ── Scheduled rate refresh ────────────────────────────────────────────────

    public function refreshRates(): void
    {
        $apiKey      = $this->get_option('rate_api_key', '');
        $baseCurrency = 'USD';

        if (!$apiKey) {
            return;
        }

        try {
            $response = \Http::timeout(10)->get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$baseCurrency}");
            if (!$response->ok()) {
                return;
            }

            $rates = $response->json('conversion_rates', []);

            foreach ($rates as $code => $rate) {
                Currency::where('code', $code)->update([
                    'rate'             => $rate,
                    'rate_updated_at'  => now(),
                ]);
            }

            Cache::forget('mc_currencies_list');
        } catch (\Throwable $e) {
            Log::channel('plugin')->error("[multi-currency] refreshRates: {$e->getMessage()}");
        }
    }

    // ── Frontend UI injection ─────────────────────────────────────────────────

    public function injectSwitcherStyles(): void
    {
        echo <<<CSS
        <style>
        .mc-navbar-switcher{display:inline-flex;align-items:center;gap:5px;cursor:pointer;position:relative;}
        .mc-navbar-switcher i{font-size:18px;line-height:1;opacity:.85;}
        .mc-navbar-switcher select{appearance:none;-webkit-appearance:none;border:none;outline:none;background:transparent;font-size:13px;font-weight:600;cursor:pointer;color:inherit;padding:0;max-width:72px;}
        .mc-navbar-switcher select option{color:#111;background:#fff;}
        </style>
        CSS;
    }

    public function injectSwitcherWidget(): void
    {
        $tenantKey  = 'mc_currencies_' . (function_exists('tenant') && tenant() ? tenant()->id : 'default');
        $currencies = Cache::remember($tenantKey, 3600, fn () =>
            TenantCurrency::enabled()
                ->map(fn ($tc) => Currency::findByCode($tc->currency_code))
                ->filter()
                ->values()
        );

        if ($currencies->count() <= 1) {
            return;
        }

        $defaultCode = TenantCurrency::getDefault()?->currency_code ?? $currencies->first()->code;
        $selected    = session('mc_currency', $defaultCode);

        $options = $currencies->map(fn ($c) =>
            '<option value="' . e($c->code) . '"' . ($selected === $c->code ? ' selected' : '') . '>'
            . e($c->symbol) . ' ' . e($c->code) . '</option>'
        )->implode('');

        $switchUrl = route('tenant.mc.switch');

        echo <<<HTML
        <div class="mc-navbar-switcher">
            <i class="mdi mdi-web"></i>
            <select id="mc-select" onchange="mcSwitch(this.value)" aria-label="Currency">
                {$options}
            </select>
        </div>
        <script>
        if(typeof mcSwitch==='undefined'){
            function mcSwitch(code){
                fetch('{$switchUrl}',{
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content??''},
                    body:JSON.stringify({code:code})
                }).then(function(r){if(r.ok)location.reload();});
            }
        }
        </script>
        HTML;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function selectedCurrency(): ?object
    {
        $code = session('mc_currency');
        if (!$code) {
            return null;
        }

        return Cache::remember("mc_rate_{$code}", 3600, fn () =>
            Currency::findByCode($code)
        );
    }
}
