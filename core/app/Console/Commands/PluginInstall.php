<?php

namespace App\Console\Commands;

use App\PluginSystem\PluginManager;
use App\PluginSystem\UpdateManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PluginInstall extends Command
{
    protected $signature   = 'plugin:install {zip : Absolute or relative path to the plugin .zip file}';
    protected $description = 'Install a plugin from a local ZIP file';

    public function handle(UpdateManager $updater, PluginManager $manager): int
    {
        $zipPath = $this->argument('zip');

        if (!str_starts_with($zipPath, '/')) {
            $zipPath = base_path($zipPath);
        }

        if (!file_exists($zipPath)) {
            $this->error("File not found: {$zipPath}");
            return self::FAILURE;
        }

        if (!str_ends_with(strtolower($zipPath), '.zip')) {
            $this->error('File must be a .zip archive.');
            return self::FAILURE;
        }

        $this->info('Inspecting ZIP…');

        $pluginId = $updater->installFromZip($zipPath);

        if (!$pluginId) {
            $this->error('Installation failed. Make sure the ZIP contains a valid plugin.json at its root directory.');
            return self::FAILURE;
        }

        $this->info("Installed plugin: <fg=green>{$pluginId}</>");

        // Re-discover
        Cache::forget('plugin_manager.manifests');

        // Activate
        if ($this->confirm("Activate '{$pluginId}' now?", true)) {
            try {
                $manager->discover();
                $manager->activate($pluginId);
                $this->info("Plugin <fg=green>{$pluginId}</> is now active.");
            } catch (\Throwable $e) {
                $this->warn("Could not activate plugin: {$e->getMessage()}");
                $this->line('You can activate it later via the admin panel.');
            }
        }

        $this->newLine();
        $this->comment("Run `php artisan plugin:list` to see all installed plugins.");

        return self::SUCCESS;
    }
}
