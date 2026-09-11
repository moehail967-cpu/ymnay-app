<?php

namespace App\PluginSystem;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsManager
{
    /** @var array<string, array> field definitions per plugin */
    private array $definitions = [];

    /**
     * In tenant context use the tenant's own DB (default connection).
     * In landlord context use the central DB for global/landlord plugin options.
     */
    private function db(): \Illuminate\Database\Connection
    {
        if (function_exists('tenant') && tenant()) {
            return DB::connection();
        }

        return DB::connection(
            config('tenancy.database.central_connection', config('database.default'))
        );
    }

    private function central(): \Illuminate\Database\Connection
    {
        return DB::connection(
            config('tenancy.database.central_connection', config('database.default'))
        );
    }

    public function register(string $plugin_id, array $fields): void
    {
        $this->definitions[$plugin_id] = $fields;
    }

    public function getDefinitions(string $plugin_id): array
    {
        return $this->definitions[$plugin_id] ?? [];
    }

    public function hasDefinitions(string $plugin_id): bool
    {
        return !empty($this->definitions[$plugin_id]);
    }

    public function getAllDefinitions(): array
    {
        return $this->definitions;
    }

    public function get(string $plugin_id, string $key, ?string $tenant_id, mixed $default = null): mixed
    {
        $cacheKey = "plugin_option.{$plugin_id}.{$tenant_id}.{$key}";

        return Cache::remember($cacheKey, 300, function () use ($plugin_id, $key, $tenant_id, $default) {
            // Tenant context: read from tenant's own DB (no tenant_id column needed)
            if ($tenant_id !== null) {
                try {
                    $row = $this->db()->table('plugin_options')
                        ->where('plugin_id', $plugin_id)
                        ->where('option_key', $key)
                        ->first();

                    if ($row) return $row->option_value;
                } catch (\Throwable) {
                    // Table may not exist yet — fall through to default
                }

                return $default;
            }

            // Landlord context: read from central DB
            $row = $this->central()->table('plugin_options')
                ->where('plugin_id', $plugin_id)
                ->where('option_key', $key)
                ->whereNull('tenant_id')
                ->first();

            return $row ? $row->option_value : $default;
        });
    }

    public function set(string $plugin_id, string $key, mixed $value, ?string $tenant_id): void
    {
        if ($tenant_id !== null) {
            // Tenant context: write to tenant's own DB
            $this->db()->table('plugin_options')->updateOrInsert(
                ['plugin_id' => $plugin_id, 'option_key' => $key],
                ['option_value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
        } else {
            // Landlord context: write to central DB with null tenant_id
            $this->central()->table('plugin_options')->updateOrInsert(
                ['plugin_id' => $plugin_id, 'option_key' => $key, 'tenant_id' => null],
                ['option_value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        Cache::forget("plugin_option.{$plugin_id}.{$tenant_id}.{$key}");
    }

    public function getAll(string $plugin_id, ?string $tenant_id): array
    {
        if ($tenant_id !== null) {
            try {
                return $this->db()->table('plugin_options')
                    ->where('plugin_id', $plugin_id)
                    ->pluck('option_value', 'option_key')
                    ->toArray();
            } catch (\Throwable) {
                return [];
            }
        }

        return $this->central()->table('plugin_options')
            ->where('plugin_id', $plugin_id)
            ->whereNull('tenant_id')
            ->pluck('option_value', 'option_key')
            ->toArray();
    }
}
