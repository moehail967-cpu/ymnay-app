@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<style>
    .tn-product-card {
        background: #fff;
        border: 1.5px solid var(--tn-border, #ECCFE0);
        border-radius: 20px;
        overflow: hidden;
        transition: all .25s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .tn-product-card:hover {
        border-color: var(--tn-blush, #F2A7C3);
        transform: translateY(-4px);
        box-shadow: 0 16px 36px rgba(242,167,195,.18);
    }
    .tn-product-img {
        position: relative;
        aspect-ratio: 4/3;
        overflow: hidden;
        background: var(--tn-blush-light, #FDE8F1);
    }
    .tn-product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .35s;
    }
    .tn-product-card:hover .tn-product-img img { transform: scale(1.05); }
    .tn-product-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .tn-badge-new  { background: var(--tn-mint, #A7E2CE); color: #1A4A35; }
    .tn-badge-hot  { background: var(--tn-blush, #F2A7C3); color: #fff; }
    .tn-badge-sale { background: var(--tn-lavender, #C8A7F2); color: #fff; }
    .tn-wish-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,.1);
        transition: all .2s;
        color: var(--tn-muted, #7A5E6E);
        font-size: 16px;
    }
    .tn-wish-btn:hover { background: var(--tn-blush, #F2A7C3); color: #fff; }
    .tn-product-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .tn-product-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 12px;
        color: var(--tn-muted, #7A5E6E);
    }
    .tn-product-rating { color: #F2C94C; display: flex; align-items: center; gap: 2px; }
    .tn-product-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--tn-dark, #2D1B2E);
        line-height: 1.4;
        margin-bottom: 12px;
        text-decoration: none;
        display: block;
        flex: 1;
    }
    .tn-product-name:hover { color: var(--tn-blush, #F2A7C3); }
    .tn-product-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: auto;
    }
    .tn-price {
        font-size: 16px;
        font-weight: 800;
        color: var(--tn-blush, #F2A7C3);
    }
    .tn-price-old {
        font-size: 13px;
        color: var(--tn-muted, #7A5E6E);
        text-decoration: line-through;
        margin-left: 4px;
    }
    .tn-add-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--tn-blush, #F2A7C3);
        border: none;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        font-size: 18px;
        transition: all .2s;
    }
    .tn-add-btn:hover {
        background: #e890b0;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(242,167,195,.4);
    }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--tn-blush-light, #FDE8F1);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="tn-sec-heading">
                <h2 class="tn-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="tn-view-all">
                    {{ __('View All') }} <i class="mdi mdi-arrow-right"></i>
                </a>
            </div>
        @endif

        @if($data['products']->isNotEmpty())
            <div class="row g-4">
                @foreach($data['products'] as $product)
                    @php
                        $img          = get_attachment_image_by_id($product->image);
                        $img_url      = !empty($img) ? $img['img_url'] : $placeholder;
                        $sale_price   = $product->sale_price ?? 0;
                        $reg_price    = $product->regular_price ?? $sale_price;
                        $has_discount = $reg_price > $sale_price;
                        $badge        = $product->badge ?? null;
                        $product_url  = route('tenant.products.single-quick-view', $product->slug);
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="tn-product-card">
                            <div class="tn-product-img">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>

                                @if($badge)
                                    <span class="tn-product-badge
                                        @if(strtolower($badge->name) === 'hot') tn-badge-hot
                                        @elseif(strtolower($badge->name) === 'new') tn-badge-new
                                        @else tn-badge-sale @endif">
                                        {{ $badge->name }}
                                    </span>
                                @endif

                                <button class="tn-wish-btn" title="{{ __('Add to Wishlist') }}"
                                    onclick="addToWishlist({{ $product->id }}, this)">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>

                            <div class="tn-product-body">
                                <div class="tn-product-meta">
                                    <span>{{ $product->category->name ?? '' }}</span>
                                    @if($product->rating_count > 0)
                                        <span class="tn-product-rating">
                                            <i class="mdi mdi-star" style="font-size:12px;"></i>
                                            {{ number_format($product->average_rating, 1) }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ $product_url }}" class="tn-product-name">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                <div class="tn-product-footer">
                                    <div>
                                        <span class="tn-price">{{ amount_with_currency_symbol($sale_price) }}</span>
                                        @if($has_discount)
                                            <span class="tn-price-old">{{ amount_with_currency_symbol($reg_price) }}</span>
                                        @endif
                                    </div>
                                    <button class="tn-add-btn" title="{{ __('Add to Cart') }}"
                                        onclick="addToCart({{ $product->id }})">
                                        <i class="mdi mdi-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--tn-muted, #7A5E6E);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
