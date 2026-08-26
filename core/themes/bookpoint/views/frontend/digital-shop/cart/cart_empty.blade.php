<div class="container" style="padding:80px 0;text-align:center;">
    <i class="las la-{{ $wishlist ? 'heart' : 'shopping-cart' }}"
       style="font-size:72px;color:var(--bp-accent);display:block;margin-bottom:16px;opacity:.6;"></i>
    <h2 style="font-size:24px;color:var(--heading-color,#1a1a1a);margin-bottom:8px;">
        {{ $wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
    </h2>
    <p style="color:#888;font-size:14px;margin-bottom:28px;">
        {{ __("Looks like you haven't added anything yet.") }}
    </p>
    <a href="{{ theme_shop_url() }}" class="bp-btn bp-btn-green">
        <i class="las la-store"></i> {{ __('Continue Shopping') }}
    </a>
</div>
