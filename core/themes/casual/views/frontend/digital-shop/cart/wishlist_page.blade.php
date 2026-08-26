@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Wishlist') }} @endsection
@section('page-title') {{ __('Wishlist') }} @endsection

@section('content')
<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ __('Wishlist') }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ __('Wishlist') }}</span>
        </div>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

    <div class="container cs-cart-wrap">
        <div class="row g-4">
            <div class="col-12">
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
                                            <input class="quantity-input cs-cart-qty-input" type="number"
                                                   value="{{ $data->qty }}" min="1" disabled readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="cs-cart-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
                                    </td>
                                    <td class="ff-jost cs-cart-actions-cell"
                                        data-product_hash_id="{{ $data->rowId }}">
                                        <button type="button" class="move-to-wishlist cs-cart-action-btn" title="{{ __('Move to Cart') }}">
                                            <i class="las la-shopping-cart"></i>
                                        </button>
                                        <button type="button" class="close-table-wishlist cs-cart-remove-btn">
                                            <i class="las la-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="cs-cart-bottom">
                    <a href="{{ theme_digital_shop_url() }}" class="cs-cart-continue-btn">
                        <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

@else
    @include(include_theme_path('digital-shop.cart.cart_empty'), ['wishlist' => true])
@endif
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    $(document).on('click', '.ff-jost .close-table-wishlist', function () {
        var hashId = $(this).closest('[data-product_hash_id]').data('product_hash_id');
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

    $(document).on('click', '.ff-jost .move-to-wishlist', function () {
        var hashId = $(this).closest('[data-product_hash_id]').data('product_hash_id');
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

})(jQuery);
</script>
@endsection
