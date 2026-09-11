<?php

namespace Modules\CommissionManage\Entities;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantWithdrawRequest extends Model
{
    protected $fillable = [
        'tenant_id',
        'amount',
        'payment_method',
        'account_details',
        'transaction_id',
        'status',
        'approved_by',
        'approved_at',
        'note',
        'image',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the withdraw request
     */
    public function tenant():BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id' , 'user_id');
    }

    /**
     * Get the admin who approved/rejected the request
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo( User::class, 'id','approved_by');
    }

    /**
     * Scope to filter pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'complete');
    }

    /**
     * Scope to filter rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'cancel');
    }
}
