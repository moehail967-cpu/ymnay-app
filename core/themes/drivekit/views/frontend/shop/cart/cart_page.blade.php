@extends('tenant.frontend.frontend-page-master')

@php $page_title = $wishlist ? __('Wishlist') : __('Cart'); @endphp

@section('title') {{ $page_title }} @endsection
@section('page-title') {{ $page_title }} @endsection

@section('content')

{{-- Page Banner --}}
<div class="dk-page-banner">
    <div class="dk-page-banner-overlay"></div>
    <div class="dk-page-banner-bar"></div>
    <div class="container dk-page-banner-inner">
        <h1 class="dk-page-banner-title">{{ $page_title }}</h1>
        <nav class="dk-page-banner-nav">
            <a href="{{ theme_home_url() }}" class="dk-page-banner-link">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right dk-page-banner-sep"></i>
            <span class="dk-page-banner-current">{{ $page_title }}</span>
        </nav>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

{{-- Step Indicator --}}
@if(!$wishlist)
<div class="container" style="padding-top:28px;">
    <div class="dk-steps">
        <div class="dk-step active">
            <span class="dk-step-num">1</span> {{ __('Cart') }}
        </div>
        <div class="dk-step-line"></div>
        <div class="dk-step">
            <span class="dk-step-num">2</span> {{ __('Checkout') }}
        </div>
        <div class="dk-step-line"></div>
        <div class="dk-step">
            <span class="dk-step-num">3</span> {{ __('Confirmed') }}
        </div>
    </div>
</div>
@endif

