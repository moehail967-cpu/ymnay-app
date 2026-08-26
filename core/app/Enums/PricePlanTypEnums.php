<?php

namespace App\Enums;

use ReflectionClass;

class PricePlanTypEnums
{
    const MONTHLY = 0;
    const YEARLY = 1;
    const LIFETIME = 2;

    public static function getText(int $const)
    {
        foreach (self::getPricePlanTypeList() as $index => $item) {
            if ($const == $index) {
                return __(ucwords(strtolower($item)));
            }
        }
    }

    private static function getAttributes(): array
    {
        $reflect = new ReflectionClass(__CLASS__);
        return $reflect->getConstants() ?? [];
    }

    public static function getPricePlanTypeList(): array
    {
        $valueArr = [];
        foreach (self::getAttributes() as $index => $attribute) {
            $valueArr[$attribute] = __(ucwords(strtolower($index)));
        }

        return $valueArr;
    }

    public static function getFeatureList()
    {
        $all_features = [
            'products' => __('products'),
            'pages' => __('pages'),
            'blog' => __('blog'),
            'storage' => __('storage'),
            'inventory' => __('inventory'),
            'campaign' => __('campaign'),
            'coupon' => __('coupon'),
            'digital_product' => __('digital product'),
            'custom_domain' => __('custom domain'),
            'newsletter' => __('newsletter'),
            'testimonial' => __('testimonial'),
            'app_api' => __('app api')
        ];

        return apply_filters('nazmart:price_plan_features', $all_features);
    }
}
