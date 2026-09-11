<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Modules\CommissionManage\Entities\CommissionHistory;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'user_id',
        'theme_slug',
        'razorpay_subscription_id',
        'recurring_enabled',
        'payment_grace_days',
        'is_renew',
        'renew_status',
        'start_date',
        'expire_date',
        'renewal_payment_log_id',
        'grace_period_end',
    ];

    protected $casts = [
        'expire_date' => 'datetime',
        'grace_period_end' => 'datetime'
    ];

    public function scopeWhereValid()
    {
        return $this->whereNotNull('user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function payment_log(): HasOne
    {
        return $this->hasOne(PaymentLogs::class, 'tenant_id', 'id')->latest();
    }

    public function domain(): HasOne
    {
        return $this->hasOne(UserDomain::class, 'tenant_id', 'id');
    }

    public function custom_domain(): HasOne
    {
        return $this->hasOne(CustomDomain::class, 'old_domain', 'id');
    }

    public function tenant_unique_key(): HasOne
    {
        return $this->hasOne(TenantUniqueKey::class, 'tenant_id', 'id');
    }
    // commissionHistories:
    public function commissionHistories(): HasMany
    {
        return $this->hasMany(CommissionHistory::class, 'tenant_id', 'id');
    }
}
