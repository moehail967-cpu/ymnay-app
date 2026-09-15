<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class StoreOnboardingRequest extends Model
{
    use CentralConnection;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'plan_id',
        'theme_slug',
        'store_name',
        'subdomain',
        'tenant_id',
        'status',
        'plan_snapshot',
        'last_error',
        'completed_at',
    ];

    protected $casts = [
        'plan_snapshot' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PricePlan::class);
    }
}
