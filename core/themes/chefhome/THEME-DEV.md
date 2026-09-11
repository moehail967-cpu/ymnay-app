# ChefHome Theme — Developer Reference

> **Rule #1:** Never use `<x-component/>` syntax for platform snippets.
> Always use the `theme_*()` helper functions listed below.
>
> **Why:** Blade component tags (`<x->`) create an isolated scope — they do not
> inherit the current view's variables. The platform helper functions handle
> data injection for you automatically.

---

## Quick-start

```blade
{{-- Wrong — crashes with "Undefined variable $product_inventory_set" --}}
<x-theme.product-details-js/>

{{-- Correct — one call, zero configuration --}}
{!! theme_product_js() !!}
```

---

## Platform Helper Functions

All helpers live in `app/Helpers/theme-frontend-helpers.php`.
Import nothing — they are globally available in every Blade view.

---

### Product Page

#### `theme_product_js()`
Renders the full product-details JavaScript block.

Handles: attribute/variant selection, price updates, image swaps,
Add to Cart, Buy Now, Wishlist, and Compare AJAX calls.

**Must be called inside `@section('scripts')` on the product details page.**

```blade
@section('scripts')
    {!! theme_product_js(
        $product_inventory_set,
        $additional_info_store,
        $campaign_product ?? null,
        $is_expired ?? 0,
        $product
    ) !!}
@endsection
```

The five variables are always provided by the platform's product-details
controller — just pass them through. All have safe defaults so the page
never crashes on products that have no variants or campaign.

---

#### `theme_add_to_cart_btn_html(string $label = '', string $extra_class = '')`
Renders the Add to Cart button with the required platform class.

```blade
{!! theme_add_to_cart_btn_html(__('Add to Cart'), 'ch-btn ch-btn-red') !!}
```

#### `theme_buy_now_btn_html(string $label = '', string $extra_class = '')`
Renders the Buy Now button.

```blade
{!! theme_buy_now_btn_html(__('Buy Now'), 'ch-btn ch-btn-outline') !!}
```

#### `theme_wishlist_btn_html(string $icon_class = 'las la-heart', string $extra_class = '')`
Renders the wishlist heart button.

```blade
{!! theme_wishlist_btn_html('las la-heart', 'ch-card-wishlist wishlist-btn') !!}
```

#### `theme_compare_btn_html(int $productId, string $icon_class = 'las la-retweet', string $extra_class = '')`
Renders the compare button for a specific product.

```blade
{!! theme_compare_btn_html($product->id, 'las la-retweet', 'ch-icon-btn') !!}
```

---

### Auth / Forms

#### `theme_ajax_login_js()`
AJAX login handler. Handles `#login_btn` click → posts to the tenant
login endpoint → reloads on success.

**Required on any page that has a sign-in form.**

```blade
@section('scripts')
    {!! theme_ajax_login_js() !!}
@endsection
```

#### `theme_generate_password_js()`
Exposes `generateRandomPassword()` globally. Call it from any button `onclick`.

```blade
@section('scripts')
    {!! theme_generate_password_js() !!}
@endsection

{{-- In your HTML: --}}
<a href="#" onclick="$('#password').val(generateRandomPassword())">
    Generate Password
</a>
```

#### `theme_phone_js(string $selector = '#telephone', string $submit_btn_id = 'register_button', int $key = 1)`
International phone input (intl-tel-input) with live validation.
Disables the submit button while the number is invalid.

```blade
@section('scripts')
    {!! theme_phone_js('#phone_number', 'register') !!}
@endsection
```

| Param | Default | Description |
|---|---|---|
| `$selector` | `#telephone` | CSS selector of the `<input type="tel">` |
| `$submit_btn_id` | `register_button` | ID of the button to disable while invalid |
| `$key` | `1` | Unique integer when multiple phone inputs exist on one page |

#### `theme_btn_loading_js(string $btn_id, string $loading_text = '')`
Drop inside a `<script>` block. When the button is clicked it shows a
spinner and disables itself, preventing double-submits.

```blade
<script>
$(document).ready(function () {
    {!! theme_btn_loading_js('register', __('Creating account…')) !!}
});
</script>
```

#### `theme_error_msg()`
Renders Laravel validation errors (same as `<x-error-msg/>`).

```blade
{!! theme_error_msg() !!}
```

#### `theme_flash_msg()`
Renders session flash messages (same as `<x-flash-msg/>`).

```blade
{!! theme_flash_msg() !!}
```

---

### Navigation & Site Identity

#### `theme_logo_html(string $link_class = '', string $img_class = '', string $wrap_class = '')`
Full logo anchor + `<img>` pulled from admin settings.

```blade
{!! theme_logo_html('ch-navbar-brand-link', 'ch-navbar-logo-img') !!}
```

#### `theme_logo_url()` / `theme_white_logo_url()`
Raw URL strings for the main / white logo.

#### `theme_site_name()` / `theme_site_tagline()`
Plain text from admin settings.

