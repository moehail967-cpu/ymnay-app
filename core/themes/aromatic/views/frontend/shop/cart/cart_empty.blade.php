<div class="ar-cart-empty-wrap">
    <i class="mdi mdi-{{ isset($wishlist) && $wishlist ? 'heart-outline' : 'cart-off' }} ar-cart-empty-icon"></i>
    <h2 class="ar-cart-empty-title">
        {{ isset($wishlist) && $wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
    </h2>
    <p class="ar-cart-empty-sub">{{ __("Looks like you haven't added anything yet.") }}</p>
    <a href="{{ theme_shop_url() }}" class="ar-btn ar-btn-red">
        <i class="mdi mdi-storefront-outline"></i> {{ __('Continue Shopping') }}
    </a>
</div>
