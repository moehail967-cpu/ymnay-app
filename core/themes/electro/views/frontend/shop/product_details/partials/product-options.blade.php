<div class="single-shop-details-wrapper el-pd-options">

    {{-- Campaign Countdown --}}
    @if($campaign_product !== null && $campaign_product->status !== 'draft')
        <div class="campaign_countdown_wrapper mb-4">
            <h3 class="text-capitalize text-start mb-3">{{ $campaign_name }}</h3>
            @if($campaign_active)
                <div class="global-timer"></div>
            @else
                <div class="text-capitalize alert alert-warning">
                    <h5>{{ __('The Campaign is over or not yet started') }}</h5>
                </div>
            @endif
        </div>
    @endif

    {{-- Badge --}}
    @if(!empty($product->badge))
        <span class="el-card-badge" style="position:static;display:inline-block;margin-bottom:12px;">{{ $product?->badge?->name }}</span>
    @endif

    {{-- Title + Wishlist + Compare --}}
    <div class="el-pd-title-row">
        <h1 class="el-pd-title">{!! $product->name !!}</h1>
        <div class="el-pd-actions">
            <a href="javascript:void(0)"
               class="{{ $quickView ? 'quick_view_add_to_wishlist' : 'add_to_wishlist_single_page' }} add-to-wishlist-btn el-pd-action-btn"
               title="{{ __('Add to Wishlist') }}">
                <i class="lar la-heart"></i>
            </a>
            <a href="javascript:void(0)"
               class="el-pd-action-btn {{ $quickView ? 'quick-view-' : '' }}compare-btn add-to-compare-btn"
               data-product_id="{{ $product->id }}"
               title="{{ __('Add to Compare') }}">
                <i class="las la-retweet"></i>
            </a>
        </div>
    </div>

    {{-- Rating --}}
    {!! render_product_star_rating_markup_with_count($product) !!}

    {{-- Price + Stock in one row --}}
    @php
        $final_price = calculatePrice($sale_price, $product);
    @endphp
    <div class="el-pd-price-stock-row">
        <div class="el-pd-price-row">
            <span class="el-pd-price flash-prices"
                  data-main-price="{{ $final_price }}"
                  data-currency-symbol="{{ site_currency_symbol() }}"
                  id="{{ $quickView ? 'quick-view-price' : 'price' }}">
                {{ amount_with_currency_symbol($final_price) }}
            </span>
            @if($deleted_price != null && $deleted_price != $final_price)
                <span class="el-pd-price-old flash-old-prices">{{ amount_with_currency_symbol($deleted_price) }}</span>
            @endif
        </div>
        <div class="el-pd-stock-row">
            <span class="el-pd-stock-label">{{ __('Status:') }}</span>
            <a id="{{ $quickView ? 'quick_view_' : '' }}stock" href="javascript:void(0)"
               data-stock-text='{!! $stock_count > 0 ? '<span class="text-success">'.__('In Stock').'</span>' : '<span class="text-danger">'.__('Out of Stock').'</span>' !!}'
               class="el-pd-stock-val">
                {!! $stock_count > 0 ? '<span class="text-success">'.__('In Stock').'</span>' : '<span class="text-danger">'.__('Out of Stock').'</span>' !!}
            </a>
        </div>
    </div>

    {{-- Summary --}}
    @if(!empty($product->summary))
        <p class="el-pd-summary">{{ $product->summary }}</p>
    @endif

    {{-- Divider --}}
    <div class="el-pd-divider"></div>

    {{-- Attributes (Size, Color, Custom) --}}
    <div class="el-pd-variants">
        {{-- Size --}}
        @if($productSizes->count() > 0 && !empty(current(current($productSizes))))
            <div class="value-input-area el-pd-variant-row {{ $quickView ? 'quick-view-value-input-area' : '' }}">
                <span class="el-pd-variant-label">
                    <strong>{{ __('Size:') }}</strong>
                    <input readonly class="form--input value-size" name="size" type="text" value="">
                    <input type="hidden" id="selected_size">
                </span>
                <ul class="size-lists select-list {{ $quickView ? 'quick-view-size-lists' : '' }}" data-type="Size">
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

        {{-- Color --}}
        @if($productColors->count() > 0 && current(current($productColors)))
            <div class="value-input-area el-pd-variant-row {{ $quickView ? 'quick-view-value-input-area' : '' }}">
                <span class="el-pd-variant-label">
                    <strong>{{ __('Color:') }}</strong>
                    <input readonly class="form--input value-size" name="color" type="text" value="">
                    <input type="hidden" id="selected_color">
                </span>
                <ul class="size-lists color-list {{ $quickView ? 'quick-view-size-lists' : '' }}" data-type="Color">
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

        {{-- Custom Attributes --}}
        @foreach($available_attributes as $attribute => $options)
            <div class="value-input-area el-pd-variant-row {{ $quickView ? 'quick-view-value-input-area' : '' }}">
                <span class="el-pd-variant-label">
                    <strong>{{ $attribute }}</strong>
                    <input readonly class="form--input value-size" type="text" value="">
                    <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
                </span>
                <ul class="size-lists {{ $quickView ? 'quick-view-size-lists' : '' }}" data-type="{{ $attribute }}">
                    @foreach($options as $option)
                        <li class="list"
                            data-value="{{ $option }}"
                            data-display-value="{{ $option }}">
                            {{ $option }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    {{-- Quantity --}}
    <div class="el-pd-qty-row">
        <span class="el-pd-qty-label">{{ __('Quantity:') }}</span>
        <div class="el-pd-qty-ctrl">
            <button class="{{ $quickView ? 'quick-view-' : '' }}substract substract el-pd-qty-btn" type="button">
                <i class="las la-minus"></i>
            </button>
            <input class="{{ $quickView ? 'quick-view-' : '' }}quantity-input quantity-input qty_"
                   type="number"
                   id="{{ $quickView ? 'quick-view-' : '' }}quantity"
                   name="quantity"
                   value="1" min="1">
            <button class="{{ $quickView ? 'quick-view-' : '' }}plus plus el-pd-qty-btn" type="button">
                <i class="las la-plus"></i>
            </button>
        </div>

        @php
            if ($product?->inventory?->stock_count > 0) {
                $text_color = 'text-success';
                $text = __('Only!').' '.$product?->inventory?->stock_count.' '.__('Item Left');
            } else {
                $text_color = 'text-danger';
                $text = __('No Item Left!');
            }
        @endphp
        <span class="stock-available color-stock {{ $text_color }} el-pd-stock-count"
              id="{{ $quickView ? 'quick_view_' : '' }}item_left"
              data-stock-text="{{ $text }}">
            {{ $text }}
        </span>
    </div>

    {{-- Add to Cart / Buy Now --}}
    @if($product?->inventory?->stock_count > 0)
        <div class="el-pd-cta-row">
            <a href="javascript:void(0)"
               class="{{ $quickView ? 'quick_view_add_to_cart' : 'add_to_cart_single_page' }} el-btn el-btn-primary el-btn-block cart-loading">
                <i class="las la-shopping-bag"></i> {{ __('Add to Cart') }}
            </a>
            <a href="javascript:void(0)"
               class="{{ $quickView ? 'quick_view_but_now' : 'but_now_single_page' }} el-btn el-btn-outline el-btn-block cart-loading">
                {{ __('Buy Now') }}
            </a>
        </div>
    @endif

    {{-- Delivery Options (trust signals right after CTA) --}}
    @if($product->product_delivery_option != null)
        <div class="el-pd-trust-strip">
            @foreach($product->product_delivery_option as $option)
                <div class="el-pd-trust-item">
                    <div class="el-pd-trust-icon"><i class="{{ $option->icon }}"></i></div>
                    <div class="el-pd-trust-text">
                        <strong>{{ $option->title }}</strong>
                        <span>{{ $option->sub_title }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Guaranteed Safe Checkout --}}
    @php
        $payment_gateway_images = \App\Models\PaymentGateway::where('status', 1)->permittedPaymentGateway()->get('image')->pluck('image');
    @endphp
    @if($payment_gateway_images->isNotEmpty())
        <div class="el-pd-safe-checkout">
            <span class="el-pd-safe-label"><i class="las la-lock"></i> {{ __('Guaranteed Safe Checkout') }}</span>
            <ul class="el-pd-payment-list">
                @foreach($payment_gateway_images as $image)
                    <li><a href="javascript:void(0)">{!! render_image_markup_by_attachment_id($image) !!}</a></li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Divider --}}
    <div class="el-pd-divider"></div>

    {{-- Category / Unit / SKU --}}
    <div class="el-pd-meta-list">
        <ul class="el-pd-meta-items">
            <li class="el-pd-meta-item">
                <span class="el-pd-meta-key">{{ __('Category:') }}</span>
                <span class="el-pd-meta-val">
                    <a href="{{ dynamicRoute($product?->category?->slug) }}">{{ $product?->category?->name }}</a>
                    @if($product?->subCategory?->slug)
                        / <a href="{{ dynamicRoute($product?->subCategory?->slug) }}">{{ $product?->subCategory?->name }}</a>
                    @endif
                    @foreach($product->childCategory ?? [] as $child_category)
                        @if($loop->first) / @endif
                        <a href="{{ dynamicRoute($child_category?->slug) }}">{{ $child_category->name }}</a>
                        @if(!$loop->last),@endif
                    @endforeach
                </span>
            </li>
            @if($product->uom != null)
                <li class="el-pd-meta-item">
                    <span class="el-pd-meta-key">{{ __('Unit:') }}</span>
                    <span class="el-pd-meta-val">{{ $product?->uom?->quantity }} {{ $product?->uom?->uom_details?->name }}</span>
                </li>
            @endif
            <li class="el-pd-meta-item">
                <span class="el-pd-meta-key">{{ __('SKU:') }}</span>
                <span class="el-pd-meta-val">{{ $product?->inventory?->sku }}</span>
            </li>
        </ul>
    </div>

    {{-- Social Share --}}
    <div class="wishlist-share social_share_parent el-pd-share">
        @php
            $product_primary_image = get_attachment_image_by_id($product->image_id);
            $product_primary_image = $product_primary_image ? $product_primary_image['img_url'] : '';
        @endphp
        <ul class="d-flex mt-2">
            {!! single_post_share($product->slug, $product->name, $product_primary_image) !!}
        </ul>
    </div>

    {{-- Custom Specifications --}}
    @if($custom_specifications->isNotEmpty())
        <div class="el-pd-specs">
            <h6 class="el-pd-specs-title">{{ __('Product Details:') }}</h6>
            <table class="el-dash-table specification-table">
                <thead>
                    <tr>
                        <th>{{ __('Specification') }}</th>
                        <th>{{ __('Value') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($custom_specifications as $spec)
                        <tr>
                            <td class="spec-title">{{ $spec->title }}</td>
                            <td class="spec-value">{{ $spec->value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
