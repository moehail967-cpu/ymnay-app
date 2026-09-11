<section class="mc-page-section" style="padding-top:48px;">
    <div class="container">
        <h2 class="mc-center-title" style="text-align:left;margin-bottom:28px;">{{ __('Related Products') }}</h2>
        <div class="mc-store-grid">
            @foreach($related_products as $product)
                @php
                    $img_data = get_attachment_image_by_id($product->image_id, 'grid');
                    $img      = !empty($img_data) ? $img_data['img_url'] : '';
                    $alt      = !empty($img_data) ? $img_data['img_alt'] : $product->name;

                    $discount = null;
                    if ($product->price) {
                        $discount = round((($product->price - $product->sale_price) / $product->price) * 100);
                    }
                @endphp

                <div class="mc-card">
                    <div class="mc-card-img-wrap">
                        <a href="{{ dynamicRoute($product->slug) }}">
                            <img class="mc-card-img" src="{{ $img }}" alt="{{ $alt }}">
                        </a>
                        <button class="mc-card-wish add-to-wishlist-btn"
                                data-product_id="{{ $product->id }}"
                                title="{{ __('Add to Wishlist') }}">
                            <i class="lar la-heart"></i>
                        </button>
                        @if($discount != null && $discount > 0)
                            <span class="mc-card-badge">{{ $discount }}% {{ __('Off') }}</span>
                        @endif
                        @if(!empty($product->badge))
                            <span class="mc-card-badge mc-card-badge-new">{{ $product?->badge?->name }}</span>
                        @endif
                    </div>
                    <div class="mc-card-body">
                        <a href="{{ dynamicRoute($product->slug) }}" class="mc-card-name">
                            {!! product_limited_text($product->name) !!}
                        </a>
                        {!! render_product_star_rating_markup_with_count($product) !!}
                        <div class="mc-card-footer">
                            <div class="mc-card-prices">
                                {!! product_prices($product, 'color-two') !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
