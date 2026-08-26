<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Http\Controllers\Controller;
use App\PluginSystem\LicenseManager;
use App\PluginSystem\PluginManager;
use App\PluginSystem\UpdateManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PluginController extends Controller
{
    /** Plugin IDs that are always active and cannot be toggled by the landlord. */
    private const CORE_PLUGIN_IDS = [
        'widget-builder',
        'page-builder',
        'menu-builder',
        'integrations',
        'commission-manage',
        'plugin-manage',
    ];

    public function __construct(
        private readonly PluginManager $manager,
        private readonly UpdateManager $updater,
        private readonly LicenseManager $licenser,
    ) {}

    public function index()
    {
        $discovered = $this->manager->allDiscovered();

        $plugins = [];
        foreach ($discovered as $manifest) {
            // Core plugins → Core Features page; Tenant plugins → Tenant Plugins page
            if (in_array($manifest->id, self::CORE_PLUGIN_IDS, true)) {
                continue;
            }
            if ($manifest->type === 'tenant') {
                continue;
            }

            $licenseStatus = $manifest->pricing === 'paid'
                ? $this->licenser->getStatus($manifest->id)
                : null;

            $plugins[] = [
                'manifest'       => $manifest,
                'active'         => PluginManager::isActive($manifest->id),
                'license_status' => $licenseStatus,
            ];
        }

        usort($plugins, function ($a, $b) {
            if ($a['active'] !== $b['active']) {
                return $b['active'] <=> $a['active'];
            }
            return strcmp($a['manifest']->name, $b['manifest']->name);
        });

        return view('landlord.admin.plugins.index', compact('plugins'));
    }

    public function tenantPlugins()
    {
        $discovered = $this->manager->allDiscovered();

        $plugins = [];
        foreach ($discovered as $manifest) {
            if ($manifest->type !== 'tenant') {
                continue;
            }
            $plugins[] = [
                'manifest' => $manifest,
                'active'   => PluginManager::isActive($manifest->id),
            ];
        }

        usort($plugins, fn($a, $b) => strcmp($a['manifest']->name, $b['manifest']->name));

        return view('landlord.admin.plugins.tenant-plugins', compact('plugins'));
    }

    public function coreFeatures()
    {
        $discovered = $this->manager->allDiscovered();

        $plugins = [];
        foreach ($discovered as $manifest) {
            if (!in_array($manifest->id, self::CORE_PLUGIN_IDS, true)) {
                continue;
            }
            $plugins[] = [
                'manifest' => $manifest,
                'active'   => true, // core plugins are always active
            ];
        }

        usort($plugins, fn($a, $b) => strcmp($a['manifest']->name, $b['manifest']->name));

        return view('landlord.admin.plugins.core-features', compact('plugins'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['plugin_id' => 'required|string']);

        $plugin_id = $request->input('plugin_id');

        $manifest = $this->manager->allDiscovered()[$plugin_id] ?? null;
        if ($manifest && $manifest->type === 'tenant') {
            return response()->json([
                'status'  => 'error',
                'message' => __('Tenant-only plugins are managed by each tenant individually and cannot be toggled from the landlord panel.'),
            ], 403);
        }

        if (in_array($plugin_id, self::CORE_PLUGIN_IDS, true)) {
            return response()->json([
                'status'  => 'error',
                'message' => __('This is a core system plugin and cannot be disabled.'),
            ], 403);
        }

        try {
            if (PluginManager::isActive($plugin_id)) {
                $this->manager->deactivate($plugin_id);
                $status  = 'inactive';
                $message = __('Plugin deactivated successfully.');
            } else {
                $this->manager->activate($plugin_id);
                $status  = 'active';
                $message = __('Plugin activated successfully.');
            }

            return response()->json(['status' => $status, 'message' => $message]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function install(Request $request)
    {
        $request->validate([
            'plugin_zip' => 'required|file|mimes:zip|max:51200',
        ]);

        $file   = $request->file('plugin_zip');
        $tmpPath = $file->getRealPath();

        try {
            $pluginId = $this->updater->installFromZip($tmpPath);
        } catch (\App\PluginSystem\Exceptions\PluginSecurityException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        if (!$pluginId) {
            return response()->json([
                'status'  => 'error',
                'message' => __('Invalid plugin ZIP. Make sure it contains a valid plugin.json.'),
            ], 422);
        }

        // Re-discover so the new plugin shows up
        Cache::forget('plugin_manager.manifests');

        return response()->json([
            'status'    => 'success',
            'plugin_id' => $pluginId,
            'message'   => __('Plugin installed successfully. Refresh the page to see it.'),
        ]);
    }

    public function checkUpdate(Request $request)
    {
        $request->validate(['plugin_id' => 'required|string']);

        $manifest = $this->manager->getManifest($request->input('plugin_id'));

        if (!$manifest) {
            return response()->json(['status' => 'error', 'message' => __('Plugin not found.')], 404);
        }

        $update = $this->updater->checkForUpdate($manifest);

        if (!$update) {
            return response()->json(['status' => 'up_to_date', 'message' => __('Plugin is up to date.')]);
        }

        return response()->json([
            'status'       => 'update_available',
            'version'      => $update['version'],
            'changelog'    => $update['changelog'],
            'download_url' => $update['download_url'],
            'sha256'       => $update['sha256'] ?? null,
            'message'      => __('Update available: v') . $update['version'],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'plugin_id'    => 'required|string',
            'download_url' => 'required|url',
            'sha256'       => 'nullable|string|size:64',
        ]);

        $manifest = $this->manager->getManifest($request->input('plugin_id'));

        if (!$manifest) {
            return response()->json(['status' => 'error', 'message' => __('Plugin not found.')], 404);
        }

        try {
            $ok = $this->updater->installUpdate(
                $manifest,
                $request->input('download_url'),
                $request->input('sha256')
            );
        } catch (\App\PluginSystem\Exceptions\PluginSecurityException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        if (!$ok) {
            return response()->json(['status' => 'error', 'message' => __('Update failed. Please try again.')], 500);
        }

        return response()->json(['status' => 'success', 'message' => __('Plugin updated successfully.')]);
    }

    public function setTenantOverride(Request $request)
    {
        $request->validate([
            'plugin_id'   => 'required|string',
            'tenant_id'   => 'required|integer',
            'status'      => 'required|in:active,inactive',
            'is_preview'  => 'boolean',
        ]);

        $manifest = $this->manager->allDiscovered()[$request->input('plugin_id')] ?? null;
        if ($manifest && $manifest->type === 'tenant') {
            return response()->json([
                'status'  => 'error',
                'message' => __('Tenant-only plugins cannot be overridden from the landlord panel.'),
            ], 403);
        }

        $this->manager->setTenantOverride(
            $request->input('plugin_id'),
            (int) $request->input('tenant_id'),
            $request->input('status'),
            (bool) $request->input('is_preview', false)
        );

        $label = $request->input('is_preview') ? 'Preview enabled' : ($request->input('status') === 'active' ? 'Plugin enabled for tenant' : 'Plugin disabled for tenant');

        return response()->json(['status' => 'success', 'message' => __($label)]);
    }

    public function removeTenantOverride(Request $request)
    {
        $request->validate([
            'plugin_id'  => 'required|string',
            'tenant_id'  => 'required|integer',
        ]);

        $this->manager->removeTenantOverride(
            $request->input('plugin_id'),
            (int) $request->input('tenant_id')
        );

        return response()->json(['status' => 'success', 'message' => __('Override removed.')]);
    }

    public function setSelfManage(Request $request)
    {
        $request->validate([
            'plugin_id'   => 'required|string',
            'tenant_id'   => 'required|integer',
            'self_manage' => 'required|boolean',
        ]);

        $this->manager->setTenantSelfManage(
            $request->input('plugin_id'),
            (int) $request->input('tenant_id'),
            (bool) $request->input('self_manage')
        );

        return response()->json(['status' => 'success', 'message' => __('Self-manage setting updated.')]);
    }

    public function activateLicense(Request $request)
    {
        $request->validate([
            'plugin_id'   => 'required|string',
            'license_key' => 'required|string|min:8',
        ]);

        $plugin_id = $request->input('plugin_id');
        $key       = $request->input('license_key');

        $status = $this->licenser->activate($plugin_id, $key);

        if ($status === 'active') {
            return response()->json(['status' => 'success', 'message' => __('License activated successfully.')]);
        }

        return response()->json(['status' => 'error', 'message' => __('License key is invalid or could not be verified.')], 422);
    }

    public function installLog()
    {
        $logs = \Illuminate\Support\Facades\DB::table('plugin_install_log')
            ->orderByDesc('id')
            ->paginate(50);

        return view('landlord.admin.plugins.install-log', compact('logs'));
    }

    public function hookLog(Request $request)
    {
        $plugin = $request->query('plugin');
        $hook   = $request->query('hook');

        $query = \Illuminate\Support\Facades\DB::table('plugin_hook_log')
            ->orderByDesc('id');

        if ($plugin) {
            $query->where('plugin_id', $plugin);
        }
        if ($hook) {
            $query->where('hook', 'like', '%' . $hook . '%');
        }

        $logs      = $query->paginate(50)->withQueryString();
        $pluginIds = \Illuminate\Support\Facades\DB::table('plugin_hook_log')
            ->distinct()->pluck('plugin_id')->sort()->values();

        return view('landlord.admin.plugins.hook-log', compact('logs', 'pluginIds', 'plugin', 'hook'));
    }
}
