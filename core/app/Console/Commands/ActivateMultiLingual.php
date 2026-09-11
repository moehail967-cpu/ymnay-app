<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * One-shot command to activate the multi-lingual plugin.
 * Run: php artisan ml:activate
 * Delete this file after running.
 */
class ActivateMultiLingual extends Command
{
    protected $signature   = 'ml:activate';
    protected $description = 'Activate the multi-lingual plugin and run its migration';

    public function handle(): int
    {
        $path = base_path('modules_statuses.json');

        if (!file_exists($path)) {
            $this->error("modules_statuses.json not found at: $path");
            return 1;
        }

        $statuses = json_decode(file_get_contents($path), true);

        if ($statuses === null) {
            $this->error('Could not parse modules_statuses.json');
            return 1;
        }

        if (isset($statuses['multi-lingual']) && $statuses['multi-lingual'] === true) {
            $this->info('multi-lingual is already active.');
        } else {
            $statuses['multi-lingual'] = true;
            file_put_contents($path, json_encode($statuses, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
            $this->info('Added multi-lingual: true to modules_statuses.json');
        }

        // Run the plugin migration on every tenant
        $this->info('Running multi-lingual migration on all tenants…');

        $tenants = \App\Models\Tenant::all();
        foreach ($tenants as $tenant) {
            try {
                tenancy()->initialize($tenant);
                \Illuminate\Support\Facades\Artisan::call('migrate', [
                    '--path'  => 'Modules/MultiLingual/database/migrations',
                    '--force' => true,
                ]);
                $output = trim(\Illuminate\Support\Facades\Artisan::output());
                $this->line("  [{$tenant->id}] " . ($output ?: 'Nothing to migrate'));
            } catch (\Throwable $e) {
                $this->warn("  [{$tenant->id}] Error: " . $e->getMessage());
            } finally {
                tenancy()->end();
            }
        }

        $this->info('Done. Delete core/app/Console/Commands/ActivateMultiLingual.php after this.');
        return 0;
    }
}
