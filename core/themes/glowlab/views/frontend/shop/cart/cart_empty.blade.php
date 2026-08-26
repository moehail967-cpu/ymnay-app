@php $is_wishlist = $wishlist ?? false; @endphp

<div style="text-align:center;padding:80px 20px;max-width:480px;margin:0 auto;">

    <div style="width:88px;height:88px;border-radius:50%;background:var(--gl-gold-pale);border:1.5px solid var(--gl-border);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:38px;color:var(--gl-gold);">
        <i class="mdi {{ $is_wishlist ? 'mdi-heart-outline' : 'mdi-cart-outline' }}"></i>
    </div>

    <div style="width:40px;height:2px;background:var(--gl-gold);border-radius:2px;margin:0 auto 20px;"></div>

    <h2 style="font-size:22px;font-weight:300;color:var(--gl-dark);letter-spacing:-.3px;margin-bottom:10px;">
        {{ $is_wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
    </h2>

    <p style="font-size:14px;color:var(--gl-muted);line-height:1.7;margin-bottom:32px;">
        {{ $is_wishlist
            ? __('Save products you love to your wishlist and add them to cart when ready.')
            : __("You haven't added any products yet. Browse the shop and find something you love.") }}
    </p>

    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ theme_shop_url() }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
           onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
            <i class="mdi mdi-store"></i> {{ __('Browse Products') }}
        </a>
        @if($is_wishlist)
        <a href="{{ theme_cart_url() }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:transparent;color:var(--gl-dark);border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;text-decoration:none;transition:all .2s;"
           onmouseover="this.style.borderColor='var(--gl-dark)'" onmouseout="this.style.borderColor='var(--gl-border)'">
            <i class="mdi mdi-cart-outline"></i> {{ __('View Cart') }}
        </a>
        @else
        <a href="{{ theme_home_url() }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:transparent;color:var(--gl-dark);border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;text-decoration:none;transition:all .2s;"
           onmouseover="this.style.borderColor='var(--gl-dark)'" onmouseout="this.style.borderColor='var(--gl-border)'">
            <i class="mdi mdi-home-outline"></i> {{ __('Go Home') }}
        </a>
        @endif
    </div>

</div>
