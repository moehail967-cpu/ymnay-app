<section class="cs-compare-section">
    <div class="container">
        <div class="row g-4">
            @foreach($product_array as $product)
            @php
                $data      = get_product_dynamic_price($product);
                $salePrice = $data['sale_price'];
                $regPrice  = $data['regular_price'];
                $discount  = $data['discount'];
                $pImg      = get_attachment_image_by_id($product->image);
                $pImgUrl   = !empty($pImg) ? $pImg['img_url'] : null;
                $pUrl      = theme_product_url($product->slug);
            @endphp
            <div class="col-lg-4 col-md-6">
                <div class="cs-compare-card">
                    <div class="cs-compare-thumb">
                        <a href="{{ $pUrl }}">
                            @if($pImgUrl)
                                <img src="{{ $pImgUrl }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="casual-new-thumb-placeholder"><i class="las la-shopping-bag"></i></div>
                            @endif
                        </a>
                    </div>
                    <div class="cs-compare-body">
                        <div class="cs-compare-name">
                            <a href="{{ $pUrl }}">{{ $product->name }}</a>
                        </div>
                        <div class="cs-compare-price-row">
                            <span class="cs-compare-price">{{ amount_with_currency_symbol($salePrice) }}</span>
                            @if($discount && $regPrice)
                                <span class="cs-compare-old-price">{{ amount_with_currency_symbol($regPrice) }}</span>
                            @endif
                        </div>
                        <ul class="cs-compare-list">
                            @if($product->category?->name)
                            <li>
                                <span class="cs-compare-key">{{ __('Category') }}</span>
                                <span class="cs-compare-val">{{ $product->category->name }}</span>
                            </li>
                            @endif
                            @if($product->inventory?->sku)
                            <li>
                                <span class="cs-compare-key">{{ __('SKU') }}</span>
                                <span class="cs-compare-val">{{ $product->inventory->sku }}</span>
                            </li>
                            @endif
                            <li>
                                <span class="cs-compare-key">{{ __('Stock') }}</span>
                                <span class="cs-compare-val">
                                    @if(optional($product->inventory)->stock_count > 0)
                                        {{ __('In Stock') }}
                                    @else
                                        {{ __('Out of Stock') }}
                                    @endif
                                </span>
                            </li>
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
