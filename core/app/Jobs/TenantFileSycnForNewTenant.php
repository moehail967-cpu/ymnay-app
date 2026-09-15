<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\CacheKeyEnums;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class TenantFileSycnForNewTenant implements ShouldQueue
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
        /* file sync test */
        $cacheKey = CacheKeyEnums::ALL_AWS_S3_DEMO_IMAGES_FILES->value;
        $allFiles = Cache::get($cacheKey);
        if (! is_array($allFiles) || $allFiles === []) {
            // Flysystem paths are relative to the configured disk root.
            $allFiles = Storage::allFiles('seeder-files/all-media');

            // Local/VPS source checkouts may use the local disk before the
            // filesystem manager has reverted from a tenant HTTP request.
            if ($allFiles === []) {
                $localRoot = base_path('storage/app/seeder-files/all-media');
                if (is_dir($localRoot)) {
                    $allFiles = collect(File::allFiles($localRoot))
                        ->map(fn ($file) => 'seeder-files/all-media/'.str_replace('\\', '/', $file->getRelativePathname()))
                        ->all();
                }
            }

            // Never cache an empty discovery result for hours; a restored
            // source disk must be discoverable by a safe retry.
            if ($allFiles !== []) {
                Cache::put($cacheKey, $allFiles, 300 * 60);
            }
        }

        $tenantKey = $this->tenant->id;
        //todo get folder name
        foreach ($allFiles as $file){
            TenanFileCopyFromCloudForNewTenant::dispatch($file,$tenantKey)->onConnection('tenant_file_sync')->delay(Carbon::now()->addSeconds(2));
        }

    }

}
