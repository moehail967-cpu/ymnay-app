<?php

namespace App\Providers;

use App\PluginSystem\AssetManager;
use App\PluginSystem\HookEngine;
use App\PluginSystem\LicenseManager;
use App\PluginSystem\MenuRegistry;
use App\PluginSystem\PluginManager;
use App\PluginSystem\PluginExporter;
use App\PluginSystem\PluginScheduler;
use App\PluginSystem\SettingsManager;
use App\PluginSystem\ShortcodeRegistry;
use App\PluginSystem\UpdateManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HookEngine::class);
        $this->app->singleton(AssetManager::class);
        $this->app->singleton(MenuRegistry::class);
        $this->app->singleton(SettingsManager::class);
        $this->app->singleton(LicenseManager::class);
        $this->app->singleton(ShortcodeRegistry::class);
        $this->app->singleton(PluginScheduler::class);
        $this->app->singleton(PluginExporter::class);
        $this->app->singleton(UpdateManager::class);

        $this->app->singleton(PluginManager::class, function ($app) {
            return new PluginManager($app->make(HookEngine::class));
        });
    }

    public function boot(): void
    {
        $this->registerBladeDirectives();
        $this->configureLogging();

        /** @var PluginManager $manager */
        $manager = $this->app->make(PluginManager::class);
        $manager->discover();
        $manager->bootAll();

        // Register plugin routes after all plugins have booted.
        // preRegisterAllRoutes() ensures tenant-type plugin API routes are in
        // $apiRouteGroups before registerApiRoutes() is called — without this,
        // tenant plugin APIs would only be added during bootForTenant() which
        // fires after the router has already matched the incoming request.
        $this->callAfterResolving('router', function () use ($manager) {
            $manager->preRegisterAllRoutes();
            $manager->registerApiRoutes();
            $manager->registerWebRoutes();
        });

        // Boot tenant-type plugins once tenancy middleware initializes the tenant
        if (class_exists(\Stancl\Tenancy\Events\TenancyInitialized::class)) {
            $this->app['events']->listen(
                \Stancl\Tenancy\Events\TenancyInitialized::class,
                function () use ($manager) {
                    $manager->bootForTenant();
                    $manager->registerWebRoutes();
                    $manager->registerApiRoutes();
                }
            );
        }
    }

    private function registerBladeDirectives(): void
    {
        Blade::directive('pluginAdminStyles', fn() => '<?php echo plugin_admin_styles(); ?>');
        Blade::directive('pluginAdminScripts', fn() => '<?php echo plugin_admin_scripts(true); ?>');
        Blade::directive('pluginFrontendStyles', fn() => '<?php echo plugin_frontend_styles(); ?>');
        Blade::directive('pluginFrontendScripts', fn() => '<?php echo plugin_frontend_scripts(true); ?>');
    }

    private function configureLogging(): void
    {
        $logConfig = config('logging.channels', []);

        if (!isset($logConfig['plugin'])) {
            config(['logging.channels.plugin' => [
                'driver' => 'daily',
                'path'   => storage_path('logs/plugin.log'),
                'level'  => 'debug',
                'days'   => 14,
            ]]);
        }
    }
}
