@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="container">

        @if(!empty($data['title']))
            <div class="ch-sec-heading">
                <h2 class="ch-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="ch-view-all">
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
                        $cart_url     = route('tenant.shop.cart.add');
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="ch-card">
                            <div class="ch-card-img">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>

                                {{-- Badge --}}
                                @if($badge)
                                    <span class="ch-card-badge
                                        @if(strtolower($badge->name) === 'hot') ch-badge-hot
                                        @elseif(strtolower($badge->name) === 'new') ch-badge-new
                                        @else ch-badge-veg @endif">
                                        {{ $badge->name }}
                                    </span>
                                @endif

                                {{-- Wishlist --}}
                                <button class="ch-card-wish" title="{{ __('Add to Wishlist') }}"
                                    onclick="addToWishlist({{ $product->id }}, this)">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>

                            <div class="ch-card-body">
                                <div class="ch-card-meta">
                                    @if($product->category)
                                        <span>{{ $product->category->name }}</span>
                                    @endif
                                    @if($product->rating_count > 0)
                                        <span style="color:var(--ch-amber);">
                                            <i class="mdi mdi-star" style="font-size:12px;"></i>
                                            {{ number_format($product->average_rating, 1) }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ $product_url }}" class="ch-card-title" style="text-decoration:none; display:block;">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                <div class="ch-card-footer">
                                    <div>
                                        <span class="ch-price" style="font-size:15px;">{{ amount_with_currency_symbol($sale_price) }}</span>
                                        @if($has_discount)
                                            <span class="ch-price-old">{{ amount_with_currency_symbol($reg_price) }}</span>
                                        @endif
                                    </div>
                                    <button class="ch-add-btn" title="{{ __('Add to Cart') }}"
                                        data-product-id="{{ $product->id }}"
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
            <p class="text-center py-5" style="color:var(--ch-muted);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
