@extends('tenant.frontend.frontend-page-master')

@php
    $page_title = $wishlist ? __('Wishlist') : __('Cart');
@endphp

@section('title') {{ $page_title }} @endsection
@section('page-title') {{ $page_title }} @endsection

@section('content')
<div class="fm-page-banner">
    <div class="container">
        <h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{{ $page_title }}</h1>
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{{ $page_title }}</span>
        </div>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

{{-- Progress Steps (cart only) --}}
@if(!$wishlist)
<div class="container" style="padding-top:28px;">
    <div class="d-flex align-items-center justify-content-center gap-0 mb-4">
        <div class="text-center" style="flex:1;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--fm-green);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;margin:0 auto 6px;">1</div>
            <div style="font-size:12px;font-weight:700;color:var(--fm-green);">{{ __('Cart') }}</div>
        </div>
        <div style="flex:2;height:3px;background:var(--fm-border);margin-bottom:20px;"></div>
        <div class="text-center" style="flex:1;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--fm-border);color:var(--fm-muted);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;margin:0 auto 6px;">2</div>
            <div style="font-size:12px;font-weight:700;color:var(--fm-muted);">{{ __('Checkout') }}</div>
        </div>
        <div style="flex:2;height:3px;background:var(--fm-border);margin-bottom:20px;"></div>
        <div class="text-center" style="flex:1;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--fm-border);color:var(--fm-muted);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;margin:0 auto 6px;">3</div>
            <div style="font-size:12px;font-weight:700;color:var(--fm-muted);">{{ __('Confirmed') }}</div>
        </div>
    </div>
</div>
@endif

<div class="container" style="{{ $wishlist ? 'padding-top:36px;' : '' }}padding-bottom:72px;">
    <div class="row g-4">

        {{-- Cart Table --}}
        <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
            <div class="fm-cart-wrap">
                <div class="table-responsive">
                    <table class="fm-cart-table">
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
                                        <div class="fm-cart-img">
                                            {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                        </div>
                                        <div>
                                            <a href="{{ theme_product_url($slug) }}"
                                               class="fm-cart-name" style="text-decoration:none;">
                                                {{ $data->name }}
                                            </a>
                                            @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                                            <div class="fm-cart-variant">
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
                                    <span class="fm-price-sale" style="font-size:15px;">{{ amount_with_currency_symbol($data->price) }}</span>
                                </td>
                                <td>
                                    <div class="fm-qty-wrap">
                                        @if(!$wishlist)
                                            <button type="button" class="fm-qty-btn substract"><i class="las la-minus"></i></button>
                                        @endif
                                        <input class="fm-qty-input quantity-input" type="number" value="{{ $data->qty }}" min="1"
                                               {{ $wishlist ? 'disabled readonly' : '' }}>
                                        @if(!$wishlist)
                                            <button type="button" class="fm-qty-btn plus"><i class="las la-plus"></i></button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="fm-price-sale" style="font-size:15px;">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
                                </td>
                                <td class="fm-cart-action-cell {{ $wishlist ? 'fm-cart-action-wishlist' : '' }}"
                                    data-product_hash_id="{{ $data->rowId }}">
                                    @if($wishlist)
                                        <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;">
                                            <i class="las la-cart-arrow-down" style="font-size:20px;color:var(--fm-green);"></i>
                                        </div>
                                    @else
                                        @auth
                                        <button class="save-for-later-btn fm-action-icon-btn" type="button"
                                                data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
                                            <i class="las la-heart"></i>
                                        </button>
                                        @endauth
                                    @endif
                                    <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}">
                                        <button class="fm-remove-btn" type="button" title="{{ __('Remove') }}">
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
                <a href="{{ theme_shop_url() }}" class="fm-btn fm-btn-outline">
                    <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
                </a>
                @if(!$wishlist)
                <a href="javascript:void(0)" class="fm-btn clear-cart-btn" style="color:#D32F2F;border-color:#FFCDD2;background:#FFF5F5;">
                    <i class="las la-trash"></i> {{ __('Clear Cart') }}
                </a>
                @endif
            </div>
        </div>

        {{-- Order Summary --}}
        @if(!$wishlist)
        <div class="col-lg-4">
            <div class="fm-order-summary">
                <div class="fm-summary-head">{{ __('Order Summary') }}</div>
                <div class="fm-summary-body">
                    <div class="coupon-contents">
                        @php
                            $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
                            $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
                        @endphp

                        <div class="fm-summary-row">
                            <span class="label">{{ __('Subtotal') }}</span>
                            <span class="fm-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
                        </div>
                        <div class="fm-summary-row">
                            <span class="label">{{ __('Tax (Incl)') }}</span>
                            <span class="fm-summary-price">--</span>
                        </div>
                        <div class="fm-summary-total">
                            <span class="label">{{ __('Total Amount') }}</span>
                            <span class="value">{{ site_currency_symbol() }}{{ $total }}</span>
                        </div>
                    </div>

                    {!! apply_filters('nazmart:cart_summary', '') !!}

                    <a href="{{ theme_checkout_url() }}"
                       class="fm-btn fm-btn-green w-100 justify-content-center mt-4"
                       style="font-size:15px;padding:13px;">
                        {{ __('Proceed to Checkout') }} <i class="las la-arrow-right"></i>
                    </a>

                    <p style="font-size:11px;color:var(--fm-muted);text-align:center;margin-top:12px;line-height:1.5;">
                        <i class="las la-shield-alt" style="color:var(--fm-green);"></i>
                        {{ __('Secure & encrypted checkout') }}
                    </p>
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

    $(document).on('click', '.close-table-cart, .close-table-cart .fm-remove-btn', function (e) {
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
                    if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); }
                    else { location.reload(); }
                }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    });

    $(document).on('click', '.close-table-wishlist, .close-table-wishlist .fm-remove-btn', function (e) {
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
            url: '{{ theme_wishlist_move_url() }}',
            type: 'GET',
            data: { product_hash_id: hashId },
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
