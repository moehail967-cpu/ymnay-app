<?php

namespace App\PluginSystem;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class PluginBase
{
    private PluginManifest $manifest;
    private HookEngine $hooks;

    // ── Required ─────────────────────────────────────────────────────────────

    abstract public function id(): string;
    abstract public function boot(): void;

    // ── Lifecycle (optional overrides) ────────────────────────────────────────

    public function on_activate(): void {}
    public function on_deactivate(): void {}
    public function on_update(string $from_version): void {}

    /**
     * Called for ALL discovered plugins regardless of active state.
     * Override to register web routes that must be reachable even when inactive
     * (e.g. admin settings pages so the Settings link works on the plugin card).
     */
    public function routes(): void {}

    // ── Internal wiring (called by PluginManager, not the plugin) ─────────────

    /** @internal */
    public function _setManifest(PluginManifest $manifest): void
    {
        $this->manifest = $manifest;
    }

    /** @internal */
    public function _setHookEngine(HookEngine $engine): void
    {
        $this->hooks = $engine;
    }

    /** @internal */
    public function _getManifest(): PluginManifest
    {
        return $this->manifest;
    }

    // ── Hook registration ─────────────────────────────────────────────────────

    protected function add_action(string $hook, callable $callback, int $priority = 10): void
    {
        $this->hooks->addAction($hook, $callback, $priority, $this->id());
    }

