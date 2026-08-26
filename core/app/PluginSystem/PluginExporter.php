<?php

namespace App\PluginSystem;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Manages data export/import for plugin-owned database tables.
 * Plugins call register_export_tables() in boot(); this class collects
 * all registrations and makes them available to the tenant export pipeline.
 */
class PluginExporter
{
    /** @var array<string, string[]> plugin_id => [table, table, ...] */
    private array $tables = [];

    public function registerTables(string $plugin_id, array $tables): void
    {
        $this->tables[$plugin_id] = array_unique(array_merge(
            $this->tables[$plugin_id] ?? [],
            $tables
        ));
    }

    /** Returns all registered tables across all plugins. */
    public function allTables(): array
    {
        return $this->tables;
    }

    /** Returns tables registered by a specific plugin. */
    public function tablesForPlugin(string $plugin_id): array
    {
        return $this->tables[$plugin_id] ?? [];
    }

    /**
     * Export all plugin-owned rows for a given tenant.
     * Returns array keyed by table name, each containing the rows.
     */
    public function exportForTenant(int $tenant_id): array
    {
        $export = [];

        foreach ($this->tables as $plugin_id => $pluginTables) {
            foreach ($pluginTables as $table) {
                try {
                    if (!DB::getSchemaBuilder()->hasColumn($table, 'tenant_id')) {
                        continue;
                    }

                    $rows = DB::table($table)->where('tenant_id', $tenant_id)->get()->toArray();
                    if (!empty($rows)) {
                        $export[$table] = $rows;
                    }
                } catch (\Throwable $e) {
                    Log::channel('plugin')->warning(
                        "Plugin [{$plugin_id}] export failed for table [{$table}]: {$e->getMessage()}"
                    );
                }
            }
        }

        return $export;
    }

    /**
     * Import plugin table rows for a tenant.
     * $data is keyed by table name with arrays of row objects.
     */
    public function importForTenant(int $tenant_id, array $data): void
    {
        foreach ($data as $table => $rows) {
            foreach ((array) $rows as $row) {
                $row = (array) $row;
                $row['tenant_id'] = $tenant_id;
                unset($row['id']); // Let DB assign new ID

                try {
                    DB::table($table)->insert($row);
                } catch (\Throwable $e) {
                    Log::channel('plugin')->warning(
                        "Plugin import failed for table [{$table}]: {$e->getMessage()}"
                    );
                }
            }
        }
    }
}
