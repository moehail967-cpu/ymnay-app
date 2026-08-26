<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\PluginSystem\PluginManager;
use App\PluginSystem\SettingsManager;
use Illuminate\Http\Request;

class PluginSettingsController extends Controller
{
    public function __construct(
        private readonly PluginManager $manager,
        private readonly SettingsManager $settings,
    ) {}

    private function currentTenantId(): ?string
    {
        try {
            return function_exists('tenant') && tenant() ? (string) tenant()->id : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function show(string $plugin_id)
    {
        $manifest = $this->manager->getManifest($plugin_id);

        if (!$manifest || !in_array($manifest->type, ['tenant', 'both'])) {
            abort(404, 'Plugin not found.');
        }

        $fields    = $this->settings->getDefinitions($plugin_id);
        $tenantId  = $this->currentTenantId();
        $current   = $this->settings->getAll($plugin_id, $tenantId);

        return view('tenant.admin.plugins.settings', compact('manifest', 'fields', 'current'));
    }

    public function save(string $plugin_id, Request $request)
    {
        $manifest = $this->manager->getManifest($plugin_id);

        if (!$manifest || !in_array($manifest->type, ['tenant', 'both'])) {
            abort(404, 'Plugin not found.');
        }

        $fields   = $this->settings->getDefinitions($plugin_id);
        $tenantId = $this->currentTenantId();

        foreach ($fields as $field) {
            $key  = $field['key'];
            $type = $field['type'] ?? 'text';

            if ($type === 'toggle') {
                $value = $request->has($key) ? '1' : '0';
            } elseif ($type === 'multiselect') {
                $value = json_encode($request->input($key, []));
            } elseif ($type === 'repeater') {
                $value = json_encode($request->input($key, []));
            } else {
                $value = $request->input($key, $field['default'] ?? '');
            }

            $this->settings->set($plugin_id, $key, $value, $tenantId);
        }

        return back()->with('success', __('Plugin settings saved.'));
    }
}
