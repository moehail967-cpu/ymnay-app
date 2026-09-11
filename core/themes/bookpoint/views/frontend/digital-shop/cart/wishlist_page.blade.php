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

<div class="container" style="padding-top:36px;padding-bottom:72px;">
    <div class="row g-4">
        <div class="col-lg-12">
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
                                            <a href="{{ theme_product_url($slug) }}" class="bp-cart-item-name">{{ $data->name }}</a>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="bp-cart-price">{{ amount_with_currency_symbol($data->price) }}</span></td>
                                <td>
                                    <div class="bp-qty">
                                        <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1" disabled readonly>
                                    </div>
                                </td>
                                <td><span class="bp-cart-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
                                <td class="ff-jost d-flex justify-content-around align-items-center" data-product_hash_id="{{ $data->rowId }}">
                                    <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;">
                                        <i class="las la-shopping-cart" style="font-size:20px;color:var(--bp-accent);"></i>
                                    </div>
                                    <div class="close-table-wishlist" style="cursor:pointer;">
                                        <button class="bp-remove-btn" type="button"><i class="las la-times"></i></button>
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
            </div>
        </div>
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
    $(document).on('click', '.ff-jost .close-table-wishlist', function () {
        var hashId = $(this).closest('[data-product_hash_id]').data('product_hash_id');
        $.ajax({ url: '{{ theme_wishlist_remove_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } }
                $('.loader').hide();
            }, error: function () { $('.loader').hide(); }
        });
    });
    $(document).on('click', '.ff-jost .move-to-wishlist', function () {
        var hashId = $(this).closest('[data-product_hash_id]').data('product_hash_id');
        $.ajax({ url: '{{ theme_wishlist_move_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } }
                $('.loader').hide();
            }, error: function () { $('.loader').hide(); }
        });
    });
})(jQuery);
</script>
@endsection
