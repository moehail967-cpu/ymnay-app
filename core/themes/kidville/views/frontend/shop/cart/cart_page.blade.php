@extends('tenant.frontend.frontend-page-master')

@php
    $page_title = $wishlist ? __('Wishlist') : __('Cart');
@endphp

@section('title') {{ $page_title }} @endsection

@section('content')
<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ $page_title }}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ $page_title }}</span>
        </div>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

@if(!$wishlist)
<div class="container" style="padding-top:28px;">
    <div class="d-flex align-items-center justify-content-center gap-0 mb-4">
        <div class="text-center" style="flex:1;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--kv-red);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:14px;margin:0 auto 6px;">1</div>
            <div style="font-size:12px;font-weight:900;color:var(--kv-red);">{{ __('Cart') }}</div>
        </div>
        <div style="flex:2;height:3px;background:var(--kv-border);margin-bottom:20px;"></div>
        <div class="text-center" style="flex:1;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--kv-border);color:var(--kv-muted);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:14px;margin:0 auto 6px;">2</div>
            <div style="font-size:12px;font-weight:900;color:var(--kv-muted);">{{ __('Checkout') }}</div>
        </div>
        <div style="flex:2;height:3px;background:var(--kv-border);margin-bottom:20px;"></div>
        <div class="text-center" style="flex:1;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--kv-border);color:var(--kv-muted);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:14px;margin:0 auto 6px;">3</div>
            <div style="font-size:12px;font-weight:900;color:var(--kv-muted);">{{ __('Confirmed') }}</div>
        </div>
    </div>
</div>
@endif

<div class="container" style="{{ $wishlist ? 'padding-top:36px;' : '' }}padding-bottom:72px;">
    <div class="row g-4">

        <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
            <div style="background:#fff;border:2.5px solid var(--kv-border);border-radius:var(--kv-radius);overflow:hidden;">
                <div class="table-responsive">
                    <table class="kv-cart-table">
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
                                        <div class="kv-cart-img">
                                            {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                        </div>
                                        <div>
                                            <a href="{{ theme_product_url($slug) }}"
                                               style="font-size:14px;font-weight:800;color:var(--kv-dark);text-decoration:none;">
                                                {{ $data->name }}
                                            </a>
                                            @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                                            <div style="font-size:12px;color:var(--kv-muted);margin-top:3px;">
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
                                <td><span class="kv-price" style="font-size:15px;">{{ amount_with_currency_symbol($data->price) }}</span></td>
                                <td>
                                    <div class="kv-qty">
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
                                <td><span class="kv-price" style="font-size:15px;">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
                                <td class="kv-cart-action-cell {{ $wishlist ? 'kv-cart-action-wishlist d-flex justify-content-around align-items-center' : '' }}"
                                    data-product_hash_id="{{ $data->rowId }}">
                                    @if($wishlist)
                                        <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;">
                                            <i class="las la-cart-arrow-down" style="font-size:20px;color:var(--kv-green);"></i>
                                        </div>
                                    @else
                                        @auth
                                        <button class="save-for-later-btn" type="button"
                                                data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}"
                                                style="background:transparent;border:none;cursor:pointer;color:var(--kv-muted);font-size:18px;display:block;margin-bottom:4px;">
                                            <i class="las la-heart"></i>
                                        </button>
                                        @endauth
                                    @endif
                                    <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" style="cursor:pointer;">
                                        <button class="kv-remove-btn" type="button">
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
                <a href="{{ theme_shop_url() }}" class="kv-btn kv-btn-outline">
                    <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
                </a>
                @if(!$wishlist)
                <a href="javascript:void(0)" class="kv-btn kv-btn-ghost clear-cart-btn" style="color:var(--kv-red);border-color:var(--kv-red);">
                    <i class="las la-trash"></i> {{ __('Clear Cart') }}
                </a>
                @endif
            </div>
        </div>

        @if(!$wishlist)
        <div class="col-lg-4">
            <div class="kv-order-summary">
                <div class="kv-order-summary-title">{{ __('Order Summary') }}</div>

                <div class="coupon-contents">
                    @php
                        $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
                        $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
                    @endphp

                    <div class="kv-summary-row">
                        <span>{{ __('Subtotal') }}</span>
                        <span class="kv-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
                    </div>
                    <div class="kv-summary-row">
                        <span>{{ __('Tax (Incl)') }}</span>
                        <span class="kv-summary-price">--</span>
                    </div>
                    <div class="kv-summary-row total">
                        <span>{{ __('Total Amount') }}</span>
                        <span class="kv-summary-price">{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>
                </div>

                {!! apply_filters('nazmart:cart_summary', '') !!}

                <a href="{{ theme_checkout_url() }}"
                   class="kv-btn kv-btn-red w-100 justify-content-center mt-4"
                   style="font-size:15px;padding:13px;">
                    {{ __('Proceed to Checkout') }} <i class="las la-arrow-right"></i>
                </a>

                <p style="font-size:11px;color:var(--kv-muted);text-align:center;margin-top:12px;line-height:1.5;">
                    <i class="las la-shield-alt" style="color:var(--kv-green);"></i>
                    {{ __('Secure & encrypted checkout') }}
                </p>
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

    $(document).on('click', '.close-table-cart', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
        $.ajax({
            url: '{{ theme_cart_remove_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) {
                    toastr.success(data.msg);
                    if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); }
                    else { location.reload(); }
                }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    });

    $(document).on('click', '.close-table-wishlist', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
        $.ajax({
            url: '{{ theme_wishlist_remove_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) {
                    toastr.success(data.msg);
                    if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); }
                    else { location.reload(); }
                }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    });

    $(document).on('click', '.move-to-wishlist', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
        $.ajax({
            url: '{{ theme_wishlist_move_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) {
                    toastr.success(data.msg);
                    if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); }
                    else { location.reload(); }
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

})(jQuery);
</script>
@endsection
