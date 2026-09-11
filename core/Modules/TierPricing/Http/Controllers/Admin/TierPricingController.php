<?php

namespace Modules\TierPricing\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TierPricing\Src\TierPricingService;

class TierPricingController extends Controller
{
    private function tenantId(): ?int
    {
        try { return function_exists('tenant') && tenant() ? (int) tenant()->id : null; } catch (\Throwable) { return null; }
    }

    private function plugin(): mixed
    {
        return app(\App\PluginSystem\PluginManager::class)->get('tier-pricing');
    }

    public function index()
    {
        $service  = new TierPricingService($this->tenantId());
        $products = $service->getProductsWithTiers(20);

        foreach ($products as $product) {
            $product->tiers = $service->getTiersForProduct($product->id);
        }

        // All tenant products for the "add tier" quick-select
        $allProducts = \DB::table('products')
            ->where('tenant_id', $this->tenantId())
            ->where('status', 'publish')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('tier-pricing::admin.index', compact('products', 'allProducts'));
    }

    public function manage(int $productId)
    {
        $service = new TierPricingService($this->tenantId());
        $product = \DB::table('products')->where('id', $productId)->first();

        if (! $product) {
            abort(404);
        }

        $tiers = $service->getTiersForProduct($productId);

        return view('tier-pricing::admin.manage', compact('product', 'tiers'));
    }

    public function saveTiers(Request $request, int $productId)
    {
        $request->validate([
            'tiers'                  => 'required|array|min:1',
            'tiers.*.min_qty'        => 'required|integer|min:2',
            'tiers.*.discount_type'  => 'required|in:percentage,flat,fixed_price',
            'tiers.*.discount_value' => 'required|numeric|min:0',
        ]);

        $service = new TierPricingService($this->tenantId());
        $service->setTiers($productId, $request->input('tiers'));

        return redirect()->route('tenant.admin.tier-pricing.index')
            ->with('success', __('Tier pricing saved'));
    }

    public function deleteTiers(Request $request, int $productId)
    {
        $service = new TierPricingService($this->tenantId());
        $service->deleteTiersForProduct($productId);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('tenant.admin.tier-pricing.index')
            ->with('success', __('Tier pricing removed'));
    }

    public function settings()
    {
        $plugin   = $this->plugin();
        $settings = [
            'enabled'                => $plugin->get_option('enabled', true),
            'show_widget_on_product' => $plugin->get_option('show_widget_on_product', true),
        ];

        return view('tier-pricing::admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $plugin = $this->plugin();
        $plugin->update_option('enabled',                 (bool) $request->input('enabled', false));
        $plugin->update_option('show_widget_on_product',  (bool) $request->input('show_widget_on_product', true));

        return redirect()->back()->with('success', __('Settings saved'));
    }
}
