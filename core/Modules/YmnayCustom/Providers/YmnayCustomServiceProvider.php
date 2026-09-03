<?php

namespace Modules\YmnayCustom\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\YmnayCustom\Support\TenantWalletOrder;

class YmnayCustomServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'YmnayCustom';
    protected string $moduleNameLower = 'ymnaycustom';

    public function boot(): void
    {
        $this->loadViewsFrom(module_path($this->moduleName, 'resources/views'), $this->moduleNameLower);
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        add_filter('nazmart:before_order_create', [TenantWalletOrder::class, 'capture'], 10, 1);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower);
        $this->app->register(RouteServiceProvider::class);
    }
}
