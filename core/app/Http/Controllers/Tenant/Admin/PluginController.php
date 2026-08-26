<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanPlugin;
use App\PluginSystem\LicenseManager;
use App\PluginSystem\PluginManifest;
use App\PluginSystem\PluginManager;
use App\PluginSystem\SettingsManager;
use App\PluginSystem\UpdateManager;
use Illuminate\Http\Request;

class PluginController extends Controller
{
    public function __construct(
        private readonly PluginManager $manager,
        private readonly UpdateManager $updater,
        private readonly LicenseManager $licenser,
    ) {}

    public function index()
    {
        $discovered = $this->manager->allDiscovered();
        $tenantId   = $this->tenantId();

        // Resolve which plugin IDs this tenant's plan grants access to.
        // If the plan has no assignments yet (new feature, old plans), show all.
        $planPluginIds = $this->resolvePlanPluginIds();

        $plugins = [];
        foreach ($discovered as $manifest) {
            if (!in_array($manifest->type, ['tenant', 'both'])) {
                continue;
            }

            // Filter by plan assignment when the plan has explicit plugin assignments
            if ($planPluginIds !== null && !in_array($manifest->id, $planPluginIds)) {
                continue;
            }

            $licenseStatus = $manifest->pricing === 'paid'
                ? $this->licenser->getStatus($manifest->id)
                : null;

            $plugins[] = [
                'manifest'       => $manifest,
                'active'         => $this->manager->isTenantActive($manifest->id, $tenantId),
                'license_status' => $licenseStatus,
                'settings_url'   => $this->resolveSettingsUrl($manifest),
            ];
        }

        usort($plugins, function ($a, $b) {
            if ($a['active'] !== $b['active']) {
                return $b['active'] <=> $a['active'];
            }
            return strcmp($a['manifest']->name, $b['manifest']->name);
        });

        return view('tenant.admin.plugins.index', compact('plugins'));
    }

    /**
     * Returns the list of plugin IDs assigned to this tenant's price plan.
     * Returns null when the plan has no explicit assignments (show all — backward compat).
     */
    private function resolvePlanPluginIds(): ?array
    {
        try {
            $t = tenant();
            if (!$t || !$t->price_plan_id) {
                return null;
            }

            $ids = PlanPlugin::where('plan_id', $t->price_plan_id)->pluck('plugin_id')->toArray();

            // If landlord hasn't assigned any plugins to this plan yet, show all
            return !empty($ids) ? $ids : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function resolveSettingsUrl(PluginManifest $manifest): ?string
    {
        $route = $manifest->settingsRoute;
        if (!$route) {
            return null;
        }
        try {
            return route($route);
        } catch (\Throwable) {
            return null;
        }
    }

    private function tenantId(): int|string
    {
        try {
            return tenant()->id;
        } catch (\Throwable) {
            abort(403, 'Tenant context required.');
        }
    }

    public function toggle(Request $request)
    {
        $request->validate(['plugin_id' => 'required|string']);

        $plugin_id = $request->input('plugin_id');

        $manifest = $this->manager->getManifest($plugin_id);
        if (!$manifest || !in_array($manifest->type, ['tenant', 'both'])) {
            return response()->json([
                'status'  => 'error',
                'message' => __('Plugin not found or not available for tenants.'),
            ], 403);
        }

        $corePlugins = ['widget-builder', 'page-builder', 'menu-builder', 'integrations', 'commission-manage'];
        if (in_array($plugin_id, $corePlugins)) {
            return response()->json([
                'status'  => 'error',
                'message' => __('This is a core system plugin and cannot be disabled.'),
            ], 403);
        }

        $tenantId = $this->tenantId();
        $override = $this->manager->getTenantOverride($plugin_id, $tenantId);

        // Block only if landlord has explicitly locked this plugin (self_manage = 0/false).
        // null means landlord has not configured it → tenant is free to toggle.
        $landlordLocked = $override && !is_null($override->self_manage) && !$override->self_manage;
        if ($landlordLocked) {
            return response()->json([
                'status'  => 'error',
                'message' => __('This plugin cannot be toggled by tenants.'),
            ], 403);
        }

        try {
            $currentlyActive = $this->manager->isTenantActive($plugin_id, $tenantId);
            $newStatus = $currentlyActive ? 'inactive' : 'active';

            $this->manager->setTenantOverride($plugin_id, $tenantId, $newStatus);

            $plugin = PluginManager::get($plugin_id);

            if ($newStatus === 'active' && $plugin) {
                try {
                    $plugin->on_activate();
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::channel('plugin')
                        ->error("Plugin [{$plugin_id}] on_activate() error: {$e->getMessage()}");
                }
            } elseif ($newStatus === 'inactive' && $plugin) {
                try {
                    $plugin->on_deactivate();
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::channel('plugin')
                        ->error("Plugin [{$plugin_id}] on_deactivate() error: {$e->getMessage()}");
                }
            }

            $status  = $newStatus;
            $message = $newStatus === 'active' ? __('Plugin activated.') : __('Plugin deactivated.');

            return response()->json(['status' => $status, 'message' => $message]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function checkUpdate(Request $request)
    {
        $request->validate(['plugin_id' => 'required|string']);

        $manifest = $this->manager->getManifest($request->input('plugin_id'));

        if (!$manifest || !in_array($manifest->type, ['tenant', 'both'])) {
            return response()->json(['status' => 'error', 'message' => __('Plugin not found.')], 404);
        }

        $update = $this->updater->checkForUpdate($manifest);

        if (!$update) {
            return response()->json(['status' => 'up_to_date', 'message' => __('Plugin is up to date.')]);
        }

        return response()->json([
            'status'      => 'update_available',
            'version'     => $update['version'],
            'changelog'   => $update['changelog'],
            'download_url' => $update['download_url'],
            'message'     => __('Update available: v') . $update['version'],
        ]);
    }
}
