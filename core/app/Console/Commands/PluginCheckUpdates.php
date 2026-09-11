<?php

namespace App\Console\Commands;

use App\PluginSystem\PluginManager;
use App\PluginSystem\UpdateManager;
use Illuminate\Console\Command;

class PluginCheckUpdates extends Command
{
    protected $signature   = 'plugin:check-updates {--plugin= : Check a specific plugin ID only} {--force : Bypass cache and force a fresh check}';
    protected $description = 'Check for available plugin updates from each plugin\'s update server';

    public function handle(PluginManager $manager, UpdateManager $updater): int
    {
        $specificId = $this->option('plugin');
        $force      = $this->option('force');

        $discovered = $manager->allDiscovered();

        if ($specificId) {
            $discovered = array_filter($discovered, fn($m) => $m->id === $specificId);
            if (empty($discovered)) {
                $this->error("Plugin [{$specificId}] not found.");
                return self::FAILURE;
            }
        }

        $withServer = array_filter($discovered, fn($m) => !empty($m->updateServer));

        if (empty($withServer)) {
            $this->warn('No plugins have an update_server configured in plugin.json.');
            return self::SUCCESS;
        }

        if ($force) {
            foreach ($withServer as $manifest) {
                \Illuminate\Support\Facades\Cache::forget("plugin_update_check.{$manifest->id}");
            }
        }

        $this->line('Checking ' . count($withServer) . ' plugin(s) for updates…');
        $this->line('');

        $rows        = [];
        $updateCount = 0;

        foreach ($withServer as $manifest) {
            $this->line("  → {$manifest->name} ({$manifest->version})");
            $info = $updater->checkForUpdate($manifest);

            if ($info) {
                $updateCount++;
                $rows[] = [
                    $manifest->id,
                    $manifest->name,
                    $manifest->version,
                    "<fg=green>{$info['version']}</>",
                    '<fg=yellow>● UPDATE AVAILABLE</>',
                ];
            } else {
                $rows[] = [
                    $manifest->id,
                    $manifest->name,
                    $manifest->version,
                    '—',
                    '<fg=gray>✓ up to date</>',
                ];
            }
        }

        $this->line('');
        $this->table(['ID', 'Name', 'Current', 'Latest', 'Status'], $rows);

        if ($updateCount > 0) {
            $this->line('');
            $this->line("<fg=yellow>{$updateCount} update(s) available.</> Install via the admin panel or upload a ZIP with plugin:install.");
        }

        return self::SUCCESS;
    }
}
