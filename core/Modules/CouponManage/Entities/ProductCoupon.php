<?php

namespace Modules\CouponManage\Entities;

use App\Models\ProductOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCoupon extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'code',
        'discount',
        'discount_type',
        'discount_on',
        'discount_on_details',
        'expire_date',
        'status',
        'minimum_quantity',
        'minimum_spend',
        'maximum_spend',
        'usage_limit_per_coupon',
        'usage_limit_per_user',
    ];

    public function product_orders(): HasMany
    {
        return $this->hasMany(ProductOrder::class, 'coupon', 'code');
    }

}
