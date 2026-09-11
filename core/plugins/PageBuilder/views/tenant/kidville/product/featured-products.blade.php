@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<style>
    .kv-card {
        background: #fff;
        border: 2px solid var(--kv-border);
        border-radius: 20px;
        overflow: hidden;
        transition: all .25s ease;
        position: relative;
    }
    .kv-card:hover {
        border-color: var(--kv-red);
        box-shadow: 0 12px 32px rgba(229,57,53,.15);
        transform: translateY(-5px);
    }
    .kv-card-img {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: var(--kv-blue-light);
    }
    .kv-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .35s ease;
    }
    .kv-card:hover .kv-card-img img { transform: scale(1.06); }
    .kv-card-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 5px 12px;
        border-radius: 50px;
        background: var(--kv-yellow);
        color: var(--kv-dark);
    }
    .kv-card-wish {
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
        color: var(--kv-red);
        font-size: 16px;
        transition: background .2s;
    }
    .kv-card-wish:hover { background: var(--kv-red); color: #fff; }
    .kv-card-body { padding: 16px; }
    .kv-card-meta {
        font-size: 11px;
        color: var(--kv-muted);
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
    }
    .kv-card-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--kv-dark);
        text-decoration: none;
        display: block;
        margin-bottom: 12px;
        line-height: 1.4;
    }
    .kv-card-title:hover { color: var(--kv-red); }
    .kv-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .kv-price {
        font-size: 17px;
        font-weight: 900;
        color: var(--kv-blue);
    }
    .kv-price-old {
        font-size: 13px;
        color: var(--kv-muted);
        text-decoration: line-through;
        margin-left: 4px;
    }
    .kv-add-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--kv-red);
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 20px;
        transition: all .2s;
        flex-shrink: 0;
    }
    .kv-add-btn:hover { background: #C62828; transform: scale(1.12); }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:var(--kv-bg, #FFFEF8);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="kv-sec-heading">
                <h2 class="kv-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="kv-view-all">
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
                        <div class="kv-card">
                            <div class="kv-card-img">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>

                                @if($badge)
                                    <span class="kv-card-badge">{{ $badge->name }}</span>
                                @endif

                                <button class="kv-card-wish" title="{{ __('Add to Wishlist') }}"
                                    onclick="addToWishlist({{ $product->id }}, this)">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>

                            <div class="kv-card-body">
                                <div class="kv-card-meta">
                                    @if($product->category)
                                        <span>{{ $product->category->name }}</span>
                                    @endif
                                    @if($product->rating_count > 0)
                                        <span style="color:var(--kv-yellow);">
                                            <i class="mdi mdi-star" style="font-size:12px;color:#F9A825;"></i>
                                            {{ number_format($product->average_rating, 1) }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ $product_url }}" class="kv-card-title">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                <div class="kv-card-footer">
                                    <div>
                                        <span class="kv-price">{{ amount_with_currency_symbol($sale_price) }}</span>
                                        @if($has_discount)
                                            <span class="kv-price-old">{{ amount_with_currency_symbol($reg_price) }}</span>
                                        @endif
                                    </div>
                                    <button class="kv-add-btn" title="{{ __('Add to Cart') }}"
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
            <p class="text-center py-5" style="color:var(--kv-muted);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
