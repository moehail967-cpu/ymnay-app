<?php

namespace App\Console\Commands\Theme;

use App\Contracts\ThemeDemoSeederContract;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Tenant;

class ThemeDemoSeedCommand extends Command
{
    protected $signature   = 'theme:demo-seed {--tenant= : Tenant ID} {--theme= : Override theme slug}';
    protected $description = 'Run the DemoSeeder for a tenant (seeds page builder widgets into the configured page)';

    public function handle(): int
    {
        $tenantId  = $this->option('tenant');
        $themeOver = $this->option('theme');

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if (! $tenant) {
                $this->error("Tenant [{$tenantId}] not found.");
                return self::FAILURE;
            }
            tenancy()->initialize($tenant);
        } elseif (! tenant()) {
            $this->error('No active tenant. Use --tenant=<id>.');
            return self::FAILURE;
        }

        $themeSlug = $themeOver ?? (tenant()->theme_slug ?? null);

        if (! $themeSlug) {
            $this->error('No theme slug found on tenant. Use --theme=<slug>.');
            return self::FAILURE;
        }

        $class = 'Themes\\' . Str::studly($themeSlug) . '\\DemoSeeder';

        if (! class_exists($class)) {
            $this->error("DemoSeeder class not found: {$class}");
            return self::FAILURE;
        }

        if (! is_a($class, ThemeDemoSeederContract::class, true)) {
            $this->error("{$class} does not implement ThemeDemoSeederContract.");
            return self::FAILURE;
        }

        $this->info("Running DemoSeeder for theme [{$themeSlug}] on tenant [" . tenant()->id . "]...");
        (new $class())->run();
        $this->info('Done.');

        return self::SUCCESS;
    }
}
