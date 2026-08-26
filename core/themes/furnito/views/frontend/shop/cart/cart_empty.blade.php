@php $is_wishlist = $wishlist ?? false; @endphp

<div class="fn-empty-state">
    <div class="fn-empty-state-icon">
        <i class="las {{ $is_wishlist ? 'la-heart' : 'la-shopping-cart' }}"></i>
    </div>
    <h2 class="fn-empty-state-title">
        {{ $is_wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
    </h2>
    <p class="fn-empty-state-text">
        {{ $is_wishlist
            ? __('Save items you love to your wishlist and find them here.')
            : __('Looks like you haven\'t added anything to your cart yet.') }}
    </p>
    <a href="{{ theme_shop_url() }}" class="fn-btn fn-btn-gold">
        <i class="las la-store"></i> {{ __('Browse Products') }}
    </a>
</div>
