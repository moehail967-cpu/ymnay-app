@php $is_wishlist = $wishlist ?? false; @endphp

<div style="text-align:center;padding:80px 20px;max-width:480px;margin:0 auto;">

    <div style="width:88px;height:88px;border-radius:50%;background:var(--gc-warm);border:1.5px solid var(--gc-border);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:40px;">
        @if($is_wishlist)<i class="las la-heart"></i>@else<i class="las la-shopping-cart"></i>@endif
    </div>

    <div style="width:40px;height:2px;background:var(--gc-rose);border-radius:2px;margin:0 auto 20px;"></div>

    <h2 style="font-size:22px;font-weight:400;color:var(--gc-dark);letter-spacing:-.3px;margin-bottom:10px;font-style:italic;">
        {{ $is_wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
    </h2>

    <p style="font-size:14px;color:var(--gc-muted);line-height:1.75;margin-bottom:32px;font-style:italic;">
        {{ $is_wishlist
            ? __('Save pieces you love to your wishlist and add them to cart when ready.')
            : __("You haven't added any pieces yet. Browse the collection and find something beautiful.") }}
    </p>

    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ theme_shop_url() }}" class="gc-btn gc-btn-primary">
            <i class="las la-store"></i> {{ __('Browse Collection') }}
        </a>
        @if($is_wishlist)
        <a href="{{ theme_cart_url() }}" class="gc-btn gc-btn-ghost">
            <i class="las la-shopping-cart"></i> {{ __('View Cart') }}
        </a>
        @else
        <a href="{{ theme_home_url() }}" class="gc-btn gc-btn-ghost">
            <i class="las la-home"></i> {{ __('Go Home') }}
        </a>
        @endif
    </div>

</div>
