{{-- LuxeGems: Empty cart / wishlist state --}}
@php $is_wishlist = request()->routeIs('tenant.user.wishlist') || (isset($wishlist) && $wishlist); @endphp
<div style="padding:80px 0;text-align:center;">
    <div style="font-size:64px;color:var(--lx-gold);margin-bottom:20px;">
        <i class="{{ $is_wishlist ? 'las la-heart' : 'las la-shopping-bag' }}"></i>
    </div>
    <h2 style="font-size:22px;font-weight:300;color:var(--lx-white);letter-spacing:2px;margin-bottom:8px;">
        {{ $is_wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
    </h2>
    <p style="font-size:13px;color:var(--lx-muted);margin-bottom:32px;">
        {{ $is_wishlist ? __('Save pieces you love — they will be waiting here.') : __('Your cart is empty. Discover our fine collection.') }}
    </p>
    <a href="{{ theme_shop_url() }}" class="lx-btn lx-btn-primary">
        <i class="las la-gem"></i> {{ __('Explore Collection') }}
    </a>
</div>
