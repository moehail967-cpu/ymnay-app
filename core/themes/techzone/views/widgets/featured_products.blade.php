<section class="tz-featured-section">
    <div class="container">
        <div class="tz-fp-header">
            <div>
                <span class="tz-section-tag"><i class="mdi mdi-fire"></i> {{ __('Hot Right Now') }}</span>
                <h2 class="tz-section-title mb-0">{{ $title }}</h2>
                @if(!empty($subtitle))
                    <p class="tz-section-sub mt-1 mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            <a href="{{ theme_shop_url() }}" class="tz-btn tz-btn-outline tz-btn-sm">
                {{ __('View All') }} <i class="mdi mdi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-3">
            @forelse($products as $product)
                @php
                    $pUrl   = theme_product_url($product->slug);
                    $pImg   = theme_product_image($product->image_id ?? null);
                    $pPrice = theme_product_price($product);
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="tz-card">
                        <a href="{{ $pUrl }}" class="tz-card-img">
                            <img src="{{ $pImg ?? theme_placeholder_image() }}" alt="{{ $product->name }}">
                        </a>
                        @if($product->is_featured ?? false)
                            <div class="tz-card-badge">{{ __('Featured') }}</div>
                        @endif
                        <button class="tz-card-wishlist add-to-wishlist-btn" type="button" data-product_id="{{ $product->id }}">
                            <i class="mdi mdi-heart-outline"></i>
                        </button>
                        <div class="tz-card-body">
                            @if($product->category)
                                <div class="tz-card-cat">{{ $product->category->name ?? '' }}</div>
                            @endif
                            <div class="tz-card-name">
                                <a href="{{ $pUrl }}">{{ Str::limit($product->name, 55) }}</a>
                            </div>
                            <div class="tz-card-price">
                                <span class="tz-price-sale">{{ $pPrice['sale_price'] }}</span>
                                @if(!empty($pPrice['regular_price']) && $pPrice['regular_price'] !== $pPrice['sale_price'])
                                    <span class="tz-price-orig">{{ $pPrice['regular_price'] }}</span>
                                @endif
                            </div>
                            <button class="tz-card-atc add-to-cart-btn" type="button" data-product_id="{{ $product->id }}">
                                <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 tz-fp-empty">
                    <i class="las la-box-open tz-fp-empty-icon"></i>
                    <p class="mt-2">{{ __('No products found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
