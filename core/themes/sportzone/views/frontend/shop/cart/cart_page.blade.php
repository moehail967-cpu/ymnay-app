@extends('tenant.frontend.frontend-page-master')

@php $page_title = $wishlist ? __('Wishlist') : __('Cart'); @endphp

@section('title') {{ $page_title }} @endsection
@section('page-title') {{ $page_title }} @endsection

@section('content')
<div class="sz-page-banner">
    <div class="container">
        <h1>{{ $page_title }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ $page_title }}</span>
        </div>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

@if(!$wishlist)
<div class="container" style="padding-top:28px;">
    <div style="display:flex;align-items:center;gap:0;margin-bottom:28px;">
        <div style="display:flex;align-items:center;gap:8px;background:var(--sz-red);color:#fff;padding:10px 20px;font-family:var(--sz-font-head);font-size:13px;font-weight:400;letter-spacing:1.5px;text-transform:uppercase;">
            <span style="width:22px;height:22px;border-radius:50%;background:#fff;color:var(--sz-red);display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:900;">1</span>
            {{ __('Cart') }}
        </div>
        <div style="flex:1;height:3px;background:var(--sz-border);"></div>
        <div style="display:flex;align-items:center;gap:8px;color:var(--sz-muted);padding:10px 20px;font-family:var(--sz-font-head);font-size:13px;letter-spacing:1.5px;text-transform:uppercase;">
            <span style="width:22px;height:22px;border-radius:50%;border:2px solid var(--sz-border);display:inline-flex;align-items:center;justify-content:center;font-size:11px;">2</span>
            {{ __('Checkout') }}
        </div>
        <div style="flex:1;height:3px;background:var(--sz-border);"></div>
        <div style="display:flex;align-items:center;gap:8px;color:var(--sz-muted);padding:10px 20px;font-family:var(--sz-font-head);font-size:13px;letter-spacing:1.5px;text-transform:uppercase;">
            <span style="width:22px;height:22px;border-radius:50%;border:2px solid var(--sz-border);display:inline-flex;align-items:center;justify-content:center;font-size:11px;">3</span>
            {{ __('Confirmed') }}
        </div>
    </div>
</div>
@endif

