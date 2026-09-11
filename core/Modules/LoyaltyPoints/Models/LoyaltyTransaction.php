<?php

namespace Modules\LoyaltyPoints\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class LoyaltyTransaction extends Model
{
    use TenantConnection;

    public $timestamps = false;

    protected $table = 'loyalty_transactions';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'order_id',
        'type',
        'points',
        'balance_after',
        'note',
        'expires_at',
        'created_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
