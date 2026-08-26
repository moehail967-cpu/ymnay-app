@extends('tenant.frontend.frontend-page-master')

@php
    $page_title = $wishlist ? __('Wishlist') : __('Cart');
@endphp

@section('title') {{ $page_title }} @endsection
@section('page-title') {{ $page_title }} @endsection

@section('content')
<div class="bp-page-banner">
    <div class="container">
        <h1>{{ $page_title }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span class="current">{{ $page_title }}</span>
        </div>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

{{-- Step Progress (cart only) --}}
@if(!$wishlist)
<div class="container" style="padding-top:32px;">
    <div class="bp-step-progress">
        <div class="bp-progress-item active">
            <div class="bp-progress-icon">1</div>
            <span>{{ __('Cart') }}</span>
        </div>
        <div class="bp-progress-sep"></div>
        <div class="bp-progress-item">
            <div class="bp-progress-icon">2</div>
            <span>{{ __('Checkout') }}</span>
        </div>
        <div class="bp-progress-sep"></div>
        <div class="bp-progress-item">
            <div class="bp-progress-icon">3</div>
            <span>{{ __('Confirmed') }}</span>
        </div>
    </div>
</div>
@endif

<div class="container" style="{{ $wishlist ? 'padding-top:36px;' : '' }}padding-bottom:72px;">
    <div class="row g-4">
        <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
            <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
                <div class="table-responsive">
                    <table class="bp-cart-table">
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
                                        <div class="bp-cart-img">
                                            {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                        </div>
                                        <div>
                                            <a href="{{ theme_product_url($slug) }}" class="bp-cart-item-name">
                                                {{ $data->name }}
                                            </a>
                                            @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                                            <div class="bp-cart-item-meta">
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
                                    <span class="bp-cart-price">{{ amount_with_currency_symbol($data->price) }}</span>
                                </td>
                                <td>
                                    <div class="bp-qty">
                                        @if(!$wishlist)
                                            <button type="button" class="substract"><i class="las la-minus"></i></button>
                                        @endif
                                        <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1"
                                               {{ $wishlist ? 'disabled readonly' : '' }}>
                                        @if(!$wishlist)
                                            <button type="button" class="plus"><i class="las la-plus"></i></button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="bp-cart-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
                                </td>
                                <td class="ff-jost {{ $wishlist ? 'd-flex justify-content-around align-items-center' : '' }}"
                                    data-product_hash_id="{{ $data->rowId }}">
                                    @if($wishlist)
                                        <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;">
                                            <i class="las la-shopping-cart" style="font-size:20px;color:var(--bp-accent);"></i>
                                        </div>
                                    @endif
                                    <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" style="cursor:pointer;">
                                        <button class="bp-remove-btn" type="button">
                                            <i class="las la-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mt-4">
                <a href="{{ theme_shop_url() }}" class="bp-btn bp-btn-outline">
                    <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
                </a>
                @if(!$wishlist)
                <a href="javascript:void(0)" class="bp-btn bp-btn-danger clear-cart-btn">
                    <i class="las la-trash"></i> {{ __('Clear Cart') }}
                </a>
                @endif
            </div>
        </div>

        @if(!$wishlist)
        <div class="col-lg-4">
            <div class="bp-coupon-card">
                <label class="bp-coupon-label">
                    <i class="las la-tag"></i> {{ __('Have a coupon?') }}
                </label>
                <div class="d-flex gap-2">
                    <input type="text" id="bp-cart-coupon-input" class="bp-coupon-input flex-grow-1"
                           placeholder="{{ __('Enter coupon code') }}">
                    <button type="button" class="bp-btn bp-btn-green bp-btn-sm" id="bp-cart-coupon-btn">
                        {{ __('Apply') }}
                    </button>
                </div>
                <div id="bp-cart-coupon-msg" class="bp-coupon-msg"></div>
            </div>

            <div class="bp-order-summary mt-3">
                <div class="bp-summary-title">{{ __('Order Summary') }}</div>
                <div class="coupon-contents">
                    @php
                        $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
                        $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
                    @endphp
                    <div class="bp-summary-row">
                        <span>{{ __('Subtotal') }}</span>
                        <span class="bp-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
                    </div>
                    <div class="bp-summary-row">
                        <span>{{ __('Tax (Incl)') }}</span>
                        <span class="bp-summary-price">--</span>
                    </div>
                    <div class="bp-summary-row bp-summary-total">
                        <span>{{ __('Total Amount') }}</span>
                        <span class="bp-summary-price">{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>
                </div>
                {!! apply_filters('nazmart:cart_summary', '') !!}
                <a href="{{ theme_checkout_url() }}"
                   class="bp-btn bp-btn-green w-100 justify-content-center mt-4"
                   style="font-size:15px;padding:13px;">
                    {{ __('Proceed to Checkout') }} <i class="las la-arrow-right"></i>
                </a>
                <p style="font-size:11px;color:#888;text-align:center;margin-top:12px;line-height:1.5;">
                    <i class="las la-shield-alt" style="color:var(--bp-accent);"></i>
                    {{ __('Secure & encrypted checkout') }}
                </p>
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
        if (parseInt(input.val()) > 1) { input[0].stepDown(1); triggerQtyUpdate($(this)); }
    });
    $(document).on('click', '.plus', function () {
        var input = $(this).prev('.quantity-input');
        input[0].stepUp(1); triggerQtyUpdate($(this));
    });
    $(document).on('change', '.quantity-input', function () { triggerQtyUpdate($(this)); });
    function triggerQtyUpdate(el) {
        var row = el.closest('[data-product-id]');
        var input = row.find('.quantity-input');
        var productId = row.data('product-id');
        var variantId = row.data('varinat-id') || row.data('variant-id') || '';
        updateQty(productId, input.val(), variantId);
    }
    function updateQty(productId, qty, variantId) {
        $.ajax({
            url: '{{ theme_cart_update_url() }}', type: 'GET',
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
        $.ajax({ url: '{{ theme_cart_remove_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } }
                $('.loader').hide();
            }, error: function () { $('.loader').hide(); }
        });
    });
    $(document).on('click', '.close-table-wishlist', function () {
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        $.ajax({ url: '{{ theme_wishlist_remove_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } }
                $('.loader').hide();
            }, error: function () { $('.loader').hide(); }
        });
    });
    $(document).on('click', '.move-to-wishlist', function () {
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        $.ajax({ url: '{{ theme_wishlist_move_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } }
                $('.loader').hide();
            }, error: function () { $('.loader').hide(); }
        });
    });
    $(document).on('click', '.clear-cart-btn', function () {
        $('.loader').show();
        setTimeout(function () { window.location.href = '{{ theme_cart_clear_url() }}'; }, 300);
    });
    $(document).on('click', '#bp-cart-coupon-btn', function () {
        var code = $('#bp-cart-coupon-input').val().trim();
        if (!code) return;
        $.ajax({ url: '{{ theme_apply_coupon_url() }}', type: 'GET', data: { coupon_code: code },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.loader').hide();
                var msg = $('#bp-cart-coupon-msg');
                if (data.type === 'success') {
                    msg.html('<span class="bp-coupon-success"><i class="las la-check-circle"></i> ' + data.msg + '</span>');
                    if (data.cart_price_markup) { $('.coupon-contents').html(data.cart_price_markup); }
                } else {
                    msg.html('<span class="bp-coupon-error"><i class="las la-exclamation-circle"></i> ' + (data.msg || '{{ __("Invalid coupon") }}') + '</span>');
                }
            }, error: function () { $('.loader').hide(); }
        });
    });
})(jQuery);
</script>
@endsection
