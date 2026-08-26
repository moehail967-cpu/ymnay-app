<?php

namespace App\PluginSystem;

use App\PluginSystem\Exceptions\InvalidPluginManifestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PluginManager
{
    /** @var array<string, PluginBase> */
    private array $loaded = [];

    /** Tracks plugins that completed full boot() — the source of truth for isActive() */
    private array $activePlugins = [];

    /** @var array<string, PluginManifest> */
    private array $discovered = [];

    /** @var array<array{plugin_id: string, callback: callable}> */
    private array $apiRouteGroups = [];

    /** @var array<array{plugin_id: string, callback: callable}> */
    private array $webRouteGroups = [];

    private array $registeredWebRouteKeys = [];
    private array $registeredApiRouteKeys = [];

    /** Tracks which context each plugin has been booted in to prevent double-registration */
    private array $bootedInContext = [];

    /** Tracks which plugins have had routes() called (active or not) */
    private array $routedPlugins = [];

    private HookEngine $hooks;
    private bool $booted = false;

    public function __construct(HookEngine $hooks)
    {
        $this->hooks = $hooks;
    }

    // ── Discovery ─────────────────────────────────────────────────────────────

    public function discover(): void
    {
        $ttl = app()->isProduction() ? 60 : 0;

        $this->discovered = Cache::remember('plugin_manager.manifests', $ttl, function () {
            return $this->scanModules();
        });
    }

    private function scanModules(): array
    {
        $manifests = [];

        // Scan Modules/ (external modules / Laravel Modules)
        foreach (glob(base_path('Modules/*/plugin.json')) as $jsonPath) {
            try {
                $manifest = PluginManifest::fromFile($jsonPath);
                $manifests[$manifest->id] = $manifest;
            } catch (InvalidPluginManifestException $e) {
                Log::channel('plugin')->warning($e->getMessage());
            }
        }

        // Scan plugins/ (core plugins: PageBuilder, WidgetBuilder, MenuBuilder)
        foreach (glob(base_path('plugins/*/plugin.json')) as $jsonPath) {
            try {
                $manifest = PluginManifest::fromFile($jsonPath);
                $manifests[$manifest->id] = $manifest;
            } catch (InvalidPluginManifestException $e) {
                Log::channel('plugin')->warning($e->getMessage());
            }
        }

        return $manifests;
    }

    // ── Boot ──────────────────────────────────────────────────────────────────

    /**
     * Called after tenancy is initialized. Boots plugins whose type is tenant or both
     * that were skipped during the initial landlord-context bootAll().
     */
    public function bootForTenant(): void
    {
        $statuses       = $this->loadStatuses();
        $tenantId       = $this->currentTenantId();
        $overrides      = $tenantId ? $this->loadTenantOverrides($tenantId) : [];
        $planPluginIds  = $tenantId ? $this->loadPlanPluginIds($tenantId) : null;

        foreach ($this->discovered as $id => $manifest) {
            if (!$manifest->isForContext('tenant')) {
                continue;
            }

            // Only skip if already fully booted in tenant context — do NOT skip just
            // because $this->loaded[$id] is set; registerPluginRoutes() sets that with
            // a routes-only instance and we still need boot() to register hooks/menus.
            if (isset($this->bootedInContext[$id]['tenant'])) {
                continue;
            }

            // Always register routes for tenant plugins (active or not) so settings
            // pages are reachable and the Settings link appears on the plugin card.
            if (!isset($this->routedPlugins[$id])) {
                $this->registerPluginRoutes($manifest);
                $this->routedPlugins[$id] = true;
            }

            // If the tenant's plan has explicit plugin assignments, only boot plugins
            // that are included in the plan — prevents unassigned plugins from registering
            // sidebar menus and hooks even when they are globally active.
            if ($planPluginIds !== null && !in_array($id, $planPluginIds, true)) {
                continue;
            }

            if ($tenantId && isset($overrides[$id])) {
                if ($overrides[$id]['status'] !== 'active') {
                    continue;
                }
            } else {
                if (!($statuses[$this->moduleNameFromId($id)] ?? $statuses[$id] ?? false)) {
                    if (!isset($overrides[$id]) || $overrides[$id]['status'] !== 'active') {
                        continue;
                    }
                }
            }

            $this->bootPlugin($manifest);
            $this->bootedInContext[$id]['tenant'] = true;
        }
    }

    /**
     * Load the plugin IDs assigned to the tenant's price plan from the central DB.
     * Returns null when no plan or no explicit assignments (backward compat: boot all).
     */
    private function loadPlanPluginIds(int|string $tenant_id): ?array
    {
        try {
            $pricePlanId = $this->central()
                ->table('tenants')
                ->where('id', $tenant_id)
                ->value('price_plan_id');

            if (!$pricePlanId) {
                return null;
            }

            return Cache::remember("plan_plugin_ids.plan.{$pricePlanId}", 300, function () use ($pricePlanId) {
                $ids = $this->central()
                    ->table('plan_plugins')
                    ->where('plan_id', $pricePlanId)
                    ->pluck('plugin_id')
                    ->toArray();

                // No assignments yet → null means "show all" for backward compat
                return !empty($ids) ? $ids : null;
            });
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Clear the plan plugin cache for a given plan ID.
     * Call this from PricePlanController after updating plan_plugins.
     */
    public function clearPlanPluginCache(int $plan_id): void
    {
        Cache::forget("plan_plugin_ids.plan.{$plan_id}");
    }

    public function bootAll(): void
    {
        if ($this->booted) {
            return;
        }

        $this->booted   = true;
        $statuses       = $this->loadStatuses();
        $context        = $this->resolveContext();
        $tenantId       = $this->currentTenantId();
        $overrides      = $tenantId ? $this->loadTenantOverrides($tenantId) : [];

        foreach ($this->discovered as $id => $manifest) {
            if (!$manifest->isForContext($context)) {
                continue;
            }

            // Tenant context: override takes precedence over global status
            if ($tenantId && isset($overrides[$id])) {
                if ($overrides[$id]['status'] !== 'active') {
                    continue;
                }
            } else {
                // No override: use global modules_statuses.json
                if (!($statuses[$this->moduleNameFromId($id)] ?? $statuses[$id] ?? false)) {
                    // Allow preview mode: if override exists with status=active and is_preview=true, boot it
                    if (!isset($overrides[$id]) || $overrides[$id]['status'] !== 'active') {
                        continue;
                    }
                }
            }

            $this->bootPlugin($manifest);
        }

        // Legacy shim removed — all modules now have plugin.json (S7-06)
    }

    private function bootPlugin(PluginManifest $manifest): void
    {
        // Check dependencies first
        foreach ($manifest->requires as $depId) {
            if (!isset($this->loaded[$depId])) {
                Log::channel('plugin')->warning(
                    "Plugin [{$manifest->id}] requires [{$depId}] which is not loaded. Skipping."
                );
                return;
            }
        }

        try {
            require_once $manifest->mainClassPath();

            $fqcn = $this->resolveClassName($manifest->mainClassPath());
            if (!$fqcn) {
                Log::channel('plugin')->error("Could not resolve class from [{$manifest->mainClassPath()}]");
                return;
            }

            /** @var PluginBase $plugin */
            $plugin = new $fqcn();

            if (!($plugin instanceof PluginBase)) {
                Log::channel('plugin')->error("Plugin [{$manifest->id}] main class must extend PluginBase");
                return;
            }

            $plugin->_setManifest($manifest);
            $plugin->_setHookEngine($this->hooks);

            // Register view namespace (idempotent)
            $viewPath = $manifest->rootPath . '/resources/views';
            if (is_dir($viewPath)) {
                app('view')->addNamespace($manifest->id, $viewPath);
            }

            // Ensure routes() is called even when bootPlugin() is invoked directly
            // (e.g. bootAll() running in tenant context). routedPlugins guards double-calls.
            if (!isset($this->routedPlugins[$manifest->id])) {
                $plugin->routes();
                $this->routedPlugins[$manifest->id] = true;
            }

            // Auto-run tenant migrations for any plugin that ships them.
            // Cached per plugin+tenant so it's a no-op after the first successful run.
            $this->ensurePluginSchema($manifest);

            $plugin->boot();

            $this->loaded[$manifest->id]       = $plugin;
            $this->activePlugins[$manifest->id] = true;
        } catch (\Throwable $e) {
            Log::channel('plugin')->error(
                "Plugin [{$manifest->id}] failed to boot: {$e->getMessage()}",
                ['exception' => $e]
            );
        }
    }

    /**
     * Pre-register routes() for ALL discovered plugins so their API routes are
     * in $apiRouteGroups before registerApiRoutes() is called. This is necessary
     * because tenant plugins are only booted after tenancy initializes (post-match),
     * too late for the router to see their routes. This safe pass calls routes()
     * only — no boot() hooks — so inactive plugins don't gain active behaviour.
     */
    public function preRegisterAllRoutes(): void
    {
        foreach ($this->discovered as $id => $manifest) {
            if (!isset($this->routedPlugins[$id])) {
                $this->registerPluginRoutes($manifest);
                $this->routedPlugins[$id] = true;
            }
        }
    }

    /**
     * Instantiate a plugin and call routes() only — no hooks, settings, or menus.
     * Safe to call for inactive plugins so their admin routes are always reachable.
     */
    private function registerPluginRoutes(PluginManifest $manifest): void
    {
        try {
            require_once $manifest->mainClassPath();
            $fqcn = $this->resolveClassName($manifest->mainClassPath());
            if (!$fqcn) return;

            /** @var PluginBase $plugin */
            $plugin = new $fqcn();
            if (!($plugin instanceof PluginBase)) return;

            $plugin->_setManifest($manifest);
            $plugin->_setHookEngine($this->hooks);
            $plugin->routes();

            // Register view namespace so controllers can use plugin-id::view syntax
            $viewPath = $manifest->rootPath . '/resources/views';
            if (is_dir($viewPath)) {
                app('view')->addNamespace($manifest->id, $viewPath);
            }

            // Make the instance available via get() so settings controllers work
            // even when the plugin is inactive (no boot() called).
            // bootPlugin() will overwrite this with a fully-booted instance if active.
            if (!isset($this->loaded[$manifest->id])) {
                $this->loaded[$manifest->id] = $plugin;
            }
        } catch (\Throwable $e) {
            Log::channel('plugin')->warning(
                "Plugin [{$manifest->id}] routes() failed: {$e->getMessage()}"
            );
        }
    }

    // ── Activation / Deactivation ─────────────────────────────────────────────

    public function activate(string $plugin_id): void
    {
        $manifest = $this->discovered[$plugin_id] ?? null;

        if (!$manifest) {
            throw new \RuntimeException("Plugin [{$plugin_id}] not found");
        }

        $this->setStatus($plugin_id, true);
        $this->bootPlugin($manifest);

        $plugin = $this->loaded[$plugin_id] ?? null;
        if ($plugin) {
            try {
                $plugin->on_activate();
            } catch (\Throwable $e) {
                Log::channel('plugin')->error("Plugin [{$plugin_id}] on_activate() error: {$e->getMessage()}");
            }
        }

        Cache::forget('plugin_manager.manifests');
    }

    public function deactivate(string $plugin_id): void
    {
        $plugin = $this->loaded[$plugin_id] ?? null;

        if ($plugin) {
            try {
                $plugin->on_deactivate();
            } catch (\Throwable $e) {
                Log::channel('plugin')->error("Plugin [{$plugin_id}] on_deactivate() error: {$e->getMessage()}");
            }
        }

        $this->setStatus($plugin_id, false);
        unset($this->loaded[$plugin_id], $this->activePlugins[$plugin_id]);
        Cache::forget('plugin_manager.manifests');
    }

    // ── Static access ─────────────────────────────────────────────────────────

    public static function get(string $plugin_id): ?PluginBase
    {
        return app(self::class)->loaded[$plugin_id] ?? null;
    }

    public static function all(): array
    {
        return app(self::class)->loaded;
    }

    public static function isActive(string $plugin_id): bool
    {
        return isset(app(self::class)->activePlugins[$plugin_id]);
    }

    // ── Tenant override API ───────────────────────────────────────────────────

    /**
     * Read active status for a tenant plugin directly from DB overrides + global statuses.
     * Use this instead of isActive() when you need reliable state regardless of boot order.
     */
    public function isTenantActive(string $plugin_id, int|string $tenant_id): bool
    {
        $overrides = $this->loadTenantOverrides($tenant_id);

        if (isset($overrides[$plugin_id])) {
            return $overrides[$plugin_id]['status'] === 'active';
        }

        // No tenant override: fall back to global modules_statuses.json
        $statuses = $this->loadStatuses();
        $moduleKey = $this->moduleNameFromId($plugin_id);
        return $statuses[$moduleKey] ?? $statuses[$plugin_id] ?? false;
    }

    /** Set or remove a tenant-level status override for a plugin. */
    public function setTenantOverride(string $plugin_id, int|string $tenant_id, string $status, bool $preview = false): void
    {
        $this->central()->table('plugin_tenant_overrides')->updateOrInsert(
            ['plugin_id' => $plugin_id, 'tenant_id' => $tenant_id],
            ['status' => $status, 'is_preview' => $preview, 'updated_at' => now(), 'created_at' => now()]
        );
        Cache::forget("plugin_tenant_overrides.{$tenant_id}");
    }

    public function removeTenantOverride(string $plugin_id, int|string $tenant_id): void
    {
        $this->central()->table('plugin_tenant_overrides')
            ->where('plugin_id', $plugin_id)
            ->where('tenant_id', $tenant_id)
            ->delete();
        Cache::forget("plugin_tenant_overrides.{$tenant_id}");
    }

    /** Set whether tenants can self-manage a plugin's activation. */
    public function setTenantSelfManage(string $plugin_id, int|string $tenant_id, bool $allowed): void
    {
        $this->central()->table('plugin_tenant_overrides')->updateOrInsert(
            ['plugin_id' => $plugin_id, 'tenant_id' => $tenant_id],
            ['self_manage' => $allowed, 'updated_at' => now(), 'created_at' => now()]
        );
        Cache::forget("plugin_tenant_overrides.{$tenant_id}");
    }

    public function getTenantOverride(string $plugin_id, int|string $tenant_id): ?object
    {
        return $this->central()->table('plugin_tenant_overrides')
            ->where('plugin_id', $plugin_id)
            ->where('tenant_id', $tenant_id)
            ->first();
    }

    public function getPreviewPlugins(): array
    {
        return $this->central()->table('plugin_tenant_overrides')
            ->where('is_preview', true)
            ->get()
            ->groupBy('plugin_id')
            ->toArray();
    }

    // ── API routes ────────────────────────────────────────────────────────────

    public function addApiRouteGroup(string $plugin_id, callable $callback): void
    {
        $this->apiRouteGroups[] = ['plugin_id' => $plugin_id, 'callback' => $callback];
    }

    public function registerApiRoutes(): void
    {
        foreach ($this->apiRouteGroups as $key => $group) {
            if (isset($this->registeredApiRouteKeys[$key])) {
                continue;
            }
            \Illuminate\Support\Facades\Route::middleware('api')
                ->prefix('api/v1/plugins/' . $group['plugin_id'])
                ->group($group['callback']);
            $this->registeredApiRouteKeys[$key] = true;
        }
    }

    public function addWebRouteGroup(string $plugin_id, callable $callback): void
    {
        $this->webRouteGroups[] = ['plugin_id' => $plugin_id, 'callback' => $callback];
    }

    public function registerWebRoutes(): void
    {
        foreach ($this->webRouteGroups as $key => $group) {
            if (isset($this->registeredWebRouteKeys[$key])) {
                continue;
            }
            ($group['callback'])();
            $this->registeredWebRouteKeys[$key] = true;
        }
    }

    // ── Discovery accessors ───────────────────────────────────────────────────

    public function allDiscovered(): array
    {
        return $this->discovered;
    }

    public function getManifest(string $plugin_id): ?PluginManifest
    {
        return $this->discovered[$plugin_id] ?? null;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Run any pending tenant-DB migrations for the given plugin.
     * Only fires in tenant context. Cached per plugin+tenant for 24 h so that
     * it is effectively a no-op on every subsequent request in that session.
     */
    private function ensurePluginSchema(PluginManifest $manifest): void
    {
        // Only relevant in tenant context
        if (!function_exists('tenant') || !tenant()) {
            return;
        }

        $migrationPath = $manifest->rootPath . '/database/migrations';
        if (!is_dir($migrationPath)) {
            return;
        }

        $tenantId = $this->currentTenantId();
        $cacheKey = "plugin_schema_checked.{$manifest->id}.{$tenantId}";

        if (Cache::has($cacheKey)) {
            return;
        }

        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--path'  => str_replace(base_path() . '/', '', $migrationPath),
                '--force' => true,
            ]);
        } catch (\Throwable $e) {
            Log::channel('plugin')->error(
                "Plugin [{$manifest->id}] tenant schema migration failed: {$e->getMessage()}"
            );
        }

        // Cache regardless of outcome — prevents hammering on every request even if a
        // migration fails (operator intervention needed in that case).
        Cache::put($cacheKey, true, now()->addDay());
    }

    private function central(): \Illuminate\Database\Connection
    {
        return \Illuminate\Support\Facades\DB::connection(
            config('tenancy.database.central_connection', config('database.default'))
        );
    }

    private function loadStatuses(): array
    {
        $path = base_path('modules_statuses.json');
        if (!file_exists($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function loadTenantOverrides(int|string $tenant_id): array
    {
        return Cache::remember("plugin_tenant_overrides.{$tenant_id}", 300, function () use ($tenant_id) {
            $rows = $this->central()->table('plugin_tenant_overrides')
                ->where('tenant_id', $tenant_id)
                ->get();

            $result = [];
            foreach ($rows as $row) {
                $result[$row->plugin_id] = [
                    'status'      => $row->status,
                    'self_manage' => (bool) $row->self_manage,
                    'is_preview'  => (bool) $row->is_preview,
                ];
            }
            return $result;
        });
    }

    private function currentTenantId(): int|string|null
    {
        try {
            return (function_exists('tenant') && tenant()) ? tenant()->id : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function setStatus(string $plugin_id, bool $active): void
    {
        $path     = base_path('modules_statuses.json');
        $statuses = $this->loadStatuses();

        // Support both plugin-id and ModuleName keys
        $moduleKey = $this->moduleNameFromId($plugin_id);
        if (array_key_exists($moduleKey, $statuses)) {
            $statuses[$moduleKey] = $active;
        } else {
            $statuses[$plugin_id] = $active;
        }

        file_put_contents($path, json_encode($statuses, JSON_PRETTY_PRINT));
    }

    private function resolveContext(): string
    {
        try {
            return (function_exists('tenant') && tenant()) ? 'tenant' : 'landlord';
        } catch (\Throwable) {
            return 'landlord';
        }
    }

    private function moduleNameFromId(string $plugin_id): string
    {
        // acme-seo-plugin → AcmeSeoPlugin
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $plugin_id)));
    }

    private function resolveClassName(string $filePath): ?string
    {
        $content = file_get_contents($filePath);

        $namespace = null;
        if (preg_match('/^namespace\s+(.+);/m', $content, $m)) {
            $namespace = $m[1];
        }

        if (!preg_match('/^(?:abstract\s+)?class\s+(\w+)/m', $content, $m)) {
            return null;
        }

        $className = $m[1];
        return $namespace ? $namespace . '\\' . $className : $className;
    }
}
