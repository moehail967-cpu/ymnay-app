<?php

namespace App\Theme\ViewComposers;

use App\Theme\Shop\Product;
use App\Theme\Shop\ProductCategory;
use Illuminate\View\View;

/**
 * Registered for theme::frontend.shop.* views.
 * Wraps raw product/category objects into typed theme API objects.
 */
class ShopComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();

        // ── Shop product list ($products) → $theme_products ───────────────────
        if (isset($data['products'])) {
            $raw = $data['products'];
            $items = ($raw instanceof \Illuminate\Pagination\AbstractPaginator)
                ? $raw->getCollection()
                : collect($raw);
            $view->with('theme_products', $items->filter(fn($p) => is_object($p))->map(fn($p) => new Product($p)));
        }

        // ── Single product ($product) → $theme_product ────────────────────────
        if (isset($data['product'])) {
            $view->with('theme_product', new Product($data['product']));
        }

        // ── Shop sidebar categories ($categories) → $theme_shop_categories ────
        if (isset($data['categories'])) {
            $view->with('theme_shop_categories', collect($data['categories'])->map(fn($c) => new ProductCategory($c)));
        }

        // ── Related products ($related_products) → $theme_related_products ─────
        if (isset($data['related_products'])) {
            $view->with('theme_related_products', collect($data['related_products'])->map(fn($p) => new Product($p)));
        }
    }
}
