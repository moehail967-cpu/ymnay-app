@extends('tenant.frontend.frontend-page-master')

@php
    $page_title = $wishlist ? __('Wishlist') : __('Cart');
@endphp

@section('title') {{ $page_title }} @endsection
@section('page-title') {{ $page_title }} @endsection

@section('style')
<style>
.ms-step-bar {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 36px;
}
.ms-step-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 22px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
}
.ms-step-num {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    flex-shrink: 0;
}
.ms-step-line {
    flex: 1;
    height: 1px;
    background: var(--ms-border);
}
.ms-step-line.filled { background: var(--ms-linen); }
.ms-btn-dark, .ms-btn-border { display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 28px;border-radius:var(--ms-radius);font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;transition:all .2s;font-family:inherit;text-decoration:none; }
</style>
@endsection

@section('content')
<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:36px 0 24px;">
    <div class="container">
        <h1 style="font-size:13px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ $wishlist ? __('Saved Items') : __('Your Cart') }}</h1>
        <h2 style="font-size:28px;font-weight:300;color:var(--ms-dark);margin:0 0 10px;">{{ $page_title }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ms-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ $page_title }}</span>
        </div>
    </div>
</div>

<div class="cart-main-wrapper">
@if($cart_data->count())

@if(!$wishlist)
<div class="container" style="padding-top:36px;">
    <div class="ms-step-bar">
        <div class="ms-step-item" style="background:var(--ms-linen);color:#fff;border-radius:var(--ms-radius) 0 0 var(--ms-radius);">
            <span class="ms-step-num" style="background:#fff;color:var(--ms-linen-d);">1</span>
            {{ __('Cart') }}
        </div>
        <div class="ms-step-line"></div>
        <div class="ms-step-item" style="color:var(--ms-muted);">
            <span class="ms-step-num" style="border:1.5px solid var(--ms-border);color:var(--ms-muted);">2</span>
            {{ __('Checkout') }}
        </div>
        <div class="ms-step-line"></div>
        <div class="ms-step-item" style="color:var(--ms-muted);border-radius:0 var(--ms-radius) var(--ms-radius) 0;">
            <span class="ms-step-num" style="border:1.5px solid var(--ms-border);color:var(--ms-muted);">3</span>
            {{ __('Confirmed') }}
        </div>
    </div>
</div>
@endif

