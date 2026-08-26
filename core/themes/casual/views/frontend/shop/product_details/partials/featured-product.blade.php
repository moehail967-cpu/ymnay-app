@if($related_products && $related_products->count())
<section class="cs-related-section">
    <div class="container">
        <div class="cs-section-head">
            <h2 class="cs-section-title">{{ __('Related Products') }}</h2>
        </div>
        <div class="row g-4">
            @foreach($related_products->take(4) as $product)
            @php
                $img_data    = get_attachment_image_by_id($product->image_id, 'grid');
                $pImg        = !empty($img_data) ? $img_data['img_url'] : null;
                $hasDiscount = $product->price && $product->sale_price && $product->sale_price < $product->price;
                $price       = $hasDiscount ? $product->sale_price : $product->price;
                $oldPrice    = $hasDiscount ? $product->price : null;
                $discPct     = $hasDiscount ? round((($product->price - $product->sale_price) / $product->price) * 100) : null;
                $pUrl        = theme_product_url($product->slug);
            @endphp
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="casual-new-product-card h-100">
                    <div class="casual-new-product-card-thumb">
                        <a href="{{ $pUrl }}">
                            @if($pImg)
                                <img src="{{ $pImg }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="casual-new-thumb-placeholder"><i class="las la-shopping-bag"></i></div>
                            @endif
                        </a>

                        <div class="casual-new-wishlist">
                            <a class="add-to-wishlist-btn cart-loading" href="javascript:void(0)"
                               data-product_id="{{ $product->id }}">
                                <i class="lar la-heart"></i>
                            </a>
                        </div>

                        @if($hasDiscount || !empty($product->badge))
                        <div class="cs-product-badges">
                            @if($hasDiscount)
                                <span class="cs-product-badge cs-product-badge-sale">{{ $discPct }}% {{ __('Off') }}</span>
                            @endif
                            @if(!empty($product->badge))
                                <span class="cs-product-badge cs-product-badge-new">{{ $product->badge->name }}</span>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="casual-new-product-card-contents">
                        <div class="casual-new-product-category">
                            {{ $product->category?->name ? strtoupper($product->category->name) : '' }}
                        </div>
                        <h5 class="casual-new-product-title">
                            <a href="{{ $pUrl }}">{!! Str::words($product->name, 10) !!}</a>
                        </h5>
                        <div class="casual-new-product-price">
                            <span class="casual-new-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                            @if($oldPrice)
                                <span class="casual-new-price-regular">{{ amount_with_currency_symbol($oldPrice) }}</span>
                            @endif
                        </div>
                        <div class="casual-new-product-action">
                            <a href="javascript:void(0)" data-product_id="{{ $product->id }}"
                               class="add-to-cart-btn casual-new-add-to-cart">
                                <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