#### `theme_nav_menu()`
Primary navigation as `<ul class="theme-nav-menu">…</ul>` with children
wrapped in `<ul class="sub-menu">`. Dropdown CSS is already in the platform
default stylesheet.

```blade
{!! theme_nav_menu() !!}
```

#### `theme_mobile_nav_menu()`
Same as above but for mobile drawer use.

#### `theme_footer_widgets(string $area, bool $show_title = true)`
Footer widget HTML for the given widget area key.

```blade
{!! theme_footer_widgets('footer', true) !!}
{!! theme_footer_widgets('footer_bottom_left', false) !!}
```

#### `theme_footer_copyright()`
Copyright text from admin settings.

---

### Icons (Navbar)

```blade
{!! theme_cart_icon_html('ch-icon-btn', 'ch-icon-badge') !!}
{!! theme_wishlist_icon_html('ch-icon-btn', 'ch-icon-badge') !!}
{!! theme_account_icon_html('ch-icon-btn') !!}
```

All icons use Line Awesome (`las la-*`) by default, which is already
loaded on every tenant page.

---

### URLs

| Helper | Returns |
|---|---|
| `theme_home_url()` | Homepage URL |
| `theme_shop_url()` | Shop listing URL |
| `theme_cart_url()` | Cart page URL |
| `theme_checkout_url()` | Checkout page URL |
| `theme_login_url()` | Login page URL |
| `theme_register_url()` | Register page URL |
| `theme_product_url($slug)` | Single product URL |
| `theme_blog_url()` | Blog index URL |

---

### Prices & Products

#### `theme_product_price($product): array`
Returns `['regular_price', 'sale_price', 'discount', 'campaign_name']`.

```blade
@php $data = theme_product_price($product); @endphp
{{ amount_with_currency_symbol($data['sale_price']) }}
```

#### `theme_product_image($attachment_id, string $size = 'full'): string`
Returns the image URL for a product attachment.

#### `theme_star_rating($product): string`
Returns the HTML star-rating markup.

#### `theme_cart_count()` / `theme_wishlist_count()`
Returns total item quantity for navbar badges.

#### `theme_product_tax(): float`
Returns the applicable tax rate (percentage) for the current session.
Always `0.0` on initial page load — the checkout page updates it via AJAX
when the user selects a country/state. Use this instead of `$product_tax`
(which is never passed by the checkout controller).

```blade
@php $product_tax = theme_product_tax(); @endphp
```

#### `theme_cart_items(): Collection`
All items currently in the cart — never depends on a `$cart_data` controller
variable, so safe to call from any page (checkout, mini-cart widget, etc.).

```blade
@foreach(theme_cart_items() as $item)
    {{ $item->name }} × {{ $item->qty }}
    — {{ amount_with_currency_symbol($item->price) }}
@endforeach
```

---

### Utilities

#### `theme_csrf_field()`
CSRF hidden input — use instead of `@csrf` inside non-Blade forms.

#### `theme_is_logged_in(): bool`
Whether the current visitor is authenticated.

#### `theme_newsletter_js()`
Newsletter subscription AJAX. Attaches to `.newsletter-submit-btn` click.

```blade
@section('scripts')
    {!! theme_newsletter_js() !!}
@endsection
```

#### `theme_lang_change_js()`
Language switcher AJAX. Attaches to `.language_dropdown ul li` click.

```blade
@section('scripts')
    {!! theme_lang_change_js() !!}
@endsection
```

#### `theme_lazy_load_js()`
Lazy-loads images with `data-src` attributes via IntersectionObserver.

```blade
@section('scripts')
    {!! theme_lazy_load_js() !!}
@endsection
```

---

## View Fallback Chain

When a tenant is on the **ChefHome** theme, Blade looks for each view in
this order:

1. `themes/chefhome/views/{path}.blade.php` ← your overrides
2. `themes/default/views/{path}.blade.php` ← platform defaults
3. `resources/views/tenant/{path}.blade.php` ← core fallback

Use `themeView('my.view')` in controllers (not `view('tenant.frontend.my.view')`).
In Blade, use `@include(include_theme_path('my.view'))` to include a
partial that respects the same chain.

---

## Icon System

The tenant frontend loads **Line Awesome** by default.

```blade
{{-- Correct --}}
<i class="las la-shopping-cart"></i>
<i class="las la-heart"></i>
<i class="las la-user"></i>

{{-- Wrong — MDI is admin-only --}}
<i class="mdi mdi-cart"></i>
```

Full icon reference: https://icons8.com/line-awesome

---

## Naming Conventions

- ChefHome CSS classes: `ch-*` (e.g. `ch-btn`, `ch-card`, `ch-price`)
- Platform CSS classes that trigger AJAX: never rename these
  - `add_to_cart_single_page` — Add to Cart
  - `but_now_single_page` — Buy Now
  - `add_to_wishlist_single_page` — Wishlist
  - `wishlist-btn` — Wishlist toggle on cards
  - `compare-btn` — Compare
  - `ch-atc-btn` — grid/list card Add to Cart
