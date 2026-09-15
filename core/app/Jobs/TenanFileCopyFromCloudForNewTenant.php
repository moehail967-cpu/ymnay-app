<?php

namespace App\Jobs;

use App\Models\MediaUploader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Facades\Tenancy;

class TenanFileCopyFromCloudForNewTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public string $pathname, public string $tenantId)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Read from central storage before the filesystem bootstrapper changes
        // the disk root to the tenant-specific location.
        $contents = Storage::get($this->pathname);
        $move_path = str_replace('seeder-files/all-media',$this->tenantId,$this->pathname);

        try {
            Tenancy::initialize($this->tenantId);
            Storage::put($move_path, $contents);
            $file_name = pathinfo($this->pathname,PATHINFO_BASENAME);
            //todo:: change database connection to tenant
            //todo:: update database to use this file from the cloud
//            DatabaseHelper::switchDatabase($this->tenantId);

            //todo:: need to switch data into this tenant for get tenant media uploader table accesss
            MediaUploader::where(['path' => $file_name])->update([
                'is_synced' => 1,
                'load_from' => 1
            ]);

        } finally {
            Tenancy::end();
        }
    }
}
