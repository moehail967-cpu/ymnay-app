@extends('tenant.frontend.frontend-page-master')

@php
    $page_title = $wishlist ? __('Wishlist') : __('Cart');
@endphp

@section('title') {{ $page_title }} @endsection
@section('page-title') {{ $page_title }} @endsection

@section('content')
{{-- Page Header --}}
<div class="vl-page-header">
    <div class="container">
        <div class="vl-breadcrumb mb-2">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span>{{ $page_title }}</span>
        </div>
        <h1 style="font-style:italic;font-size:38px;font-weight:400;color:#fff;margin:0;">{{ $page_title }}</h1>
    </div>
</div>

<div class="vl-cart-main-wrapper">
@if($cart_data->count())

{{-- Progress Steps (cart only) --}}
@if(!$wishlist)
<div class="container" style="padding-top:28px;">
    <div style="display:flex;align-items:center;justify-content:center;max-width:480px;margin:0 auto 40px;gap:0;">
        <div style="display:flex;flex-direction:column;align-items:center;">
            <div style="width:32px;height:32px;border-radius:50%;background:var(--vl-plum);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-family:'Inter',sans-serif;">1</div>
            <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);margin-top:6px;font-family:'Inter',sans-serif;">{{ __('Cart') }}</div>
        </div>
        <div style="flex:1;height:1px;background:var(--vl-border);margin:0 8px;margin-bottom:20px;"></div>
        <div style="display:flex;flex-direction:column;align-items:center;">
            <div style="width:32px;height:32px;border-radius:50%;border:1px solid var(--vl-border);color:var(--vl-muted);display:flex;align-items:center;justify-content:center;font-size:12px;font-family:'Inter',sans-serif;">2</div>
            <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);margin-top:6px;font-family:'Inter',sans-serif;">{{ __('Checkout') }}</div>
        </div>
        <div style="flex:1;height:1px;background:var(--vl-border);margin:0 8px;margin-bottom:20px;"></div>
        <div style="display:flex;flex-direction:column;align-items:center;">
            <div style="width:32px;height:32px;border-radius:50%;border:1px solid var(--vl-border);color:var(--vl-muted);display:flex;align-items:center;justify-content:center;font-size:12px;font-family:'Inter',sans-serif;">3</div>
            <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);margin-top:6px;font-family:'Inter',sans-serif;">{{ __('Confirmed') }}</div>
        </div>
    </div>
</div>
@endif

