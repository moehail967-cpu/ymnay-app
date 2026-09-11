@php
    $data = get_product_dynamic_price($product);
    $campaign_name = $data['campaign_name'];
    $data_regular_price = $data['regular_price'];
    $data_sale_price = $data['sale_price'];
    $discount = $data['discount'];

    $campaign_product = $product?->campaign_product;
    $sale_price = $data_sale_price;
    $deleted_price = $data_regular_price;
    $campaign_percentage = $discount;
    $stock_count = $campaign_product
        ? ($campaign_product->units_for_sale !== null
            ? max(0, $campaign_product->units_for_sale - (int) $campaign_product->sold_count)
            : null)
        : optional($product->inventory)->stock_count;
    $stock_count = $stock_count > 0 ? $stock_count : 0;
    if ($campaign_product) {
        $campaign_title = \Modules\Campaign\Entities\Campaign::select('id','title')->where("id",$campaign_product?->id)->first();
    }
@endphp

<div class="col-lg-6 col-xl-6">
    <div class="cs-qv-body">
        <h2 class="cs-qv-title">{{ $product->name }}</h2>
        {!! render_product_star_rating_markup_with_count($product) !!}

        <div class="cs-qv-status">
            <span class="cs-qv-status-label">{{ __('Status') }}</span>
            <a id="stock" href="javascript:void(0)" class="cs-qv-status-val">
                {!! $stock_count > 0
                    ? '<span class="text-success">'.__('In Stock').'</span>'
                    : '<span class="text-danger">'.__('Out of Stock').'</span>' !!}
            </a>
        </div>

        <div class="cs-qv-price-row">
            <span class="cs-qv-price-sale flash-prices"
                  data-main-price="{{ $sale_price }}"
                  data-currency-symbol="{{ site_currency_symbol() }}"
                  id="price">
                {{ amount_with_currency_symbol($sale_price) }}
            </span>
            @if($deleted_price)
            <span class="cs-qv-price-regular flash-old-prices">
                {{ amount_with_currency_symbol($deleted_price) }}
            </span>
            @endif
        </div>

        <div class="value-input-area">
            @if($productSizes->count() > 0 && !empty(current(current($productSizes))))
            <div class="cs-qv-attr-group size_list mt-3">
                <span class="cs-qv-attr-label">
                    <strong>{{ __('Size:') }}</strong>
                    <input readonly class="cs-dash-input cs-qv-attr-input" name="size" type="text" value="">
                    <input type="hidden" id="selected_size">
                </span>
                <ul class="size-lists select-list" data-type="Size">
                    @foreach($productSizes as $product_size)
                    @if(!empty($product_size))
                    <li class="list"
                        data-value="{{ optional($product_size)->id }}"
                        data-display-value="{{ optional($product_size)->name }}">
                        {{ optional($product_size)->size_code }}
                    </li>
                    @endif
                    @endforeach
                </ul>
            </div>
            @endif

            @if($productColors->count() > 0 && current(current($productColors)))
            <div class="cs-qv-attr-group color_list mt-3">
                <span class="cs-qv-attr-label">
                    <strong>{{ __('Color:') }}</strong>
                    <input readonly class="cs-dash-input cs-qv-attr-input" name="color" type="text" value="">
                    <input type="hidden" id="selected_color">
                </span>
                <ul class="size-lists color-list select-list" data-type="Color">
                    @foreach($productColors as $product_color)
                    @if(!empty($product_color))
                    <li style="background-color: {{ $product_color->color_code }}"
                        data-value="{{ optional($product_color)->id }}"
                        data-display-value="{{ optional($product_color)->name }}">
                    </li>
                    @endif
                    @endforeach
                </ul>
            </div>
            @endif

            @foreach($available_attributes as $attribute => $options)
            <div class="cs-qv-attr-group attribute_options_list mt-3">
                <span class="cs-qv-attr-label">
                    <strong>{{ $attribute }}</strong>
                    <input readonly class="cs-dash-input cs-qv-attr-input" type="text" value="">
                    <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
                </span>
                <ul class="size-lists" data-type="{{ $attribute }}">
                    @foreach($options as $option)
                    <li class="list" data-value="{{ $option }}" data-display-value="{{ $option }}">
                        {{ $option }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>

        <div class="cs-qv-qty-area">
            <div class="cs-qv-qty-flex">
                <span class="cs-qv-qty-label">{{ __('Quantity:') }}</span>
                <div class="product-quantity cs-qty-wrap">
                    <span class="substract"><i class="las la-minus"></i></span>
                    <input class="quantity-input qty_" type="number" id="quantity" name="quantity" value="1">
                    <span class="plus"><i class="las la-plus"></i></span>
                </div>
                <a class="cs-qv-stock-left text-success" href="javascript:void(0)" id="item_left">
                    {{ __('Only!') }} {{ $product?->inventory?->stock_count }} {{ __('Item left') }}
                </a>
            </div>
            <div class="cs-qv-btn-group">
                <a href="javascript:void(0)"
                   class="add_to_cart_single_page cs-checkout-btn cart-loading">
                    {{ __('Add to Cart') }}
                </a>
                <a href="javascript:void(0)"
                   class="but_now_single_page cs-checkout-btn-outline cart-loading">
                    {{ __('Buy Now') }}
                </a>
            </div>
        </div>

        <div class="cs-qv-actions-row">
            <a href="javascript:void(0)" class="cs-qv-action-link add_to_wishlist_single_page cart-loading">
                <i class="lar la-heart"></i> {{ __('Add to Wishlist') }}
            </a>
            <a href="javascript:void(0)" class="cs-qv-action-link compare-btn cart-loading">
                <i class="las la-retweet"></i> {{ __('Add to Compare') }}
            </a>
            @php
                $product_primary_image = get_attachment_image_by_id($product->image_id);
                $product_primary_image = $product_primary_image ? $product_primary_image['img_url'] : '';
            @endphp
            {!! single_post_share($product->slug, $product->name, $product_primary_image) !!}
        </div>

        <div class="cs-qv-meta">
            <ul class="cs-qv-meta-list">
                @if($product?->category?->name)
                <li class="cs-qv-meta-item">
                    <span class="cs-qv-meta-key">{{ __('Category:') }}</span>
                    <span class="cs-qv-meta-val">{{ $product->category->name }}</span>
                </li>
                @endif
                @if($product?->subCategory?->name)
                <li class="cs-qv-meta-item">
                    <span class="cs-qv-meta-key">{{ __('Sub Category:') }}</span>
                    <span class="cs-qv-meta-val">{{ $product->subCategory->name }}</span>
                </li>
                @endif
                @if($product?->inventory?->sku)
                <li class="cs-qv-meta-item">
                    <span class="cs-qv-meta-key">{{ __('SKU:') }}</span>
                    <span class="cs-qv-meta-val">{{ $product->inventory->sku }}</span>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
