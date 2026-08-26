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

class TenantSeedDatabaseJob implements ShouldQueue
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
        Log::info("TenantSeedDatabaseJob job started", ['tenant_id' => $this->tenant->getTenantKey()]);

        try {
            Config::set('app.debug', true);

            $exitCode = Artisan::call('tenants:seed', [
                '--tenants' => [$this->tenant->getTenantKey()],
                '--force' => true,
            ]);

            $output = Artisan::output();

            if ($exitCode !== 0) {
                Log::error("Tenant seeding failed with exit code: {$exitCode}", [
                    'tenant_id' => $this->tenant->getTenantKey(),
                    'output' => $output,
                ]);
                throw new \Exception("Tenant seeding failed: " . $output);
            }

            Log::info("Tenant {$this->tenant->getTenantKey()} successfully seeded", [
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            Log::error("TenantSeedDatabaseJob failed", [
                'tenant_id' => $this->tenant->getTenantKey(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
