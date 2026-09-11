<div class="col-xxl-4 col-lg-5">
    <div class="el-dash-card">
        <h4 class="el-dash-card-title">
            <i class="las la-book"></i> {{ __('Related Books') }}
        </h4>

        @foreach($related_products as $product)
            @php
                $dynamic_discount = get_digital_product_dynamic_price($product);

                $regular_price = $product->regular_price;
                $sale_price = $product->sale_price;

                if (!is_null($product->promotional_date) && !is_null($product->promotional_price)) {
                    $sale_price = $product->promotional_price;
                }

                $rel_img_data = get_attachment_image_by_id($product->image_id, 'grid');
                $rel_img_url  = !empty($rel_img_data) ? $rel_img_data['img_url'] : '';
                $rel_img_alt  = !empty($rel_img_data) ? $rel_img_data['img_alt'] : $product->name;
            @endphp

            <div class="el-digital-related-item">
                <a href="{{ dynamicRoute($product->slug) }}" class="el-digital-related-img-wrap">
                    <img src="{{ $rel_img_url }}" alt="{{ $rel_img_alt }}" class="el-digital-related-img">
                </a>
                <div class="el-digital-related-content">
                    @if($dynamic_discount['discount'] > 0)
                        <span class="el-card-badge" style="position:static;display:inline-block;font-size:10px;margin-bottom:4px;">{{ $dynamic_discount['discount'] }}% {{ __('off') }}</span>
                    @endif
                    <h5 class="el-digital-related-name">
                        <a href="{{ dynamicRoute($product->slug) }}">{{ Str::words($product->name, 5) }}</a>
                    </h5>
                    @if(!empty($product->additionalFields?->author))
                        <p class="el-digital-related-author">
                            {{ __('by') }} <strong>{{ $product->additionalFields?->author?->name }}</strong>
                        </p>
                    @endif
                    {!! render_product_star_rating_markup_with_count($product) !!}
                    <div class="el-card-prices mt-1">
                        @if($product->accessibility != 'free')
                            @if(!empty($sale_price) && $sale_price > 0)
                                <span class="el-card-price">{{ float_amount_with_currency_symbol($sale_price) }}</span>
                                <span class="el-card-old">{{ float_amount_with_currency_symbol($regular_price) }}</span>
                            @else
                                <span class="el-card-price">{{ float_amount_with_currency_symbol($regular_price) }}</span>
                            @endif
                        @else
                            <span class="el-card-price">{{ __('Free') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