<div class="container" style="{{ $wishlist ? 'padding-top:24px;' : '' }}padding-bottom:80px;">
    <div class="row g-4">

        {{-- Cart Table --}}
        <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);overflow:hidden;">
                <div class="table-responsive">
                    <table class="vl-cart-table" style="width:100%;border-collapse:collapse;">
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
                            <tr class="table-cart-row"
                                data-product-id="{{ $key }}"
                                data-variant-id="{{ $data->options->variant_id ?? '' }}"
                                data-varinat-id="{{ $data->options->variant_id ?? '' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="vl-cart-thumb">
                                            {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                        </div>
                                        <div>
                                            <a href="{{ theme_product_url($slug) }}" class="vl-cart-name"
                                               style="text-decoration:none;">{{ $data->name }}</a>
                                            @php
                                                $metaParts = [];
                                                if ($data?->options?->color_name) $metaParts[] = __('Color') . ': ' . $data->options->color_name;
                                                if ($data?->options?->size_name)  $metaParts[] = __('Size')  . ': ' . $data->options->size_name;
                                                foreach ((array)($data?->options?->attributes ?? []) as $attrK => $attrV) {
                                                    if ($attrV !== null && $attrV !== '') $metaParts[] = ucfirst($attrK) . ': ' . $attrV;
                                                }
                                            @endphp
                                            @if(!empty($metaParts))
                                            <div class="vl-cart-meta">{{ implode(' · ', $metaParts) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="vl-cart-price">{{ amount_with_currency_symbol($data->price) }}</td>
                                <td>
                                    <div class="vl-qty-stepper">
                                        @if(!$wishlist)
                                            <button type="button" class="vl-qty-btn substract"><i class="las la-minus"></i></button>
                                        @endif
                                        <input class="vl-qty-input quantity-input" type="number" value="{{ $data->qty }}" min="1"
                                               {{ $wishlist ? 'disabled readonly' : '' }}>
                                        @if(!$wishlist)
                                            <button type="button" class="vl-qty-btn plus"><i class="las la-plus"></i></button>
                                        @endif
                                    </div>
                                </td>
                                <td class="vl-cart-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</td>
                                <td class="vl-cart-action-cell {{ $wishlist ? 'vl-cart-action-wishlist' : '' }}"
                                    data-product_hash_id="{{ $data->rowId }}">
                                    @if($wishlist)
                                        <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;color:var(--vl-champagne);">
                                            <i class="las la-cart-arrow-down" style="font-size:20px;"></i>
                                        </div>
                                    @else
                                        @auth
                                        <button class="save-for-later-btn" type="button"
                                                data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
                                            <i class="las la-heart"></i>
                                        </button>
                                        @endauth
                                    @endif
                                    <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}">
                                        <button class="vl-remove-btn" type="button">
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
                <a href="{{ theme_shop_url() }}" class="vl-btn vl-btn-outline">
                    <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
                </a>
                @if(!$wishlist)
                <a href="javascript:void(0)" class="vl-btn vl-btn-outline clear-cart-btn"
                   style="color:#E53E3E;border-color:#E53E3E;">
                    <i class="las la-trash"></i> {{ __('Clear Cart') }}
                </a>
                @endif
            </div>
        </div>

        {{-- Order Summary --}}
        @if(!$wishlist)
        <div class="col-lg-4">
            <div class="vl-summary-card">
                <div class="vl-summary-title">{{ __('Order Summary') }}</div>

                <div class="coupon-contents">
                    @php
                        $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
                        $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
                    @endphp

                    <div class="vl-summary-row">
                        <span>{{ __('Subtotal') }}</span>
                        <span class="vl-cart-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
                    </div>
                    <div class="vl-summary-row">
                        <span>{{ __('Tax (Incl)') }}</span>
                        <span>--</span>
                    </div>
                    <div class="vl-summary-total">
                        <span>{{ __('Total Amount') }}</span>
                        <span>{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>
                </div>

                {{-- Cart Discount tier progress (injected by plugin if active) --}}
                <div id="cd-cart-summary-widget">{!! apply_filters('nazmart:cart_summary', '') !!}</div>

                <a href="{{ theme_checkout_url() }}"
                   class="vl-btn vl-btn-primary w-100 justify-content-center mt-4"
                   style="font-size:10px;padding:14px;">
                    {{ __('Proceed to Checkout') }} <i class="las la-arrow-right"></i>
                </a>

                <div class="mt-3" style="font-size:11px;color:var(--vl-muted);text-align:center;letter-spacing:.5px;">
                    <i class="las la-shield-alt" style="color:var(--vl-champagne);"></i>
                    {{ __('Secure & encrypted checkout') }}
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
        var row       = el.closest('[data-product-id]');
        var input     = row.find('.quantity-input');
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
        $.ajax({ url: '{{ theme_cart_remove_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) {
                    toastr.success(data.msg);
                    if (data.empty_cart !== '') { $('.vl-cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); }
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
        $.ajax({ url: '{{ theme_wishlist_remove_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) {
                    toastr.success(data.msg);
                    if (data.empty_cart !== '') { $('.vl-cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); }
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
        $.ajax({ url: '{{ theme_wishlist_move_url() }}', type: 'GET', data: { product_hash_id: hashId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                if (data.msg) {
                    toastr.success(data.msg);
                    if (data.empty_cart !== '') { $('.vl-cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); }
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
