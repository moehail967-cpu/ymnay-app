<section class="fp-products-section">
    <div class="container">

        {{-- Section Header --}}
        <div class="fp-products-header mb-4">
            <div>
                @if(!empty($tag))
                    <div class="fp-section-label mb-2">{{ $tag }}</div>
                @endif
                @if(!empty($title))
                    <h2 class="fp-products-title">{!! $title !!}</h2>
                @endif
            </div>
            @if(!empty($view_all_url))
                <a href="{{ $view_all_url }}" class="fp-view-all-btn">
                    {{ __('View All') }} <i class="mdi mdi-arrow-right"></i>
                </a>
            @endif
        </div>

        @if($products->isNotEmpty())
            <div class="row g-3">
                @foreach($products as $product)
                    @php
                        $img_url  = theme_product_image($product->image_id ?? null, 'grid') ?? theme_placeholder_image();
                        $price    = $product->sale_price ?? 0;
                        $regular  = $product->regular_price ?? $price;
                        $badge    = $product->badge ?? null;
                        $prod_url = theme_product_url($product->slug);
                        $cat_name = $product->category->name ?? '';
                        $spec     = $product->summary ?? '';
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3 d-flex">
                        <div class="fp-card w-100">
                            <div class="fp-card-img">
                                <a href="{{ $prod_url }}">
                                    @if($img_url)
                                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                    @else
                                        <div class="fp-img-ph"><i class="mdi mdi-dumbbell fp-img-ph-icon"></i></div>
                                    @endif
                                </a>
                                @if($badge)
                                    <span class="fp-card-badge">{{ $badge->name }}</span>
                                @endif
                                <button class="fp-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" title="{{ __('Wishlist') }}">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>
                            <div class="fp-card-body">
                                @if($cat_name)
                                    <div class="fp-card-cat">{{ $cat_name }}</div>
                                @endif
                                <a href="{{ $prod_url }}" class="fp-card-title">{{ $product->name }}</a>
                                @if($spec)
                                    <p class="fp-card-spec">{{ $spec }}</p>
                                @endif
                                <div class="fp-stars">{!! theme_star_rating($product) !!}</div>
                                <div class="fp-price-row">
                                    <span class="fp-price">{{ amount_with_currency_symbol($price) }}</span>
                                    @if($regular > $price)
                                        <span class="fp-price-old">{{ amount_with_currency_symbol($regular) }}</span>
                                    @endif
                                </div>
                                <button class="fp-card-atc-full add_to_cart_btn"
                                        data-product="{{ $product->id }}"
                                        data-name="{{ $product->name }}">
                                    <i class="mdi mdi-cart-outline"></i> {{ __('Add to Cart') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="fp-empty-state">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
