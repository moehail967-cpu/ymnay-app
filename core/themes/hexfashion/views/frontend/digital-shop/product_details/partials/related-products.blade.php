<div class="col-xxl-4 col-lg-5">
    <div class="hf-dash-card">
        <h4 class="hf-dash-card-title">
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

                $rhf_img_data = get_attachment_image_by_id($product->image_id, 'grid');
                $rhf_img_url  = !empty($rhf_img_data) ? $rhf_img_data['img_url'] : '';
                $rhf_img_alt  = !empty($rhf_img_data) ? $rhf_img_data['img_alt'] : $product->name;
            @endphp

            <div class="hf-digital-related-item">
                <a href="{{ dynamicRoute($product->slug) }}" class="hf-digital-related-img-wrap">
                    <img src="{{ $rhf_img_url }}" alt="{{ $rhf_img_alt }}" class="hf-digital-related-img">
                </a>
                <div class="hf-digital-related-content">
                    @if($dynamic_discount['discount'] > 0)
                        <span class="hf-card-badge" style="position:static;display:inline-block;font-size:10px;margin-bottom:4px;">{{ $dynamic_discount['discount'] }}% {{ __('off') }}</span>
                    @endif
                    <h5 class="hf-digital-related-name">
                        <a href="{{ dynamicRoute($product->slug) }}">{{ Str::words($product->name, 5) }}</a>
                    </h5>
                    @if(!empty($product->additionalFields?->author))
                        <p class="hf-digital-related-author">
                            {{ __('by') }} <strong>{{ $product->additionalFields?->author?->name }}</strong>
                        </p>
                    @endif
                    {!! render_product_star_rating_markup_with_count($product) !!}
                    <div class="hf-card-prices mt-1">
                        @if($product->accessibility != 'free')
                            @if(!empty($sale_price) && $sale_price > 0)
                                <span class="hf-card-price">{{ float_amount_with_currency_symbol($sale_price) }}</span>
                                <span class="hf-card-old">{{ float_amount_with_currency_symbol($regular_price) }}</span>
                            @else
                                <span class="hf-card-price">{{ float_amount_with_currency_symbol($regular_price) }}</span>
                            @endif
                        @else
                            <span class="hf-card-price">{{ __('Free') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
