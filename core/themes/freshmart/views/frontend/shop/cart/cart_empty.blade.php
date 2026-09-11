<div class="container" style="padding:80px 0;text-align:center;">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div style="font-size:72px;color:var(--fm-green);margin-bottom:20px;line-height:1;">
                <i class="las la-shopping-cart"></i>
            </div>
            <h2 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:10px;">
                {{ $wishlist ?? false ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
            </h2>
            <p style="font-size:14px;color:var(--fm-muted);margin-bottom:28px;line-height:1.7;">
                {{ $wishlist ?? false
                    ? __('Save items you love and shop them later.')
                    : __("Looks like you haven't added anything yet. Let's change that!") }}
            </p>
            <a href="{{ theme_shop_url() }}" class="fm-btn fm-btn-green" style="padding:13px 32px;font-size:15px;">
                <i class="las la-store"></i> {{ __('Continue Shopping') }}
            </a>
        </div>
    </div>
</div>
