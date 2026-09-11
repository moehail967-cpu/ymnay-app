<?php

namespace App\Theme\Shop;

use Illuminate\Support\Str;

/**
 * Wraps a Product model with a stable method API for theme templates.
 */
class Product
{
    private array $priceData;

    public function __construct(private object $product)
    {
        $this->priceData = get_product_dynamic_price($product);
    }

    public function raw(): object
    {
        return $this->product;
    }

    // ── Identity ──────────────────────────────────────────────────────────────

    public function id(): int
    {
        return $this->product->id;
    }

    public function name(int $words = 0): string
    {
        $name = $this->product->name ?? '';
        return $words > 0 ? Str::words($name, $words) : $name;
    }

    public function url(): string
    {
        return dynamicRoute($this->product->slug);
    }

    public function slug(): string
    {
        return $this->product->slug ?? '';
    }

    public function summary(): string
    {
        return $this->product->summary ?? '';
    }

    public function description(): string
    {
        return parse_shortcodes($this->product->description ?? '');
    }

    // ── Media ─────────────────────────────────────────────────────────────────

    public function image_url(string $size = 'grid'): ?string
    {
        $data = get_attachment_image_by_id($this->product->image_id ?? null, $size);
        return !empty($data) ? $data['img_url'] : null;
    }

    public function image_tag(string $size = 'grid', string $class = '', bool $lazy = true): string
    {
        return render_image_markup_by_attachment_id($this->product->image_id ?? null, $class, $size, $lazy);
    }

    /** @return array<int, string>  list of gallery image URLs */
    public function gallery_urls(string $size = 'grid'): array
    {
        $urls = [];
        foreach ($this->product->product_gallery ?? [] as $gallery) {
            $data = get_attachment_image_by_id($gallery->image_id ?? null, $size);
            if (!empty($data)) {
                $urls[] = $data['img_url'];
            }
        }
        return $urls;
    }

    // ── Pricing ───────────────────────────────────────────────────────────────

    /** Display price (campaign or sale price, formatted with currency) */
    public function price(): string
    {
        return amount_with_currency_symbol($this->priceData['sale_price'] ?? 0);
    }

    /** Original price (null if no discount), formatted */
    public function regular_price(): ?string
    {
        $rp = $this->priceData['regular_price'] ?? null;
        return $rp ? amount_with_currency_symbol($rp) : null;
    }

    /** Raw sale price number (already currency-converted via get_product_dynamic_price) */
    public function sale_price_raw(): float
    {
        return (float) ($this->priceData['sale_price'] ?? 0);
    }

    /** Raw regular price number (0 if no discount) */
    public function regular_price_raw(): float
    {
        return (float) ($this->priceData['regular_price'] ?? 0);
    }

    /** Discount percentage string e.g. "20" or null */
    public function discount(): ?string
    {
        $d = $this->priceData['discount'] ?? null;
        return $d ? (string) $d : null;
    }

    public function is_on_sale(): bool
    {
        return !empty($this->priceData['discount']);
    }

    public function campaign_name(): ?string
    {
        return $this->priceData['campaign_name'] ?? null;
    }

    public function has_campaign(): bool
    {
        return !empty($this->priceData['campaign_name']);
    }

    // ── Badge / Label ─────────────────────────────────────────────────────────

    public function badge(): ?string
    {
        return $this->product->badge?->name ?? null;
    }

    public function has_badge(): bool
    {
        return !empty($this->badge());
    }

    // ── Stock ─────────────────────────────────────────────────────────────────

    public function stock(): int
    {
        return (int) ($this->product->inventory?->stock_count ?? 0);
    }

    public function in_stock(): bool
    {
        return $this->stock() > 0;
    }

    // ── Ratings ───────────────────────────────────────────────────────────────

    public function star_rating(): string
    {
        return render_product_star_rating_markup_with_count($this->product);
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function add_to_cart_url(): string
    {
        return theme_add_to_cart_url();
    }

    public function add_to_wishlist_url(): string
    {
        return theme_add_to_wishlist_url();
    }

    public function buy_now_url(): string
    {
        return theme_buy_now_url();
    }

    public function quick_view_url(): string
    {
        return theme_quick_view_url();
    }

    // ── Taxonomy ──────────────────────────────────────────────────────────────

    public function category(): ?ProductCategory
    {
        $cat = $this->product->category ?? null;
        return $cat ? new ProductCategory($cat) : null;
    }

    // ── Conditionals ──────────────────────────────────────────────────────────

    public function has_image(): bool
    {
        return !empty($this->product->image_id);
    }

    public function is_refundable(): bool
    {
        return (bool) ($this->product->is_refundable ?? false);
    }
}
