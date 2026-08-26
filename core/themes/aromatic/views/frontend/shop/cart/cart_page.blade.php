@extends('tenant.frontend.frontend-page-master')

@php
    $page_title = $wishlist ? __('Wishlist') : __('Cart');
@endphp

@section('title') {{ $page_title }} @endsection
@section('page-title') {{ $page_title }} @endsection

@section('content')
<div class="ar-page-banner">
    <div class="container">
        <h1>{{ $page_title }}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <span class="ar-breadcrumb-current">{{ $page_title }}</span>
        </div>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

    {{-- Step progress (cart only) --}}
    @if(!$wishlist)
    <div class="container ar-cart-steps-wrap">
        <div class="ar-steps">
            <div class="ar-step active">
                <div class="ar-step-num">1</div>
                <span>{{ __('Cart') }}</span>
            </div>
            <div class="ar-step-line"></div>
            <div class="ar-step">
                <div class="ar-step-num">2</div>
                <span>{{ __('Checkout') }}</span>
            </div>
            <div class="ar-step-line"></div>
            <div class="ar-step">
                <div class="ar-step-num">3</div>
                <span>{{ __('Confirmed') }}</span>
            </div>
        </div>
    </div>
    @endif

    <div class="container ar-cart-body">
        <div class="row g-4">

            {{-- Cart Table --}}
            <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
                <div class="ar-cart-table-card">
                    <div class="table-responsive">
                        <table class="ar-cart-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cart_tbody">
                            @foreach($cart_data as $key => $data)
                                @php
                                    if (isset($data->options->type) && $data->options->type == \App\Enums\ProductTypeEnum::DIGITAL) {
                                        $slug = \Modules\DigitalProduct\Entities\DigitalProduct::select('id','slug')->find($data->id)?->slug;
                                        $product_type = 'Digital';
                                    } else {
                                        $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug;
                                        $product_type = 'Normal';
                                    }
                                    $product_details_route = dynamicRoute($slug);
                                    $subtotal = calculatePrice($data->price, $data->options) * $data->qty;
                                @endphp
                                <tr class="table-cart-row ar-cart-row"
                                    data-product-id="{{ $key }}"
                                    data-variant-id="{{ $data->options->variant_id ?? '' }}"
                                    data-varinat-id="{{ $data->options->variant_id ?? '' }}">

                                    {{-- Product --}}
                                    <td>
                                        <div class="ar-cart-product">
                                            <div class="ar-cart-img">
                                                {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                            </div>
                                            <div class="ar-cart-info carts-contents">
                                                <a href="{{ $product_details_route }}" class="ar-cart-name">{{ $data->name }}</a>
                                                <span class="ar-cart-variants name-subtitle">
                                                    @if($data?->options?->color_name)
                                                        {{ __('Color:') }} {{ $data->options->color_name }}
                                                    @endif
                                                    @if($data?->options?->size_name)
                                                        · {{ __('Size:') }} {{ $data->options->size_name }}
                                                    @endif
                                                    @if($data?->options?->attributes)
                                                        @foreach($data->options->attributes as $akey => $aval)
                                                            · {{ $akey }}: {{ $aval }}
                                                        @endforeach
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Price --}}
                                    <td>
                                        <span class="ar-cart-price">{{ amount_with_currency_symbol(calculatePrice($data->price, $data->options)) }}</span>
                                    </td>

                                    {{-- Quantity --}}
                                    <td>
                                        @if($wishlist)
                                            <input class="quantity-input" type="hidden" value="{{ $data->qty }}">
                                            <span class="ar-cart-price">{{ $data->qty }}</span>
                                        @elseif(isset($data->options->type) && $data->options->type == \App\Enums\ProductTypeEnum::PHYSICAL)
                                            <div class="ar-qty-wrap product-quantity">
                                                <button type="button" class="ar-qty-btn substract"><i class="mdi mdi-minus"></i></button>
                                                <input type="number" class="ar-qty-input quantity-input" value="{{ $data->qty }}" min="1">
                                                <button type="button" class="ar-qty-btn plus"><i class="mdi mdi-plus"></i></button>
                                            </div>
                                        @else
                                            <input class="quantity-input" type="hidden" value="1">
                                            <span class="ar-cart-price">1</span>
                                        @endif
                                    </td>

                                    {{-- Subtotal --}}
                                    <td>
                                        <span class="ar-cart-price ar-cart-subtotal">{{ float_amount_with_currency_symbol($subtotal) }}</span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="ff-jost {{ $wishlist ? 'ar-cart-action-cell-wish' : '' }}"
                                        data-product_hash_id="{{ $data->rowId }}">
                                        @if($wishlist)
                                            <button class="ar-cart-icon-btn ar-cart-move move-to-wishlist" title="{{ __('Move to Cart') }}">
                                                <i class="mdi mdi-cart-arrow-down"></i>
                                            </button>
                                        @endif
                                        <button class="ar-cart-icon-btn ar-cart-remove close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" title="{{ __('Remove') }}">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Bottom actions --}}
                <div class="ar-cart-footer-btns">
                    <a href="{{ theme_shop_url() }}" class="ar-btn ar-btn-outline">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
                    </a>
                    @if(!$wishlist)
                    <a href="javascript:void(0)" class="ar-btn ar-cart-clear-link clear-cart-btn">
                        <i class="mdi mdi-trash-can-outline"></i> {{ __('Clear Cart') }}
                    </a>
                    @endif
                </div>
            </div>

            {{-- Order Summary --}}
            @if(!$wishlist)
            <div class="col-lg-4">

                {{-- Coupon card --}}
                <div class="ar-coupon-card">
                    <div class="ar-coupon-label">
                        <i class="mdi mdi-ticket-percent-outline ar-coupon-icon"></i>
                        {{ __('Have a coupon?') }}
                    </div>
                    <div class="d-flex gap-2">
                        <input type="text" id="ar-coupon-input" class="ar-coupon-input flex-grow-1"
                               placeholder="{{ __('Enter coupon code') }}">
                        <button type="button" class="ar-btn ar-btn-red ar-btn-sm" id="ar-coupon-btn">
                            {{ __('Apply') }}
                        </button>
                    </div>
                    <div id="ar-coupon-msg" class="ar-coupon-msg"></div>
                </div>

                {{-- Summary card --}}
                <div class="ar-order-summary">
                    <div class="ar-summary-title">{{ __('Order Summary') }}</div>

                    <div class="coupon-contents">
                        @php
                            $subtotal = 0;
                            $carts = \Gloudemans\Shoppingcart\Facades\Cart::content();
                            foreach ($carts ?? [] as $item) {
                                $subtotal += calculatePrice($item->price, $item->options) * $item->qty;
                            }
                            $total = $subtotal;
                        @endphp

                        <div class="ar-summary-lines">
                            <div class="ar-summary-line">
                                <span>{{ __('Sub Total') }}</span>
                                <span class="ar-summary-val">{{ amount_with_currency_symbol($subtotal) }}</span>
                            </div>
                            <div class="ar-summary-line">
                                <span>{{ __('Tax (Incl)') }}</span>
                                <span class="ar-summary-val">--</span>
                            </div>
                            <div class="ar-summary-line ar-summary-total">
                                <span>{{ __('Total Amount') }}</span>
                                <span class="ar-summary-val-total">{{ amount_with_currency_symbol($total) }}</span>
                            </div>
                        </div>

                        {!! apply_filters('nazmart:cart_summary', '') !!}

                        <a href="{{ theme_checkout_url() }}"
                           class="ar-btn ar-btn-red w-100 justify-content-center mt-4">
                            {{ __('Proceed to Checkout') }} <i class="mdi mdi-arrow-right"></i>
                        </a>

                        <p class="ar-cart-secure-note">
                            <i class="mdi mdi-shield-check ar-cart-secure-icon"></i>
                            {{ __('Secure & encrypted checkout') }}
                        </p>

                        <div class="ar-cart-trust-icons">
                            <i class="mdi mdi-lock" title="{{ __('Secure') }}"></i>
                            <i class="mdi mdi-credit-card-outline" title="{{ __('Cards accepted') }}"></i>
                            <i class="mdi mdi-shield-check" title="{{ __('Protected') }}"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

