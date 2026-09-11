@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="container">

        @if(!empty($data['title']))
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <span class="bk-section-tag d-inline-block mb-2">{{ __('Our Selection') }}</span>
                    <h2 class="bk-section-title mb-0">{{ $data['title'] }}</h2>
                </div>
                <a href="{{ $shop_url }}" style="color:var(--bk-rose);font-weight:700;text-decoration:none;"
                   onmouseover="this.style.color='var(--bk-rose-deep)'" onmouseout="this.style.color='var(--bk-rose)'">
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
                        <div class="bk-card">

                            {{-- Card Image --}}
                            <div class="bk-card-img">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>

                                {{-- Badge --}}
                                @if($badge)
                                    <span class="bk-card-badge">{{ $badge->name }}</span>
                                @endif

                                {{-- Wishlist --}}
                                <button class="bk-card-wishlist" title="{{ __('Add to Wishlist') }}"
                                        onclick="addToWishlist({{ $product->id }}, this)">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>

                            {{-- Card Body --}}
                            <div class="bk-card-body">
                                @if($product->category)
                                    <div class="bk-card-cat">{{ $product->category->name }}</div>
                                @endif

                                <a href="{{ $product_url }}" class="bk-card-name" style="text-decoration:none; display:block;">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                {{-- Star Rating --}}
                                @if($product->rating_count > 0)
                                    <div class="bk-stars mb-2">
                                        @for($s = 1; $s <= 5; $s++)
                                            <i class="mdi {{ $s <= round($product->average_rating) ? 'mdi-star' : 'mdi-star-outline' }}"
                                               style="font-size:13px; color:var(--bk-gold);"></i>
                                        @endfor
                                        <span style="font-size:12px; color:var(--bk-muted); margin-left:4px;">
                                            ({{ $product->rating_count }})
                                        </span>
                                    </div>
                                @endif

                                {{-- Price + ATC --}}
                                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto">
                                    <div>
                                        <span class="bk-card-price bk-price-sale">
                                            {{ amount_with_currency_symbol($sale_price) }}
                                        </span>
                                        @if($has_discount)
                                            <span class="bk-price-orig">
                                                {{ amount_with_currency_symbol($reg_price) }}
                                            </span>
                                        @endif
                                    </div>
                                    <button class="bk-card-atc" title="{{ __('Add to Cart') }}"
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
            <p class="text-center py-5" style="color:var(--bk-muted);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
