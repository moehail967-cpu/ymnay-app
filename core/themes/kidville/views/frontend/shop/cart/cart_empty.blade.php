@php $is_wishlist = $wishlist ?? false; @endphp

<div style="text-align:center;padding:80px 20px;max-width:480px;margin:0 auto;">

    <div style="width:96px;height:96px;border-radius:50%;background:var(--kv-light);border:3px solid var(--kv-border);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:44px;color:var(--kv-red);">
        @if($is_wishlist)
            <i class="las la-heart"></i>
        @else
            <i class="las la-shopping-cart"></i>
        @endif
    </div>

    <h2 style="font-size:26px;font-weight:800;color:var(--kv-dark);margin-bottom:12px;letter-spacing:-.5px;">
        {{ $is_wishlist ? __('Your Wishlist is Empty!') : __('Oops! Your Cart is Empty') }}
    </h2>

    <p style="font-size:15px;color:var(--kv-muted);line-height:1.75;margin-bottom:32px;">
        {{ $is_wishlist
            ? __('Save your favourite toys and gifts to your wishlist and add them to cart when ready.')
            : __("Looks like you haven't added anything yet. Let's find something fun!") }}
    </p>

    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ theme_shop_url() }}" class="kv-btn kv-btn-red">
            <i class="las la-store"></i> {{ __('Browse Shop') }}
        </a>
        @if($is_wishlist)
        <a href="{{ theme_cart_url() }}" class="kv-btn kv-btn-outline">
            <i class="las la-shopping-cart"></i> {{ __('View Cart') }}
        </a>
        @else
        <a href="{{ theme_home_url() }}" class="kv-btn kv-btn-ghost">
            <i class="las la-home"></i> {{ __('Go Home') }}
        </a>
        @endif
    </div>

</div>
