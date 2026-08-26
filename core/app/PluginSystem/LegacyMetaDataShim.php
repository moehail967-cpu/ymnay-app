<?php

namespace App\PluginSystem;

use Illuminate\Support\Facades\Log;

/**
 * Reads old nazmartMetaData from module.json files and registers the
 * equivalent hooks/menus in the new system.
 *
 * Removed once all modules have been migrated to plugin.json (Sprint 7).
 */
class LegacyMetaDataShim
{
    private HookEngine $hooks;

    private const HOOK_MAP = [
        'head_start' => 'nazmart:frontend_head',
        'head_end'   => 'nazmart:frontend_head_end',
        'body_start' => 'nazmart:frontend_body_start',
        'body_end'   => 'nazmart:frontend_footer',
    ];

    public function __construct(HookEngine $hooks)
    {
        $this->hooks = $hooks;
    }

    public function run(array $statuses, string $context): void
    {
        $modulesDir = base_path('Modules');

        if (!is_dir($modulesDir)) {
            return;
        }

        foreach (glob($modulesDir . '/*/module.json') as $jsonPath) {
            $moduleDir = dirname($jsonPath);

            // Skip if this module already has a plugin.json (new system handles it)
            if (file_exists($moduleDir . '/plugin.json')) {
                continue;
            }

            $raw = json_decode(file_get_contents($jsonPath), true);
            if (!$raw) {
                continue;
            }

            $moduleName = $raw['name'] ?? basename($moduleDir);

            // Skip disabled modules
            if (!($statuses[$moduleName] ?? false)) {
                continue;
            }

            $meta = $raw['nazmartMetaData'] ?? null;
            if (!$meta) {
                continue;
            }

            $pluginType = $meta['plugin_type'] ?? 'both';
            if (!$this->contextMatches($pluginType, $context)) {
                continue;
            }

            try {
                $this->processHooks($meta, $moduleDir, $moduleName);
                $this->processMenus($meta, $context);
            } catch (\Throwable $e) {
                Log::channel('plugin')->warning(
                    "Legacy shim error for module [{$moduleName}]: {$e->getMessage()}"
                );
            }
        }
    }

    private function processHooks(array $meta, string $moduleDir, string $moduleName): void
    {
        $hooks = $meta['hooks'] ?? [];

        foreach (self::HOOK_MAP as $legacyKey => $newHook) {
            if (empty($hooks[$legacyKey])) {
                continue;
            }

            $views = (array) $hooks[$legacyKey];

            foreach ($views as $viewName) {
                $this->hooks->addAction($newHook, function () use ($viewName) {
                    try {
                        if (view()->exists($viewName)) {
                            echo view($viewName)->render();
                        }
                    } catch (\Throwable $e) {
                        Log::channel('plugin')->warning("Legacy hook view error [{$viewName}]: {$e->getMessage()}");
                    }
                }, 10, 'legacy:shim');
            }
        }
    }

    private function processMenus(array $meta, string $context): void
    {
        $adminSettings = $meta['admin_settings'] ?? [];
        $menuItems     = $adminSettings['menu_item'] ?? [];

        if (empty($menuItems)) {
            return;
        }

        $menuRegistry = app(MenuRegistry::class);

        $showLandlord = $adminSettings['show_admin_landlord'] ?? false;
        $showTenant   = $adminSettings['show_admin_tenant'] ?? false;

        $menuContext = match (true) {
            $showLandlord && $showTenant => 'both',
            $showLandlord               => 'landlord',
            $showTenant                 => 'tenant',
            default                     => 'both',
        };

        foreach ($menuItems as $item) {
            $route = match ($context) {
                'tenant'   => $item['tenantRoute'] ?? $item['route'] ?? null,
                default    => $item['route'] ?? null,
            };

            if (!$route) {
                continue;
            }

            $parent = $item['parent'] ?? null;

            $config = [
                'id'         => $item['id'] ?? $route,
                'label'      => $item['label'] ?? $route,
                'route'      => $route,
                'context'    => $menuContext,
                'permission' => $item['permissions'] ?? null,
                'order'      => 50,
            ];

            if ($parent) {
                $menuRegistry->addSubmenu($parent, $config);
            } else {
                $menuRegistry->addMenu($config);
            }
        }
    }

    private function contextMatches(string $pluginType, string $context): bool
    {
        if ($pluginType === 'external') {
            return true; // old "external" type = show everywhere
        }
        return $pluginType === 'both' || $pluginType === $context;
    }
}
