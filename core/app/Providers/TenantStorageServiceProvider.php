<?php
namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Stancl\Tenancy\Events\TenancyBootstrapped;

class TenantStorageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register tenant-specific disk when tenancy is bootstrapped
        $this->app['events']->listen(TenancyBootstrapped::class, function (TenancyBootstrapped $event) {
            $tenantId = $event->tenancy->tenant->id;
            Config::set("filesystems.disks.tenant{$tenantId}", [
                'driver' => 'local',
                'root' => storage_path("tenant{$tenantId}/app"),
            ]);
        });
    }
}
