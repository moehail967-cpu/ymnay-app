@php
    $pt          = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb          = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url    = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<style>
    .lg-card {
        background: var(--lg-card, #141414);
        border: 1px solid var(--lg-border, #2A2410);
        overflow: hidden;
        transition: box-shadow .25s, transform .25s, border-color .25s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .lg-card:hover {
        border-color: var(--lg-gold, #C9A84C);
        box-shadow: 0 8px 40px rgba(201,168,76,.15);
        transform: translateY(-4px);
    }
    .lg-card-img {
        position: relative;
        overflow: hidden;
        background: #0E0E00;
    }
    .lg-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .35s;
    }
    .lg-card:hover .lg-card-img img { transform: scale(1.06); }
    .lg-card-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: var(--lg-gold, #C9A84C);
        color: #080808;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: 4px 12px;
        font-family: var(--lg-font-head, 'Cinzel', Georgia, serif);
    }
    .lg-card-wishlist {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 36px;
        height: 36px;
        background: rgba(8,8,8,.7);
        border: 1px solid var(--lg-border, #2A2410);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 16px;
        color: var(--lg-muted, #8A7E6E);
        transition: all .2s;
        backdrop-filter: blur(4px);
    }
    .lg-card-wishlist:hover { color: #e53935; border-color: #e53935; }
    .lg-card-body {
        padding: 18px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .lg-card-cat {
        font-size: 10px;
        font-weight: 600;
        color: var(--lg-gold, #C9A84C);
        text-transform: uppercase;
        letter-spacing: .12em;
        margin-bottom: 8px;
    }
    .lg-card-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--lg-dark, #E8E0D0);
        text-decoration: none;
        display: block;
        margin-bottom: 12px;
        line-height: 1.5;
        flex: 1;
        font-family: var(--lg-font-head, 'Cinzel', Georgia, serif);
    }
    .lg-card-name:hover { color: var(--lg-gold-light, #F0D98C); }
    .lg-price-sale {
        font-size: 15px;
        font-weight: 700;
        color: var(--lg-gold, #C9A84C);
    }
    .lg-price-orig {
        font-size: 12px;
        color: var(--lg-muted, #8A7E6E);
        text-decoration: line-through;
        margin-left: 6px;
    }
    .lg-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid var(--lg-border, #2A2410);
    }
    .lg-card-atc {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: transparent;
        color: var(--lg-gold, #C9A84C);
        border: 1px solid var(--lg-gold, #C9A84C);
        font-size: 11px;
        font-weight: 600;
        padding: 8px 14px;
        cursor: pointer;
        transition: all .2s;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .lg-card-atc:hover {
        background: var(--lg-gold, #C9A84C);
        color: #080808;
    }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--lg-surface, #111111);">
    <div class="container">

        @if(!empty($data['title']))
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:40px; padding-bottom:20px; border-bottom:1px solid var(--lg-border,#2A2410);">
                <h2 style="font-size:clamp(20px,2.5vw,30px); font-weight:700; color:var(--lg-dark,#E8E0D0); margin:0; font-family:var(--lg-font-head,'Cinzel',Georgia,serif); letter-spacing:.05em;">
                    {{ $data['title'] }}
                </h2>
                <a href="{{ $shop_url }}" style="font-size:11px; font-weight:600; color:var(--lg-gold,#C9A84C); text-decoration:none; display:inline-flex; align-items:center; gap:6px; letter-spacing:.1em; text-transform:uppercase;">
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
                        <div class="lg-card">
                            <div class="lg-card-img" style="height:220px;">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>
                                @if($badge)
                                    <span class="lg-card-badge">{{ $badge->name }}</span>
                                @endif
                                <button class="lg-card-wishlist" title="{{ __('Add to Wishlist') }}"
                                    onclick="addToWishlist({{ $product->id }}, this)">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>
                            <div class="lg-card-body">
                                @if($product->category)
                                    <div class="lg-card-cat">{{ $product->category->name }}</div>
                                @endif
                                <a href="{{ $product_url }}" class="lg-card-name">
                                    {{ Str::limit($product->name, 40) }}
                                </a>
                                <div class="lg-card-footer">
                                    <div>
                                        <span class="lg-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                                        @if($has_discount)
                                            <span class="lg-price-orig">{{ amount_with_currency_symbol($reg_price) }}</span>
                                        @endif
                                    </div>
                                    <button class="lg-card-atc" onclick="addToCart({{ $product->id }})">
                                        <i class="mdi mdi-cart-plus"></i> {{ __('Add') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--lg-muted,#8A7E6E);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
