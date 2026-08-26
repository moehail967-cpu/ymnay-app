<?php

namespace App\Console\Commands;

use App\PluginSystem\PluginManager;
use Illuminate\Console\Command;

class PluginDeactivate extends Command
{
    protected $signature   = 'plugin:deactivate {id : Plugin ID to deactivate}';
    protected $description = 'Deactivate an active plugin by ID';

    public function handle(PluginManager $manager): int
    {
        $id = $this->argument('id');

        if (!$manager->getManifest($id)) {
            $this->error("Plugin [{$id}] not found. Run 'php artisan plugin:list' to see available plugins.");
            return self::FAILURE;
        }

        if (!PluginManager::isActive($id)) {
            $this->warn("Plugin [{$id}] is not currently active.");
            return self::SUCCESS;
        }

        try {
            $manager->deactivate($id);
            $this->info("Plugin [{$id}] deactivated successfully.");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to deactivate [{$id}]: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
