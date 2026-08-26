<?php

namespace App\Http\Controllers\Landlord\Admin;

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

    public function show(string $plugin_id)
    {
        $manifest = $this->manager->getManifest($plugin_id);

        if (!$manifest) {
            abort(404, 'Plugin not found.');
        }

        $fields  = $this->settings->getDefinitions($plugin_id);
        $current = $this->settings->getAll($plugin_id, null);

        return view('landlord.admin.plugins.settings', compact('manifest', 'fields', 'current'));
    }

    public function save(string $plugin_id, Request $request)
    {
        $manifest = $this->manager->getManifest($plugin_id);

        if (!$manifest) {
            abort(404, 'Plugin not found.');
        }

        $fields = $this->settings->getDefinitions($plugin_id);

        foreach ($fields as $field) {
            $key = $field['key'];

            if (($field['type'] ?? 'text') === 'toggle') {
                $value = $request->has($key) ? '1' : '0';
            } elseif (($field['type'] ?? 'text') === 'multiselect') {
                $value = json_encode($request->input($key, []));
            } else {
                $value = $request->input($key, $field['default'] ?? '');
            }

            $this->settings->set($plugin_id, $key, $value, null);
        }

        return back()->with('success', __('Plugin settings saved.'));
    }
}
