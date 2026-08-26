<section class="fm-featured-products-widget">
    <div class="container">
        <div class="fm-sec-heading">
            <div>
                <h2 class="fm-section-title mb-1">{{ $title }}</h2>
                @if(!empty($subtitle))
                    <p class="fm-section-sub mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            @if(!empty($view_all_url))
                <a href="{{ $view_all_url }}" class="fm-view-all">
                    {{ __('View All') }} <i class="las la-arrow-right"></i>
                </a>
            @endif
        </div>

        @if($products->isNotEmpty())
            <div class="row g-3">
                @foreach($products as $product)
                    @php
                        $data     = theme_product_price($product);
                        $img_url  = theme_product_image($product->image_id ?? null, 'grid');
                        $prod_url = theme_product_url($product->slug);
                        $badge    = $product->badge?->name ?? null;
                        $discount = $data['discount'];
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="fm-card">
                            <div class="fm-card-img">
                                @if($img_url)
                                    <a href="{{ $prod_url }}">
                                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                    </a>
                                @else
                                    <a href="{{ $prod_url }}" class="fm-img-ph">
                                        <i class="mdi mdi-basket fm-img-ph-icon"></i>
                                    </a>
                                @endif
                                @if($discount)
                                    <span class="fm-card-badge">{{ $discount }}% {{ __('off') }}</span>
                                @elseif($badge)
                                    <span class="fm-card-badge">{{ $badge }}</span>
                                @endif
                                <button class="fm-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                                    <i class="las la-heart"></i>
                                </button>
                            </div>
                            <div class="fm-card-body">
                                <div class="fm-card-name">
                                    <a href="{{ $prod_url }}">{{ \Illuminate\Support\Str::words($product->name, 8) }}</a>
                                </div>
                                <div class="fm-stars">{!! theme_star_rating($product) !!}</div>
                                <div class="fm-card-price">
                                    <span class="fm-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                                    @if($data['regular_price'])
                                        <span class="fm-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                                    @endif
                                </div>
                                <button class="fm-card-atc add-to-cart-btn"
                                        data-id="{{ $product->id }}"
                                        data-slug="{{ $product->slug }}"
                                        aria-label="{{ __('Add to cart') }}">
                                    <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="fm-empty-state">{{ __('No products found.') }}</p>
        @endif
    </div>
</section>
