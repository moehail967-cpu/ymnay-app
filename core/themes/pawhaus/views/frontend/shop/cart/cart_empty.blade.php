{{-- PawHaus: Empty cart / wishlist state --}}
<div class="container" style="padding:80px 0 100px;text-align:center;">
    <div style="max-width:420px;margin:0 auto;">
        <div style="width:96px;height:96px;border-radius:50%;background:var(--ph-terra-light);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;border:3px solid var(--ph-border);">
            <i class="las {{ $wishlist ? 'la-heart' : 'la-shopping-cart' }}" style="font-size:44px;color:var(--ph-terra);"></i>
        </div>
        <h2 style="font-size:24px;font-weight:800;color:var(--ph-dark);margin-bottom:10px;">
            {{ $wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
        </h2>
        <p style="font-size:14px;color:var(--ph-muted);margin-bottom:32px;line-height:1.7;">
            {{ $wishlist
                ? __('Save your favourite pet products here and come back when you\'re ready to order.')
                : __('Looks like you haven\'t added any paw-some products yet. Start shopping now!') }}
        </p>
        <a href="{{ theme_shop_url() }}" class="ph-btn ph-btn-terra">
            <i class="las la-store"></i> {{ __('Shop Now') }}
        </a>
    </div>
</div>
