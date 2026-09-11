<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Modules\CountryManage\Entities\City;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;
use Modules\CouponManage\Entities\ProductCoupon;
use Modules\ShippingModule\Entities\UserShippingAddress;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class ProductOrder extends Model
{
    use HasFactory , TenantConnection;

    protected $table = 'product_orders';

    protected $fillable = [
        'name',
        'email',
        'user_id', // buyer - nullable
        'country',
        'address',
        'city',
        'state',
        'zipcode',
        'phone',
        'message',

        'product_id',
        'coupon',
        'coupon_discounted',
        'total_amount',
        'status',

        'payment_status',
        'payment_gateway',
        'payment_track',
        'transaction_id',
        'checkout_image_path',
        'checkout_type',

        'order_details',
        'payment_meta',
        'shipping_address_id', // UserShippingAddress->id

        'selected_shipping_option',
        'uid'
    ];
//    protected static function boot()
//    {
//        parent::boot();
//
//        static::created(function ($order) {
//            $tenantId = strtolower(optional(tenant())->id ?? 'MAIN');
//            $unique_order_id = $tenantId . '-' . strtoupper(Str::random(3)) . $order->id;
//            $order->updateQuietly(['uid' => $unique_order_id]);
//        });
//    }



    public function getTotalAttribute()
    {
        return $this->total_amount;
    }

    public function scopeCompleted()
    {
        return $this->where(['status'=> 'complete', 'payment_status'=> 'success']);
    }

    public function shipping()
    {
        return $this->hasOne(UserShippingAddress::class, 'id', 'shipping_address_id');
    }

    public function getCountry(): HasOne
    {
        return $this->hasOne(Country::class, 'id', 'country');
    }

    public function getState(): HasOne
    {
        return $this->hasOne(State::class, 'id', 'state');
    }

    public function getCity(): HasOne
    {
        return $this->hasOne(City::class, 'id', 'city');
    }

    public function sale_details()
    {
        return $this->hasMany(SaleDetails::class, 'order_id', 'id');
    }
//    public function product_coupon(){
//        return $this->hasOne(ProductCoupon::class, 'id', 'coupon');
//    }
    public function product_coupon(): HasOne
    {
        return $this->hasOne(ProductCoupon::class, 'code', 'coupon');
    }

}
