@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Cart') }} @endsection
@section('page-title') {{ __('Cart') }} @endsection

@section('content')
<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{{ __('Shopping Cart') }}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Cart') }}</span>
        </div>
    </div>
</div>

<div class="container tn-page-wrap">

    {{-- Flash messages --}}
    {!! theme_flash_msg() !!}

    @if(theme_cart_items()->isEmpty())
        <div class="tn-cart-empty">
            <div class="tn-cart-empty-icon"><i class="las la-shopping-cart"></i></div>
            <h3 class="tn-cart-empty-title">{{ __('Your cart is empty') }}</h3>
            <a href="{{ theme_shop_url() }}" class="tn-btn tn-btn-primary mt-2">
                {{ __('Continue Shopping') }} <i class="las la-arrow-right ms-2"></i>
            </a>
        </div>
    @else

    {{-- Tab Nav --}}
    <div class="tn-tab-nav mb-4">
        <button class="tn-tab-btn active" id="tn_cart_tab">{{ __('Cart') }}</button>
        <button class="tn-tab-btn" id="tn_wish_tab">{{ __('Wishlist') }}</button>
    </div>

    {{-- Cart Panel --}}
    <div id="tn_cart_panel">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="tn-cart-table">
                    <div class="tn-cart-head d-none d-md-grid">
                        <span>{{ __('Product') }}</span>
                        <span class="text-center">{{ __('Price') }}</span>
                        <span class="text-center">{{ __('Qty') }}</span>
                        <span class="text-center">{{ __('Total') }}</span>
                        <span></span>
                    </div>
                    @foreach(theme_cart_items() as $item)
                    <div class="tn-cart-row" data-row-id="{{ $item->rowId }}">
                        <div class="tn-cart-product">
                            <div class="tn-cart-img">
                                {!! render_image_markup_by_attachment_id($item->options->image ?? null, 'tn-cart-thumb') !!}
                            </div>
                            <div>
                                <div class="tn-cart-name">{{ $item->name }}</div>
                                @php $tn_meta = theme_cart_item_meta($item); @endphp
                                @if($tn_meta)<small class="tn-cart-meta">{{ $tn_meta }}</small>@endif
                            </div>
                        </div>
                        <div class="tn-cart-cell text-center">
                            {{ amount_with_currency_symbol($item->price) }}
                        </div>
                        <div class="tn-cart-cell text-center">
                            <div class="tn-qty-ctrl">
                                <button class="tn-qty-btn tn-cart-qty-minus" data-row="{{ $item->rowId }}">–</button>
                                <input type="number" class="tn-qty-input tn-cart-qty-input" value="{{ $item->qty }}"
                                       data-row="{{ $item->rowId }}" min="1">
                                <button class="tn-qty-btn tn-cart-qty-plus" data-row="{{ $item->rowId }}">+</button>
                            </div>
                        </div>
                        <div class="tn-cart-cell text-center tn-cart-subtotal" data-row="{{ $item->rowId }}">
                            {{ amount_with_currency_symbol($item->subtotal()) }}
                        </div>
                        <div class="tn-cart-cell text-center">
                            @auth
                            <button class="save-for-later-btn" type="button"
                                    data-row_id="{{ $item->rowId }}" title="{{ __('Save for Later') }}">
                                <i class="las la-heart"></i>
                            </button>
                            @endauth
                            <button class="tn-remove-btn tn-cart-remove-btn" data-row="{{ $item->rowId }}"
                                    title="{{ __('Remove') }}">
                                <i class="las la-times"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between mt-3 flex-wrap gap-2">
                    <a href="{{ theme_shop_url() }}" class="tn-btn tn-btn-outline">
                        <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
                    </a>
                    <button class="tn-btn tn-btn-outline" id="tn_clear_cart_btn">
                        <i class="las la-trash"></i> {{ __('Clear Cart') }}
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="tn-sidebar-card">
                    <div class="tn-sidebar-title">{{ __('Order Summary') }}</div>
                    <div class="d-flex justify-content-between py-2 border-bottom tn-summary-sep">
                        <span class="tn-summary-label">{{ __('Subtotal') }}</span>
                        <strong id="tn_cart_subtotal">{{ amount_with_currency_symbol(theme_cart_subtotal()) }}</strong>
                    </div>
                    @if(theme_cart_tax() > 0)
                    <div class="d-flex justify-content-between py-2 border-bottom tn-summary-sep">
                        <span class="tn-summary-label">{{ __('Tax') }}</span>
                        <strong>{{ amount_with_currency_symbol(theme_cart_tax()) }}</strong>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between py-2 mb-3">
                        <span class="tn-summary-total-label">{{ __('Total') }}</span>
                        <strong class="tn-summary-total-val" id="tn_cart_total">
                            {{ amount_with_currency_symbol(theme_cart_total()) }}
                        </strong>
                    </div>
                    {!! apply_filters('nazmart:cart_summary', '') !!}
                    <a href="{{ theme_checkout_url() }}" class="tn-btn tn-btn-primary w-100">
                        {{ __('Proceed to Checkout') }} <i class="las la-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Wishlist Panel --}}
    <div id="tn_wish_panel">
        @if(theme_wishlist_items()->isEmpty())
            <div class="tn-cart-empty">
                <div class="tn-wish-empty-icon"><i class="las la-heart"></i></div>
                <h4 class="tn-wish-empty-title">{{ __('Your wishlist is empty') }}</h4>
            </div>
        @else
        <div class="row g-4">
            @foreach(theme_wishlist_items() as $item)
            <div class="col-6 col-md-3">
                <div class="tn-card">
                    <div class="tn-card-img">
                        <img src="{{ $item->options->image ?? theme_placeholder_image() }}" alt="{{ $item->name }}">
                    </div>
                    <div class="tn-card-body">
                        <div class="tn-card-name">{{ $item->name }}</div>
                        <div class="tn-card-price">{{ amount_with_currency_symbol($item->price) }}</div>
                        <div class="d-flex gap-2 mt-2">
                            <button class="tn-btn tn-btn-primary tn-btn-sm tn-wish-move-btn"
                                    data-row="{{ $item->rowId }}">
                                {{ __('Move to Cart') }}
                            </button>
                            <button class="tn-remove-btn tn-wish-remove-btn" data-row="{{ $item->rowId }}">
                                <i class="las la-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    @endif
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    // Tab switch
    $('#tn_cart_tab').on('click', function () {
        $(this).addClass('active'); $('#tn_wish_tab').removeClass('active');
        $('#tn_cart_panel').show(); $('#tn_wish_panel').hide();
    });
    $('#tn_wish_tab').on('click', function () {
        $(this).addClass('active'); $('#tn_cart_tab').removeClass('active');
        $('#tn_wish_panel').show(); $('#tn_cart_panel').hide();
    });

    // Qty controls
    $(document).on('click', '.tn-cart-qty-minus', function () {
        var row = $(this).data('row');
        var inp = $('.tn-cart-qty-input[data-row="' + row + '"]');
        var v   = parseInt(inp.val());
        if (v > 1) { inp.val(v - 1).trigger('change'); }
    });
    $(document).on('click', '.tn-cart-qty-plus', function () {
        var row = $(this).data('row');
        var inp = $('.tn-cart-qty-input[data-row="' + row + '"]');
        inp.val(parseInt(inp.val()) + 1).trigger('change');
    });
    $(document).on('change', '.tn-cart-qty-input', function () {
        var row = $(this).data('row');
        var qty = $(this).val();
        $.post('{{ theme_cart_update_url() }}', { _token: '{{ theme_csrf() }}', rowId: row, qty: qty }, function (res) {
            if (res.subtotal) $('.tn-cart-subtotal[data-row="' + row + '"]').text(res.subtotal);
            if (res.cart_total) $('#tn_cart_total').text(res.cart_total);
            if (res.cart_subtotal) $('#tn_cart_subtotal').text(res.cart_subtotal);
        });
    });

    // Remove cart item
    $(document).on('click', '.tn-cart-remove-btn', function () {
        var row = $(this).data('row');
        $.post('{{ theme_cart_remove_url() }}', { _token: '{{ theme_csrf() }}', rowId: row }, function () {
            location.reload();
        });
    });

    // Clear cart
    $(document).on('click', '#tn_clear_cart_btn', function () {
        $.post('{{ theme_cart_clear_url() }}', { _token: '{{ theme_csrf() }}' }, function () {
            location.reload();
        });
    });

    // Wishlist remove
    $(document).on('click', '.tn-wish-remove-btn', function () {
        var row = $(this).data('row');
        $.post('{{ theme_wishlist_remove_url() }}', { _token: '{{ theme_csrf() }}', rowId: row }, function () {
            location.reload();
        });
    });

    // Wishlist move to cart
    $(document).on('click', '.tn-wish-move-btn', function () {
        var row = $(this).data('row');
        $.post('{{ theme_wishlist_move_url() }}', { _token: '{{ theme_csrf() }}', rowId: row }, function () {
            location.reload();
        });
    });
})(jQuery);
</script>
@endsection
