@extends('tenant.frontend.frontend-page-master')

@php $page_title = $wishlist ? __('Wishlist') : __('Cart'); @endphp

@section('title') {{ $page_title }} @endsection
@section('page-title') {{ $page_title }} @endsection

@section('content')
<div style="background:var(--tz-surface);border-bottom:1px solid var(--tz-border);padding:28px 0 20px;">
    <div class="container">
        <h2 style="font-size:22px;font-weight:700;color:#fff;margin-bottom:8px;">{{ $page_title }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--tz-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--tz-muted);text-decoration:none;" onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-muted)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ $page_title }}</span>
        </div>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

@if(!$wishlist)
<div class="container" style="padding-top:28px;">
    <div style="display:flex;align-items:center;background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);overflow:hidden;margin-bottom:28px;">
        <div style="flex:1;display:flex;align-items:center;gap:8px;padding:12px 20px;background:var(--tz-blue);color:#fff;font-size:13px;font-weight:600;">
            <span style="width:22px;height:22px;border-radius:50%;background:#fff;color:var(--tz-blue);display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;">1</span>
            {{ __('Cart') }}
        </div>
        <div style="flex:2;height:2px;background:var(--tz-border);"></div>
        <div style="display:flex;align-items:center;gap:8px;padding:12px 20px;font-size:13px;color:var(--tz-muted);">
            <span style="width:22px;height:22px;border-radius:50%;border:1.5px solid var(--tz-border);display:inline-flex;align-items:center;justify-content:center;font-size:11px;">2</span>
            {{ __('Checkout') }}
        </div>
        <div style="flex:2;height:2px;background:var(--tz-border);"></div>
        <div style="display:flex;align-items:center;gap:8px;padding:12px 20px;font-size:13px;color:var(--tz-muted);">
            <span style="width:22px;height:22px;border-radius:50%;border:1.5px solid var(--tz-border);display:inline-flex;align-items:center;justify-content:center;font-size:11px;">3</span>
            {{ __('Confirmed') }}
        </div>
    </div>
</div>
@endif

