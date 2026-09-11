<?php

namespace Modules\CommissionManage\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionPaymentLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'amount',
        'payment_gateway',
        'transaction_id',
        'payment_status',
        'payment_date',
        'commission_ids',
        'order_id',
        'notes'
    ];

    protected $casts = [
        'commission_ids' => 'array',
        'payment_date' => 'datetime'
    ];

    /**
     * Get the user that made the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the commission histories that were paid
     */
    public function commissionHistories()
    {
        return $this->hasMany(CommissionHistory::class, 'payment_log_id');
    }
}
