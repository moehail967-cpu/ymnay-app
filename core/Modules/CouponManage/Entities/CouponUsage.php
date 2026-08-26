<?php

namespace Modules\CouponManage\Entities;

use App\Models\ProductOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'order_id',
        'discount_amount',
    ];

//    protected static function newFactory()
//    {
//        return \Modules\CouponManage\Database\factories\CouponUsageFactory::new();
//    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(ProductCoupon::class);
    }

    public function order()
    {
        return $this->belongsTo(ProductOrder::class);
    }
}
