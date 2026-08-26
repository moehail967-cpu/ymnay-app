<?php

namespace App\Console\Commands\Theme;

use App\Services\ThemeDemoImporter;
use Illuminate\Console\Command;
use App\Models\Tenant;

class ThemeDemoImportCommand extends Command
{
    protected $signature   = 'theme:demo-import {slug} {--tenant= : Tenant ID to import into}';
    protected $description = 'Import demo data from themes/{slug}/demo/data.json into a tenant database';

    public function handle(): int
    {
        $slug     = $this->argument('slug');
        $tenantId = $this->option('tenant');

        $themeBase = config('theme.base_path') . '/' . $slug;
        $dataFile  = $themeBase . '/demo/data.json';

        if (!is_dir($themeBase)) {
            $this->error("Theme [{$slug}] not found at themes/{$slug}/.");
            return self::FAILURE;
        }

        if (!file_exists($dataFile)) {
            $this->error("No demo data file at themes/{$slug}/demo/data.json.");
            return self::FAILURE;
        }

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                $this->error("Tenant [{$tenantId}] not found.");
                return self::FAILURE;
            }

            tenancy()->initialize($tenant);
        } elseif (!tenant()) {
            $this->error('No active tenant. Use --tenant=<id> to specify one.');
            return self::FAILURE;
        }

        $this->info("Importing demo data for theme [{$slug}]...");

        $importer = new ThemeDemoImporter($slug);
        $result   = $importer->import();

        if ($result['status']) {
            $this->info($result['msg']);
            return self::SUCCESS;
        }

        $this->error($result['msg']);
        return self::FAILURE;
    }
}
