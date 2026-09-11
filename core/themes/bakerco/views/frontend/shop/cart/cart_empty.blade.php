<div class="container" style="padding:80px 0;text-align:center;">
    <i class="mdi mdi-{{ $wishlist ? 'heart-outline' : 'cart-off' }}"
       style="font-size:72px;color:var(--bk-rose);display:block;margin-bottom:16px;opacity:.6;"></i>
    <h2 style="font-family:var(--bk-font-head);font-size:24px;color:var(--bk-dark);margin-bottom:8px;">
        {{ $wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
    </h2>
    <p style="color:var(--bk-muted);font-size:14px;margin-bottom:28px;">
        {{ __('Looks like you haven\'t added anything yet.') }}
    </p>
    <a href="{{ theme_shop_url() }}" class="bk-btn bk-btn-rose">
        <i class="mdi mdi-storefront-outline"></i> {{ __('Continue Shopping') }}
    </a>
</div>
