@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<style>
    .fp-product-card {
        background: var(--fp-card, #161616);
        border: 1px solid var(--fp-border, #1A3A1A);
        border-radius: 4px;
        overflow: hidden;
        transition: all .25s;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .fp-product-card:hover {
        border-color: var(--fp-green-dim, #007A1F);
        transform: translateY(-4px);
        box-shadow: 0 16px 36px rgba(0,255,65,.08);
    }
    .fp-product-img {
        position: relative;
        aspect-ratio: 4/3;
        overflow: hidden;
        background: var(--fp-surface, #111111);
    }
    .fp-product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .35s;
        filter: grayscale(10%);
    }
    .fp-product-card:hover .fp-product-img img {
        transform: scale(1.05);
        filter: grayscale(0%);
    }
    .fp-product-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 4px 10px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .8px;
        border-radius: 2px;
    }
    .fp-badge-new  { background: var(--fp-green, #00FF41); color: #000; }
    .fp-badge-hot  { background: #FF4141; color: #fff; }
    .fp-badge-sale { background: var(--fp-surface, #111); color: var(--fp-green, #00FF41); border: 1px solid var(--fp-green, #00FF41); }
    .fp-wish-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(0,0,0,.6);
        border: 1px solid var(--fp-border, #1A3A1A);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .2s;
        color: var(--fp-muted, #707070);
        font-size: 16px;
    }
    .fp-wish-btn:hover { background: rgba(0,255,65,.15); color: var(--fp-green, #00FF41); border-color: var(--fp-green, #00FF41); }
    .fp-product-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .fp-product-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 11px;
        color: var(--fp-muted, #707070);
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .fp-product-rating { display: flex; align-items: center; gap: 2px; color: var(--fp-green, #00FF41); }
    .fp-product-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--fp-text, #E0E0E0);
        line-height: 1.4;
        margin-bottom: 12px;
        text-decoration: none;
        display: block;
        flex: 1;
    }
    .fp-product-name:hover { color: var(--fp-green, #00FF41); }
    .fp-product-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid var(--fp-border, #1A3A1A);
    }
    .fp-price {
        font-size: 18px;
        font-weight: 900;
        color: var(--fp-green, #00FF41);
    }
    .fp-price-old {
        font-size: 13px;
        color: var(--fp-muted, #707070);
        text-decoration: line-through;
        margin-left: 4px;
    }
    .fp-add-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--fp-green, #00FF41);
        border: none;
        color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        font-size: 18px;
        font-weight: 900;
        transition: all .2s;
    }
    .fp-add-btn:hover {
        background: var(--fp-green-deep, #00CC33);
        transform: scale(1.1);
        box-shadow: 0 4px 14px rgba(0,255,65,.4);
    }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--fp-bg, #0A0A0A);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="fp-sec-heading">
                <h2 class="fp-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="fp-view-all">
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
                        <div class="fp-product-card">
                            <div class="fp-product-img">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>

                                @if($badge)
                                    <span class="fp-product-badge
                                        @if(strtolower($badge->name) === 'hot') fp-badge-hot
                                        @elseif(strtolower($badge->name) === 'new') fp-badge-new
                                        @else fp-badge-sale @endif">
                                        {{ $badge->name }}
                                    </span>
                                @endif

                                <button class="fp-wish-btn" title="{{ __('Add to Wishlist') }}"
                                    onclick="addToWishlist({{ $product->id }}, this)">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>

                            <div class="fp-product-body">
                                <div class="fp-product-meta">
                                    <span>{{ $product->category->name ?? '' }}</span>
                                    @if($product->rating_count > 0)
                                        <span class="fp-product-rating">
                                            <i class="mdi mdi-star" style="font-size:12px;"></i>
                                            {{ number_format($product->average_rating, 1) }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ $product_url }}" class="fp-product-name">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                <div class="fp-product-footer">
                                    <div>
                                        <span class="fp-price">{{ amount_with_currency_symbol($sale_price) }}</span>
                                        @if($has_discount)
                                            <span class="fp-price-old">{{ amount_with_currency_symbol($reg_price) }}</span>
                                        @endif
                                    </div>
                                    <button class="fp-add-btn" title="{{ __('Add to Cart') }}"
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
            <p class="text-center py-5" style="color:var(--fp-muted, #707070);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
