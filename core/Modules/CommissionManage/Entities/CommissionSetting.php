<?php

namespace Modules\CommissionManage\Entities;

use App\Models\PaymentGateway;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class CommissionSetting extends Model
{
    use HasFactory , CentralConnection;

    // Constants for commission types
    public const COMMISSION_TYPE_SUBSCRIPTION_ONLY = 'subscription_only';
    public const COMMISSION_TYPE_SUBSCRIPTION_AND_COMMISSION = 'subscription_and_commission';
    public const COMMISSION_TYPE_COMMISSION_ONLY = 'commission_only';

    // Constants for payment gateway sources
    public const GATEWAY_SOURCE_LANDLORD = 'landlord_gateway';
    public const GATEWAY_SOURCE_TENANT_DEFAULT = 'tenant_default_gateway';

    protected $fillable = [
        'user_id',
        'commission_type',
        'payment_gateway_source',
        'selected_gateway',
        'commission_rate',
        'payment_gateways',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'payment_gateways' => 'array',
    ];


    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accessors & Mutators
    public function setCommissionRateAttribute($value): void
    {
        $this->attributes['commission_rate'] = min($value, 100);
    }

    // Helper Methods
    public function isUsingLandlordGateway(): bool
    {
        return $this->payment_gateway_source === self::GATEWAY_SOURCE_LANDLORD;
    }

    public function isUsingTenantGateway(): bool
    {
        return $this->payment_gateway_source === self::GATEWAY_SOURCE_TENANT_DEFAULT;
    }

    public function hasCommission()
    {
        return in_array($this->commission_type, [
            self::COMMISSION_TYPE_SUBSCRIPTION_AND_COMMISSION,
            self::COMMISSION_TYPE_COMMISSION_ONLY
        ]);
    }

    public function hasSubscription(): bool
    {
        return in_array($this->commission_type, [
            self::COMMISSION_TYPE_SUBSCRIPTION_ONLY,
            self::COMMISSION_TYPE_SUBSCRIPTION_AND_COMMISSION,
        ]);
    }

    // Scope
    public function scopeForLandlord($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
//    public function selectedGateways()
//    {
//        return $this->hasMany(\Modules\CommissionManage\Entities\CommissionSelectedGateway::class, 'commission_setting_id');
//    }

    public function selectedGateways()
    {
        return $this->belongsToMany(
            \App\Models\PaymentGateway::class,
            'commission_selected_gateways',
            'commission_setting_id',
            'payment_gateway_id'
        );
    }
    // for withdraw gateways:
    public function withdrawGateways()
    {
        return $this->hasMany(WithdrawGateway::class)->ordered();
    }

    public function activeWithdrawGateways()
    {
        return $this->hasMany(WithdrawGateway::class)->active()->ordered();
    }



}
