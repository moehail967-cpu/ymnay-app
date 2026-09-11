<div class="shop-grid-contents">
    <div class="row g-4">
        @foreach($products as $product)
        @php
            $data_info     = get_digital_product_dynamic_price($product);
            $regular_price = $data_info['regular_price'];
            $sale_price    = $data_info['sale_price'];
            $discount      = $data_info['discount'];
            $campaign_name = $data_info['campaign_name'] ?? null;
            $pImg = null;
            if (!empty($product->image_id)) {
                $pd   = get_attachment_image_by_id($product->image_id, 'grid');
                $pImg = !empty($pd) ? $pd['img_url'] : null;
            }
            $pUrl         = theme_product_url($product->slug);
            $isFree       = $product->accessibility === 'free';
            $displayPrice = $sale_price > 0 ? $sale_price : $regular_price;
        @endphp

        <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6">
            <div class="casual-new-product-card h-100 h-100 d-flex flex-column">
                <div class="casual-new-product-card-thumb">
                    <a href="{{ $pUrl }}">
                        @if($pImg)
                            <img src="{{ $pImg }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <div class="casual-new-thumb-placeholder"><i class="las la-book"></i></div>
                        @endif
                    </a>

                    <div class="casual-new-wishlist">
                        <a class="add-to-wishlist-btn cart-loading" href="javascript:void(0)"
                           data-product_id="{{ $product->id }}">
                            <i class="lar la-heart"></i>
                        </a>
                    </div>

                    @if($discount > 0 || !empty($product->additionalFields?->badge_id) || !is_null($campaign_name))
                    <div class="cs-product-badges">
                        @if($discount > 0)
                            <span class="cs-product-badge cs-product-badge-sale">{{ $discount }}% {{ __('off') }}</span>
                        @endif
                        @if(!empty($product->additionalFields?->badge?->name))
                            <span class="cs-product-badge cs-product-badge-new">{{ $product->additionalFields->badge->name }}</span>
                        @endif
                        @if(!is_null($campaign_name))
                            <span class="cs-product-badge cs-product-badge-new">{{ $campaign_name }}</span>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="casual-new-product-card-contents">
                    @if($product->additionalFields?->author?->name)
                    <div class="casual-new-product-category">
                        {{ strtoupper($product->additionalFields->author->name) }}
                    </div>
                    @endif

                    <h5 class="casual-new-product-title">
                        <a href="{{ $pUrl }}">{!! Str::words($product->name, 10) !!}</a>
                    </h5>

                    <div class="casual-new-product-price">
                        @if($isFree)
                            <span class="casual-new-price-sale cs-price-free">{{ __('Free') }}</span>
                        @else
                            <span class="casual-new-price-sale">{{ float_amount_with_currency_symbol($displayPrice) }}</span>
                            @if($regular_price && $sale_price && $sale_price < $regular_price)
                                <span class="casual-new-price-regular">{{ float_amount_with_currency_symbol($regular_price) }}</span>
                            @endif
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

    @if(count($links) > 1)
    <div class="cs-pagination mt-4">
        <ul class="cs-pagination-list">
            @foreach($links as $link)
            <li>
                <a data-page="{{ $loop->iteration }}" href="{{ $link }}"
                   class="cs-page-btn {{ $loop->iteration === $current_page ? 'active' : '' }}">
                    {{ $loop->iteration }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
