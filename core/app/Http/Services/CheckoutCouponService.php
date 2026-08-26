<?php

namespace App\Http\Services;

use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Modules\CouponManage\Entities\CouponUsage;
use Modules\CouponManage\Entities\ProductCoupon;
use Modules\Product\Entities\Product;

class CheckoutCouponService
{
    private static function get_category_product_ids($products, $category, $discount_on): array
    {
        $prd_ids = [];

        foreach($products as $product){
            if ($product?->category?->id === $category && $discount_on == "category"){
                $prd_ids[] = $product->id;
            }elseif ($product?->subCategory?->id === $category && $discount_on == "subcategory"){
                $prd_ids[] = $product->id;
            }elseif ($discount_on == "childcategory"){
                foreach($product?->childCategory as $childCategory){
                    if (in_array($childCategory->id,$category)){
                        $prd_ids[] = $product->id;
                    }
                }
            }
        }

        return $prd_ids;
    }

    private function get_sub_category_product_ids($products, $childCategories){
        $prd_ids = [];

        foreach($products as $product){
            if (in_array($product?->childCategory?->pluck("id"), $childCategories)){
                $prd_ids[] = $product->id;
            }
        }

        return $prd_ids;
    }

    /**
     * Calculate prices of the given product given products
     * @param array $product_ids
     * @param  $products
     * @return float|int
     */
    public static function getCartItemTotalPrice(array $product_ids, $products): float|int
    {
        // now first of all need to get all cart items and take only available product for this coupon
        $cart_items = Cart::content();
        $total_price = 0;
        foreach($cart_items as $item){
            if (in_array($item->id,$product_ids)){
                $total_price += $item->price * $item->qty;
            }
        }

        return $total_price;
    }

    /**
     * Subtract coupon from total amount if coupon is applied and available
     * @param $request
     * @param $subtotal (Subtotal + Tax)
     * @param $products - All products' DB collection from cart
     * @param string $return_type
     * @param null $shippingAmount
     * @param null $purpose
     * @return float|int|mixed|null
     */
    public static function calculateCoupon(object $request, $subtotal, $products, string $return_type = 'TOTAL', $shippingAmount = null, $purpose = null): mixed
    {
        if (empty($request->coupon)) {
            return [
                'success' => false,
                'message' => 'Coupon is required.',
                'discount' => 0
            ];
        }
        $total = $subtotal;
        $coupon_code = $request->coupon;
        $coupon_amount = null;
        $coupon_type = null;
        $discount_total = 0;
        // if coupon input given
        if ($coupon_code) {
            $coupon = ProductCoupon::where('code', $coupon_code)->where('status', 'publish')->first();
            if (!$coupon) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired coupon code.',
                    'discount' => 0
                ];
            }
            // if expired
            if ($coupon && !Carbon::parse($coupon->expire_date)->greaterThan(\Carbon\Carbon::today())) {
                return [
                    'success' => false,
                    'message' => 'This coupon has expired.',
                    'discount' => 0
                ];
            }
            $coupon_amount = $coupon->discount;
            $coupon_type = $coupon->discount_type;
        }
        $discount_on = $coupon->discount_on;

        //  Check usage_limit_per_coupon
        if (!is_null($coupon->usage_limit_per_coupon)) {
            $totalUsages = CouponUsage::where('coupon_id', $coupon->id)->count();
            if ($totalUsages >= $coupon->usage_limit_per_coupon) {
                return [
                    'success' => false,
                    'message' => 'This coupon has reached its maximum usage limit.',
                    'discount' => 0
                ];
            }
        }
        // usage_limit_per_user
        if (!is_null($coupon->usage_limit_per_user) && auth()->check()) {
            $userUsageCount = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', auth()->id())
                ->count();
            if ($userUsageCount >= $coupon->usage_limit_per_user) {
                return [
                    'success' => false,
                    'message' => 'You have already used this coupon the maximum number of times allowed.',
                    'discount' => 0
                ];
            }
        }
        $product_ids = [];
        if ($discount_on == 'all') {
            $subtotal = (float) $subtotal;
            $product_ids = Cart::content()->pluck('id')->toArray();
        } elseif (in_array($discount_on, ['category', 'subcategory'])) {
            $categories = (array) json_decode($coupon->discount_on_details);
            $category = (int) $categories[0];
            $product_ids = self::get_category_product_ids($products, $category, $discount_on);
            if (count($product_ids) < 1) return [
                'success' => false,
                'message' => 'This coupon is not applicable to this category/subcategory products in your cart.',
                'discount' => 0
            ];
            $subtotal = self::getCartItemTotalPrice($product_ids, $products);
        } elseif ($discount_on == 'childcategory') {
            $categories = (array) json_decode($coupon->discount_on_details);
            $product_ids = self::get_category_product_ids($products, $categories, $discount_on);
            if (count($product_ids) < 1) return [
                'success' => false,
                'message' => 'This coupon is not applicable to this child category products in your cart.',
                'discount' => 0
            ];
            $subtotal = self::getCartItemTotalPrice($product_ids, $products);
        } elseif ($discount_on == 'product') {
            $product_ids = (array) json_decode($coupon->discount_on_details);
            if (count($product_ids) < 1) return [
                'success' => false,
                'message' => 'This coupon is not applicable to this products in your cart.',
                'discount' => 0
            ];
            $subtotal = self::getCartItemTotalPrice($product_ids, $products);
        }

        $global_cart_items = Cart::content();
        $global_quantity = $global_cart_items->sum('qty');
        $global_subtotal = $global_cart_items->sum(function ($item) {
            return $item->price * $item->qty;
        });

        if (!is_null($coupon->minimum_quantity) && $global_quantity < $coupon->minimum_quantity) {
            return [
                'success' => false,
                'message' => __('You must have at least').' '. $coupon->minimum_quantity . ' items in your cart to use this coupon.',
                'discount' => 0
            ];
        }

        if (!is_null($coupon->minimum_spend) && $global_subtotal < $coupon->minimum_spend) {
            return [
                'success' => false,
                'message' => 'This coupon requires a minimum subtotal of '.amount_with_currency_symbol($coupon->minimum_spend),
                'discount' => 0
            ];
        }

        if (!is_null($coupon->maximum_spend) && $global_subtotal > $coupon->maximum_spend) {
            return [
                'success' => false,
                'message' => 'This coupon is only valid for orders with a subtotal up to '.amount_with_currency_symbol($coupon->maximum_spend, 2),
                'discount' => 0
            ];
        }

        $subtotal = str_replace(',', '', $subtotal);
        if ($coupon_type === 'percentage') {
            $discount_total = $subtotal * ($coupon_amount / 100);
        } elseif ($coupon_type === 'amount') {
            $discount_total = $coupon_amount;
        }
        $final_total = max(0, $subtotal - $discount_total);
        return [
            'success' => true,
            'message' => __('Coupon applied successfully!'),
            'discount' => round($discount_total, 2),
            'total' => round($final_total, 2),
            'coupon_code' => $coupon->code,
            'coupon_id' => $coupon->id,
        ];
    }
}