    protected function add_filter(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void
    {
        $this->hooks->addFilter($hook, $callback, $priority, $accepted_args, $this->id());
    }

    // ── Asset injection ───────────────────────────────────────────────────────

    protected function enqueue_admin_style(string $handle, string $src, array $deps = []): void
    {
        app(AssetManager::class)->enqueueStyle('admin', $handle, $src, $deps);
    }

    protected function enqueue_admin_script(string $handle, string $src, array $deps = [], bool $in_footer = true): void
    {
        app(AssetManager::class)->enqueueScript('admin', $handle, $src, $deps, $in_footer);
    }

    protected function enqueue_frontend_style(string $handle, string $src, array $deps = []): void
    {
        app(AssetManager::class)->enqueueStyle('frontend', $handle, $src, $deps);
    }

    protected function enqueue_frontend_script(string $handle, string $src, array $deps = [], bool $in_footer = true): void
    {
        app(AssetManager::class)->enqueueScript('frontend', $handle, $src, $deps, $in_footer);
    }

    // ── Menu registration ─────────────────────────────────────────────────────

    protected function add_menu(array $config): void
    {
        app(MenuRegistry::class)->addMenu($config);
    }

    protected function add_submenu(string $parent_id, array $config): void
    {
        app(MenuRegistry::class)->addSubmenu($parent_id, $config);
    }

    // ── Settings ──────────────────────────────────────────────────────────────

    protected function register_settings(array $fields): void
    {
        app(SettingsManager::class)->register($this->id(), $fields);
    }

    public function get_option(string $key, mixed $default = null): mixed
    {
        $tenant_id = $this->currentTenantId();
        return app(SettingsManager::class)->get($this->id(), $key, $tenant_id, $default);
    }

    public function update_option(string $key, mixed $value): void
    {
        $tenant_id = $this->currentTenantId();
        app(SettingsManager::class)->set($this->id(), $key, $value, $tenant_id);
    }

    // ── Path helpers ──────────────────────────────────────────────────────────

    protected function plugin_path(string $path = ''): string
    {
        $root = $this->manifest->rootPath;
        return $path ? $root . '/' . ltrim($path, '/') : $root;
    }

    /**
     * Return a public URL for a file inside the plugin's resources/ directory.
     *
     * Uses the /plugins/{id}/assets/{path} route which is served by
     * PluginAssetController — safe, versioned (ETag), no folder-block issues.
     *
     * @param  string $path  Path relative to the plugin root (e.g. 'resources/css/style.css')
     */
    protected function plugin_url(string $path = ''): string
    {
        // Strip leading 'resources/' so callers can pass either form:
        //   plugin_url('resources/css/style.css')  → /plugins/{id}/assets/css/style.css
        //   plugin_url('css/style.css')            → /plugins/{id}/assets/css/style.css
        $assetPath = ltrim(preg_replace('#^resources/#', '', ltrim($path, '/')), '/');

        if (!$assetPath) {
            return url('/plugins/' . $this->id());
        }

        return url('/plugins/' . $this->id() . '/assets/' . $assetPath);
    }

    protected function plugin_asset(string $path): string
    {
        return $this->plugin_url('resources/' . ltrim($path, '/'));
    }

    protected function plugin_view(string $view, array $data = []): string
    {
        // Plugins register their views namespace as their plugin id
        $namespace = str_replace('-', '_', $this->id());
        return view("{$namespace}::{$view}", $data)->render();
    }

    // ── Database migrations ───────────────────────────────────────────────────

    protected function run_migrations(): void
    {
        $path = $this->plugin_path('database/migrations');
        if (is_dir($path)) {
            Artisan::call('migrate', [
                '--path'  => str_replace(base_path() . '/', '', $path),
                '--force' => true,
            ]);
        }
    }

    protected function run_rollback(): void
    {
        $path = $this->plugin_path('database/migrations');
        if (is_dir($path)) {
            Artisan::call('migrate:rollback', [
                '--path'  => str_replace(base_path() . '/', '', $path),
                '--force' => true,
            ]);
        }
    }

    // ── Data export/import ────────────────────────────────────────────────────

    /**
     * Declare which DB tables this plugin owns.
     * These will be included in tenant data exports when the plugin is active.
     *
     * Example:
     *   $this->register_export_tables(['acme_seo_meta', 'acme_seo_redirects']);
     */
    protected function register_export_tables(array $tables): void
    {
        app(PluginExporter::class)->registerTables($this->id(), $tables);
    }

    // ── Shortcodes ────────────────────────────────────────────────────────────

    protected function register_shortcode(string $tag, callable $handler): void
    {
        app(ShortcodeRegistry::class)->register($tag, $handler);
    }

    // ── Scheduled tasks ───────────────────────────────────────────────────────

    /**
     * Register a scheduled task. Call from boot().
     * $frequency: named constant (daily, hourly, everyMinute…) or cron expression.
     */
    protected function schedule(string $frequency, callable $callback): void
    {
        app(PluginScheduler::class)->add($this->id(), $frequency, $callback);
    }

    // ── REST API routes ───────────────────────────────────────────────────────

    /**
     * Register API routes for this plugin.
     * Routes will be mounted at /api/v1/plugins/{plugin-id}/.
     * Call from boot() with a closure that defines routes.
     */
    protected function register_api_routes(callable $callback): void
    {
        app(PluginManager::class)->addApiRouteGroup($this->id(), $callback);
    }

    // ── Web routes ────────────────────────────────────────────────────────────

    /**
     * Register web routes for this plugin (tenant frontend / admin).
     * The closure receives no arguments — define routes normally inside it.
     * Routes are registered during the standard route loading phase.
     *
     * Example:
     *   $this->register_web_routes(function () {
     *       Route::middleware(['auth'])->prefix('user-home/my-plugin')->name('tenant.user.my-plugin.')->group(function () {
     *           Route::get('/', [MyController::class, 'index'])->name('index');
     *       });
     *   });
     */
    protected function register_web_routes(callable $callback): void
    {
        app(PluginManager::class)->addWebRouteGroup($this->id(), $callback);
    }

    // ── License ───────────────────────────────────────────────────────────────

    protected function is_licensed(): bool
    {
        if ($this->manifest->pricing === 'free') {
            return true;
        }
        return app(LicenseManager::class)->verify($this->id());
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private function currentTenantId(): ?string
    {
        try {
            return function_exists('tenant') && tenant() ? (string) tenant()->id : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
