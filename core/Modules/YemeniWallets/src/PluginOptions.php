<?php

namespace Modules\YemeniWallets\Src;

use App\PluginSystem\SettingsManager;

/**
 * Thin wrapper around App\PluginSystem\SettingsManager so controllers
 * (which are not PluginBase instances) can read/write the same
 * per-plugin, tenant-aware settings storage that PluginBase::get_option()
 * / update_option() use internally.
 *
 * Mirrors PluginBase's private currentTenantId() resolution exactly.
 */
class PluginOptions
{
    public const PLUGIN_ID = 'yemeni-wallets';

    public static function get(string $key, mixed $default = null, bool $forceGlobal = false): mixed
    {
        $tenantId = $forceGlobal ? null : self::currentTenantId();

        return app(SettingsManager::class)->get(self::PLUGIN_ID, $key, $tenantId, $default);
    }

    public static function set(string $key, mixed $value, bool $forceGlobal = false): void
    {
        $tenantId = $forceGlobal ? null : self::currentTenantId();

        app(SettingsManager::class)->set(self::PLUGIN_ID, $key, $value, $tenantId);
    }

    /**
     * Landlord-scoped (global) catalog helpers. Always tenant_id = null,
     * regardless of whatever tenant context the call happens to run in --
     * only the Landlord admin controller should ever write here.
     */
    public static function getGlobal(string $key, mixed $default = null): mixed
    {
        return self::get($key, $default, forceGlobal: true);
    }

    public static function setGlobal(string $key, mixed $value): void
    {
        self::set($key, $value, forceGlobal: true);
    }

    private static function currentTenantId(): ?string
    {
        try {
            return function_exists('tenant') && tenant() ? (string) tenant()->id : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
