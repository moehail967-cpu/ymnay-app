<?php

namespace App\Console\Commands;

use App\PluginSystem\PluginManager;
use Illuminate\Console\Command;

class PluginActivate extends Command
{
    protected $signature   = 'plugin:activate {id : Plugin ID to activate}';
    protected $description = 'Activate a discovered plugin by ID';

    public function handle(PluginManager $manager): int
    {
        $id = $this->argument('id');

        if (!$manager->getManifest($id)) {
            $this->error("Plugin [{$id}] not found. Run 'php artisan plugin:list' to see available plugins.");
            return self::FAILURE;
        }

        if (PluginManager::isActive($id)) {
            $this->warn("Plugin [{$id}] is already active.");
            return self::SUCCESS;
        }

        try {
            $manager->activate($id);
            $this->info("Plugin [{$id}] activated successfully.");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to activate [{$id}]: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