<div class="container" style="{{ $wishlist ? 'padding-top:36px;' : '' }}padding-bottom:80px;">
    <div class="row g-4">

        {{-- Cart Table --}}
        <div class="col-lg-{{ $wishlist ? '12' : '8' }}">
            <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);overflow:hidden;box-shadow:var(--ms-shadow);">
                <div class="table-responsive">
                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                        <thead>
                            <tr style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);">
                                <th style="padding:14px 20px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);">{{ __('Product') }}</th>
                                <th style="padding:14px 16px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);">{{ __('Price') }}</th>
                                <th style="padding:14px 16px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);">{{ __('Quantity') }}</th>
                                <th style="padding:14px 16px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);">{{ __('Subtotal') }}</th>
                                <th style="padding:14px 16px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cart_tbody">
                        @foreach($cart_data as $key => $data)
                            @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
                            <tr class="table-cart-row" style="border-bottom:1px solid var(--ms-border);"
                                data-product-id="{{ $key }}"
                                data-variant-id="{{ $data->options->variant_id ?? '' }}"
                                data-varinat-id="{{ $data->options->variant_id ?? '' }}">
                                <td style="padding:18px 20px;">
                                    <div style="display:flex;align-items:center;gap:14px;">
                                        <div style="width:70px;height:70px;border-radius:var(--ms-radius);overflow:hidden;border:1px solid var(--ms-border);flex-shrink:0;background:var(--ms-warm);">
                                            {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                                        </div>
                                        <div>
                                            <a href="{{ theme_product_url($slug) }}" style="font-size:14px;font-weight:500;color:var(--ms-dark);text-decoration:none;display:block;margin-bottom:4px;"
                                               onmouseover="this.style.color='var(--ms-linen-d)'"
                                               onmouseout="this.style.color='var(--ms-dark)'">
                                                {{ $data->name }}
                                            </a>
                                            @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                                            <div style="font-size:11px;color:var(--ms-muted);letter-spacing:.03em;">
                                                @if($data?->options?->color_name) {{ $data->options->color_name }} @endif
                                                @if($data?->options?->size_name) · {{ $data->options->size_name }} @endif
                                                @if($data?->options?->attributes)
                                                    @foreach($data->options->attributes as $attrKey => $attrVal) · {{ $attrKey }}: {{ $attrVal }} @endforeach
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:18px 16px;">
                                    <span style="font-size:15px;font-weight:500;color:var(--ms-linen-d);">{{ amount_with_currency_symbol($data->price) }}</span>
                                </td>
                                <td style="padding:18px 16px;">
                                    <div style="display:flex;align-items:center;border:1px solid var(--ms-border);border-radius:var(--ms-radius);overflow:hidden;width:fit-content;">
                                        @if(!$wishlist)
                                            <button type="button" class="substract"
                                                    style="width:32px;height:36px;border:0;background:var(--ms-warm);cursor:pointer;font-size:14px;color:var(--ms-charcoal);transition:background .2s;"
                                                    onmouseover="this.style.background='var(--ms-surface)'"
                                                    onmouseout="this.style.background='var(--ms-warm)'">
                                                <i class="mdi mdi-minus"></i>
                                            </button>
                                        @endif
                                        <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1"
                                               {{ $wishlist ? 'disabled readonly' : '' }}
                                               style="width:48px;height:36px;border:0;border-left:1px solid var(--ms-border);border-right:1px solid var(--ms-border);text-align:center;font-size:14px;font-weight:600;font-family:inherit;color:var(--ms-dark);">
                                        @if(!$wishlist)
                                            <button type="button" class="plus"
                                                    style="width:32px;height:36px;border:0;background:var(--ms-warm);cursor:pointer;font-size:14px;color:var(--ms-charcoal);transition:background .2s;"
                                                    onmouseover="this.style.background='var(--ms-surface)'"
                                                    onmouseout="this.style.background='var(--ms-warm)'">
                                                <i class="mdi mdi-plus"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding:18px 16px;">
                                    <span style="font-size:15px;font-weight:600;color:var(--ms-dark);">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
                                </td>
                                <td style="padding:18px 16px;" class="ms-cart-action-cell {{ $wishlist ? 'ms-cart-action-wishlist d-flex justify-content-around align-items-center' : '' }}" data-product_hash_id="{{ $data->rowId }}">
                                    @if($wishlist)
                                        <div class="move-to-wishlist" title="{{ __('Move to Cart') }}"
                                             style="cursor:pointer;color:var(--ms-olive);font-size:20px;">
                                            <i class="mdi mdi-cart-arrow-down"></i>
                                        </div>
                                    @else
                                        @auth
                                        <button class="save-for-later-btn" type="button"
                                                data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}"
                                                style="background:transparent;border:1px solid var(--ms-border);border-radius:50%;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--ms-muted);font-size:14px;margin-bottom:6px;transition:all .2s;">
                                            <i class="mdi mdi-heart-outline"></i>
                                        </button>
                                        @endauth
                                    @endif
                                    <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" style="cursor:pointer;">
                                        <button type="button"
                                                style="width:30px;height:30px;border-radius:50%;border:1px solid var(--ms-border);background:transparent;cursor:pointer;color:var(--ms-muted);font-size:13px;display:flex;align-items:center;justify-content:center;transition:all .2s;"
                                                onmouseover="this.style.borderColor='#C0392B';this.style.color='#C0392B'"
                                                onmouseout="this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-muted)'">
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

            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:20px;">
                <a href="{{ theme_shop_url() }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--ms-charcoal);text-decoration:none;transition:all .2s;"
                   onmouseover="this.style.borderColor='var(--ms-linen)';this.style.color='var(--ms-linen-d)'"
                   onmouseout="this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-charcoal)'">
                    <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
                </a>
                @if(!$wishlist)
                <button type="button" class="clear-cart-btn"
                        style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:1px solid #E8BFBF;border-radius:var(--ms-radius);font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:#C0392B;background:transparent;cursor:pointer;transition:all .2s;"
                        onmouseover="this.style.background='#FEF2F2'"
                        onmouseout="this.style.background='transparent'">
                    <i class="mdi mdi-trash-can-outline"></i> {{ __('Clear Cart') }}
                </button>
                @endif
            </div>
        </div>

        {{-- Order Summary --}}
        @if(!$wishlist)
        <div class="col-lg-4">
            <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:28px;box-shadow:var(--ms-shadow);position:sticky;top:100px;">
                <div style="font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--ms-border);">
                    {{ __('Order Summary') }}
                </div>

                <div class="coupon-contents">
                    @php
                        $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
                        $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
                    @endphp
                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px dashed var(--ms-border);font-size:13px;">
                        <span style="color:var(--ms-muted);">{{ __('Subtotal') }}</span>
                        <span style="font-weight:600;color:var(--ms-dark);">{{ site_currency_symbol() }}{{ $subtotal }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px dashed var(--ms-border);font-size:13px;">
                        <span style="color:var(--ms-muted);">{{ __('Shipping') }}</span>
                        <span style="font-weight:500;color:var(--ms-muted);">{{ __('Calculated at checkout') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:16px 0;font-size:16px;font-weight:700;color:var(--ms-dark);">
                        <span>{{ __('Total') }}</span>
                        <span style="color:var(--ms-linen-d);">{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>
                </div>

                {!! apply_filters('nazmart:cart_summary', '') !!}

                <a href="{{ theme_checkout_url() }}"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;padding:14px;background:var(--ms-dark);color:#fff;text-decoration:none;border-radius:var(--ms-radius);font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;transition:background .2s;margin-top:4px;"
                   onmouseover="this.style.background='var(--ms-linen-d)'"
                   onmouseout="this.style.background='var(--ms-dark)'">
                    {{ __('Proceed to Checkout') }}
                    <i class="mdi mdi-arrow-right" style="font-size:16px;"></i>
                </a>

                <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:14px;font-size:11px;color:var(--ms-muted);letter-spacing:.04em;">
                    <i class="mdi mdi-shield-check-outline" style="color:var(--ms-olive);"></i>
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
            success: function (data) {
                if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } }
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
                if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } }
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
                if (data.msg) { toastr.success(data.msg); if (data.empty_cart !== '') { $('.cart-main-wrapper').html(data.empty_cart).hide().fadeIn(); } else { location.reload(); } }
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
