<?php

namespace Modules\FomoNotifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\FomoNotifications\Models\FomoSyntheticEntry;

class FomoController extends Controller
{
    private function plugin()
    {
        return app(\App\PluginSystem\PluginManager::class)->get('fomo-notifications');
    }

    private function tenantId(): ?string
    {
        return function_exists('tenant') && tenant() ? (string) tenant('id') : null;
    }

    public function settings()
    {
        $plugin = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');
        $keys   = ['enabled','data_source','real_order_count','min_real_orders','display_delay','display_duration','cycle_interval','loop','show_on_pages','anonymise_names'];
        $options = [];
        foreach ($keys as $key) {
            $options[$key] = $plugin->get_option($key, '');
        }
        return view('fomo-notifications::admin.settings', compact('options'));
    }

    public function saveSettings(Request $request)
    {
        $plugin  = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');
        $toggles = ['enabled', 'loop', 'anonymise_names'];
        $texts   = ['data_source','real_order_count','min_real_orders','display_delay','display_duration','cycle_interval'];

        foreach ($toggles as $key) {
            $plugin->update_option($key, $request->has($key) ? '1' : '0');
        }
        foreach ($texts as $key) {
            $plugin->update_option($key, $request->input($key, ''));
        }

        $pages = $request->input('show_on_pages', []);
        $plugin->update_option('show_on_pages', json_encode($pages));

        return back()->with('success', __('FOMO settings saved.'));
    }

    public function entries()
    {
        $tenantId = $this->tenantId();
        $entries  = FomoSyntheticEntry::where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->get();

        return view('fomo-notifications::admin.entries', compact('entries'));
    }

    public function storeEntry(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|integer',
            'display_name' => 'required|string|max:80',
            'location'     => 'required|string|max:80',
        ]);

        FomoSyntheticEntry::create([
            'tenant_id'     => $this->tenantId(),
            'product_id'    => $request->product_id,
            'variant_label' => $request->variant_label,
            'display_name'  => $request->display_name,
            'location'      => $request->location,
            'active'        => $request->has('active') ? 1 : 0,
            'sort_order'    => FomoSyntheticEntry::where('tenant_id', $this->tenantId())->max('sort_order') + 1,
        ]);

        return back()->with('success', __('Entry added.'));
    }

    public function updateEntry(Request $request, int $id)
    {
        $entry = FomoSyntheticEntry::where('tenant_id', $this->tenantId())->findOrFail($id);
        $entry->update($request->only(['product_id','variant_label','display_name','location','active','sort_order']));
        return back()->with('success', __('Entry updated.'));
    }

    public function destroyEntry(int $id)
    {
        FomoSyntheticEntry::where('tenant_id', $this->tenantId())->findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function toggleEntry(int $id)
    {
        $entry = FomoSyntheticEntry::where('tenant_id', $this->tenantId())->findOrFail($id);
        $entry->update(['active' => !$entry->active]);
        return response()->json(['active' => $entry->active]);
    }

    public function preview()
    {
        $plugin = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');

        $tenantId = $this->tenantId();

        // Fetch synthetic entries for inline preview
        $syntheticEntries = FomoSyntheticEntry::where('tenant_id', $tenantId)
            ->where('active', 1)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($e) => [
                'name'         => $e->display_name,
                'location'     => $e->location,
                'product_name' => 'Product #' . $e->product_id,
                'variant'      => $e->variant_label,
                'minutes_ago'  => rand(2, 59),
                'product_image'=> null,
            ])
            ->values()
            ->toArray();

        // Fall back to demo entries if none configured
        if (empty($syntheticEntries)) {
            $syntheticEntries = [
                ['name'=>'Sarah F.',  'location'=>'New York, US',   'product_name'=>'Classic Sneakers',  'variant'=>'Red · Size 42', 'minutes_ago'=>3,  'product_image'=>null],
                ['name'=>'James K.',  'location'=>'London, UK',     'product_name'=>'Leather Backpack',  'variant'=>null,            'minutes_ago'=>11, 'product_image'=>null],
                ['name'=>'Amara O.',  'location'=>'Lagos, NG',      'product_name'=>'Wireless Earbuds',  'variant'=>'Black',         'minutes_ago'=>27, 'product_image'=>null],
                ['name'=>'Liu W.',    'location'=>'Shanghai, CN',   'product_name'=>'Smart Watch',       'variant'=>'Silver',        'minutes_ago'=>45, 'product_image'=>null],
            ];
        }

        $config = $this->buildConfig($plugin);

        return view('fomo-notifications::admin.preview', compact('config', 'syntheticEntries'));
    }

    private function buildConfig($plugin): array
    {
        return [
            'delay'      => (int) $plugin->get_option('display_delay', 5),
            'duration'   => (int) $plugin->get_option('display_duration', 6),
            'interval'   => (int) $plugin->get_option('cycle_interval', 15),
            'loop'       => (bool) $plugin->get_option('loop', '1'),
            'apiUrl'     => route('api.fomo-notifications.entries'),
        ];
    }
}
