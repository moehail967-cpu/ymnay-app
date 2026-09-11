<?php

namespace Modules\CommissionManage\Entities;

use App\Models\PaymentLogs;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class CommissionHistory extends Model
{
    use HasFactory , CentralConnection;

//    protected $connection = 'landlord';

    protected $fillable = [
        'tenant_id',
        'order_id',
        'user_id',
        'order_total',
        'commission_rate',
        'commission_amount',
        'payment_status',
        'status',
        'uid',
        'payment_gateway'
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(PaymentLogs::class, 'order_id', 'id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
