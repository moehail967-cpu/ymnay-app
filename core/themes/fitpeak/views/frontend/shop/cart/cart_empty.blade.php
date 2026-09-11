<div class="container" style="padding:80px 0;text-align:center;">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div style="font-size:80px;color:var(--fp-green);margin-bottom:20px;line-height:1;">
                <i class="mdi mdi-cart-off"></i>
            </div>
            <h2 style="font-family:var(--fp-font-head);font-size:36px;font-weight:800;color:var(--fp-text);text-transform:uppercase;letter-spacing:2px;margin-bottom:12px;">
                {{ $wishlist ?? false ? __('Wishlist Empty') : __('Cart Empty') }}
            </h2>
            <p style="font-size:14px;color:var(--fp-muted);margin-bottom:32px;line-height:1.7;">
                {{ $wishlist ?? false
                    ? __("Save products you love and come back to them anytime.")
                    : __("You haven't added anything yet. Time to fuel your performance!") }}
            </p>
            <a href="{{ theme_shop_url() }}" class="fp-btn fp-btn-primary" style="display:inline-flex;align-items:center;gap:10px;padding:14px 32px;font-size:15px;">
                <i class="mdi mdi-shopping"></i> {{ __('Browse Products') }}
            </a>
        </div>
    </div>
</div>
