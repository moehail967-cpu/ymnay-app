<?php

namespace Modules\Pos\Services;

use App\Models\OrderProducts;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Campaign\Entities\CampaignProduct;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductInventory;
use Modules\Product\Entities\ProductInventoryDetail;

class FrontendInventoryService
{
    /**
     * @param int $order_id
     * @return void
     * @throws Exception
     */
    public static function updateInventory(int $order_id): void
    {
        $ordered_products = OrderProducts::with('campaignProduct')->where('order_id', $order_id)->get();

        foreach ($ordered_products ?? [] as $product) {
            DB::transaction(function () use ($product) {
                // --- Campaign limit check + sold_count increment (with row lock) ---
                if (!empty($product->campaignProduct)) {
                    $cp = CampaignProduct::where('product_id', $product->product_id)
                        ->where('campaign_id', $product->campaignProduct->campaign_id)
                        ->lockForUpdate()->first();

                    if ($cp) {
                        $units_for_sale = $cp->units_for_sale;
                        if ($units_for_sale !== null) {
                            if ($cp->sold_count >= $units_for_sale) {
                                throw new Exception(__('Campaign sell limitation is over, You can not purchase this product right now'));
                            }
                            if ($units_for_sale < ($product->quantity + $cp->sold_count)) {
                                throw new Exception(__('Campaign sell limitation is over, You can not purchase current amount'));
                            }
                        }
                        $cp->increment('sold_count', $product->quantity);
                    }
                }

                // --- Variant stock: lock row, validate, decrement ---
                if ($product->variant_id !== null) {
                    $variants = ProductInventoryDetail::where(['product_id' => $product->product_id, 'id' => $product->variant_id])
                        ->lockForUpdate()->get();
                    foreach ($variants as $variant) {
                        if ($variant->stock_count < $product->quantity) {
                            throw new Exception($product->name . ' ' . __('This product is Stock out please remove it from your cart and try again'));
                        }
                        $variant->decrement('stock_count', $product->quantity);
                        $variant->increment('sold_count', $product->quantity);
                    }
                }

                // --- Product-level inventory: lock row, validate, decrement ---
                $product_inventory = ProductInventory::where('product_id', $product->product_id)->lockForUpdate()->first();
                if ($product_inventory) {
                    if ($product_inventory->stock_count < $product->quantity) {
                        throw new Exception($product->name . ' ' . __('This product is Stock out please remove it from your cart and try again'));
                    }
                    $product_inventory->decrement('stock_count', $product->quantity);
                    $product_inventory->sold_count = ($product_inventory->sold_count ?? 0) + $product->quantity;
                    $product_inventory->save();
                }
            });
        }

        self::checkStock(); // Checking Stock for warning and email notification
    }

    private static function checkStock(): void
    {
        // Inventory Warnings
        $threshold_amount = get_static_option('stock_threshold_amount') ?? 10;

        $inventory_product_items = ProductInventoryDetail::where('stock_count', '<=', $threshold_amount)
            ->whereHas('is_inventory_warn_able', function ($query) {
                $query->where('is_inventory_warn_able', 1);
            })
            ->select('id', 'product_id')
            ->get();

        $inventory_product_items_id = !empty($inventory_product_items) ? $inventory_product_items->pluck('product_id')->toArray() : [];

        $products = Product::with('inventory')
            ->where('is_inventory_warn_able', 1)
            ->whereHas('inventory', function ($query) use ($threshold_amount) {
                $query->where('stock_count', '<=', $threshold_amount);
            })
            ->select('id')
            ->get();

        $products_id = !empty($products) ? $products->pluck('id')->toArray() : [];

        $every_filtered_product_id = array_unique(array_merge($inventory_product_items_id, $products_id));
        $all_products = Product::with("inventory","inventoryDetail")
            ->whereIn('id', $every_filtered_product_id)
            ->select('id', 'name', 'is_inventory_warn_able')
            ->get();

        foreach ($all_products as $item) {
            $inventory = $item?->inventory?->stock_count;
            $variant = $item->inventoryDetail->where('stock_count', '<=', $threshold_amount)->first();
            $variant = !empty($variant) ? $variant->stock_count : [];

            $stock = min($inventory, $variant);
            $item->stock = $stock;
        }
    }
}
