<?php

namespace Modules\MultiCurrency\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class TenantCurrency extends Model
{
    use TenantConnection;

    protected $table = 'mc_tenant_currencies';

    protected $fillable = ['currency_code', 'is_default'];

    protected $casts = ['is_default' => 'boolean'];

    public static function enabled(): Collection
    {
        return static::all();
    }

    public static function isEnabled(string $code): bool
    {
        return static::where('currency_code', $code)->exists();
    }

    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first();
    }

    public static function clearDefault(): void
    {
        static::query()->update(['is_default' => false]);
    }
}