<div class="container" style="{{ $wishlist ? 'padding-top:28px;' : '' }}padding-bottom:72px;">
    <div class="row g-4">

        <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);overflow:hidden;">
                <div class="table-responsive">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr style="background:var(--tz-mid);border-bottom:1px solid var(--tz-border);">
                                <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);">{{ __('Product') }}</th>
                                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);">{{ __('Price') }}</th>
                                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);">{{ __('Qty') }}</th>
                                <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);">{{ __('Total') }}</th>
                                <th style="padding:12px 16px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cart_tbody">
                        @foreach($cart_data as $key => $data)
                            @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
                            <tr class="table-cart-row" style="border-bottom:1px solid var(--tz-border);" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
                                <td style="padding:14px 20px;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:60px;height:60px;border-radius:var(--tz-radius-sm);border:1px solid var(--tz-border);background:var(--tz-surface);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;padding:4px;">
                                            {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                        </div>
                                        <div>
                                            <a href="{{ theme_product_url($slug) }}" style="font-size:13px;font-weight:600;color:var(--tz-text);text-decoration:none;" onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-text)'">{{ $data->name }}</a>
                                            @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                                            <div style="font-size:11px;color:var(--tz-muted);margin-top:3px;">
                                                @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                                                @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                                                @if($data?->options?->attributes) @foreach($data->options->attributes as $attrKey => $attrVal)· {{ $attrKey }}: {{ $attrVal }} @endforeach @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:14px 16px;"><span style="font-size:14px;font-weight:700;color:var(--tz-blue);">{{ amount_with_currency_symbol($data->price) }}</span></td>
                                <td style="padding:14px 16px;">
                                    <div style="display:flex;align-items:center;border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);overflow:hidden;width:fit-content;background:var(--tz-mid);">
                                        @if(!$wishlist)
                                            <button type="button" class="substract" style="width:30px;height:34px;border:0;background:transparent;cursor:pointer;font-size:14px;color:var(--tz-muted);"><i class="mdi mdi-minus"></i></button>
                                        @endif
                                        <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1" {{ $wishlist ? 'disabled readonly' : '' }} style="width:44px;height:34px;border:0;background:transparent;text-align:center;font-size:13px;font-weight:600;font-family:var(--tz-font);color:var(--tz-text);outline:none;">
                                        @if(!$wishlist)
                                            <button type="button" class="plus" style="width:30px;height:34px;border:0;background:transparent;cursor:pointer;font-size:14px;color:var(--tz-muted);"><i class="mdi mdi-plus"></i></button>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding:14px 16px;"><span style="font-size:14px;font-weight:700;color:var(--tz-blue);">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
                                <td style="padding:14px 16px;" class="tz-cart-action-cell {{ $wishlist ? 'tz-cart-action-wishlist' : '' }}" data-product_hash_id="{{ $data->rowId }}">
                                    @if($wishlist)
                                        <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;color:var(--tz-blue);font-size:18px;"><i class="mdi mdi-cart-arrow-down"></i></div>
                                    @else
                                        @auth
                                        <button class="save-for-later-btn" type="button"
                                                data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}"
                                                style="background:none;border:none;cursor:pointer;color:var(--tz-muted);font-size:16px;padding:0;line-height:1;">
                                            <i class="mdi mdi-heart-outline"></i>
                                        </button>
                                        @endauth
                                    @endif
                                    <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" style="cursor:pointer;">
                                        <button type="button" title="{{ __('Remove') }}" style="width:28px;height:28px;border-radius:50%;border:1px solid var(--tz-border);background:transparent;cursor:pointer;color:var(--tz-muted);font-size:13px;display:flex;align-items:center;justify-content:center;"><i class="mdi mdi-close"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mt-4">
                <a href="{{ theme_shop_url() }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 20px;background:transparent;border:1px solid var(--tz-border);border-radius:var(--tz-radius);font-size:13px;font-weight:600;color:var(--tz-muted);text-decoration:none;"><i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}</a>
                @if(!$wishlist)
                <a href="javascript:void(0)" class="clear-cart-btn" style="display:inline-flex;align-items:center;gap:6px;padding:9px 20px;background:transparent;border:1px solid rgba(255,100,100,.3);border-radius:var(--tz-radius);font-size:13px;font-weight:600;color:#ff6464;text-decoration:none;">
                    <i class="mdi mdi-trash-can-outline"></i> {{ __('Clear Cart') }}
                </a>
                @endif
            </div>
        </div>

        @if(!$wishlist)
        <div class="col-lg-4">
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:24px;">
                <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--tz-border);">{{ __('Order Summary') }}</div>

                <div class="coupon-contents">
                    @php
                        $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
                        $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
                    @endphp
                    @php $rowSt = 'display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--tz-border);font-size:13px;'; @endphp
                    <div style="{{ $rowSt }}"><span style="color:var(--tz-muted);">{{ __('Subtotal') }}</span><span style="font-weight:600;color:var(--tz-text);">{{ site_currency_symbol() }}{{ $subtotal }}</span></div>
                    <div style="{{ $rowSt }}"><span style="color:var(--tz-muted);">{{ __('Tax (Incl)') }}</span><span style="font-weight:600;color:var(--tz-text);">--</span></div>
                    <div style="display:flex;justify-content:space-between;padding:14px 0;font-size:15px;font-weight:800;">
                        <span style="color:var(--tz-text);">{{ __('Total Amount') }}</span>
                        <span style="color:var(--tz-blue);">{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>
                </div>

                {!! apply_filters('nazmart:cart_summary', '') !!}
                <a href="{{ theme_checkout_url() }}" style="display:flex;align-items:center;justify-content:center;gap:8px;background:var(--tz-blue);color:#fff;padding:13px;border-radius:var(--tz-radius);font-size:14px;font-weight:700;text-decoration:none;margin-top:8px;transition:background .2s;" onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                    {{ __('Proceed to Checkout') }} <i class="mdi mdi-arrow-right"></i>
                </a>

                <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:14px;font-size:11px;color:var(--tz-muted);">
                    <i class="mdi mdi-shield-check" style="color:var(--tz-green);"></i>
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
    $(document).on('click', '.substract', function () { var input = $(this).next('.quantity-input'); if (parseInt(input.val()) > 1) { input[0].stepDown(1); triggerQtyUpdate($(this)); } });
    $(document).on('click', '.plus', function () { var input = $(this).prev('.quantity-input'); input[0].stepUp(1); triggerQtyUpdate($(this)); });
    $(document).on('change', '.quantity-input', function () { triggerQtyUpdate($(this)); });
    function triggerQtyUpdate(el) { var row = el.closest('[data-product-id]'); updateQty(row.data('product-id'), row.find('.quantity-input').val(), row.data('varinat-id') || row.data('variant-id') || ''); }
    function updateQty(productId, qty, variantId) {
        $.ajax({ url: '{{ theme_cart_update_url() }}', type: 'GET', data: { product_id: productId, quantity: qty, variant_id: variantId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) { if (data.type === 'success') { toastr.success(data.msg); } else if (data.quantity_msg) { toastr.warning(data.quantity_msg); } if (data.markup) { $('#cart_tbody').html(data.markup); } if (data.cart_price_markup) { $('.coupon-contents').html(data.cart_price_markup); } $('.loader').hide(); },
            error: function () { $('.loader').hide(); }
        });
    }
    $(document).on('click', '.close-table-cart', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
        $.ajax({ url: '{{ theme_cart_remove_url() }}', type: 'GET', data: { product_hash_id: hashId }, beforeSend: function () { $('.loader').show(); },
            success: function (data) { if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } } $('.loader').hide(); },
            error: function () { $('.loader').hide(); }
        });
    });
    $(document).on('click', '.close-table-wishlist', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
        $.ajax({ url: '{{ theme_wishlist_remove_url() }}', type: 'GET', data: { product_hash_id: hashId }, beforeSend: function () { $('.loader').show(); },
            success: function (data) { if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } } $('.loader').hide(); },
            error: function () { $('.loader').hide(); }
        });
    });
    $(document).on('click', '.move-to-wishlist', function (e) {
        e.stopPropagation();
        var hashId = $(this).closest('[data-product_hash_id]').attr('data-product_hash_id');
        if (!hashId) return;
        $.ajax({ url: '{{ theme_wishlist_move_url() }}', type: 'GET', data: { product_hash_id: hashId }, beforeSend: function () { $('.loader').show(); },
            success: function (data) { if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } } $('.loader').hide(); },
            error: function () { $('.loader').hide(); }
        });
    });
    $(document).on('click', '.clear-cart-btn', function () { $('.loader').show(); setTimeout(function () { window.location.href = '{{ theme_cart_clear_url() }}'; }, 300); });
})(jQuery);
</script>
@endsection
