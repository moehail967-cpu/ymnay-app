<?php

namespace Modules\TierPricing\Src;

use App\PluginSystem\PluginBase;
use Modules\TierPricing\Src\TierPricingService;

class TierPricingPlugin extends PluginBase
{
    public function id(): string
    {
        return 'tier-pricing';
    }

    private function tenantId(): ?int
    {
        try { return function_exists('tenant') && tenant() ? (int) tenant()->id : null; } catch (\Throwable) { return null; }
    }

    public function routes(): void
    {
        $this->register_settings();
    }

    public function boot(): void
    {
        $this->register_settings();

        if (! $this->get_option('enabled', true)) {
            return;
        }

        $this->add_filter('nazmart:cart_total', [$this, 'applyTierDiscounts'], 5, 1);
        $this->add_action('nazmart:frontend_footer', [$this, 'injectTierWidget'], 10, 0);
    }

    public function applyTierDiscounts(array $totals): array
    {
        $service   = new TierPricingService($this->tenantId());
        $cartItems = \Gloudemans\Shoppingcart\Facades\Cart::instance('default')->content();

        if ($cartItems->isEmpty()) {
            return $totals;
        }

        // Collect product IDs and quantities
        $productQtys = [];
        foreach ($cartItems as $item) {
            $pid = $item->options->product_id ?? null;
            if ($pid) {
                $productQtys[$pid] = ($productQtys[$pid] ?? 0) + (int) $item->qty;
            }
        }

        $tiersMap    = $service->getTiersForProducts(array_keys($productQtys));
        $totalSaving = 0;

        foreach ($productQtys as $pid => $qty) {
            if (empty($tiersMap[$pid])) {
                continue;
            }

            // Find best applicable tier
            $best = null;
            foreach ($tiersMap[$pid] as $tier) {
                if ($qty >= $tier->min_qty) {
                    $best = $tier;
                }
            }

            if (! $best) {
                continue;
            }

            // Compute saving for this product across all its cart rows
            foreach ($cartItems as $item) {
                $itemPid = $item->options->product_id ?? null;
                if ($itemPid != $pid) {
                    continue;
                }

                $regularPrice = (float) $item->price;
                $tierPrice    = $service->calculateTierPrice($best, $regularPrice);
                $saving       = ($regularPrice - $tierPrice) * (int) $item->qty;
                $totalSaving += $saving;
            }
        }

        if ($totalSaving > 0) {
            $totals['tier_discount']  = round($totalSaving, 2);
            $totals['grand_total']    = max(0, ($totals['grand_total'] ?? 0) - round($totalSaving, 2));
        }

        return $totals;
    }

    public function injectTierWidget(): void
    {
        if (! $this->get_option('show_widget_on_product', true)) {
            return;
        }

        $path = request()->path();

        if (! preg_match('#product[s]?/#i', $path)) {
            return;
        }

        $segments = explode('/', trim($path, '/'));
        $slug     = end($segments);

        $product = \DB::table('products')->where('slug', $slug)->first();
        if (! $product) {
            return;
        }

        $service = new TierPricingService($this->tenantId());
        $tiers   = $service->getTiersForProduct($product->id);

        if (empty($tiers)) {
            return;
        }

        echo view('tier-pricing::frontend.widget', [
            'tiers'        => $tiers,
            'regularPrice' => (float) ($product->regular_price ?? 0),
            'service'      => $service,
        ])->render();
    }

    public function on_activate(): void
    {
        $this->run_migrations(__DIR__ . '/../database/migrations');
    }

    public function register_web_routes(): void
    {
        $prefix     = route_prefix('admin');
        $controller = \Modules\TierPricing\Http\Controllers\Admin\TierPricingController::class;
        $mid        = ['web', 'auth:web', 'tenant_admin'];

        \Route::prefix($prefix . '/tier-pricing')->middleware($mid)->name('tenant.admin.tier-pricing.')->group(function () use ($controller) {
            \Route::get('/',                    [$controller, 'index'])->name('index');
            \Route::get('/manage/{productId}',  [$controller, 'manage'])->name('manage');
            \Route::post('/manage/{productId}', [$controller, 'saveTiers'])->name('save');
            \Route::post('/delete/{productId}', [$controller, 'deleteTiers'])->name('delete');
            \Route::get('/settings',            [$controller, 'settings'])->name('settings');
            \Route::post('/settings/save',      [$controller, 'saveSettings'])->name('settings.save');
        });
    }

    public function register_api_routes(): void
    {
        $controller = \Modules\TierPricing\Http\Controllers\Api\TierPricingApiController::class;

        \Route::prefix('api/v1/plugins/tier-pricing')->middleware(['api'])->group(function () use ($controller) {
            \Route::get('/product/{id}', [$controller, 'forProduct']);
        });
    }

    public function register_settings(): void
    {
        $this->add_menu('tier-pricing', [
            'label'      => __('Tier Pricing'),
            'icon'       => 'mdi mdi-layers-triple-outline',
            'route'      => 'tenant.admin.tier-pricing.index',
            'permission' => 'tier_pricing_manage',
        ]);

        $this->add_submenu('tier-pricing', [
            ['label' => __('Products with Tiers'), 'route' => 'tenant.admin.tier-pricing.index'],
            ['label' => __('Settings'),            'route' => 'tenant.admin.tier-pricing.settings'],
        ]);
    }
}
