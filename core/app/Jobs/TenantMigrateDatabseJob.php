<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class TenantMigrateDatabseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var TenantWithDatabase */
    protected $tenant;

    public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("TenantMigrateDatabse job started", ['tenant_id' => $this->tenant->getTenantKey()]);

        // Log migration paths for debugging
        $migrationPaths = config('tenancy.migration_parameters.--path', []);
        Log::info("Tenant migration paths configured", [
            'tenant_id' => $this->tenant->getTenantKey(),
            'paths' => $migrationPaths,
            'paths_exist' => array_map(function($path) {
                return ['path' => $path, 'exists' => is_dir($path)];
            }, $migrationPaths),
        ]);

        Config::set('app.debug', true);
        Artisan::call('tenants:migrate', [
            '--tenants' => [$this->tenant->getTenantKey()],
            '--force' => true,
        ]);

        $output = Artisan::output();
        Log::info('Tenant database migrated successfully', [
            'tenant_id' => $this->tenant->id,
            'database'  => $this->tenant->database()->getName(),
            'output' => $output,
        ]);
    }
}
