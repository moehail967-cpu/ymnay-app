<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductInventoryDetail;

class InventoryAdjustmentLog extends Model
{
    protected $table = 'inventory_adjustment_logs';

    protected $fillable = [
        'product_id',
        'variant_id',
        'qty_before',
        'qty_change',
        'qty_after',
        'reason',
        'note',
        'admin_id',
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
