@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Wishlist Page') }} @endsection
@section('page-title') {{ __('Wishlist Page') }} @endsection

@section('content')
<div class="ar-page-banner">
    <div class="container">
        <h1>{{ __('Wishlist') }}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span>{{ __('Wishlist') }}</span>
        </div>
    </div>
</div>

<div class="ar-cart-section">
    <div class="container">
        @if($cart_data->count())
            <div class="row">
                @include(include_theme_path('shop.cart.partials.cart_left_contents'))
            </div>
        @else
            @include(include_theme_path('shop.cart.cart_empty'))
        @endif
    </div>
</div>
@endsection

@section('scripts')
    <script>
        (function ($) {
            'use strict'

            $(document).on('click', '.ff-jost .close-table-wishlist', function (e) {
                let el = $(this);
                let product_hash_id = el.closest('[data-product_hash_id]').data('product_hash_id');
                $.ajax({
                    url: '{{theme_wishlist_remove_url()}}',
                    type: 'GET',
                    data: { 'product_hash_id': product_hash_id },
                    beforeSend: function () { $('.loader').show(); },
                    success: function (data) {
                        if (data.msg) {
                            toastr.success(data.msg);
                            if (data.empty_cart !== '') {
                                $('.ar-cart-section .container').html(data.empty_cart).hide().fadeIn();
                            }
                            $('.track-icon-list').load(location.href + ' .track-icon-list');
                            $('#cart_tbody').load(location.href + ' #cart_tbody > *');
                        }
                        $('.loader').hide();
                    },
                    error: function () {}
                });
            });

            $(document).on('click', '.ff-jost .move-to-wishlist', function (e) {
                let el = $(this);
                let product_hash_id = el.closest('[data-product_hash_id]').data('product_hash_id');
                $.ajax({
                    url: '{{theme_wishlist_move_url()}}',
                    type: 'GET',
                    data: { 'product_hash_id': product_hash_id },
                    beforeSend: function () { $('.loader').show(); },
                    success: function (data) {
                        if (data.msg) {
                            toastr.success(data.msg);
                            if (data.empty_cart !== '') {
                                $('.ar-cart-section .container').html(data.empty_cart).hide().fadeIn();
                            }
                            $('.track-icon-list').load(location.href + ' .track-icon-list');
                            $('#cart_tbody').load(location.href + ' #cart_tbody > *');
                        }
                        $('.loader').hide();
                    },
                    error: function () {}
                });
            });
        })(jQuery)
    </script>
@endsection
