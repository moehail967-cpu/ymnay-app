<?php

namespace Modules\MultiCurrency\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class Currency extends Model
{
    use TenantConnection;

    protected $table = 'mc_currencies';

    protected $fillable = [
        'code', 'name', 'symbol', 'rate', 'base_code', 'is_active', 'rate_updated_at',
    ];

    protected $casts = [
        'rate'            => 'float',
        'is_active'       => 'boolean',
        'rate_updated_at' => 'datetime',
    ];

    public static function allActive(): Collection
    {
        return static::where('is_active', true)->orderBy('code')->get();
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->where('is_active', true)->first();
    }
}
