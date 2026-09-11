<?php

namespace App\PluginSystem;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseManager
{
    private function central(): \Illuminate\Database\Connection
    {
        return DB::connection(
            config('tenancy.database.central_connection', config('database.default'))
        );
    }

    public function activate(string $plugin_id, string $key, string $domain = ''): string
    {
        $manifest = app(PluginManager::class)->getManifest($plugin_id);

        if (!$manifest || !$manifest->updateServer) {
            return 'invalid';
        }

        try {
            $response = Http::timeout(10)->post($manifest->updateServer . '/activate', [
                'plugin_id'   => $plugin_id,
                'license_key' => $key,
                'domain'      => $domain ?: request()->getHost(),
            ]);

            $data   = $response->json();
            $status = $data['status'] ?? 'invalid';

            $this->central()->table('plugin_licenses')->updateOrInsert(
                ['plugin_id' => $plugin_id],
                [
                    'license_key'  => encrypt($key),
                    'status'       => $status,
                    'activated_at' => now(),
                    'expires_at'   => isset($data['expires_at']) ? $data['expires_at'] : null,
                    'last_checked' => now(),
                    'updated_at'   => now(),
                    'created_at'   => now(),
                ]
            );

            Cache::forget("plugin_license.{$plugin_id}");
            return $status;
        } catch (\Throwable $e) {
            Log::channel('plugin')->error("License activation failed for [{$plugin_id}]: {$e->getMessage()}");
            return 'invalid';
        }
    }

    public function verify(string $plugin_id): bool
    {
        return Cache::remember("plugin_license.{$plugin_id}", 86400, function () use ($plugin_id) {
            return $this->getStatus($plugin_id) === 'active';
        });
    }

    public function deactivate(string $plugin_id): void
    {
        $this->central()->table('plugin_licenses')
            ->where('plugin_id', $plugin_id)
            ->update(['status' => 'none', 'updated_at' => now()]);

        Cache::forget("plugin_license.{$plugin_id}");
    }

    public function getStatus(string $plugin_id): string
    {
        $row = $this->central()->table('plugin_licenses')->where('plugin_id', $plugin_id)->first();
        return $row->status ?? 'none';
    }
}