@else
    @include(include_theme_path('shop.cart.cart_empty'))
@endif
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    // Qty — subtract
    $(document).on('click', '.substract', function () {
        var input = $(this).next('.quantity-input');
        if (parseInt(input.val()) > 1) {
            input[0].stepDown(1);
            triggerQtyUpdate($(this));
        }
    });

    // Qty — add
    $(document).on('click', '.plus', function () {
        var input = $(this).prev('.quantity-input');
        input[0].stepUp(1);
        triggerQtyUpdate($(this));
    });

    // Qty — manual input
    $(document).on('change', '.quantity-input', function () {
        triggerQtyUpdate($(this));
    });

    function triggerQtyUpdate(el) {
        var row       = el.closest('[data-product-id]');
        var input     = row.find('.quantity-input');
        var productId = row.data('product-id');
        var variantId = row.data('varinat-id') || row.data('variant-id') || '';
        updateQty(productId, input.val(), variantId);
    }

    function updateQty(productId, qty, variantId) {
        $.ajax({
            url: '{{ theme_cart_update_url() }}',
            type: 'GET',
            data: { product_id: productId, quantity: qty, variant_id: variantId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.type === 'success') { toastr.success(data.msg); }
                else if (data.quantity_msg) { toastr.warning(data.quantity_msg); }
                if (data.markup) { $('#cart_tbody').html(data.markup); }
                if (data.cart_price_markup) { $('.coupon-contents').html(data.cart_price_markup); }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

    // Remove from cart
    $(document).on('click', '.close-table-cart', function () {
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        $.ajax({
            url: '{{ theme_cart_remove_url() }}',
            type: 'GET',
            data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) {
                    toastr.success(data.msg);
                    if (data.empty_cart !== '') {
                        $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn();
                    } else {
                        location.reload();
                    }
                }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    });

    // Remove from wishlist
    $(document).on('click', '.close-table-wishlist', function () {
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        $.ajax({
            url: '{{ theme_wishlist_remove_url() }}',
            type: 'GET',
            data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) {
                    toastr.success(data.msg);
                    if (data.empty_cart !== '') {
                        $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn();
                    } else {
                        location.reload();
                    }
                }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    });

    // Move wishlist item to cart
    $(document).on('click', '.move-to-wishlist', function () {
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        $.ajax({
            url: '{{ theme_wishlist_move_url() }}',
            type: 'GET',
            data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) {
                    toastr.success(data.msg);
                    if (data.empty_cart !== '') {
                        $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn();
                    } else {
                        location.reload();
                    }
                }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    });

    // Clear cart
    $(document).on('click', '.clear-cart-btn', function () {
        $('.loader').show();
        setTimeout(function () {
            window.location.href = '{{ theme_cart_clear_url() }}';
        }, 300);
    });

    // Coupon apply
    $(document).on('click', '#ar-coupon-btn', function () {
        var code = $('#ar-coupon-input').val().trim();
        if (!code) return;
        $.ajax({
            url: '{{ theme_apply_coupon_url() }}',
            type: 'GET',
            data: { coupon_code: code },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.loader').hide();
                var msg = $('#ar-coupon-msg');
                if (data.type === 'success') {
                    msg.html('<span class="ar-coupon-success"><i class="mdi mdi-check-circle-outline"></i> ' + data.msg + '</span>');
                    if (data.cart_price_markup) { $('.coupon-contents').html(data.cart_price_markup); }
                } else {
                    msg.html('<span class="ar-coupon-error"><i class="mdi mdi-alert-circle-outline"></i> ' + (data.msg || '{{ __("Invalid coupon") }}') + '</span>');
                }
            },
            error: function () { $('.loader').hide(); }
        });
    });

})(jQuery);
</script>
@endsection
