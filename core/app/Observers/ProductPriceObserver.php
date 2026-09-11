<?php

namespace App\Observers;

use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductPriceObserver
{
    public function updated(Product $product): void
    {
        $price_changed      = $product->isDirty('price');
        $sale_price_changed = $product->isDirty('sale_price');

        if (!$price_changed && !$sale_price_changed) return;

        try {
            DB::table('product_price_histories')->insert([
                'product_id'     => $product->id,
                'old_price'      => $price_changed ? $product->getOriginal('price') : null,
                'new_price'      => $price_changed ? $product->price : null,
                'old_sale_price' => $sale_price_changed ? $product->getOriginal('sale_price') : null,
                'new_sale_price' => $sale_price_changed ? $product->sale_price : null,
                'admin_id'       => Auth::guard('admin')->id(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        } catch (\Throwable) {
            // silently fail — price history is non-critical
        }
    }
}
