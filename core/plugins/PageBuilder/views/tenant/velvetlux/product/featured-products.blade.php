@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<style>
    .vl-card {
        background: #fff;
        border: 1px solid var(--vl-border);
        border-radius: 16px;
        overflow: hidden;
        transition: all .25s ease;
        position: relative;
    }
    .vl-card:hover {
        border-color: var(--vl-plum);
        box-shadow: 0 10px 32px rgba(92,31,107,.13);
        transform: translateY(-4px);
    }
    .vl-card-img {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: var(--vl-plum-light);
    }
    .vl-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .35s ease;
    }
    .vl-card:hover .vl-card-img img { transform: scale(1.05); }
    .vl-card-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 50px;
        background: var(--vl-plum);
        color: #fff;
    }
    .vl-card-wish {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(255,255,255,.9);
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--vl-plum);
        font-size: 16px;
        transition: background .2s;
    }
    .vl-card-wish:hover { background: var(--vl-plum); color: #fff; }
    .vl-card-body { padding: 16px; }
    .vl-card-meta {
        font-size: 11px;
        color: var(--vl-muted);
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .vl-card-title {
        font-family: var(--vl-font-head);
        font-size: 16px;
        font-weight: 700;
        color: var(--vl-dark);
        text-decoration: none;
        display: block;
        margin-bottom: 12px;
        line-height: 1.35;
    }
    .vl-card-title:hover { color: var(--vl-plum); }
    .vl-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .vl-price {
        font-size: 16px;
        font-weight: 700;
        color: var(--vl-plum);
    }
    .vl-price-old {
        font-size: 13px;
        color: var(--vl-muted);
        text-decoration: line-through;
        margin-left: 4px;
    }
    .vl-add-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--vl-plum);
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 18px;
        transition: background .2s;
        flex-shrink: 0;
    }
    .vl-add-btn:hover { background: var(--vl-plum-deep); }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:var(--vl-bg, #F8F5F2);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="vl-sec-heading">
                <h2 class="vl-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="vl-view-all">
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
                        <div class="vl-card">
                            <div class="vl-card-img">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>

                                @if($badge)
                                    <span class="vl-card-badge">{{ $badge->name }}</span>
                                @endif

                                <button class="vl-card-wish" title="{{ __('Add to Wishlist') }}"
                                    onclick="addToWishlist({{ $product->id }}, this)">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>

                            <div class="vl-card-body">
                                <div class="vl-card-meta">
                                    @if($product->category)
                                        <span>{{ $product->category->name }}</span>
                                    @endif
                                    @if($product->rating_count > 0)
                                        <span style="color:var(--vl-champagne);">
                                            <i class="mdi mdi-star" style="font-size:12px;"></i>
                                            {{ number_format($product->average_rating, 1) }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ $product_url }}" class="vl-card-title">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                <div class="vl-card-footer">
                                    <div>
                                        <span class="vl-price">{{ amount_with_currency_symbol($sale_price) }}</span>
                                        @if($has_discount)
                                            <span class="vl-price-old">{{ amount_with_currency_symbol($reg_price) }}</span>
                                        @endif
                                    </div>
                                    <button class="vl-add-btn" title="{{ __('Add to Cart') }}"
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
            <p class="text-center py-5" style="color:var(--vl-muted);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
