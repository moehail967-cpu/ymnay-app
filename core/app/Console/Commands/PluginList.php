<?php

namespace App\Console\Commands;

use App\PluginSystem\PluginManager;
use Illuminate\Console\Command;

class PluginList extends Command
{
    protected $signature   = 'plugin:list';
    protected $description = 'List all discovered plugins with their status and version';

    public function handle(PluginManager $manager): int
    {
        $discovered = $manager->allDiscovered();
        $statuses   = $this->loadStatuses();

        if (empty($discovered)) {
            $this->warn('No plugins with plugin.json found in Modules/.');
            return self::SUCCESS;
        }

        $rows = [];
        foreach ($discovered as $manifest) {
            $moduleKey = $this->moduleNameFromId($manifest->id);
            $active    = PluginManager::isActive($manifest->id)
                ? '<fg=green>active</>'
                : '<fg=yellow>inactive</>';

            $pricing = $manifest->pricing === 'paid'
                ? '<fg=cyan>paid</>'
                : '<fg=gray>free</>';

            $rows[] = [
                $manifest->id,
                $manifest->name,
                $manifest->version,
                $manifest->type,
                $pricing,
                $active,
            ];
        }

        $this->table(
            ['ID', 'Name', 'Version', 'Type', 'Pricing', 'Status'],
            $rows
        );

        $this->line('');
        $this->line('<fg=gray>Total: ' . count($discovered) . ' plugin(s)</>');

        return self::SUCCESS;
    }

    private function loadStatuses(): array
    {
        $path = base_path('modules_statuses.json');
        if (!file_exists($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function moduleNameFromId(string $plugin_id): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $plugin_id)));
    }
}
