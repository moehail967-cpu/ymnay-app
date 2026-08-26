@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<style>
    .ms-product-card {
        background: #fff;
        border: 1px solid var(--ms-border, #DDD5C4);
        border-radius: 2px;
        overflow: hidden;
        transition: all .25s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .ms-product-card:hover {
        border-color: var(--ms-olive, #7D8C5A);
        transform: translateY(-4px);
        box-shadow: 0 16px 36px rgba(125,140,90,.12);
    }
    .ms-product-img {
        position: relative;
        aspect-ratio: 4/3;
        overflow: hidden;
        background: var(--ms-warm, #F8F4EE);
    }
    .ms-product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .35s;
    }
    .ms-product-card:hover .ms-product-img img { transform: scale(1.05); }
    .ms-product-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        border-radius: 1px;
    }
    .ms-badge-new  { background: var(--ms-olive, #7D8C5A); color: #fff; }
    .ms-badge-hot  { background: var(--ms-accent, #C4956A); color: #fff; }
    .ms-badge-sale { background: var(--ms-dark, #2A2416); color: #fff; }
    .ms-wish-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid var(--ms-border, #DDD5C4);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .2s;
        color: var(--ms-muted, #6E6048);
        font-size: 16px;
    }
    .ms-wish-btn:hover { background: var(--ms-olive, #7D8C5A); color: #fff; border-color: var(--ms-olive, #7D8C5A); }
    .ms-product-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .ms-product-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 12px;
        color: var(--ms-muted, #6E6048);
    }
    .ms-product-rating { display: flex; align-items: center; gap: 2px; }
    .ms-product-name {
        font-family: var(--ms-font-head, 'Lora', Georgia, serif);
        font-size: 15px;
        font-weight: 600;
        color: var(--ms-dark, #2A2416);
        line-height: 1.4;
        margin-bottom: 12px;
        text-decoration: none;
        display: block;
        flex: 1;
    }
    .ms-product-name:hover { color: var(--ms-olive, #7D8C5A); }
    .ms-product-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid var(--ms-border, #DDD5C4);
    }
    .ms-price {
        font-size: 17px;
        font-weight: 700;
        color: var(--ms-olive-deep, #5A6840);
    }
    .ms-price-old {
        font-size: 13px;
        color: var(--ms-muted, #6E6048);
        text-decoration: line-through;
        margin-left: 4px;
    }
    .ms-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: var(--ms-olive, #7D8C5A);
        border: none;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .4px;
        cursor: pointer;
        border-radius: 1px;
        transition: all .2s;
        text-transform: uppercase;
    }
    .ms-add-btn:hover {
        background: var(--ms-olive-deep, #5A6840);
        transform: translateY(-1px);
    }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--ms-linen, #F5F0E8);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="ms-sec-heading">
                <h2 class="ms-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="ms-view-all">
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
                        <div class="ms-product-card">
                            <div class="ms-product-img">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>

                                @if($badge)
                                    <span class="ms-product-badge
                                        @if(strtolower($badge->name) === 'hot') ms-badge-hot
                                        @elseif(strtolower($badge->name) === 'new') ms-badge-new
                                        @else ms-badge-sale @endif">
                                        {{ $badge->name }}
                                    </span>
                                @endif

                                <button class="ms-wish-btn" title="{{ __('Add to Wishlist') }}"
                                    onclick="addToWishlist({{ $product->id }}, this)">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>

                            <div class="ms-product-body">
                                <div class="ms-product-meta">
                                    <span>{{ $product->category->name ?? '' }}</span>
                                    @if($product->rating_count > 0)
                                        <span class="ms-product-rating">
                                            <i class="mdi mdi-star" style="font-size:12px; color:#C4956A;"></i>
                                            {{ number_format($product->average_rating, 1) }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ $product_url }}" class="ms-product-name">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                <div class="ms-product-footer">
                                    <div>
                                        <span class="ms-price">{{ amount_with_currency_symbol($sale_price) }}</span>
                                        @if($has_discount)
                                            <span class="ms-price-old">{{ amount_with_currency_symbol($reg_price) }}</span>
                                        @endif
                                    </div>
                                    <button class="ms-add-btn" title="{{ __('Add to Cart') }}"
                                        onclick="addToCart({{ $product->id }})">
                                        <i class="mdi mdi-plus"></i>
                                        {{ __('Add') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--ms-muted, #6E6048);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
