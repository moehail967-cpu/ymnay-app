@php $is_wishlist = $wishlist ?? false; @endphp

<section style="background:var(--dk-carbon);padding:80px 0 96px;">
    <div class="container">
        <div style="text-align:center;max-width:480px;margin:0 auto;">

            {{-- Icon --}}
            <div style="width:90px;height:90px;border-radius:50%;background:var(--dk-surface);border:2px solid var(--dk-border);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:40px;color:var(--dk-border);">
                <i class="mdi {{ $is_wishlist ? 'mdi-heart-outline' : 'mdi-cart-outline' }}"></i>
            </div>

            {{-- Red accent line --}}
            <div style="width:48px;height:3px;background:var(--dk-red);border-radius:2px;margin:0 auto 20px;"></div>

            <h2 style="font-family:var(--dk-font-head);font-size:22px;font-weight:900;text-transform:uppercase;letter-spacing:1px;color:var(--dk-white);margin-bottom:10px;">
                {{ $is_wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
            </h2>

            <p style="font-size:14px;color:var(--dk-silver);line-height:1.6;margin-bottom:32px;">
                {{ $is_wishlist
                    ? __('Save products you love to your wishlist and add them to cart when ready.')
                    : __('You haven\'t added any products yet. Browse the shop and find something you love.') }}
            </p>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ theme_shop_url() }}" class="dk-btn dk-btn-red">
                    <i class="mdi mdi-store"></i> {{ __('Browse Products') }}
                </a>
                @if($is_wishlist)
                <a href="{{ theme_cart_url() }}" class="dk-btn dk-btn-ghost">
                    <i class="mdi mdi-cart-outline"></i> {{ __('View Cart') }}
                </a>
                @else
                <a href="{{ theme_home_url() }}" class="dk-btn dk-btn-ghost">
                    <i class="mdi mdi-home-outline"></i> {{ __('Go Home') }}
                </a>
                @endif
            </div>

        </div>
    </div>
</section>
