@extends('tenant.frontend.frontend-page-master')

@php
    $page_title = $wishlist ? __('Wishlist') : __('Cart');
@endphp

@section('title') {{ $page_title }} @endsection
@section('page-title') {{ $page_title }} @endsection

@section('content')
<div class="bk-page-banner">
    <div class="container">
        <h1>{{ $page_title }}</h1>
        <div class="bk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ $page_title }}</span>
        </div>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

{{-- Step Progress (cart only) --}}
@if(!$wishlist)
<div class="container" style="padding-top:32px;">
    <div class="bk-step-progress">
        <div class="bk-progress-item active">
            <div class="bk-progress-icon">1</div>
            <span>{{ __('Cart') }}</span>
        </div>
        <div class="bk-progress-sep"></div>
        <div class="bk-progress-item">
            <div class="bk-progress-icon">2</div>
            <span>{{ __('Checkout') }}</span>
        </div>
        <div class="bk-progress-sep"></div>
        <div class="bk-progress-item">
            <div class="bk-progress-icon">3</div>
            <span>{{ __('Confirmed') }}</span>
        </div>
    </div>
</div>
@endif

<div class="container" style="{{ $wishlist ? 'padding-top:36px;' : '' }}padding-bottom:72px;">
    <div class="row g-4">

        {{-- Cart Table --}}
        <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
            <div style="background:#fff;border:1px solid var(--bk-border);border-radius:var(--bk-radius);overflow:hidden;">
                <div class="table-responsive">
                    <table class="bk-cart-table">
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
                            @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
                            <tr class="table-cart-row" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bk-cart-img">
                                            {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                        </div>
                                        <div>
                                            <a href="{{ theme_product_url($slug) }}"
                                               style="font-size:14px;font-weight:700;color:var(--bk-dark);text-decoration:none;">
                                                {{ $data->name }}
                                            </a>
                                            @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                                            <div style="font-size:12px;color:var(--bk-muted);margin-top:3px;">
                                                @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                                                @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                                                @if($data?->options?->attributes)
                                                    @foreach($data->options->attributes as $attrKey => $attrVal)
                                                        · {{ $attrKey }}: {{ $attrVal }}
                                                    @endforeach
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size:15px;font-weight:600;color:var(--bk-rose);">{{ amount_with_currency_symbol($data->price) }}</span>
                                </td>
                                <td>
                                    <div class="bk-qty">
                                        @if(!$wishlist)
                                            <button type="button" class="substract"><i class="mdi mdi-minus"></i></button>
                                        @endif
                                        <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1"
                                               {{ $wishlist ? 'disabled readonly' : '' }}>
                                        @if(!$wishlist)
                                            <button type="button" class="plus"><i class="mdi mdi-plus"></i></button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size:15px;font-weight:600;color:var(--bk-rose);">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
                                </td>
                                <td class="bk-cart-action-cell {{ $wishlist ? 'bk-cart-action-wishlist' : '' }}"
                                    data-product_hash_id="{{ $data->rowId }}">
                                    @if($wishlist)
                                        <div class="move-to-wishlist bk-cart-action-btn" title="{{ __('Move to Cart') }}">
                                            <i class="mdi mdi-cart-arrow-down"></i>
                                        </div>
                                    @else
                                        @auth
                                        <button class="save-for-later-btn bk-action-icon-btn" type="button"
                                                data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
                                            <i class="mdi mdi-heart-outline"></i>
                                        </button>
                                        @endauth
                                    @endif
                                    <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }} bk-cart-action-btn">
                                        <button class="bk-remove-btn bk-action-icon-btn" type="button" title="{{ __('Remove') }}">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bottom Actions --}}
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mt-4">
                <a href="{{ theme_shop_url() }}" class="bk-btn bk-btn-outline">
                    <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
                </a>
                @if(!$wishlist)
                <a href="javascript:void(0)" class="bk-btn bk-btn-outline clear-cart-btn" style="color:var(--bk-rose);border-color:var(--bk-rose);">
                    <i class="mdi mdi-trash-can-outline"></i> {{ __('Clear Cart') }}
                </a>
                @endif
            </div>
        </div>

        {{-- Order Summary --}}
        @if(!$wishlist)
        <div class="col-lg-4">
            {{-- Coupon --}}
            <div class="bk-cart-coupon-card">
                <div class="bk-cart-coupon-label">
                    <i class="mdi mdi-ticket-percent-outline bk-cart-coupon-icon"></i>
                    {{ __('Have a coupon?') }}
                </div>
                <div class="d-flex gap-2">
                    <input type="text" id="bk-cart-coupon-input" class="bk-coupon-input flex-grow-1"
                           placeholder="{{ __('Enter coupon code') }}">
                    <button type="button" class="bk-btn bk-btn-rose bk-btn-sm" id="bk-cart-coupon-btn">
                        {{ __('Apply') }}
                    </button>
                </div>
                <div id="bk-cart-coupon-msg" class="bk-cart-coupon-msg"></div>
            </div>

            <div class="bk-order-summary">
                <div class="bk-summary-title">{{ __('Order Summary') }}</div>

                <div class="coupon-contents">
                    @php
                        $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
                        $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
                    @endphp

                    <div class="bk-summary-row">
                        <span>{{ __('Subtotal') }}</span>
                        <span class="bk-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
                    </div>
                    <div class="bk-summary-row">
                        <span>{{ __('Tax (Incl)') }}</span>
                        <span class="bk-summary-price">--</span>
                    </div>
                    <div class="bk-summary-row total">
                        <span>{{ __('Total Amount') }}</span>
                        <span class="bk-summary-price">{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>
                </div>

                {!! apply_filters('nazmart:cart_summary', '') !!}

                <a href="{{ theme_checkout_url() }}"
                   class="bk-btn bk-btn-rose w-100 justify-content-center mt-4"
                   style="font-size:15px;padding:13px;">
                    {{ __('Proceed to Checkout') }} <i class="mdi mdi-arrow-right"></i>
                </a>

                <p style="font-size:11px;color:var(--bk-muted);text-align:center;margin-top:12px;line-height:1.5;">
                    <i class="mdi mdi-shield-check" style="color:var(--bk-rose);"></i>
                    {{ __('Secure & encrypted checkout') }}
                </p>

                <div class="d-flex justify-content-center gap-3 mt-2" style="font-size:20px;color:var(--bk-muted);">
                    <i class="mdi mdi-lock" title="{{ __('Secure') }}"></i>
                    <i class="mdi mdi-credit-card-outline" title="{{ __('Cards accepted') }}"></i>
                    <i class="mdi mdi-shield-check" title="{{ __('Protected') }}"></i>
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
    $(document).on('click', '.close-table-cart, .close-table-cart .bk-remove-btn', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
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
    $(document).on('click', '.close-table-wishlist, .close-table-wishlist .bk-remove-btn', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
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
    $(document).on('click', '#bk-cart-coupon-btn', function () {
        var code = $('#bk-cart-coupon-input').val().trim();
        if (!code) return;
        $.ajax({
            url: '{{ theme_apply_coupon_url() }}',
            type: 'GET',
            data: { coupon_code: code },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.loader').hide();
                var msg = $('#bk-cart-coupon-msg');
                if (data.type === 'success') {
                    msg.html('<span class="bk-coupon-success"><i class="mdi mdi-check-circle-outline"></i> ' + data.msg + '</span>');
                    if (data.cart_price_markup) { $('.coupon-contents').html(data.cart_price_markup); }
                } else {
                    msg.html('<span class="bk-coupon-error"><i class="mdi mdi-alert-circle-outline"></i> ' + (data.msg || '{{ __("Invalid coupon") }}') + '</span>');
                }
            },
            error: function () { $('.loader').hide(); }
        });
    });

})(jQuery);
</script>
@endsection
