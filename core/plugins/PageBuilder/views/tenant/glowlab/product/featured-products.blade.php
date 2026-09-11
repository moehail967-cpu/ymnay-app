@php
    $pt          = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb          = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url    = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<style>
    .gl-card {
        background: #fff;
        border: 1px solid var(--gl-border, #E8DCC8);
        border-radius: 12px;
        overflow: hidden;
        transition: box-shadow .25s, transform .25s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .gl-card:hover {
        box-shadow: 0 10px 36px rgba(201,164,76,.18);
        transform: translateY(-4px);
    }
    .gl-card-img {
        position: relative;
        overflow: hidden;
        background: var(--gl-gold-pale, #FDF8EE);
    }
    .gl-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .35s;
    }
    .gl-card:hover .gl-card-img img { transform: scale(1.05); }
    .gl-card-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: var(--gl-gold, #C9A44C);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 100px;
    }
    .gl-card-wishlist {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 36px;
        height: 36px;
        background: #fff;
        border: 1px solid var(--gl-border, #E8DCC8);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 16px;
        color: var(--gl-muted, #6B5E4A);
        transition: all .2s;
    }
    .gl-card-wishlist:hover { color: #e53935; border-color: #e53935; }
    .gl-card-body {
        padding: 18px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .gl-card-cat {
        font-size: 11px;
        font-weight: 700;
        color: var(--gl-gold, #C9A44C);
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 6px;
    }
    .gl-card-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--gl-dark, #1A1208);
        text-decoration: none;
        display: block;
        margin-bottom: 12px;
        line-height: 1.45;
        flex: 1;
    }
    .gl-card-name:hover { color: var(--gl-gold-deep, #A8852E); }
    .gl-price-sale {
        font-size: 16px;
        font-weight: 800;
        color: var(--gl-gold-deep, #A8852E);
    }
    .gl-price-orig {
        font-size: 13px;
        color: var(--gl-muted, #6B5E4A);
        text-decoration: line-through;
        margin-left: 6px;
    }
    .gl-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 12px;
    }
    .gl-card-atc {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--gl-gold, #C9A44C);
        color: #fff;
        border: none;
        font-size: 12px;
        font-weight: 700;
        padding: 9px 16px;
        border-radius: 100px;
        cursor: pointer;
        transition: background .2s;
    }
    .gl-card-atc:hover { background: var(--gl-gold-deep, #A8852E); }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--gl-bg, #FAFAFA);">
    <div class="container">

        @if(!empty($data['title']))
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:36px;">
                <h2 style="font-size:clamp(20px,2.5vw,30px); font-weight:800; color:var(--gl-dark,#1A1208); margin:0;">
                    {{ $data['title'] }}
                </h2>
                <a href="{{ $shop_url }}" style="font-size:13px; font-weight:600; color:var(--gl-gold-deep,#A8852E); text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
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
                        <div class="gl-card">
                            <div class="gl-card-img" style="height:220px;">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>
                                @if($badge)
                                    <span class="gl-card-badge">{{ $badge->name }}</span>
                                @endif
                                <button class="gl-card-wishlist" title="{{ __('Add to Wishlist') }}"
                                    onclick="addToWishlist({{ $product->id }}, this)">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>
                            <div class="gl-card-body">
                                @if($product->category)
                                    <div class="gl-card-cat">{{ $product->category->name }}</div>
                                @endif
                                <a href="{{ $product_url }}" class="gl-card-name">
                                    {{ Str::limit($product->name, 40) }}
                                </a>
                                <div class="gl-card-footer">
                                    <div>
                                        <span class="gl-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                                        @if($has_discount)
                                            <span class="gl-price-orig">{{ amount_with_currency_symbol($reg_price) }}</span>
                                        @endif
                                    </div>
                                    <button class="gl-card-atc" onclick="addToCart({{ $product->id }})">
                                        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--gl-muted,#6B5E4A);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
