@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Wishlist') }} @endsection
@section('page-title') {{ __('Wishlist') }} @endsection

@section('content')

<div class="fn-page-banner">
    <div class="container">
        <h1>{{ __('My Wishlist') }}</h1>
        <div class="fn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ __('Wishlist') }}</span>
        </div>
    </div>
</div>

<div class="fn-cart-section">
    <div class="container">
        <div id="fn-cart-main">
            @if($cart_data->count())
                <div class="row g-4 align-items-start">
                    @include(include_theme_path('shop.cart.partials.cart_left_contents'))
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

    /* ── Remove from wishlist ── */
    $(document).on('click', '.fn-wish-remove', function () {
        var hash = $(this).data('hash');
        $.ajax({
            url: '{{ theme_wishlist_remove_url() }}',
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

    /* ── Move to cart ── */
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

})(jQuery);
</script>
@endsection
