<?php

namespace Modules\ProductBundles\Src;

use App\PluginSystem\PluginBase;
use Modules\ProductBundles\Src\BundleService;

class ProductBundlesPlugin extends PluginBase
{
    public function id(): string
    {
        return 'product-bundles';
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

        $this->add_filter('nazmart:cart_total', [$this, 'applyBundleDiscount'], 10, 1);
        $this->add_action('nazmart:frontend_footer', [$this, 'injectBundleWidget'], 10, 0);
        $this->add_action('nazmart:order_completed', [$this, 'onOrderCompleted'], 20, 1);
    }

    public function applyBundleDiscount(array $totals): array
    {
        $service = new BundleService($this->tenantId());
        $result  = $service->checkCartForBundle();

        if (! $result) {
            return $totals;
        }

        [$bundle, $discount] = $result;

        $totals['bundle_discount']      = $discount;
        $totals['bundle_name']          = $bundle->name;
        $totals['grand_total']          = max(0, ($totals['grand_total'] ?? 0) - $discount);
        $totals['applied_bundle_id']    = $bundle->id;

        return $totals;
    }

    public function injectBundleWidget(): void
    {
        $path = request()->path();

        // Only inject on product detail pages
        if (! preg_match('#product[s]?/#i', $path)) {
            return;
        }

        // Extract slug from path
        $segments = explode('/', trim($path, '/'));
        $slug     = end($segments);

        $product = \DB::table('products')->where('slug', $slug)->first();
        if (! $product) {
            return;
        }

        $service = new BundleService($this->tenantId());
        $bundles = $service->getBundlesForProduct($product->id);

        if (empty($bundles)) {
            return;
        }

        echo view('product-bundles::frontend.widget', compact('bundles'))->render();
    }

    public function onOrderCompleted(object $order): void
    {
        if (! isset($order->applied_bundle_id)) {
            return;
        }

        $service = new BundleService($this->tenantId());
        $service->incrementSoldCount($order->applied_bundle_id);
    }

    public function on_activate(): void
    {
        $this->run_migrations(__DIR__ . '/../database/migrations');
    }

    public function register_web_routes(): void
    {
        $prefix     = route_prefix('admin');
        $controller = \Modules\ProductBundles\Http\Controllers\Admin\BundleController::class;
        $mid        = ['web', 'auth:web', 'tenant_admin'];

        \Route::prefix($prefix . '/product-bundles')->middleware($mid)->name('tenant.admin.product-bundles.')->group(function () use ($controller) {
            \Route::get('/',                [$controller, 'index'])->name('index');
            \Route::get('/create',          [$controller, 'create'])->name('create');
            \Route::post('/',               [$controller, 'store'])->name('store');
            \Route::get('/{id}/edit',       [$controller, 'edit'])->name('edit');
            \Route::post('/{id}',           [$controller, 'update'])->name('update');
            \Route::post('/{id}/destroy',   [$controller, 'destroy'])->name('destroy');
            \Route::get('/settings',        [$controller, 'settings'])->name('settings');
            \Route::post('/settings/save',  [$controller, 'saveSettings'])->name('settings.save');
        });
    }

    public function register_api_routes(): void
    {
        $controller = \Modules\ProductBundles\Http\Controllers\Api\BundleApiController::class;

        \Route::prefix('api/v1/plugins/product-bundles')->middleware(['api'])->group(function () use ($controller) {
            \Route::get('/check', [$controller, 'check']);
            \Route::get('/for-product/{id}', [$controller, 'forProduct']);
        });
    }

    public function register_settings(): void
    {
        $this->add_menu('product-bundles', [
            'label'      => __('Product Bundles'),
            'icon'       => 'mdi mdi-package-variant-closed',
            'route'      => 'tenant.admin.product-bundles.index',
            'permission' => 'product_bundles_manage',
        ]);

        $this->add_submenu('product-bundles', [
            ['label' => __('All Bundles'), 'route' => 'tenant.admin.product-bundles.index'],
            ['label' => __('Add Bundle'),  'route' => 'tenant.admin.product-bundles.create'],
            ['label' => __('Settings'),    'route' => 'tenant.admin.product-bundles.settings'],
        ]);
    }
}
