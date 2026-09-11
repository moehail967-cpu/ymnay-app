<section class="cs-compare-section">
    <div class="container">
        <div class="row g-4">
            @foreach($product_array as $product)
            @php
                $dynamic   = get_digital_product_dynamic_price($product);
                $salePrice = $product->promotional_date && $product->promotional_price
                    ? $product->promotional_price
                    : $product->sale_price;
                $regPrice  = $product->regular_price;
                $discount  = $dynamic['discount'];
                $pImg      = get_attachment_image_by_id($product->image);
                $pImgUrl   = !empty($pImg) ? $pImg['img_url'] : null;
                $pUrl      = theme_product_url($product->slug);
                $isFree    = $product->accessibility === 'free';
            @endphp
            <div class="col-lg-4 col-md-6">
                <div class="cs-compare-card">
                    <div class="cs-compare-thumb">
                        <a href="{{ $pUrl }}">
                            @if($pImgUrl)
                                <img src="{{ $pImgUrl }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="casual-new-thumb-placeholder"><i class="las la-book"></i></div>
                            @endif
                        </a>
                    </div>
                    <div class="cs-compare-body">
                        <div class="cs-compare-name">
                            <a href="{{ $pUrl }}">{{ $product->name }}</a>
                        </div>
                        <div class="cs-compare-price-row">
                            @if($isFree)
                                <span class="cs-compare-price cs-price-free">{{ __('Free') }}</span>
                            @else
                                <span class="cs-compare-price">{{ float_amount_with_currency_symbol($salePrice ?: $regPrice) }}</span>
                                @if($discount > 0 && $regPrice)
                                    <span class="cs-compare-old-price">{{ float_amount_with_currency_symbol($regPrice) }}</span>
                                @endif
                            @endif
                        </div>
                        <ul class="cs-compare-list">
                            @if($product->additionalFields?->author?->name)
                            <li>
                                <span class="cs-compare-key">{{ __('Author') }}</span>
                                <span class="cs-compare-val">{{ $product->additionalFields->author->name }}</span>
                            </li>
                            @endif
                            @if($product->category?->name)
                            <li>
                                <span class="cs-compare-key">{{ __('Category') }}</span>
                                <span class="cs-compare-val">{{ $product->category->name }}</span>
                            </li>
                            @endif
                        </ul>
                        <a href="javascript:void(0)" class="cs-compare-remove close-compare"
                           data-product-id="{{ $product->id }}">
                            <i class="las la-times"></i> {{ __('Remove') }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
