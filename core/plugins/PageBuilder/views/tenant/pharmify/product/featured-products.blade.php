@php
    $pt          = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb          = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url    = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<style>
    .pf-card {
        background: #fff;
        border: 1px solid var(--pf-border, #DDE6EA);
        overflow: hidden;
        transition: box-shadow .2s, transform .2s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .pf-card:hover {
        box-shadow: 0 8px 32px rgba(0,137,123,.12);
        transform: translateY(-3px);
    }
    .pf-card-img {
        position: relative;
        overflow: hidden;
        background: var(--pf-teal-light, #E0F2F1);
    }
    .pf-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .3s;
    }
    .pf-card:hover .pf-card-img img { transform: scale(1.04); }
    .pf-card-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: var(--pf-teal, #00897B);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        padding: 3px 10px;
    }
    .pf-card-wishlist {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 34px;
        height: 34px;
        background: #fff;
        border: 1px solid var(--pf-border, #DDE6EA);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 16px;
        color: var(--pf-muted, #607080);
        transition: all .2s;
    }
    .pf-card-wishlist:hover { color: #e53935; border-color: #e53935; }
    .pf-card-body {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .pf-card-cat {
        font-size: 11px;
        font-weight: 600;
        color: var(--pf-teal, #00897B);
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 6px;
    }
    .pf-card-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--pf-dark, #1A2332);
        text-decoration: none;
        display: block;
        margin-bottom: 10px;
        line-height: 1.4;
        flex: 1;
    }
    .pf-card-name:hover { color: var(--pf-teal, #00897B); }
    .pf-price-sale {
        font-size: 16px;
        font-weight: 800;
        color: var(--pf-teal, #00897B);
    }
    .pf-price-orig {
        font-size: 13px;
        color: var(--pf-muted, #607080);
        text-decoration: line-through;
        margin-left: 6px;
    }
    .pf-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 12px;
    }
    .pf-card-atc {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--pf-teal, #00897B);
        color: #fff;
        border: none;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 14px;
        cursor: pointer;
        transition: background .2s;
    }
    .pf-card-atc:hover { background: var(--pf-teal-deep, #006358); }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--pf-bg, #F7FAFB);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="pf-sec-heading" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:32px;">
                <h2 style="font-size:clamp(20px,2.5vw,28px); font-weight:800; color:var(--pf-dark,#1A2332); margin:0;">
                    {{ $data['title'] }}
                </h2>
                <a href="{{ $shop_url }}" style="font-size:13px; font-weight:600; color:var(--pf-teal,#00897B); text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
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
                        <div class="pf-card">
                            <div class="pf-card-img" style="height:220px;">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>
                                @if($badge)
                                    <span class="pf-card-badge">{{ $badge->name }}</span>
                                @endif
                                <button class="pf-card-wishlist" title="{{ __('Add to Wishlist') }}"
                                    onclick="addToWishlist({{ $product->id }}, this)">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>
                            <div class="pf-card-body">
                                @if($product->category)
                                    <div class="pf-card-cat">{{ $product->category->name }}</div>
                                @endif
                                <a href="{{ $product_url }}" class="pf-card-name">
                                    {{ Str::limit($product->name, 40) }}
                                </a>
                                <div class="pf-card-footer">
                                    <div>
                                        <span class="pf-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                                        @if($has_discount)
                                            <span class="pf-price-orig">{{ amount_with_currency_symbol($reg_price) }}</span>
                                        @endif
                                    </div>
                                    <button class="pf-card-atc" onclick="addToCart({{ $product->id }})">
                                        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--pf-muted,#607080);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
