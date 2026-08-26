<section class="el-page-section" style="padding-top:48px;">
    <div class="container">
        <h2 class="el-center-title" style="text-align:left;margin-bottom:28px;">{{ __('Related Products') }}</h2>
        <div class="el-store-grid">
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

                <div class="el-card">
                    <div class="el-card-img-wrap">
                        <a href="{{ dynamicRoute($product->slug) }}">
                            <img class="el-card-img" src="{{ $img }}" alt="{{ $alt }}">
                        </a>
                        <button class="el-card-wish add-to-wishlist-btn"
                                data-product_id="{{ $product->id }}"
                                title="{{ __('Add to Wishlist') }}">
                            <i class="lar la-heart"></i>
                        </button>
                        @if($discount != null && $discount > 0)
                            <span class="el-card-badge">{{ $discount }}% {{ __('Off') }}</span>
                        @endif
                        @if(!empty($product->badge))
                            <span class="el-card-badge el-card-badge-new">{{ $product?->badge?->name }}</span>
                        @endif
                    </div>
                    <div class="el-card-body">
                        <a href="{{ dynamicRoute($product->slug) }}" class="el-card-name">
                            {!! product_limited_text($product->name) !!}
                        </a>
                        {!! render_product_star_rating_markup_with_count($product) !!}
                        <div class="el-card-footer">
                            <div class="el-card-prices">
                                {!! product_prices($product, 'color-two') !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
