@extends('tenant.frontend.frontend-page-master')

@php $page_title = $wishlist ? __('Wishlist') : __('Cart'); @endphp

@section('title') {{ $page_title }} @endsection
@section('page-title') {{ $page_title }} @endsection

@section('content')
<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:32px 0 24px;">
    <div class="container">
        <h2 style="font-size:22px;font-weight:300;color:var(--gl-dark);margin-bottom:8px;letter-spacing:-.3px;">{{ $page_title }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span>{{ $page_title }}</span>
        </div>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

@if(!$wishlist)
<div class="container" style="padding-top:32px;">
    <div style="display:flex;align-items:center;margin-bottom:32px;">
        <div style="display:flex;align-items:center;gap:8px;background:var(--gl-dark);color:#fff;padding:10px 20px;border-radius:var(--gl-radius) 0 0 var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">
            <span style="width:20px;height:20px;border-radius:50%;background:#fff;color:var(--gl-dark);display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;">1</span>
            {{ __('Cart') }}
        </div>
        <div style="flex:1;height:2px;background:var(--gl-border);"></div>
        <div style="display:flex;align-items:center;gap:8px;color:var(--gl-muted);padding:10px 20px;font-size:12px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;">
            <span style="width:20px;height:20px;border-radius:50%;border:1.5px solid var(--gl-border);display:inline-flex;align-items:center;justify-content:center;font-size:10px;">2</span>
            {{ __('Checkout') }}
        </div>
        <div style="flex:1;height:2px;background:var(--gl-border);"></div>
        <div style="display:flex;align-items:center;gap:8px;color:var(--gl-muted);padding:10px 20px;font-size:12px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;border-radius:0 var(--gl-radius) var(--gl-radius) 0;">
            <span style="width:20px;height:20px;border-radius:50%;border:1.5px solid var(--gl-border);display:inline-flex;align-items:center;justify-content:center;font-size:10px;">3</span>
            {{ __('Confirmed') }}
        </div>
    </div>
</div>
@endif

<div class="container" style="{{ $wishlist ? 'padding-top:36px;' : '' }}padding-bottom:72px;">
    <div class="row g-4">

        <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);">
                <div class="table-responsive">
                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                        <thead>
                            <tr style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);">
                                <th style="padding:14px 20px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--gl-muted);">{{ __('Product') }}</th>
                                <th style="padding:14px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--gl-muted);">{{ __('Price') }}</th>
                                <th style="padding:14px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--gl-muted);">{{ __('Quantity') }}</th>
                                <th style="padding:14px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--gl-muted);">{{ __('Subtotal') }}</th>
                                <th style="padding:14px 16px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cart_tbody">
                        @foreach($cart_data as $key => $data)
                            @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
                            <tr class="table-cart-row" style="border-bottom:1px solid var(--gl-border);" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
                                <td style="padding:16px 20px;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:64px;height:64px;border-radius:var(--gl-radius);overflow:hidden;border:1px solid var(--gl-border);flex-shrink:0;background:var(--gl-blush);">
                                            {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                        </div>
                                        <div>
                                            <a href="{{ theme_product_url($slug) }}" style="font-size:14px;font-weight:600;color:var(--gl-dark);text-decoration:none;">{{ $data->name }}</a>
                                            @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                                            <div style="font-size:12px;color:var(--gl-muted);margin-top:3px;">
                                                @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                                                @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                                                @if($data?->options?->attributes)
                                                    @foreach($data->options->attributes as $attrKey => $attrVal)· {{ $attrKey }}: {{ $attrVal }} @endforeach
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:16px;"><span style="font-size:15px;font-weight:700;color:var(--gl-dark);">{{ amount_with_currency_symbol($data->price) }}</span></td>
                                <td style="padding:16px;">
                                    <div style="display:flex;align-items:center;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;width:fit-content;">
                                        @if(!$wishlist)
                                            <button type="button" class="substract" style="width:32px;height:36px;border:0;background:var(--gl-gold-pale);cursor:pointer;font-size:14px;color:var(--gl-dark);"><i class="mdi mdi-minus"></i></button>
                                        @endif
                                        <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1"
                                               {{ $wishlist ? 'disabled readonly' : '' }}
                                               style="width:48px;height:36px;border:0;text-align:center;font-size:14px;font-weight:600;font-family:inherit;">
                                        @if(!$wishlist)
                                            <button type="button" class="plus" style="width:32px;height:36px;border:0;background:var(--gl-gold-pale);cursor:pointer;font-size:14px;color:var(--gl-dark);"><i class="mdi mdi-plus"></i></button>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding:16px;"><span style="font-size:15px;font-weight:700;color:var(--gl-dark);">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
                                <td style="padding:16px;" class="gl-cart-action-cell" data-product_hash_id="{{ $data->rowId }}">
                                    @if($wishlist)
                                        <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;color:var(--gl-gold);font-size:20px;"><i class="mdi mdi-cart-arrow-down"></i></div>
                                    @else
                                        @auth
                                        <button class="save-for-later-btn gl-action-icon-btn" type="button"
                                                data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}"
                                                style="width:30px;height:30px;border-radius:50%;border:1.5px solid var(--gl-border);background:transparent;cursor:pointer;color:var(--gl-muted);font-size:13px;display:flex;align-items:center;justify-content:center;margin-bottom:6px;">
                                            <i class="mdi mdi-heart-outline"></i>
                                        </button>
                                        @endauth
                                    @endif
                                    <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" style="cursor:pointer;">
                                        <button type="button" style="width:30px;height:30px;border-radius:50%;border:1.5px solid var(--gl-border);background:transparent;cursor:pointer;color:var(--gl-muted);font-size:13px;display:flex;align-items:center;justify-content:center;">
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
                <a href="{{ theme_shop_url() }}"
                   style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:13px;font-weight:600;color:var(--gl-dark);text-decoration:none;transition:all .2s;"
                   onmouseover="this.style.borderColor='var(--gl-dark)'" onmouseout="this.style.borderColor='var(--gl-border)'">
                    <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
                </a>
                @if(!$wishlist)
                <a href="javascript:void(0)" class="clear-cart-btn"
                   style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border:1.5px solid #E53935;border-radius:var(--gl-radius);font-size:13px;font-weight:600;color:#E53935;text-decoration:none;background:transparent;cursor:pointer;">
                    <i class="mdi mdi-trash-can-outline"></i> {{ __('Clear Cart') }}
                </a>
                @endif
            </div>
        </div>

        @if(!$wishlist)
        <div class="col-lg-4">
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:28px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--gl-border);">
                    {{ __('Order Summary') }}
                </div>

                <div class="coupon-contents">
                    @php
                        $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
                        $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
                    @endphp
                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px dashed var(--gl-border);font-size:13px;">
                        <span style="color:var(--gl-muted);">{{ __('Subtotal') }}</span>
                        <span style="font-weight:600;">{{ site_currency_symbol() }}{{ $subtotal }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px dashed var(--gl-border);font-size:13px;">
                        <span style="color:var(--gl-muted);">{{ __('Tax (Incl)') }}</span>
                        <span style="font-weight:600;">--</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:16px 0;font-size:16px;font-weight:700;color:var(--gl-dark);">
                        <span>{{ __('Total Amount') }}</span>
                        <span>{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>
                </div>

                {!! apply_filters('nazmart:cart_summary', '') !!}

                <a href="{{ theme_checkout_url() }}"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;text-decoration:none;margin-top:16px;transition:background .2s;"
                   onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                    {{ __('Proceed to Checkout') }} <i class="mdi mdi-arrow-right"></i>
                </a>

                <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:14px;font-size:11px;color:var(--gl-muted);">
                    <i class="mdi mdi-shield-check" style="color:var(--gl-sage);"></i>
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
