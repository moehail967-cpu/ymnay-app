<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCustomSpecification extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'title',
        'value',
        'sort_order'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