<div class="container dk-cart-section" style="{{ $wishlist ? 'padding-top:28px;' : '' }}">
    <div class="row g-4">

        {{-- Cart Table --}}
        <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
            <div class="dk-cart-table-wrap">
                <div class="table-responsive">
                    <table class="dk-cart-table">
                        <thead class="dk-cart-thead">
                            <tr>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Qty') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cart_tbody">
                        @foreach($cart_data as $key => $data)
                            @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
                            <tr class="dk-cart-row table-cart-row" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
                                <td class="dk-cart-td">
                                    <div class="dk-cart-product">
                                        <div class="dk-cart-thumb">
                                            {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                        </div>
                                        <div>
                                            <a href="{{ theme_product_url($slug) }}" class="dk-cart-product-link">{{ $data->name }}</a>
                                            @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                                            <div class="dk-cart-variant">
                                                @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                                                @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                                                @if($data?->options?->attributes) @foreach($data->options->attributes as $attrKey => $attrVal) · {{ $attrKey }}: {{ $attrVal }} @endforeach @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="dk-cart-td"><span class="dk-cart-price">{{ amount_with_currency_symbol($data->price) }}</span></td>
                                <td class="dk-cart-td">
                                    <div class="dk-qty-control">
                                        @if(!$wishlist)
                                            <button type="button" class="substract dk-qty-btn"><i class="mdi mdi-minus"></i></button>
                                        @endif
                                        <input class="quantity-input dk-qty-input" type="number" value="{{ $data->qty }}" min="1" {{ $wishlist ? 'disabled readonly' : '' }}>
                                        @if(!$wishlist)
                                            <button type="button" class="plus dk-qty-btn"><i class="mdi mdi-plus"></i></button>
                                        @endif
                                    </div>
                                </td>
                                <td class="dk-cart-td"><span class="dk-cart-total-val">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
                                <td class="dk-cart-td dk-cart-action-cell {{ $wishlist ? 'dk-cart-action-wishlist' : '' }}" data-product_hash_id="{{ $data->rowId }}">
                                    @if($wishlist)
                                        <button type="button" class="move-to-wishlist dk-btn dk-btn-ghost dk-btn-sm" title="{{ __('Move to Cart') }}" style="gap:4px;padding:6px 10px;">
                                            <i class="mdi mdi-cart-arrow-down"></i> {{ __('Add to Cart') }}
                                        </button>
                                    @else
                                        @auth
                                        <button class="save-for-later-btn" type="button"
                                                data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
                                            <i class="mdi mdi-heart-outline"></i>
                                        </button>
                                        @endauth
                                    @endif
                                    <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}">
                                        <button type="button" class="dk-remove-btn"><i class="mdi mdi-close"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dk-cart-actions">
                <a href="{{ theme_shop_url() }}" class="dk-cart-continue-btn">
                    <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
                </a>
                @if(!$wishlist)
                <a href="javascript:void(0)" class="clear-cart-btn dk-cart-clear-btn">
                    <i class="mdi mdi-trash-can-outline"></i> {{ __('Clear Cart') }}
                </a>
                @endif
            </div>

            @if(!$wishlist)
            {{-- Coupon / Promo Code --}}
            <div class="dk-cart-promo mt-3">
                <div class="dk-cart-promo-title"><i class="mdi mdi-tag-outline" style="color:var(--dk-red);"></i> {{ __('Promo / Coupon Code') }}</div>
                <div class="dk-coupon-bar">
                    <input type="text" id="cart_coupon_input" class="dk-coupon-input" placeholder="{{ __('Enter coupon code…') }}">
                    <button class="dk-btn dk-btn-primary dk-btn-sm cart-coupon-apply-btn">{{ __('Apply') }}</button>
                </div>
                <div id="cart_coupon_msg" style="font-size:12px;margin-top:6px;"></div>
            </div>
            @endif
        </div>

        {{-- Order Summary --}}
        @if(!$wishlist)
        <div class="col-lg-4">
            <div class="dk-order-summary">
                <div class="dk-order-summary-title"><i class="mdi mdi-receipt"></i> {{ __('Order Summary') }}</div>

                <div class="coupon-contents">
                    <div class="dk-order-row">
                        <span class="dk-order-row-label">{{ __('Subtotal') }}</span>
                        <span class="dk-order-row-value">{{ amount_with_currency_symbol(theme_cart_subtotal()) }}</span>
                    </div>
                    <div class="dk-order-row">
                        <span class="dk-order-row-label">{{ __('Tax (Incl)') }}</span>
                        <span class="dk-order-row-value">--</span>
                    </div>
                    <div class="dk-order-total">
                        <span class="dk-order-total-label">{{ __('Total') }}</span>
                        <span class="dk-order-total-value">{{ amount_with_currency_symbol(theme_cart_subtotal()) }}</span>
                    </div>
                </div>

                {!! apply_filters('nazmart:cart_summary', '') !!}

                <a href="{{ theme_checkout_url() }}" class="dk-btn dk-btn-primary w-100" style="justify-content:center;font-size:13px;padding:14px;margin-top:4px;">
                    {{ __('Proceed to Checkout') }} <i class="mdi mdi-arrow-right"></i>
                </a>

                <a href="{{ theme_shop_url() }}" class="dk-btn dk-btn-ghost w-100" style="justify-content:center;font-size:11px;padding:10px;margin-top:8px;">
                    <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
                </a>

                <div class="dk-secure-note">
                    <i class="mdi mdi-shield-check"></i> {{ __('Secure & encrypted checkout') }}
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
    $(document).on('click', '.substract', function () { var input = $(this).next('.quantity-input'); if (parseInt(input.val()) > 1) { input[0].stepDown(1); triggerQtyUpdate($(this)); } });
    $(document).on('click', '.plus', function () { var input = $(this).prev('.quantity-input'); input[0].stepUp(1); triggerQtyUpdate($(this)); });
    $(document).on('change', '.quantity-input', function () { triggerQtyUpdate($(this)); });
    function triggerQtyUpdate(el) { var row = el.closest('[data-product-id]'); updateQty(row.data('product-id'), row.find('.quantity-input').val(), row.data('varinat-id') || row.data('variant-id') || ''); }
    function updateQty(productId, qty, variantId) {
        $.ajax({ url: '{{ theme_cart_update_url() }}', type: 'GET', data: { product_id: productId, quantity: qty, variant_id: variantId },
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
    $(document).on('click', '.close-table-cart', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
        $.ajax({ url: '{{ theme_cart_remove_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    });
    $(document).on('click', '.close-table-wishlist', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
        $.ajax({ url: '{{ theme_wishlist_remove_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    });
    $(document).on('click', '.move-to-wishlist', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
        $.ajax({ url: '{{ theme_wishlist_move_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    });
    $(document).on('click', '.clear-cart-btn', function () { $('.loader').show(); setTimeout(function () { window.location.href = '{{ theme_cart_clear_url() }}'; }, 300); });

    $(document).on('click', '.cart-coupon-apply-btn', function () {
        var coupon = $('#cart_coupon_input').val().trim();
        var msg = $('#cart_coupon_msg');
        if (!coupon) { msg.html('<span style="color:var(--dk-silver);">{{ __('Please enter a coupon code.') }}</span>'); return; }
        $.ajax({
            url: '{{ theme_checkout_coupon_ajax_url() }}', type: 'GET',
            data: { coupon: coupon, country: '', state: '', shipping_method: '' },
            success: function (data) {
                if (data.type === 'error') { msg.html('<span style="color:var(--dk-red);">' + data.msg + '</span>'); }
                else { msg.html('<span style="color:#4CAF50;"><i class="mdi mdi-check-circle"></i> ' + data.msg + '</span>'); sessionStorage.setItem('dk_coupon', coupon); }
            },
            error: function () { msg.html('<span style="color:var(--dk-red);">{{ __('Could not apply coupon.') }}</span>'); }
        });
    });
})(jQuery);
</script>
@endsection
