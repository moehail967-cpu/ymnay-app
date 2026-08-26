@extends('tenant.frontend.frontend-page-master')

@php $page_title = $wishlist ? __('Wishlist') : __('Cart'); @endphp

@section('title') {{ $page_title }} @endsection
@section('page-title') {{ $page_title }} @endsection

@section('content')
<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ $page_title }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ $page_title }}</span>
        </div>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

    {{-- Step progress (cart only) --}}
    @if(!$wishlist)
    <div class="container cs-cart-steps-wrap">
        <div class="cs-cart-steps">
            <div class="cs-cart-step active">
                <span class="cs-cart-step-num">1</span>
                <span class="cs-cart-step-label">{{ __('Cart') }}</span>
            </div>
            <div class="cs-cart-step-line"></div>
            <div class="cs-cart-step">
                <span class="cs-cart-step-num">2</span>
                <span class="cs-cart-step-label">{{ __('Checkout') }}</span>
            </div>
            <div class="cs-cart-step-line"></div>
            <div class="cs-cart-step">
                <span class="cs-cart-step-num">3</span>
                <span class="cs-cart-step-label">{{ __('Confirmed') }}</span>
            </div>
        </div>
    </div>
    @endif

    <div class="container cs-cart-wrap">
        <div class="row g-4">

            {{-- Cart Table --}}
            <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
                <div class="cs-cart-table-wrap">
                    <div class="table-responsive">
                        <table class="cs-cart-table">
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
                                <tr class="cs-cart-row table-cart-row"
                                    data-product-id="{{ $key }}"
                                    data-variant-id="{{ $data->options->variant_id ?? '' }}"
                                    data-varinat-id="{{ $data->options->variant_id ?? '' }}">
                                    <td>
                                        <div class="cs-cart-product">
                                            <div class="cs-cart-product-img">
                                                {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                            </div>
                                            <div class="cs-cart-product-info">
                                                <span class="cs-cart-product-name">{{ $data->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="cs-cart-price">{{ amount_with_currency_symbol($data->price) }}</span>
                                    </td>
                                    <td>
                                        <div class="cs-cart-qty">
                                            @if(!$wishlist)
                                            <button type="button" class="substract cs-qty-btn">
                                                <i class="las la-minus"></i>
                                            </button>
                                            @endif
                                            <input class="quantity-input cs-cart-qty-input" type="number"
                                                   value="{{ $data->qty }}" min="1"
                                                   {{ $wishlist ? 'disabled readonly' : '' }}>
                                            @if(!$wishlist)
                                            <button type="button" class="plus cs-qty-btn">
                                                <i class="las la-plus"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="cs-cart-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
                                    </td>
                                    <td class="ff-jost {{ $wishlist ? 'cs-cart-actions-cell' : '' }}"
                                        data-product_hash_id="{{ $data->rowId }}">
                                        @if($wishlist)
                                        <button type="button" class="move-to-wishlist cs-cart-action-btn" title="{{ __('Move to Cart') }}">
                                            <i class="las la-shopping-cart"></i>
                                        </button>
                                        @endif
                                        <button type="button" class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }} cs-cart-remove-btn">
                                            <i class="las la-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Bottom actions --}}
                <div class="cs-cart-bottom">
                    <a href="{{ theme_digital_shop_url() }}" class="cs-cart-continue-btn">
                        <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
                    </a>
                    @if(!$wishlist)
                    <a href="javascript:void(0)" class="cs-cart-clear-btn clear-cart-btn">
                        <i class="las la-trash-alt"></i> {{ __('Clear Cart') }}
                    </a>
                    @endif
                </div>
            </div>

            {{-- Order Summary --}}
            @if(!$wishlist)
            <div class="col-lg-4">
                {{-- Coupon --}}
                <div class="cs-coupon-card">
                    <div class="cs-coupon-label">
                        <i class="las la-tag"></i> {{ __('Have a coupon?') }}
                    </div>
                    <div class="cs-coupon-row">
                        <input type="text" id="cs-coupon-input" class="cs-coupon-input"
                               placeholder="{{ __('Enter coupon code') }}">
                        <button type="button" class="cs-apply-btn" id="cs-coupon-btn">
                            {{ __('Apply') }}
                        </button>
                    </div>
                    <div id="cs-coupon-msg" class="cs-coupon-msg"></div>
                </div>

                {{-- Summary --}}
                <div class="cs-order-summary">
                    <div class="cs-summary-title">{{ __('Order Summary') }}</div>
                    <div class="coupon-contents">
                        @php
                            $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
                            $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
                        @endphp
                        <div class="cs-summary-row">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="cs-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
                        </div>
                        <div class="cs-summary-row">
                            <span>{{ __('Tax (Incl)') }}</span>
                            <span class="cs-summary-price">--</span>
                        </div>
                        <div class="cs-summary-row cs-summary-total">
                            <span>{{ __('Total Amount') }}</span>
                            <span class="cs-summary-price">{{ site_currency_symbol() }}{{ $total }}</span>
                        </div>
                    </div>

                    {!! apply_filters('nazmart:cart_summary', '') !!}

                    <a href="{{ theme_checkout_url() }}" class="cs-checkout-btn">
                        {{ __('Proceed to Checkout') }} <i class="las la-arrow-right"></i>
                    </a>

                    <p class="cs-summary-secure">
                        <i class="las la-shield-alt"></i> {{ __('Secure & encrypted checkout') }}
                    </p>

                    <div class="cs-summary-icons">
                        <i class="las la-lock" title="{{ __('Secure') }}"></i>
                        <i class="las la-credit-card" title="{{ __('Cards accepted') }}"></i>
                        <i class="las la-shield-alt" title="{{ __('Protected') }}"></i>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

@else
    @include(include_theme_path('digital-shop.cart.cart_empty'))
@endif
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    $(document).on('click', '.substract', function () {
        var input = $(this).next('.quantity-input');
        if (parseInt(input.val()) > 1) {
            input[0].stepDown(1);
            triggerQtyUpdate($(this));
        }
    });

    $(document).on('click', '.plus', function () {
        var input = $(this).prev('.quantity-input');
        input[0].stepUp(1);
        triggerQtyUpdate($(this));
    });

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

    $(document).on('click', '.clear-cart-btn', function () {
        $('.loader').show();
        setTimeout(function () { window.location.href = '{{ theme_cart_clear_url() }}'; }, 300);
    });

    $(document).on('click', '#cs-coupon-btn', function () {
        var code = $('#cs-coupon-input').val().trim();
        if (!code) return;
        $.ajax({
            url: '{{ theme_apply_coupon_url() }}',
            type: 'GET',
            data: { coupon_code: code },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.loader').hide();
                var msg = $('#cs-coupon-msg');
                if (data.type === 'success') {
                    msg.html('<span class="cs-coupon-ok"><i class="las la-check-circle"></i> ' + data.msg + '</span>');
                    if (data.cart_price_markup) { $('.coupon-contents').html(data.cart_price_markup); }
                } else {
                    msg.html('<span class="cs-coupon-err"><i class="las la-times-circle"></i> ' + (data.msg || '{{ __("Invalid coupon") }}') + '</span>');
                }
            },
            error: function () { $('.loader').hide(); }
        });
    });

})(jQuery);
</script>
@endsection
