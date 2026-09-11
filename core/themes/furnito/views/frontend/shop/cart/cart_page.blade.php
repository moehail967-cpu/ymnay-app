@extends('tenant.frontend.frontend-page-master')

@php $is_wishlist = $wishlist ?? false; @endphp

@section('title') {{ $is_wishlist ? __('Wishlist') : __('Cart') }} @endsection
@section('page-title') {{ $is_wishlist ? __('Wishlist') : __('Cart') }} @endsection

@section('content')

<div class="fn-page-banner">
    <div class="container">
        <h1>{{ $is_wishlist ? __('My Wishlist') : __('Shopping Cart') }}</h1>
        <div class="fn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ $is_wishlist ? __('Wishlist') : __('Cart') }}</span>
        </div>
    </div>
</div>

<div class="fn-cart-section">
    <div class="container">
        <div id="fn-cart-main">
            @if($cart_data->count())
                <div class="row g-4 align-items-start">
                    @include(include_theme_path('shop.cart.partials.cart_left_contents'))
                    @if(!$is_wishlist)
                        @include(include_theme_path('shop.cart.partials.cart_right_contents'))
                    @endif
                </div>
            @else
                @include(include_theme_path('shop.cart.cart_empty'))
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    /* ── Qty input change ── */
    $(document).on('change', '.fn-qty-input', function () {
        var el = $(this);
        getSubtotal(
            el.closest('[data-product-id]').data('product-id'),
            el.val(),
            el.closest('[data-variant-id]').data('variant-id')
        );
    });

    /* ── Qty ± buttons ── */
    $(document).on('click', '.fn-qty-plus', function () {
        var inp = $(this).siblings('.fn-qty-input');
        inp[0].stepUp(1);
        var row = $(this).closest('[data-product-id]');
        getSubtotal(row.data('product-id'), inp.val(), row.data('variant-id'));
    });

    $(document).on('click', '.fn-qty-minus', function () {
        var inp = $(this).siblings('.fn-qty-input');
        if (inp.val() > 1) {
            inp[0].stepDown(1);
            var row = $(this).closest('[data-product-id]');
            getSubtotal(row.data('product-id'), inp.val(), row.data('variant-id'));
        }
    });

    /* ── Clear cart ── */
    $(document).on('click', '.fn-clear-cart-btn', function () {
        $('.loader').show();
        setTimeout(function () {
            $(location).attr('href', '{{ theme_cart_clear_url() }}');
        }, 300);
    });

    /* ── Remove from cart ── */
    $(document).on('click', '.fn-cart-remove', function () {
        var hash = $(this).data('hash');
        $.ajax({
            url: '{{ theme_cart_remove_url() }}',
            type: 'GET',
            data: { product_hash_id: hash },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) {
                    toastr.success(data.msg);
                    if (data.empty_cart !== '') {
                        $('#fn-cart-main').html(data.empty_cart).hide().fadeIn();
                    }
                    $('.fn-cart-summary-content').parent().load(location.href + ' .fn-cart-summary-content');
                }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    });

    /* ── Move wishlist → cart ── */
    $(document).on('click', '.fn-cart-move', function () {
        var hash = $(this).data('hash');
        $.ajax({
            url: '{{ theme_wishlist_move_url() }}',
            type: 'GET',
            data: { product_hash_id: hash },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) {
                    toastr.success(data.msg);
                    if (data.empty_cart !== '') {
                        $('#fn-cart-main').html(data.empty_cart).hide().fadeIn();
                    }
                }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    });

    /* ── Qty AJAX ── */
    function getSubtotal(productId, qty, variantId) {
        $.ajax({
            url: '{{ theme_cart_update_url() }}',
            type: 'GET',
            data: { product_id: productId, quantity: qty, variant_id: variantId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.type === 'success') { toastr.success(data.msg); }
                else if (data.quantity_msg) { toastr.warning(data.quantity_msg); }
                if (data.markup) { $('#fn-cart-tbody').html(data.markup); }
                if (data.cart_price_markup) { $('.fn-cart-summary-content').html(data.cart_price_markup); }
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

})(jQuery);
</script>
@endsection
