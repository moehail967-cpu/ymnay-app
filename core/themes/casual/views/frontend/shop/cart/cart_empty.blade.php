{{-- Casual: Empty cart / wishlist state --}}
<div class="cs-cart-empty">
    <i class="las {{ isset($wishlist) && $wishlist ? 'la-heart' : 'la-shopping-cart' }} cs-cart-empty-icon"></i>
    <h2 class="cs-cart-empty-title">
        {{ isset($wishlist) && $wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
    </h2>
    <p class="cs-cart-empty-sub">{{ __("Looks like you haven't added anything yet.") }}</p>
    <a href="{{ theme_shop_url() }}" class="cs-apply-btn">
        <i class="las la-store"></i> {{ __('Continue Shopping') }}
    </a>
</div>
