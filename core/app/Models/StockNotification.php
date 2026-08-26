<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductInventoryDetail;

class StockNotification extends Model
{
    protected $table = 'stock_notifications';

    protected $fillable = [
        'product_id',
        'variant_id',
        'email',
        'name',
        'notified',
        'notified_at',
    ];

    protected $casts = [
        'notified'     => 'boolean',
        'notified_at'  => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductInventoryDetail::class, 'variant_id');
    }
}
