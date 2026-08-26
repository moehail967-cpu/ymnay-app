{{-- Casual: Popular Collection --}}
<section class="cs-popular-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="cs-section-head">
            <h2 class="cs-section-title">{{ $title }}</h2>
            <a href="{{ theme_shop_url() }}" class="cs-section-view-all">{{ __('View All') }} <i class="las la-arrow-right"></i></a>
        </div>

        @if($products->isNotEmpty())
        <div class="cs-popular-grid">
            @foreach($products as $product)
            @php
                $pImg = null;
                if (!empty($product->image_id)) {
                    $pd = get_attachment_image_by_id($product->image_id);
                    $pImg = $pd['img_url'] ?? null;
                }
                $hasDiscount = $product->sale_price && $product->sale_price < $product->price;
                $price    = $hasDiscount ? $product->sale_price : $product->price;
                $oldPrice = $hasDiscount ? $product->price : null;
                $pUrl    = theme_product_url($product->slug ?? $product->id);
                $cartUrl = theme_add_to_cart_url($product->id);
            @endphp
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
                        <a class="add-to-wishlist-btn cart-loading" href="javascript:void(0)" data-product_id="{{ $product->id }}">
                            <i class="lar la-heart"></i>
                        </a>
                    </div>
                    
                    <div class="cs-product-badges">
                        @if($hasDiscount)
                            @php $discountPercent = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
                            <span class="cs-product-badge cs-product-badge-sale">{{ $discountPercent }}% {{ __('Off') }}</span>
                        @endif
                    </div>
                </div>

                <div class="casual-new-product-card-contents">
                    <div class="casual-new-product-category">
                        {{ $product->category?->name ? strtoupper($product->category?->name) : 'BODY CARE' }}
                    </div>
                    
                    <h5 class="casual-new-product-title">
                        <a href="{{ $pUrl }}"> {!! Str::words($product->name, 15) !!} </a>
                    </h5>
                    
                    <div class="casual-new-product-price">
                        <span class="casual-new-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                        @if($oldPrice)
                            <span class="casual-new-price-regular">{{ amount_with_currency_symbol($oldPrice) }}</span>
                        @endif
                    </div>
                    
                    <div class="casual-new-product-action">
                        <a href="javascript:void(0)" data-product_id="{{ $product->id }}" class="add-to-cart-btn casual-new-add-to-cart">
                            <i class="las la-shopping-cart"></i> {{__('Add to Cart')}}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="cs-no-data">{{ __('No products found.') }}</p>
        @endif
    </div>
</section>
