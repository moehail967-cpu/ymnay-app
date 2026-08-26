<?php

namespace Modules\ProductBundles\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ProductBundles\Src\BundleService;

class BundleController extends Controller
{
    private function tenantId(): ?int
    {
        try { return function_exists('tenant') && tenant() ? (int) tenant()->id : null; } catch (\Throwable) { return null; }
    }

    private function plugin(): mixed
    {
        return app(\App\PluginSystem\PluginManager::class)->get('product-bundles');
    }

    public function index()
    {
        $service = new BundleService($this->tenantId());
        $bundles = $service->getAllBundles(20);

        foreach ($bundles as $bundle) {
            $bundle->product_ids = $service->getItems($bundle->id);
        }

        return view('product-bundles::admin.index', compact('bundles'));
    }

    public function create()
    {
        $products = \DB::table('products')
            ->where('tenant_id', $this->tenantId())
            ->where('status', 'publish')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('product-bundles::admin.form', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'discount_type'  => 'required|in:percentage,flat',
            'discount_value' => 'required|numeric|min:0',
            'product_ids'    => 'required|array|min:2|max:5',
            'product_ids.*'  => 'integer',
        ]);

        $service = new BundleService($this->tenantId());
        $service->create($request->only(['name', 'description', 'discount_type', 'discount_value', 'status']), $request->input('product_ids'));

        return redirect()->route('tenant.admin.product-bundles.index')->with('success', __('Bundle created successfully'));
    }

    public function edit(int $id)
    {
        $service = new BundleService($this->tenantId());
        $bundle  = $service->find($id);

        if (! $bundle) {
            abort(404);
        }

        $bundle->product_ids = $service->getItems($id);

        $products = \DB::table('products')
            ->where('tenant_id', $this->tenantId())
            ->where('status', 'publish')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('product-bundles::admin.form', compact('bundle', 'products'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'discount_type'  => 'required|in:percentage,flat',
            'discount_value' => 'required|numeric|min:0',
            'product_ids'    => 'required|array|min:2|max:5',
            'product_ids.*'  => 'integer',
        ]);

        $service = new BundleService($this->tenantId());
        $service->update($id, $request->only(['name', 'description', 'discount_type', 'discount_value', 'status']), $request->input('product_ids'));

        return redirect()->route('tenant.admin.product-bundles.index')->with('success', __('Bundle updated successfully'));
    }

    public function destroy(Request $request, int $id)
    {
        $service = new BundleService($this->tenantId());
        $service->delete($id);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('tenant.admin.product-bundles.index')->with('success', __('Bundle deleted'));
    }

    public function settings()
    {
        $plugin   = $this->plugin();
        $settings = [
            'enabled' => $plugin->get_option('enabled', true),
            'show_widget_on_product' => $plugin->get_option('show_widget_on_product', true),
        ];

        return view('product-bundles::admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $plugin = $this->plugin();
        $plugin->update_option('enabled',                 (bool) $request->input('enabled', false));
        $plugin->update_option('show_widget_on_product',  (bool) $request->input('show_widget_on_product', true));

        return redirect()->back()->with('success', __('Settings saved'));
    }
}