<div class="container" style="{{ $wishlist ? 'padding-top:32px;' : '' }}padding-bottom:72px;">
    <div class="row g-4">

        <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
            <div style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);overflow:hidden;">
                <div style="background:var(--sz-navy);padding:14px 20px;font-family:var(--sz-font-head);font-size:14px;font-weight:400;color:#fff;text-transform:uppercase;letter-spacing:2px;">
                    <i class="mdi mdi-cart-outline"></i> {{ $page_title }}
                </div>
                <div class="table-responsive">
                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                        <thead>
                            <tr style="background:var(--sz-bg);border-bottom:2px solid var(--sz-border);">
                                <th style="padding:14px 20px;text-align:left;font-family:var(--sz-font-head);font-size:11px;font-weight:400;text-transform:uppercase;letter-spacing:2px;color:var(--sz-muted);">{{ __('Product') }}</th>
                                <th style="padding:14px 16px;text-align:left;font-family:var(--sz-font-head);font-size:11px;font-weight:400;text-transform:uppercase;letter-spacing:2px;color:var(--sz-muted);">{{ __('Price') }}</th>
                                <th style="padding:14px 16px;text-align:left;font-family:var(--sz-font-head);font-size:11px;font-weight:400;text-transform:uppercase;letter-spacing:2px;color:var(--sz-muted);">{{ __('Qty') }}</th>
                                <th style="padding:14px 16px;text-align:left;font-family:var(--sz-font-head);font-size:11px;font-weight:400;text-transform:uppercase;letter-spacing:2px;color:var(--sz-muted);">{{ __('Subtotal') }}</th>
                                <th style="padding:14px 16px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cart_tbody">
                        @foreach($cart_data as $key => $data)
                            @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
                            <tr class="table-cart-row" style="border-bottom:1px solid var(--sz-border);" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
                                <td style="padding:16px 20px;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:64px;height:64px;border-radius:var(--sz-radius);overflow:hidden;border:2px solid var(--sz-border);flex-shrink:0;background:var(--sz-bg);">
                                            {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                        </div>
                                        <div>
                                            <a href="{{ theme_product_url($slug) }}" style="font-family:var(--sz-font-head);font-size:14px;font-weight:400;color:var(--sz-dark);text-decoration:none;text-transform:uppercase;letter-spacing:.5px;">{{ $data->name }}</a>
                                            @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                                            <div style="font-size:12px;color:var(--sz-muted);margin-top:3px;">
                                                @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                                                @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                                                @if($data?->options?->attributes)
                                                    @foreach($data->options->attributes as $attrKey => $attrVal) · {{ $attrKey }}: {{ $attrVal }} @endforeach
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:16px;"><span class="sz-price-sale" style="font-size:16px;">{{ amount_with_currency_symbol($data->price) }}</span></td>
                                <td style="padding:16px;">
                                    <div style="display:flex;align-items:center;border:2px solid var(--sz-border);border-radius:var(--sz-radius);overflow:hidden;width:fit-content;">
                                        @if(!$wishlist)
                                            <button type="button" class="substract" style="width:32px;height:36px;border:0;background:var(--sz-bg);cursor:pointer;font-size:14px;color:var(--sz-dark);"><i class="mdi mdi-minus"></i></button>
                                        @endif
                                        <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1"
                                               {{ $wishlist ? 'disabled readonly' : '' }}
                                               style="width:48px;height:36px;border:0;text-align:center;font-family:var(--sz-font-head);font-size:14px;font-weight:600;">
                                        @if(!$wishlist)
                                            <button type="button" class="plus" style="width:32px;height:36px;border:0;background:var(--sz-bg);cursor:pointer;font-size:14px;color:var(--sz-dark);"><i class="mdi mdi-plus"></i></button>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding:16px;"><span class="sz-price-sale" style="font-size:16px;">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
                                <td style="padding:16px;" class="sz-cart-action-cell {{ $wishlist ? 'sz-cart-action-wishlist' : '' }}" data-product_hash_id="{{ $data->rowId }}">
                                    @if($wishlist)
                                        <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;color:var(--sz-red);font-size:20px;"><i class="mdi mdi-cart-arrow-down"></i></div>
                                    @else
                                        @auth
                                        <button class="save-for-later-btn" type="button"
                                                data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}"
                                                style="background:none;border:none;cursor:pointer;color:var(--sz-muted);font-size:18px;padding:0;line-height:1;">
                                            <i class="mdi mdi-heart-outline"></i>
                                        </button>
                                        @endauth
                                    @endif
                                    <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" style="cursor:pointer;">
                                        <button type="button" title="{{ __('Remove') }}" style="width:32px;height:32px;border-radius:50%;border:2px solid var(--sz-border);background:transparent;cursor:pointer;color:var(--sz-muted);font-size:14px;display:flex;align-items:center;justify-content:center;">
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

            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mt-4">
                <a href="{{ theme_shop_url() }}" class="sz-btn sz-btn-outline"><i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}</a>
                @if(!$wishlist)
                <a href="javascript:void(0)" class="sz-btn clear-cart-btn" style="background:transparent;border:2px solid var(--sz-red);color:var(--sz-red);">
                    <i class="mdi mdi-trash-can-outline"></i> {{ __('Clear Cart') }}
                </a>
                @endif
            </div>
        </div>

        @if(!$wishlist)
        <div class="col-lg-4">
            <div style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);overflow:hidden;">
                <div style="background:var(--sz-navy);padding:14px 20px;font-family:var(--sz-font-head);font-size:14px;font-weight:400;color:#fff;text-transform:uppercase;letter-spacing:2px;">
                    {{ __('Order Summary') }}
                </div>
                <div style="padding:24px;" class="coupon-contents">
                    @php
                        $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
                        $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
                    @endphp
                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--sz-border);font-size:14px;">
                        <span style="color:var(--sz-muted);font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:1px;font-size:12px;">{{ __('Subtotal') }}</span>
                        <span style="font-weight:600;">{{ site_currency_symbol() }}{{ $subtotal }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--sz-border);font-size:14px;">
                        <span style="color:var(--sz-muted);font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:1px;font-size:12px;">{{ __('Tax (Incl)') }}</span>
                        <span style="font-weight:600;">--</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:14px 0;border-bottom:2px solid var(--sz-border);">
                        <span style="font-family:var(--sz-font-head);font-size:16px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--sz-dark);">{{ __('Total') }}</span>
                        <span class="sz-price-sale" style="font-size:20px;">{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>

                    {!! apply_filters('nazmart:cart_summary', '') !!}
                    <a href="{{ theme_checkout_url() }}" class="sz-btn sz-btn-red w-100 mt-4" style="justify-content:center;font-size:14px;">
                        {{ __('Proceed to Checkout') }} <i class="mdi mdi-arrow-right"></i>
                    </a>

                    <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:14px;font-size:12px;color:var(--sz-muted);font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:1px;">
                        <i class="mdi mdi-shield-check" style="color:var(--sz-red);"></i>
                        {{ __('Secure & encrypted checkout') }}
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
        updateQty(row.data('product-id'), input.val(), row.data('varinat-id') || row.data('variant-id') || '');
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
        $.ajax({ url: '{{ theme_cart_remove_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) { if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } } $('.loader').hide(); },
            error: function () { $('.loader').hide(); }
        });
    });

    $(document).on('click', '.close-table-wishlist', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
        $.ajax({ url: '{{ theme_wishlist_remove_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) { if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } } $('.loader').hide(); },
            error: function () { $('.loader').hide(); }
        });
    });

    $(document).on('click', '.move-to-wishlist', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
        $.ajax({ url: '{{ theme_wishlist_move_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) { if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } } $('.loader').hide(); },
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
