<?php

/**
 * Theme Frontend Helpers
 *
 * Stable contracts for theme developers. If core renames a route, changes an
 * auth guard, or restructures internals, only this file needs to change —
 * not every theme blade file.
 *
 * Organised into sections:
 *   - Navigation / Common
 *   - Shop
 *   - Blog
 *   - Auth / User
 *   - Formatting / Rendering
 */

// ── Navigation / Common ───────────────────────────────────────────────────────

if (!function_exists('theme_home_url')) {
    /** Homepage URL */
    function theme_home_url(): string
    {
        return route('tenant.frontend.homepage');
    }
}

if (!function_exists('theme_product_url')) {
    /** URL for a product or any dynamic-slug page */
    function theme_product_url(string $slug): string
    {
        return dynamicRoute($slug);
    }
}

if (!function_exists('theme_csrf')) {
    /** CSRF token string */
    function theme_csrf(): string
    {
        return csrf_token();
    }
}

if (!function_exists('theme_csrf_field')) {
    /** CSRF hidden input field (safe to echo directly) */
    function theme_csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

// ── Auth / User ───────────────────────────────────────────────────────────────

if (!function_exists('theme_auth')) {
    /** Returns the web auth guard (call ->check(), ->user(), ->id(), etc.) */
    function theme_auth(): \Illuminate\Contracts\Auth\Guard
    {
        return auth('web');
    }
}

if (!function_exists('theme_is_logged_in')) {
    /** Whether the front-end user is authenticated */
    function theme_is_logged_in(): bool
    {
        return auth('web')->check();
    }
}

if (!function_exists('theme_current_user')) {
    /** Authenticated front-end user model, or null */
    function theme_current_user(): ?\App\Models\User
    {
        return auth('web')->user();
    }
}

if (!function_exists('theme_login_url')) {
    /** Login page URL */
    function theme_login_url(): string
    {
        return route('tenant.user.login');
    }
}

if (!function_exists('theme_register_url')) {
    /** Registration page URL */
    function theme_register_url(): string
    {
        return route('tenant.user.register');
    }
}

if (!function_exists('theme_logout_url')) {
    /** Logout URL */
    function theme_logout_url(): string
    {
        return route('tenant.user.logout');
    }
}

if (!function_exists('theme_user_dashboard_url')) {
    /** Authenticated user's dashboard URL */
    function theme_user_dashboard_url(): string
    {
        return route('tenant.user.home');
    }
}

if (!function_exists('theme_forget_password_url')) {
    function theme_forget_password_url(): string
    {
        return route('tenant.user.forget.password');
    }
}

if (!function_exists('theme_otp_login_url')) {
    function theme_otp_login_url(): string
    {
        return route('tenant.user.login.otp');
    }
}

if (!function_exists('theme_register_store_url')) {
    function theme_register_store_url(): string
    {
        return route('tenant.user.register.store');
    }
}

if (!function_exists('theme_state_search_url')) {
    function theme_state_search_url(): string
    {
        return route('tenant.state.search');
    }
}

if (!function_exists('theme_city_search_url')) {
    function theme_city_search_url(): string
    {
        return route('tenant.city.search');
    }
}

// ── Shop ──────────────────────────────────────────────────────────────────────

if (!function_exists('theme_shop_url')) {
    /** Main shop listing page URL */
    function theme_shop_url(): string
    {
        $slug = get_page_slug(get_static_option('shop_page'), 'shop_page');
        return dynamicRoute($slug);
    }
}

if (!function_exists('theme_cart_url')) {
    /** Cart page URL */
    function theme_cart_url(): string
    {
        return route('tenant.shop.cart');
    }
}

if (!function_exists('theme_wishlist_url')) {
    /** Wishlist page URL */
    function theme_wishlist_url(): string
    {
        return route('tenant.shop.wishlist.page');
    }
}

if (!function_exists('theme_checkout_url')) {
    /** Checkout page URL */
    function theme_checkout_url(): string
    {
        return route('tenant.shop.checkout');
    }
}

if (!function_exists('theme_order_track_url')) {
    /** Order tracking page URL */
    function theme_order_track_url(): string
    {
        return route('tenant.shop.order.track');
    }
}

if (!function_exists('theme_add_to_cart_url')) {
    /** AJAX endpoint — add product to cart */
    function theme_add_to_cart_url(): string
    {
        return route('tenant.shop.product.add.to.cart.ajax');
    }
}

if (!function_exists('theme_add_to_wishlist_url')) {
    /** AJAX endpoint — add product to wishlist */
    function theme_add_to_wishlist_url(): string
    {
        return route('tenant.shop.product.add.to.wishlist.ajax');
    }
}

if (!function_exists('theme_buy_now_url')) {
    /** AJAX endpoint — buy now (single product) */
    function theme_buy_now_url(): string
    {
        return route('tenant.shop.product.buy.now.ajax');
    }
}

if (!function_exists('theme_quick_view_url')) {
    /** AJAX endpoint — product quick view */
    function theme_quick_view_url(): string
    {
        return route('tenant.shop.quick.view');
    }
}

if (!function_exists('theme_product_review_url')) {
    /** AJAX endpoint — submit product review */
    function theme_product_review_url(): string
    {
        return route('tenant.shop.product.review');
    }
}

if (!function_exists('theme_product_review_more_url')) {
    /** AJAX endpoint — load more product reviews */
    function theme_product_review_more_url(): string
    {
        return route('tenant.shop.product.review.more.ajax');
    }
}

if (!function_exists('theme_add_to_compare_url')) {
    /** AJAX endpoint — add product to compare list */
    function theme_add_to_compare_url(): string
    {
        return route('tenant.shop.product.add.to.compare.ajax');
    }
}

if (!function_exists('theme_cart_update_url')) {
    /** AJAX endpoint — update cart item quantity */
    function theme_cart_update_url(): string
    {
        return route('tenant.shop.cart.update.ajax');
    }
}

if (!function_exists('theme_cart_remove_url')) {
    /** AJAX endpoint — remove a product from cart */
    function theme_cart_remove_url(): string
    {
        return route('tenant.shop.cart.remove.product.ajax');
    }
}

if (!function_exists('theme_cart_clear_url')) {
    /** AJAX endpoint — clear the entire cart */
    function theme_cart_clear_url(): string
    {
        return route('tenant.shop.cart.clear.ajax');
    }
}

if (!function_exists('theme_wishlist_remove_url')) {
    /** AJAX endpoint — remove a product from wishlist */
    function theme_wishlist_remove_url(): string
    {
        return route('tenant.shop.wishlist.remove.product.ajax');
    }
}

if (!function_exists('theme_wishlist_move_url')) {
    /** AJAX endpoint — move a wishlist item to cart */
    function theme_wishlist_move_url(): string
    {
        return route('tenant.shop.wishlist.move.product.ajax');
    }
}

if (!function_exists('theme_checkout_state_url')) {
    /** AJAX endpoint — fetch states for a given country at checkout */
    function theme_checkout_state_url(): string
    {
        return route('tenant.shop.checkout.state.ajax');
    }
}

if (!function_exists('theme_ajax_login_url')) {
    function theme_ajax_login_url(): string
    {
        return route('tenant.user.ajax.login');
    }
}

if (!function_exists('theme_checkout_shipping_ajax_url')) {
    function theme_checkout_shipping_ajax_url(): string
    {
        return route('tenant.shop.checkout.sync-product-shipping.ajax');
    }
}

if (!function_exists('theme_checkout_total_ajax_url')) {
    function theme_checkout_total_ajax_url(): string
    {
        return route('tenant.shop.checkout.sync-product-total.ajax');
    }
}

if (!function_exists('theme_checkout_coupon_ajax_url')) {
    function theme_checkout_coupon_ajax_url(): string
    {
        return route('tenant.shop.checkout.sync-product-coupon.ajax');
    }
}

if (!function_exists('theme_compare_remove_url')) {
    function theme_compare_remove_url(): string
    {
        return route('tenant.shop.compare.product.remove');
    }
}

if (!function_exists('theme_compare_page_url')) {
    function theme_compare_page_url(): string
    {
        return route('tenant.shop.compare.product.page');
    }
}

if (!function_exists('theme_apply_coupon_url')) {
    /** AJAX endpoint — apply coupon code at checkout */
    function theme_apply_coupon_url(): string
    {
        return route('tenant.cart.apply.coupon');
    }
}

if (!function_exists('theme_coupon_apply_url')) {
    /** AJAX endpoint — apply coupon (alias for theme_apply_coupon_url) */
    function theme_coupon_apply_url(): string
    {
        return route('tenant.cart.apply.coupon');
    }
}

if (!function_exists('theme_cart_store_url')) {
    /** AJAX endpoint — add product to cart (alias for theme_add_to_cart_url) */
    function theme_cart_store_url(): string
    {
        return route('tenant.shop.product.add.to.cart.ajax');
    }
}

// ── Blog ──────────────────────────────────────────────────────────────────────

if (!function_exists('theme_blog_url')) {
    /** URL of the blog listing page */
    function theme_blog_url(): string
    {
        return route('tenant.frontend.blog.index');
    }
}

if (!function_exists('theme_blog_search_url')) {
    /** Blog search form action URL */
    function theme_blog_search_url(): string
    {
        return route('tenant.frontend.blog.search.page');
    }
}

if (!function_exists('theme_blog_comment_store_url')) {
    /** AJAX endpoint — submit a blog comment */
    function theme_blog_comment_store_url(): string
    {
        return route(route_prefix() . 'frontend.blog.comment.store');
    }
}

if (!function_exists('theme_blog_load_comments_url')) {
    /** AJAX endpoint — load more blog comments */
    function theme_blog_load_comments_url(): string
    {
        return route(route_prefix() . 'frontend.load.blog.comment.data');
    }
}

if (!function_exists('theme_blog_tag_url')) {
    /** URL for a blog tag archive */
    function theme_blog_tag_url(string $tag): string
    {
        return tenant_blog_tag_route(\Illuminate\Support\Str::slug($tag));
    }
}

if (!function_exists('theme_blog_category_url')) {
    /** URL for a blog category archive */
    function theme_blog_category_url(string $slug): string
    {
        return dynamicRoute($slug);
    }
}

// ── Formatting / Rendering ────────────────────────────────────────────────────

if (!function_exists('theme_price')) {
    /** Format a number as a price with the tenant's currency symbol */
    function theme_price(float|int|string $amount): string
    {
        return amount_with_currency_symbol($amount);
    }
}

if (!function_exists('theme_product_image')) {
    /** Return the image URL for a product's attachment ID, or null */
    function theme_product_image(int|null $attachmentId, string $size = 'grid'): ?string
    {
        $data = get_attachment_image_by_id($attachmentId, $size);
        return !empty($data) ? $data['img_url'] : null;
    }
}

if (!function_exists('theme_star_rating')) {
    /** Render star rating markup with review count for a product */
    function theme_star_rating(object $product): string
    {
        return render_product_star_rating_markup_with_count($product);
    }
}

if (!function_exists('theme_product_price')) {
    /**
     * Return product price data array:
     *   regular_price, sale_price, discount, campaign_name
     */
    function theme_product_price(object $product): array
    {
        return get_product_dynamic_price($product);
    }
}

if (!function_exists('theme_share_links')) {
    /**
     * Render social share links for a URL + title.
     * Mirrors single_post_share() used in blog single pages.
     */
    function theme_share_links(string $url, string $title, mixed $image = null): string
    {
        return single_post_share($url, $title, $image);
    }
}

if (!function_exists('theme_static_option')) {
    /** Retrieve a site setting value by key */
    function theme_static_option(string $key): mixed
    {
        return get_static_option($key);
    }
}

if (!function_exists('theme_render_image')) {
    /** Render an <img> tag for an attachment ID */
    function theme_render_image(int|null $attachmentId, string $class = '', string $size = '', bool $lazyload = true): string
    {
        return render_image_markup_by_attachment_id($attachmentId, $class, $size, $lazyload);
    }
}

// ── Page Context Helpers (WordPress-style template tags) ──────────────────────
//
// These read the typed wrapper objects that BlogComposer/ShopComposer inject
// into views. Use them inside any theme template instead of raw model variables.
//
// Blog list:    $theme_blogs, theme_blogs()
// Blog single:  $theme_post, theme_post()
// Blog sidebar: $theme_blog_categories, $theme_blog_tags
// Shop:         $theme_products, theme_products()
// Product:      $theme_product, theme_product()
// Shop sidebar: $theme_shop_categories

if (!function_exists('theme_blogs')) {
    /**
     * Current page blog posts as Post wrapper objects.
     * Available in blog list, category, search, and tag views.
     *
     * @return \Illuminate\Support\Collection<\App\Theme\Blog\Post>
     */
    function theme_blogs(): \Illuminate\Support\Collection
    {
        return view()->shared('theme_blogs') ?? collect();
    }
}

if (!function_exists('theme_blogs_paginator')) {
    /** Raw LengthAwarePaginator for the current blog listing page */
    function theme_blogs_paginator(): mixed
    {
        return view()->shared('theme_blogs_paginator');
    }
}

if (!function_exists('theme_post')) {
    /**
     * Current single blog post as a Post wrapper.
     * Available in blog single (detail) views.
     */
    function theme_post(): ?\App\Theme\Blog\Post
    {
        return view()->shared('theme_post');
    }
}

if (!function_exists('theme_comments')) {
    /**
     * Top-level comments for the current blog post.
     *
     * @return \Illuminate\Support\Collection<\App\Theme\Blog\Comment>
     */
    function theme_comments(): \Illuminate\Support\Collection
    {
        return view()->shared('theme_comments') ?? collect();
    }
}

if (!function_exists('theme_comments_count')) {
    /** Total comment count for the current blog post */
    function theme_comments_count(): int
    {
        return view()->shared('theme_comments_count') ?? 0;
    }
}

if (!function_exists('theme_blog_categories')) {
    /**
     * Blog sidebar categories as Category wrapper objects.
     *
     * @return \Illuminate\Support\Collection<\App\Theme\Blog\Category>
     */
    function theme_blog_categories(): \Illuminate\Support\Collection
    {
        return view()->shared('theme_blog_categories') ?? collect();
    }
}

if (!function_exists('theme_blog_tags')) {
    /**
     * Blog sidebar tags as Tag wrapper objects.
     *
     * @return \Illuminate\Support\Collection<\App\Theme\Blog\Tag>
     */
    function theme_blog_tags(): \Illuminate\Support\Collection
    {
        return view()->shared('theme_blog_tags') ?? collect();
    }
}

if (!function_exists('theme_products')) {
    /**
     * Current page product list as Product wrapper objects.
     * Available in shop listing views.
     *
     * @return \Illuminate\Support\Collection<\App\Theme\Shop\Product>
     */
    function theme_products(): \Illuminate\Support\Collection
    {
        return view()->shared('theme_products') ?? collect();
    }
}

if (!function_exists('theme_product')) {
    /**
     * Current product detail as a Product wrapper.
     * Available in product detail views.
     */
    function theme_product(): ?\App\Theme\Shop\Product
    {
        return view()->shared('theme_product');
    }
}

if (!function_exists('theme_related_products')) {
    /**
     * Related products on the product detail page.
     *
     * @return \Illuminate\Support\Collection<\App\Theme\Shop\Product>
     */
    function theme_related_products(): \Illuminate\Support\Collection
    {
        return view()->shared('theme_related_products') ?? collect();
    }
}

if (!function_exists('theme_shop_categories')) {
    /**
     * Shop sidebar categories as ProductCategory wrapper objects.
     *
     * @return \Illuminate\Support\Collection<\App\Theme\Shop\ProductCategory>
     */
    function theme_shop_categories(): \Illuminate\Support\Collection
    {
        return view()->shared('theme_shop_categories') ?? collect();
    }
}

// ── Site Identity ─────────────────────────────────────────────────────────────

if (!function_exists('theme_logo_url')) {
    /**
     * URL of the site logo image, or null if not set.
     * site_logo stores an attachment ID — resolved via get_attachment_image_by_id().
     */
    function theme_logo_url(): ?string
    {
        $id   = get_static_option('site_logo');
        $data = $id ? get_attachment_image_by_id($id, 'full') : null;
        return !empty($data['img_url']) ? $data['img_url'] : null;
    }
}

if (!function_exists('theme_white_logo_url')) {
    /** URL of the white/inverted logo (falls back to main logo) */
    function theme_white_logo_url(): ?string
    {
        $id   = get_static_option('site_white_logo') ?: get_static_option('site_logo');
        $data = $id ? get_attachment_image_by_id($id, 'full') : null;
        return !empty($data['img_url']) ? $data['img_url'] : null;
    }
}

if (!function_exists('theme_site_name')) {
    /** Site/store name */
    function theme_site_name(): string
    {
        return (string) get_static_option('site_title');
    }
}

if (!function_exists('theme_site_tagline')) {
    /** Site tagline */
    function theme_site_tagline(): string
    {
        return (string) get_static_option('site_tag_line');
    }
}

// ── Auth Page Content ─────────────────────────────────────────────────────────

if (!function_exists('theme_login_page_headline')) {
    /**
     * Main heading shown on the login page.
     * Admin key: login_page_headline  (General Settings → Login & Register Page)
     * Falls back to "{Site Name} Account".
     */
    function theme_login_page_headline(): string
    {
        $v = get_static_option('login_page_headline');
        return $v ? (string) $v : theme_site_name() . ' ' . __('Account');
    }
}

if (!function_exists('theme_login_page_subtitle')) {
    /**
     * Welcome sub-text shown below the Sign In heading.
     * Admin key: login_page_subtitle  (General Settings → Login & Register Page)
     */
    function theme_login_page_subtitle(): string
    {
        $v = get_static_option('login_page_subtitle');
        return $v ? (string) $v : __('Welcome back. Sign in to access your account.');
    }
}

if (!function_exists('theme_login_page_features')) {
    /**
     * Array of feature lines shown on the "Create Account" panel of the login page.
     * Admin keys: login_page_feature_1 / _2 / _3  (General Settings → Login & Register Page)
     * Returns only non-empty entries; falls back to 3 default bullets.
     *
     * @return string[]
     */
    function theme_login_page_features(): array
    {
        $f1 = get_static_option('login_page_feature_1');
        $f2 = get_static_option('login_page_feature_2');
        $f3 = get_static_option('login_page_feature_3');

        $raw = array_filter([$f1, $f2, $f3]);

        if (!empty($raw)) {
            return array_values($raw);
        }

        return [
            __('Member-only pricing & exclusive deals'),
            __('Fast dispatch & tracked delivery'),
            __('Full order history & secure account'),
        ];
    }
}

if (!function_exists('theme_register_page_headline')) {
    /**
     * Main heading shown on the register page.
     * Admin key: register_page_headline  (General Settings → Login & Register Page)
     * Falls back to "Join {Site Name}".
     */
    function theme_register_page_headline(): string
    {
        $v = get_static_option('register_page_headline');
        return $v ? (string) $v : __('Join') . ' ' . theme_site_name();
    }
}

if (!function_exists('theme_register_page_subtitle')) {
    /**
     * Subtitle shown below the register page heading.
     * Admin key: register_page_subtitle  (General Settings → Login & Register Page)
     */
    function theme_register_page_subtitle(): string
    {
        $v = get_static_option('register_page_subtitle');
        return $v ? (string) $v : __('Get access to exclusive deals, order tracking, and member benefits.');
    }
}

if (!function_exists('theme_register_page_promo_text')) {
    /**
     * Short promotional highlight shown on the register page.
     * Admin key: register_page_promo_text  (General Settings → Login & Register Page)
     */
    function theme_register_page_promo_text(): string
    {
        $v = get_static_option('register_page_promo_text');
        return $v ? (string) $v : __('Members get exclusive pricing, fast delivery, and a secure account.');
    }
}

if (!function_exists('theme_login_page')) {
    /**
     * Convenience — returns all login page content as an array.
     * Keys: headline, subtitle, features (array).
     */
    function theme_login_page(): array
    {
        return [
            'headline'  => theme_login_page_headline(),
            'subtitle'  => theme_login_page_subtitle(),
            'features'  => theme_login_page_features(),
        ];
    }
}

if (!function_exists('theme_register_page')) {
    /**
     * Convenience — returns all register page content as an array.
     * Keys: headline, subtitle, promo_text.
     */
    function theme_register_page(): array
    {
        return [
            'headline'   => theme_register_page_headline(),
            'subtitle'   => theme_register_page_subtitle(),
            'promo_text' => theme_register_page_promo_text(),
        ];
    }
}

if (!function_exists('theme_logo_html')) {
    /**
     * Render a logo <a><img></a> linked to the homepage.
     * Falls back to site name as text when no logo attachment exists.
     *
     * @param  string $link_class      CSS class(es) on the <a> tag
     * @param  string $img_class       CSS class(es) on the <img> tag
     * @param  string $fallback_class  CSS class(es) on the fallback <span>
     */
    function theme_logo_html(string $link_class = '', string $img_class = '', string $fallback_class = ''): string
    {
        $url  = theme_home_url();
        $name = e(theme_site_name());
        $lc   = $link_class ? ' class="' . e($link_class) . '"' : '';
        $id   = get_static_option('site_logo');

        if ($id) {
            $ic = $img_class ? ' class="' . e($img_class) . '"' : '';
            // render_image_markup_by_attachment_id handles srcset, lazy-load, alt correctly
            $img = render_image_markup_by_attachment_id($id, $img_class, 'full');
            return '<a href="' . $url . '"' . $lc . '>' . $img . '</a>';
        }

        $fc = $fallback_class ? ' class="' . e($fallback_class) . '"' : '';
        return '<a href="' . $url . '"' . $lc . '><span' . $fc . '>' . $name . '</span></a>';
    }
}

// ── Cart / Wishlist ───────────────────────────────────────────────────────────

if (!function_exists('theme_cart_count')) {
    /**
     * Total number of items (sum of quantities) currently in the cart.
     * Use this for the navbar badge.
     *
     * Usage:  {{ theme_cart_count() }}
     */
    function theme_cart_count(): int
    {
        return (int) \Gloudemans\Shoppingcart\Facades\Cart::instance('default')->count();
    }
}

if (!function_exists('theme_cart_items')) {
    /**
     * All items currently in the shopping cart.
     *
     * Returns the cart collection — each item has: id, name, qty, price,
     * rowId, options (image, color_name, size_name, variant_id, …).
     *
     * Use this on any page that needs to display cart contents without
     * relying on a $cart_data variable from the controller.
     *
     * Usage:
     *   @foreach(theme_cart_items() as $item)
     *       {{ $item->name }} × {{ $item->qty }}
     *   @endforeach
     */
    function theme_cart_items(): \Illuminate\Support\Collection
    {
        return \Gloudemans\Shoppingcart\Facades\Cart::instance('default')->content();
    }
}

if (!function_exists('theme_wishlist_count')) {
    /** Total number of items (sum of quantities) currently in the wishlist */
    function theme_wishlist_count(): int
    {
        return (int) \Gloudemans\Shoppingcart\Facades\Cart::instance('wishlist')->count();
    }
}

if (!function_exists('theme_product_tax')) {
    /**
     * Returns the applicable product tax rate (percentage) for the current session.
     *
     * On initial page load no country/state is selected yet, so this returns 0.
     * The checkout page updates tax dynamically via AJAX when the user picks a location.
     *
     * Usage:
     *   $product_tax = theme_product_tax();
     */
    function theme_product_tax(): float
    {
        return 0.0;
    }
}

// ── Navbar Icon HTML helpers ──────────────────────────────────────────────────
//
// These render semantic, accessible icon links with optional badge counts.
// Pass any CSS class string for your theme's icon button style.
// All icons use the Material Design Icons (mdi) set loaded by the platform.

if (!function_exists('theme_cart_icon_html')) {
    /**
     * Render the cart icon link with an optional badge showing item count.
     *
     * @param  string $link_class    CSS class(es) on the <a> tag
     * @param  string $badge_class   CSS class(es) on the badge <span>
     * @param  string $icon_class    MDI icon class, e.g. "mdi mdi-cart-outline"
     */
    function theme_cart_icon_html(
        string $link_class  = '',
        string $badge_class = '',
        string $icon_class  = 'las la-shopping-cart'
    ): string {
        $url   = theme_cart_url();
        $count = theme_cart_count();
        $lc    = $link_class ? ' class="' . e($link_class) . '"' : '';
        $badgeClasses = trim('theme-cart-count ' . ($badge_class ? e($badge_class) : ''));
        $badge = '<span class="' . $badgeClasses . '"'
               . ($count > 0 ? '' : ' style="display:none"') . '>'
               . ($count > 99 ? '99+' : $count) . '</span>';
        return '<a href="' . $url . '"' . $lc . ' aria-label="' . __('Cart') . '">'
             . '<i class="' . e($icon_class) . '"></i>' . $badge
             . '</a>';
    }
}

if (!function_exists('theme_wishlist_icon_html')) {
    /**
     * Render the wishlist icon link with an optional badge showing item count.
     *
     * @param  string $link_class    CSS class(es) on the <a> tag
     * @param  string $badge_class   CSS class(es) on the badge <span>
     * @param  string $icon_class    MDI icon class
     */
    function theme_wishlist_icon_html(
        string $link_class  = '',
        string $badge_class = '',
        string $icon_class  = 'las la-heart'
    ): string {
        $url   = theme_wishlist_url();
        $count = theme_wishlist_count();
        $lc    = $link_class  ? ' class="' . e($link_class)  . '"' : '';
        $bc    = $badge_class ? ' class="' . e($badge_class) . '"' : '';
        $badgeClasses = trim('theme-wishlist-count ' . ($badge_class ? e($badge_class) : ''));
        $badge = '<span class="' . $badgeClasses . '" style="' . ($count > 0 ? '' : 'display:none') . '">'
               . ($count > 99 ? '99+' : $count) . '</span>';
        return '<a href="' . $url . '"' . $lc . ' aria-label="' . __('Wishlist') . '">'
             . '<i class="' . e($icon_class) . '"></i>' . $badge
             . '</a>';
    }
}

if (!function_exists('theme_account_icon_html')) {
    /**
     * Render the account icon link.
     * Points to the user dashboard when logged in, login page otherwise.
     *
     * @param  string $link_class   CSS class(es) on the <a> tag
     * @param  string $icon_class   MDI icon class
     */
    function theme_account_icon_html(
        string $link_class = '',
        string $icon_class = 'las la-user'
    ): string {
        $url   = theme_is_logged_in() ? theme_user_dashboard_url() : theme_login_url();
        $label = theme_is_logged_in() ? __('Account') : __('Login');
        $lc    = $link_class ? ' class="' . e($link_class) . '"' : '';
        return '<a href="' . $url . '"' . $lc . ' aria-label="' . $label . '">'
             . '<i class="' . e($icon_class) . '"></i>'
             . '</a>';
    }
}

// ── Navigation Menu & Footer Widgets ─────────────────────────────────────────

if (!function_exists('theme_primary_menu_id')) {
    /** ID of the menu marked as default in the admin panel, or null */
    function theme_primary_menu_id(): ?int
    {
        static $id;
        if ($id === null) {
            $id = \App\Models\Menu::where('status', 'default')->first()?->id;
        }
        return $id;
    }
}

if (!function_exists('theme_nav_menu')) {
    /**
     * Render the primary navigation menu as a <ul class="theme-nav-menu"> element.
     *
     * The wrapping <ul> with class "theme-nav-menu" is always included so
     * default dropdown CSS (custom-style.css) works without any per-theme code.
     * Themes scope additional styles to their own nav container.
     *
     * Pass a menu ID to override; omit to use the admin-panel default.
     * $listClass and $itemClass let themes apply their own CSS classes.
     */
    function theme_nav_menu(?int $menuId = null, string $listClass = 'theme-nav-menu', string $itemClass = ''): string
    {
        $id = $menuId ?? theme_primary_menu_id();
        if (!$id) return '';
        $items = (string) render_frontend_menu($id);
        if (empty(trim($items))) return '';
        $class = trim('theme-nav-menu ' . $listClass);
        return '<ul class="' . e($class) . '">' . $items . '</ul>';
    }
}

if (!function_exists('theme_mobile_nav_menu')) {
    /**
     * Render the primary navigation menu as a mobile-friendly markup.
     * Pass a menu ID to override; omit to use the admin-panel default.
     */
    function theme_mobile_nav_menu(?int $menuId = null): string
    {
        $id = $menuId ?? theme_primary_menu_id();
        if (!$id) return '';
        return (string) render_frontend_mobile_menu($id);
    }
}

if (!function_exists('theme_footer_widgets')) {
    /**
     * Render a footer widget area by location name.
     *
     * Common locations: 'footer', 'footer_bottom_left', 'footer_bottom_right', 'shop_footer'
     *
     * @param  string $location  Widget area location slug
     * @param  bool   $column    Whether to wrap each widget in a column (Bootstrap col)
     */
    function theme_footer_widgets(string $location = 'footer', bool $column = true): string
    {
        return (string) render_frontend_sidebar($location, ['column' => $column]);
    }
}

if (!function_exists('theme_footer_copyright')) {
    /** Render the footer copyright text (supports {copy} and {year} tokens) */
    function theme_footer_copyright(): string
    {
        return (string) get_footer_copyright_text();
    }
}

if (!function_exists('theme_footer_menu_links')) {
    /**
     * Return the primary menu's top-level items as a flat array of ['url', 'label'] pairs,
     * or null if no menu is configured. Used by theme footers to render quick-link columns.
     *
     * @return array<int, array{url: string, label: string}>|null
     */
    function theme_footer_menu_links(?int $menuId = null): ?array
    {
        $id = $menuId ?? theme_primary_menu_id();
        if (!$id) return null;

        $menu = \App\Models\Menu::find($id);
        if (!$menu || empty($menu->content)) return null;

        $items = json_decode($menu->content, false);
        if (!is_array($items) || empty($items)) return null;

        $links = [];
        $defaultLang = app()->getLocale();

        foreach ($items as $item) {
            $item = (object) $item;
            $ptype = $item->ptype ?? '';

            if ($ptype === 'custom') {
                $url   = str_replace('@url', url('/'), $item->purl ?? '#');
                $label = $item->menulabel ?? $item->pname ?? '';
                if ($label) $links[] = ['url' => $url, 'label' => __($label)];

            } elseif ($ptype === 'static') {
                $slug  = get_static_option(str_replace('-', '_', $item->pslug ?? '') . '_page_slug') ?? '#';
                $name  = get_static_option(str_replace('-', '_', $item->pslug ?? '') . '_page_name') ?? ($item->pname ?? '');
                $label = $item->menulabel ?? $name;
                if ($label) $links[] = ['url' => url('/') . '/' . ltrim($slug, '/'), 'label' => __($label)];
            }
        }

        return empty($links) ? null : $links;
    }
}

// ── Product Action Button HTML helpers ───────────────────────────────────────
//
// These render the action buttons for product pages with the correct CSS classes
// that the platform's JS handlers (.add_to_cart_single_page, .but_now_single_page,
// .add_to_wishlist_single_page) listen to.
// Theme developers pass their own CSS class for styling; the platform class is
// always added automatically so the AJAX logic works.

if (!function_exists('theme_add_to_cart_btn_html')) {
    /**
     * Render the Add to Cart button.
     * The platform class "add_to_cart_single_page" is always included.
     *
     * @param  string $label      Button label (translatable)
     * @param  string $extra_class  Additional CSS classes for theme styling
     */
    function theme_add_to_cart_btn_html(string $label = '', string $extra_class = ''): string
    {
        $label = $label ?: __('Add to Cart');
        $cls   = trim('add_to_cart_single_page cart-loading ' . $extra_class);
        return '<a href="javascript:void(0)" class="' . e($cls) . '">' . e($label) . '</a>';
    }
}

if (!function_exists('theme_buy_now_btn_html')) {
    /**
     * Render the Buy Now button.
     * The platform class "but_now_single_page" is always included.
     */
    function theme_buy_now_btn_html(string $label = '', string $extra_class = ''): string
    {
        $label = $label ?: __('Buy Now');
        $cls   = trim('but_now_single_page cart-loading ' . $extra_class);
        return '<a href="javascript:void(0)" class="' . e($cls) . '">' . e($label) . '</a>';
    }
}

if (!function_exists('theme_wishlist_btn_html')) {
    /**
     * Render the Add to Wishlist button.
     * The platform class "add_to_wishlist_single_page" is always included.
     *
     * @param  string $icon_class  Icon class (defaults to las la-heart)
     * @param  string $extra_class Additional CSS classes
     */
    function theme_wishlist_btn_html(string $icon_class = 'las la-heart', string $extra_class = ''): string
    {
        $cls = trim('add_to_wishlist_single_page ' . $extra_class);
        return '<a href="javascript:void(0)" class="' . e($cls) . '"><i class="' . e($icon_class) . '"></i></a>';
    }
}

if (!function_exists('theme_compare_btn_html')) {
    /**
     * Render the Add to Compare button.
     * Requires data-product_id to be set by caller, or pass product ID.
     */
    function theme_compare_btn_html(int $productId, string $icon_class = 'las la-retweet', string $extra_class = ''): string
    {
        $cls   = trim('compare-btn ' . $extra_class);
        $title = e(__('Add to Compare'));
        return '<a href="javascript:void(0)" class="' . e($cls) . '" data-product_id="' . $productId . '" title="' . $title . '"><i class="' . e($icon_class) . '"></i></a>';
    }
}

// ============================================================
// PLATFORM COMPONENT HELPERS
// ============================================================
// These replace <x-component/> syntax which has isolated scope.
// Use {!! theme_*_js() !!} — no view variables needed.
// ============================================================

if (!function_exists('theme_product_js')) {
    /**
     * Render the product-details JavaScript block.
     *
     * Includes: attribute/variant selection, price updates, image swaps,
     * Add-to-Cart, Buy-Now, Wishlist, and Compare AJAX handlers.
     *
     * Call inside @section('scripts') on the product details page, passing
     * the variables the controller provides. All parameters have safe defaults
     * so the page never crashes if a product has no variants.
     *
     * @param  array       $product_inventory_set  Variant inventory map (from controller)
     * @param  array       $additional_info_store  Variant additional info map (from controller)
     * @param  mixed|null  $campaign_product       Active campaign product or null
     * @param  int         $campaign_active             1 if campaign has expired, else 0
     * @param  mixed|null  $product                The product Eloquent model
     *
     * Usage:
     *   @section('scripts')
     *       {!! theme_product_js(
     *           $product_inventory_set,
     *           $additional_info_store,
     *           $campaign_product ?? null,
     *           $campaign_active ?? 0,
     *           $product
     *       ) !!}
     *   @endsection
     */
    function theme_product_js(
        array  $product_inventory_set = [],
        array  $additional_info_store = [],
               $campaign_product      = null,
        int    $campaign_active            = 0,
               $product               = null
    ): string {
        return view('components.theme.product-details-js', compact(
            'product_inventory_set',
            'additional_info_store',
            'campaign_product',
            'campaign_active',
            'product'
        ))->render();
    }
}

if (!function_exists('theme_ajax_login_js')) {
    /**
     * Render the AJAX login JS block.
     *
     * Handles the #login_btn click event, posts credentials to the tenant
     * login endpoint and reloads on success. Required on any page that
     * shows the guest login form (e.g. checkout page).
     *
     * Usage:
     *   @section('scripts')
     *       {!! theme_ajax_login_js() !!}
     *   @endsection
     */
    function theme_ajax_login_js(): string
    {
        return view('components.custom-js.ajax-login')->render();
    }
}

if (!function_exists('theme_generate_password_js')) {
    /**
     * Render the password generator JS helper.
     *
     * Exposes the global JS function generateRandomPassword() which
     * returns a random 8-12 char alphanumeric+symbol string.
     * Attach to a button with onclick="generateRandomPassword()".
     *
     * Usage:
     *   @section('scripts')
     *       {!! theme_generate_password_js() !!}
     *   @endsection
     */
    function theme_generate_password_js(): string
    {
        return view('components.custom-js.generate-password')->render();
    }
}

if (!function_exists('theme_newsletter_js')) {
    /**
     * Render the newsletter subscription JS block.
     *
     * Handles .newsletter-submit-btn click and posts to the tenant
     * newsletter subscribe endpoint. The email input must have
     * class="email" inside a .footer-widget container.
     *
     * Usage:
     *   @section('scripts')
     *       {!! theme_newsletter_js() !!}
     *   @endsection
     */
    function theme_newsletter_js(): string
    {
        return view('components.custom-js.newsletter-store')->render();
    }
}

if (!function_exists('theme_phone_js')) {
    /**
     * Render the international phone number input JS + CSS.
     *
     * Initialises intl-tel-input on the given selector, validates the
     * number on keyup and enables/disables the submit button.
     *
     * @param  string $selector      CSS selector for the phone input  (default '#telephone')
     * @param  string $submit_btn_id ID of the submit button to disable while invalid
     * @param  int    $key           Unique integer when multiple phone inputs exist on one page
     *
     * Usage:
     *   @section('scripts')
     *       {!! theme_phone_js('#phone', 'register_button') !!}
     *   @endsection
     */
    function theme_phone_js(string $selector = '#telephone', string $submit_btn_id = 'register_button', int $key = 1): string
    {
        return view('components.custom-js.phone-number-config', [
            'selector'       => $selector,
            'submitButtonId' => $submit_btn_id,
            'key'            => $key,
        ])->render();
    }
}

if (!function_exists('theme_lang_change_js')) {
    /**
     * Render the language-switcher JS block.
     *
     * Handles .language_dropdown ul li click, posts the selected locale
     * to the tenant lang-change endpoint and reloads the page.
     *
     * Usage:
     *   @section('scripts')
     *       {!! theme_lang_change_js() !!}
     *   @endsection
     */
    function theme_lang_change_js(): string
    {
        return view('components.custom-js.lang-change')->render();
    }
}

if (!function_exists('theme_lazy_load_js')) {
    /**
     * Render the lazy-load image JS.
     *
     * Initialises the IntersectionObserver-based lazy loader for images
     * with data-src attributes. Call once per page, ideally at the
     * bottom of the scripts section.
     *
     * Usage:
     *   @section('scripts')
     *       {!! theme_lazy_load_js() !!}
     *   @endsection
     */
    function theme_lazy_load_js(): string
    {
        return view('components.custom-js.lazy-load-image')->render();
    }
}

if (!function_exists('theme_error_msg')) {
    /**
     * Render validation error messages (equivalent to <x-error-msg/>).
     *
     * Outputs the standard error message block using Laravel's $errors bag.
     * Safe to call on any form page.
     *
     * Usage:
     *   {!! theme_error_msg() !!}
     */
    function theme_error_msg(): string
    {
        return view('components.error-msg')->render();
    }
}

if (!function_exists('theme_flash_msg')) {
    /**
     * Render session flash messages (equivalent to <x-flash-msg/>).
     *
     * Outputs success/error/info flash messages from the session.
     * Safe to call on any page.
     *
     * Usage:
     *   {!! theme_flash_msg() !!}
     */
    function theme_flash_msg(): string
    {
        return view('components.flash-msg')->render();
    }
}

if (!function_exists('theme_btn_loading_js')) {
    /**
     * Render a button loading-state JS snippet.
     *
     * When the button is clicked it shows a spinner and the given loading
     * label, preventing double-submits. Drop this inside any <script> block
     * after $(document).ready() or inside its callback.
     *
     * @param  string $btn_id       The button's HTML id (without #)
     * @param  string $loading_text Text shown while loading (default: 'Please Wait…')
     *
     * Usage (inside a <script> block inside @section('scripts')):
     *   {!! theme_btn_loading_js('register', __('Creating account…')) !!}
     */
    function theme_btn_loading_js(string $btn_id, string $loading_text = ''): string
    {
        $loading_text = $loading_text ?: __('Please Wait…');
        $id           = e($btn_id);
        $text         = e($loading_text);

        return <<<JS
$(document).on('click', '#{$id}', function () {
    var el = $(this);
    el.prop('disabled', true);
    el.html('<i class="las la-spinner la-spin"></i> {$text}');
});
JS;
    }
}

// ── Missing globally-required helpers ────────────────────────────────────────

if (!function_exists('theme_cart_subtotal')) {
    /**
     * Cart subtotal (before tax) in the currently selected currency.
     * Uses sale_price_raw stored in cart options to ensure correct conversion
     * even when currency is switched after items were added.
     */
    function theme_cart_subtotal(): float
    {
        $total = 0.0;
        foreach (\Gloudemans\Shoppingcart\Facades\Cart::instance('default')->content() as $item) {
            // sale_price_raw = pre-conversion base price; fall back to stored price for old sessions
            $base = isset($item->options->sale_price_raw)
                ? (float) $item->options->sale_price_raw
                : (float) $item->price;
            $converted = (float) apply_filters('nazmart:product_price', $base, null);
            $total += $converted * $item->qty;
        }
        return round($total, 2);
    }
}

if (!function_exists('theme_cart_tax')) {
    /** Cart tax amount as a float based on session-stored tax rate */
    function theme_cart_tax(): float
    {
        $subtotal = theme_cart_subtotal();
        $rate     = (float) session('product_tax', 0);
        return round(($subtotal * $rate) / 100, 2);
    }
}

if (!function_exists('theme_cart_total')) {
    /** Cart total (subtotal + tax) as a float */
    function theme_cart_total(): float
    {
        return theme_cart_subtotal() + theme_cart_tax();
    }
}

if (!function_exists('theme_wishlist_items')) {
    /** All items currently in the wishlist cart instance */
    function theme_wishlist_items(): \Illuminate\Support\Collection
    {
        return \Gloudemans\Shoppingcart\Facades\Cart::instance('wishlist')->content();
    }
}

if (!function_exists('theme_cart_item_meta')) {
    /**
     * Build a display string for all selected variant options on a cart item.
     * Includes Color, Size, and any custom attribute axes (RAM, Material, etc.).
     *
     * Usage in blade:
     *   @php $meta = theme_cart_item_meta($data); @endphp
     *   @if($meta) <div class="cart-meta">{{ $meta }}</div> @endif
     *
     * @param  mixed   $item       Cart item (Gloudemans\Shoppingcart\CartItem)
     * @param  string  $separator  Separator between option parts (default ' · ')
     */
    function theme_cart_item_meta($item, string $separator = ' · '): string
    {
        $opts = $item->options ?? null;
        if (!$opts) return '';
        $parts = [];
        if (!empty($opts->color_name)) $parts[] = __('Color') . ': ' . $opts->color_name;
        if (!empty($opts->size_name))  $parts[] = __('Size')  . ': ' . $opts->size_name;
        foreach ((array) ($opts->attributes ?? []) as $k => $v) {
            if ($v !== null && $v !== '') $parts[] = ucfirst($k) . ': ' . $v;
        }
        return implode($separator, $parts);
    }
}

if (!function_exists('theme_placeholder_image')) {
    /** URL of the platform's generic placeholder/no-image asset */
    function theme_placeholder_image(): string
    {
        return global_asset('assets/img/no-image.jpeg');
    }
}

if (!function_exists('theme_categories')) {
    /** All active top-level product categories */
    function theme_categories(): \Illuminate\Support\Collection
    {
        if (!function_exists('tenant') || !tenant()) {
            return collect();
        }

        // Use product_categories (hasMany) for withCount — avoids hasManyThrough issues
        $base = \Modules\Attributes\Entities\Category::where('status_id', 1)
            ->withCount(['product_categories as product_count']);

        try {
            $themeSlug = tenant()->theme_slug ?? null;

            // Guard: only filter by theme_slug if the column actually exists
            $hasCol = \Illuminate\Support\Facades\Schema::hasColumn('categories', 'theme_slug');

            if ($themeSlug && $hasCol) {
                $themed = (clone $base)->where('theme_slug', $themeSlug)->get();
                if ($themed->isNotEmpty()) {
                    return $themed;
                }
            }

            // Fallback: return all active categories (ignore theme_slug)
            return $base->get();

        } catch (\Exception $e) {
            // DB not ready yet — return empty so widget renders gracefully
            return collect();
        }
    }
}

if (!function_exists('theme_category_url')) {
    /** Storefront URL for a category by its slug */
    function theme_category_url(string $slug): string
    {
        return dynamicRoute($slug);
    }
}

if (!function_exists('theme_category_image')) {
    /** Resolved image URL for a category model instance, or the placeholder */
    function theme_category_image(\Modules\Attributes\Entities\Category $category): string
    {
        $data = $category->image_id
            ? get_attachment_image_by_id($category->image_id, 'full')
            : null;
        return !empty($data['img_url']) ? $data['img_url'] : theme_placeholder_image();
    }
}

if (!function_exists('theme_countries')) {
    /** All countries available in the platform */
    function theme_countries(): \Illuminate\Support\Collection
    {
        return \Modules\CountryManage\Entities\Country::all();
    }
}

if (!function_exists('theme_payment_gateways')) {
    /** Active payment gateways as an array (same data used by core checkout) */
    function theme_payment_gateways(): array
    {
        return \App\Helpers\PaymentGatewayRenderHelper::getActiveGateways() ?? [];
    }
}

if (!function_exists('theme_shipping_methods')) {
    /** Shipping methods available for the current cart session */
    function theme_shipping_methods(): \Illuminate\Support\Collection
    {
        $zoneIds = session('shipping_zones', []);
        if (empty($zoneIds)) {
            return collect();
        }
        return \Modules\ShippingModule\Entities\ShippingMethod::with('options')
            ->whereIn('zone_id', $zoneIds)
            ->get();
    }
}

if (!function_exists('theme_products_paginator')) {
    /** LengthAwarePaginator for the current shop listing page, or null */
    function theme_products_paginator(): mixed
    {
        return view()->shared('theme_products_paginator');
    }
}

if (!function_exists('theme_featured_products')) {
    /** Featured / latest products for homepage widgets */
    function theme_featured_products(int $count = 8): \Illuminate\Support\Collection
    {
        return view()->shared('theme_featured_products')
            ?? \Modules\Product\Entities\Product::with(['badge', 'campaign_product', 'inventory'])
                ->where('status_id', 1)
                ->latest()
                ->take($count)
                ->get();
    }
}

if (!function_exists('theme_product_images')) {
    /** Gallery images for a product — returns a Collection of attachment URLs */
    function theme_product_images(\Modules\Product\Entities\Product $product): \Illuminate\Support\Collection
    {
        return $product->gallery_images ?? collect();
    }
}

if (!function_exists('theme_product_attributes')) {
    /** Product attributes (size, colour, etc.) for the given product */
    function theme_product_attributes(\Modules\Product\Entities\Product $product): \Illuminate\Support\Collection
    {
        return $product->attributes ?? collect();
    }
}

if (!function_exists('theme_product_variants')) {
    /** Product custom variants as an array, suitable for JS */
    function theme_product_variants(\Modules\Product\Entities\Product $product): array
    {
        return $product->product_custom_variants ?? [];
    }
}

if (!function_exists('theme_product_in_stock')) {
    /** Returns true when the product has available inventory */
    function theme_product_in_stock(\Modules\Product\Entities\Product $product): bool
    {
        $inventory = $product->inventory;
        if (!$inventory) return false;
        return (int) ($inventory->stock_count ?? 0) > 0;
    }
}

if (!function_exists('theme_product_reviews')) {
    /** Render the product reviews section HTML (uses core component) */
    function theme_product_reviews(\Modules\Product\Entities\Product $product): string
    {
        return (string) view('tenant.frontend.shop.product-details.partials.product-reviews', [
            'product' => $product,
        ])->render();
    }
}

if (!function_exists('theme_newsletter_form')) {
    /**
     * Render a newsletter subscription form.
     * $formClass is applied to the <form> element; $buttonText sets the submit label.
     */
    function theme_newsletter_form(string $formClass = 'newsletter-form', string $buttonText = 'Subscribe'): string
    {
        $action = route('tenant.frontend.subscribe.newsletter');
        $csrf   = csrf_field();
        $cls    = e($formClass);
        $btn    = e($buttonText);
        return <<<HTML
<form class="{$cls} footer-widget" method="POST" action="{$action}">
    {$csrf}
    <div class="form-message-show"></div>
    <div class="input-group">
        <input type="email" name="email" class="form--control email" placeholder="{$btn}" required>
        <button type="submit" class="newsletter-submit-btn">{$btn}</button>
    </div>
</form>
HTML;
    }
}

if (!function_exists('theme_path')) {
    /** Alias — already defined in funtions.php; declared here as a safety guard */
    function theme_path($name): string
    {
        $slug = tenant()?->theme_slug ?? 'default';
        return "theme-{$slug}::{$name}";
    }
}

// ── Search ────────────────────────────────────────────────────────────────────
if (!function_exists('theme_search_ajax_url')) {
    function theme_search_ajax_url(): string { return route('tenant.search.ajax'); }
}

// ── Shop Filter (AJAX) ────────────────────────────────────────────────────────
// Returns the JSON filter endpoint (/s/search) used by shop/category AJAX filters.
// Do NOT use theme_shop_url() for AJAX — that returns the full HTML page.
if (!function_exists('theme_shop_filter_url')) {
    function theme_shop_filter_url(): string { return route('tenant.shop'); }
}

// ── Digital Shop Filter (AJAX) ────────────────────────────────────────────────
// Returns the JSON filter endpoint (/s/digital/search).
// Do NOT use theme_digital_shop_url() for AJAX — that returns the full HTML page.
if (!function_exists('theme_digital_shop_filter_url')) {
    function theme_digital_shop_filter_url(): string { return route('tenant.digital.shop'); }
}

// ── Digital Shop ──────────────────────────────────────────────────────────────
if (!function_exists('theme_digital_shop_url')) {
    function theme_digital_shop_url(): string {
        $slug = get_page_slug(get_static_option('digital_shop_page'), 'digital_shop_page');
        return dynamicRoute($slug);
    }
}
if (!function_exists('theme_digital_product_add_to_cart_url')) {
    function theme_digital_product_add_to_cart_url(): string { return route('tenant.digital.shop.product.add.to.cart.ajax'); }
}
if (!function_exists('theme_digital_product_review_url')) {
    function theme_digital_product_review_url(): string { return route('tenant.digital.shop.product.review'); }
}

// ── User Dashboard ────────────────────────────────────────────────────────────
if (!function_exists('theme_user_package_orders_url')) {
    function theme_user_package_orders_url(): string { return route('tenant.user.dashboard.package.order'); }
}
if (!function_exists('theme_user_cancel_order_url')) {
    function theme_user_cancel_order_url(): string { return route('tenant.user.dashboard.order.change.status'); }
}
if (!function_exists('theme_user_downloads_url')) {
    function theme_user_downloads_url(): string { return route('tenant.user.dashboard.download.list'); }
}
if (!function_exists('theme_user_tickets_url')) {
    function theme_user_tickets_url(): string { return route('tenant.user.home.support.tickets'); }
}
if (!function_exists('theme_user_ticket_url')) {
    function theme_user_ticket_url(int $id): string { return route('tenant.user.dashboard.support.ticket.view', $id); }
}
if (!function_exists('theme_user_ticket_reply_url')) {
    function theme_user_ticket_reply_url(): string { return route('tenant.user.dashboard.support.ticket.message'); }
}
if (!function_exists('theme_user_new_ticket_url')) {
    function theme_user_new_ticket_url(): string { return route('tenant.frontend.support.ticket'); }
}
if (!function_exists('theme_user_change_password_page_url')) {
    function theme_user_change_password_page_url(): string { return route('tenant.user.home.change.password'); }
}
if (!function_exists('theme_user_password_change_url')) {
    function theme_user_password_change_url(): string { return route('tenant.user.password.change'); }
}
if (!function_exists('theme_user_manage_account_url')) {
    function theme_user_manage_account_url(): string { return route('tenant.user.home.manage.account'); }
}
if (!function_exists('theme_user_address_update_url')) {
    function theme_user_address_update_url(): string { return route('tenant.user.home.address.update'); }
}
if (!function_exists('theme_user_profile_update_url')) {
    function theme_user_profile_update_url(): string { return route('tenant.user.profile.update'); }
}
if (!function_exists('theme_user_download_url')) {
    function theme_user_download_url(string $slug): string { return route('tenant.user.dashboard.download.file', $slug); }
}
if (!function_exists('theme_package_order_confirm_url')) {
    function theme_package_order_confirm_url(int $id): string { return route('tenant.frontend.order.confirm', $id); }
}
if (!function_exists('theme_package_order_cancel_url')) {
    function theme_package_order_cancel_url(): string { return route('tenant.user.dashboard.package.order.cancel'); }
}
if (!function_exists('theme_package_invoice_url')) {
    function theme_package_invoice_url(): string { return route('tenant.frontend.package.invoice.generate'); }
}
if (!function_exists('theme_otp_verify_url')) {
    function theme_otp_verify_url(): string { return route('tenant.user.login.otp.verification'); }
}
if (!function_exists('theme_otp_resend_url')) {
    function theme_otp_resend_url(): string { return route('tenant.user.login.otp.resend'); }
}
if (!function_exists('theme_user_orders_url')) {
    function theme_user_orders_url(): string { return route('tenant.user.dashboard.package.order'); }
}
if (!function_exists('theme_user_order_detail_url')) {
    function theme_user_order_detail_url(int $id): string { return route('tenant.user.dashboard.package.order', $id); }
}
if (!function_exists('theme_newsletter_subscribe_url')) {
    function theme_newsletter_subscribe_url(): string { return route('tenant.frontend.subscribe.newsletter'); }
}
if (!function_exists('theme_state_ajax_url')) {
    function theme_state_ajax_url(): string { return route('tenant.admin.au.state.all'); }
}
if (!function_exists('theme_user_ticket_store_url')) {
    function theme_user_ticket_store_url(): string { return route('tenant.frontend.support.ticket.store'); }
}
if (!function_exists('theme_payment_form_url')) {
    function theme_payment_form_url(): string { return route('tenant.frontend.order.payment.form'); }
}
if (!function_exists('theme_order_confirm_url')) {
    function theme_order_confirm_url(int $id): string { return route('tenant.frontend.order.confirm', $id); }
}
if (!function_exists('theme_order_success_url')) {
    function theme_order_success_url(int $id): string { return route('tenant.frontend.order.payment.success', $id); }
}
if (!function_exists('theme_order_cancel_url')) {
    function theme_order_cancel_url(int $id): string { return route('tenant.frontend.order.payment.cancel', $id); }
}
if (!function_exists('theme_blog_comment_store_url')) {
    function theme_blog_comment_store_url(): string { return route('tenant.frontend.blog.comment.store'); }
}
