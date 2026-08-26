<div class="container" style="padding:80px 0;text-align:center;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div style="font-size:80px;color:var(--tr-olive);margin-bottom:20px;line-height:1;opacity:.4;">
                <i class="mdi {{ ($wishlist ?? false) ? 'mdi-heart-outline' : 'mdi-cart-outline' }}"></i>
            </div>
            <h2 style="font-size:28px;font-weight:900;color:var(--tr-bark);margin-bottom:10px;">
                {{ ($wishlist ?? false) ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
            </h2>
            <p style="font-size:14px;color:var(--tr-stone);margin-bottom:32px;line-height:1.7;">
                {{ ($wishlist ?? false)
                    ? __('Save gear you love and come back for it on your next adventure.')
                    : __("Looks like you haven't packed your bag yet. Let's hit the trail!") }}
            </p>
            <a href="{{ theme_shop_url() }}" class="tr-btn tr-btn-primary" style="padding:12px 32px;font-size:15px;">
                <i class="mdi mdi-store"></i> {{ __('Continue Shopping') }}
            </a>
        </div>
    </div>
</div>
