<div class="single-shop-details-wrapper tn-product-options">

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

    {{-- Title + Badge --}}
    <div class="name_badge">
        <h2 class="details-title">{{ $product->name }}
            @if(!empty($product->badge))
                <span class="global-card-thumb-badge-box bg-color-new">{{ $product?->badge?->name }}</span>
            @endif
        </h2>
    </div>

    {{-- Rating --}}
    {!! render_product_star_rating_markup_with_count($product) !!}

    {{-- Stock Status --}}
    <div class="status-details d-flex align-items-center mt-3">
        <span class="status-details-title fw-500 me-3">{{ __('Status') }}</span>
        <a id="{{ $quickView ? 'quick_view_' : '' }}stock" href="javascript:void(0)"
           data-stock-text='{!! $stock_count > 0 ? '<span class="text-success">'.__('In Stock').'</span>' : '<span class="text-danger">'.__('Out of Stock').'</span>' !!}'
           class="status-details-title color-stock fw-600">
            {!! $stock_count > 0 ? '<span class="text-success">'.__('In Stock').'</span>' : '<span class="text-danger">'.__('Out of Stock').'</span>' !!}
        </a>
    </div>

    {{-- Price --}}
    <div class="price-update-through mt-3">
        <h3 class="ff-rubik flash-prices"
            data-main-price="{{ $sale_price }}"
            data-currency-symbol="{{ site_currency_symbol() }}"
            id="{{ $quickView ? 'quick-view-price' : 'price' }}">
            {{ amount_with_currency_symbol($sale_price) }}
        </h3>
        @if($deleted_price != null)
            <span class="fs-22 flash-old-prices">{{ amount_with_currency_symbol($deleted_price) }}</span>
        @endif
    </div>

    {{-- Out of stock notify --}}
    @if($stock_count <= 0)
    <div id="stock_notify_form_wrap" class="mt-4">
        <p class="text-danger fw-500 mb-2">{{ __('This product is currently out of stock.') }}</p>
        <div class="d-flex gap-2 flex-wrap">
            <input type="email" id="stock_notify_email" class="form--input flex-grow-1"
                   placeholder="{{ __('Your email address') }}" style="max-width:280px;">
            <button type="button" class="cmn-btn cmn-btn-bg-heading" onclick="submitStockNotify()">
                {{ __('Notify Me') }}
            </button>
        </div>
        <div id="stock_notify_msg" class="mt-2"></div>
        <script>
        function submitStockNotify() {
            var email = document.getElementById('stock_notify_email').value.trim();
            var msgEl = document.getElementById('stock_notify_msg');
            if (!email) { msgEl.innerHTML = '<span class="text-danger">{{ __("Please enter your email.") }}</span>'; return; }
            $.post('{{ route("tenant.frontend.stock.notify") }}', {
                _token: '{{ csrf_token() }}',
                product_id: {{ $product->id }},
                email: email,
            }).done(function(res) {
                msgEl.innerHTML = '<span class="text-success">' + res.msg + '</span>';
                document.getElementById('stock_notify_email').value = '';
            }).fail(function() {
                msgEl.innerHTML = '<span class="text-danger">{{ __("Something went wrong. Please try again.") }}</span>';
            });
        }
        </script>
    </div>
    @endif

    {{-- Variants --}}
    <div class="value-input-area mt-3">
        @if($productSizes->count() > 0 && !empty(current(current($productSizes))))
            <div class="value-input-area single-input-list mt-4 size_list {{ $quickView ? 'quick-view-value-input-area' : '' }}">
                <span class="input-title fw-500 color-heading">
                    <strong class="color-light">{{ __('Size:') }}</strong>
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

        @if($productColors->count() > 0 && current(current($productColors)))
            <div class="value-input-area single-input-list mt-4 color_list {{ $quickView ? 'quick-view-value-input-area' : '' }}">
                <span class="input-title fw-500 color-heading">
                    <strong class="color-light">{{ __('Color:') }}</strong>
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

        @foreach($available_attributes as $attribute => $options)
            <div class="value-input-area single-input-list mt-4 attribute_options_list {{ $quickView ? 'quick-view-value-input-area' : '' }}">
                <span class="input-title fw-500 color-heading input-list">
                    <strong class="color-light">{{ $attribute }}</strong>
                    <input readonly class="form--input value-size" type="text" value="">
                    <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
                </span>
                <ul class="size-lists {{ $quickView ? 'quick-view-size-lists' : '' }}" data-type="{{ $attribute }}">
                    @foreach($options as $option)
                        <li class="list" data-value="{{ $option }}" data-display-value="{{ $option }}">{{ $option }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    {{-- Quantity --}}
    <div class="quantity-area mt-4">
        <div class="quantity-flex">
            <span class="quantity-title color-heading fw-500">{{ __('Quantity:') }}</span>
            <div class="product-quantity">
                <span class="{{ $quickView ? 'quick-view-' : '' }}substract substract"><i class="las la-minus"></i></span>
                <input class="{{ $quickView ? 'quick-view-' : '' }}quantity-input quantity-input qty_"
                       type="number"
                       id="{{ $quickView ? 'quick-view-' : '' }}quantity"
                       name="quantity" value="1">
                <span class="{{ $quickView ? 'quick-view-' : '' }}plus plus"><i class="las la-plus"></i></span>
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
            <a class="stock-available color-stock {{ $text_color }}" href="javascript:void(0)"
               id="{{ $quickView ? 'quick_view_' : '' }}item_left"
               data-stock-text="{{ $text }}">{{ $text }}</a>
        </div>

        {{-- Add to Cart / Buy Now --}}
        <div class="quantity-btn mt-4">
            <div class="btn-wrapper">
                <a href="javascript:void(0)"
                   class="{{ $quickView ? 'quick_view_add_to_cart' : 'add_to_cart_single_page' }} cmn-btn cmn-btn-bg-heading radius-0 w-100 cart-loading">
                    {{ __('Add to Cart') }}
                </a>
            </div>
            <div class="btn-wrapper">
                <a href="javascript:void(0)"
                   class="{{ $quickView ? 'quick_view_but_now' : 'but_now_single_page' }} cmn-btn cmn-btn-bg-steam radius-0 w-100 cart-loading">
                    {{ __('Buy Now') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Delivery Options (right after CTA) --}}
    @if($product->product_delivery_option != null && $product->product_delivery_option->count())
        <div class="tn-delivery-wrap mt-4">
            @foreach($product->product_delivery_option as $option)
                <div class="tn-delivery-item">
                    <span class="tn-delivery-icon"><i class="{{ $option->icon }}"></i></span>
                    <div class="tn-delivery-text">
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
        <div class="tn-safe-checkout mt-3">
            <span class="tn-safe-label"><i class="las la-lock"></i> {{ __('Guaranteed Safe Checkout') }}</span>
            <ul class="tn-payment-list">
                @foreach($payment_gateway_images as $image)
                    <li><a href="javascript:void(0)">{!! render_image_markup_by_attachment_id($image) !!}</a></li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Wishlist / Compare / Share --}}
    <div class="wishlist-compare mt-4">
        <div class="wishlist-compare-btn">
            <a href="javascript:void(0)"
               class="{{ $quickView ? 'quick_view_add_to_wishlist' : 'add_to_wishlist_single_page' }} btn-wishlist share-icon fw-500">
                <span class="icon"><i class="lar la-heart"></i></span>
            </a>
            <a href="javascript:void(0)"
               class="btn-wishlist share-icon fw-500 {{ $quickView ? 'quick-view-' : '' }}compare-btn"
               data-product_id="{{ $product->id }}"
               title="{{ __('Add to Compare') }}">
                <span class="icon"><i class="las la-retweet"></i></span>
            </a>
        </div>
        <div class="wishlist-share social_share_parent">
            <a href="javascript:void(0)" class="share-icon fw-500">
                <span class="icon"><i class="las la-share-alt"></i></span>
            </a>
            @php
                $product_primary_image = get_attachment_image_by_id($product->image_id);
                $product_primary_image = $product_primary_image ? $product_primary_image['img_url'] : '';
            @endphp
            <ul class="social_share_wrapper_item">
                {!! single_post_share(URL::current(), $product->name, $product_primary_image) !!}
            </ul>
        </div>
    </div>

    {{-- Meta: Category / Unit / SKU --}}
    <div class="shop-details-stock shop-border-top pt-4 mt-4">
        <ul class="stock-category">
            <li class="category-list">
                <span class="list-item fw-600">
                    @if($product?->category?->slug)
                        <a href="{{ theme_category_url($product->category->slug) }}">{{ $product->category->name }}</a>
                    @endif
                    @if($product?->subCategory?->slug)
                        | <a href="{{ theme_category_url($product->subCategory->slug) }}">{{ $product->subCategory->name }}</a>
                    @endif
                    @foreach($product->childCategory ?? [] as $child_category)
                        @if($child_category?->slug)
                            | <a href="{{ theme_category_url($child_category->slug) }}">{{ $child_category->name }}</a>
                        @endif
                    @endforeach
                </span>
            </li>
            @if($product->uom != null)
                <li class="category-list">
                    <span>{{ __('Unit:') }}</span>
                    <a class="list-item fw-600" href="javascript:void(0)">
                        {{ $product?->uom?->quantity }} {{ $product?->uom?->uom_details?->name }}
                    </a>
                </li>
            @endif
            <li class="category-list">
                <span>{{ __('SKU:') }}</span>
                <a class="list-item fw-600" href="javascript:void(0)">{{ $product?->inventory?->sku }}</a>
            </li>
        </ul>
    </div>

</div>
