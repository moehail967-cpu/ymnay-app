@php $is_wishlist = $wishlist ?? false; @endphp

<section style="background:var(--ms-cream);padding:80px 0 96px;">
    <div class="container">
        <div style="text-align:center;max-width:480px;margin:0 auto;">

            {{-- Icon --}}
            <div style="width:96px;height:96px;border-radius:50%;background:#fff;border:1px solid var(--ms-border);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;box-shadow:var(--ms-shadow);">
                <i class="mdi {{ $is_wishlist ? 'mdi-heart-outline' : 'mdi-cart-outline' }}"
                   style="font-size:42px;color:var(--ms-olive);"></i>
            </div>

            {{-- Divider --}}
            <div style="width:40px;height:2px;background:var(--ms-linen);border-radius:1px;margin:0 auto 20px;"></div>

            <h2 style="font-size:22px;font-weight:300;color:var(--ms-dark);letter-spacing:.04em;margin-bottom:12px;">
                {{ $is_wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
            </h2>

            <p style="font-size:14px;color:var(--ms-muted);line-height:1.7;margin-bottom:32px;">
                {{ $is_wishlist
                    ? __('Save items you love to your wishlist and revisit them when ready.')
                    : __('You haven\'t added anything yet. Explore our collection and find something beautiful.') }}
            </p>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ theme_shop_url() }}" class="ms-btn-dark">
                    <i class="mdi mdi-storefront-outline" style="margin-right:6px;"></i>
                    {{ __('Explore Collection') }}
                </a>
                @if($is_wishlist)
                <a href="{{ theme_cart_url() }}" class="ms-btn-border">
                    <i class="mdi mdi-cart-outline" style="margin-right:6px;"></i>
                    {{ __('View Cart') }}
                </a>
                @else
                <a href="{{ theme_home_url() }}" class="ms-btn-border">
                    <i class="mdi mdi-home-outline" style="margin-right:6px;"></i>
                    {{ __('Go Home') }}
                </a>
                @endif
            </div>

            @if(!$is_wishlist)
            <div style="margin-top:24px;">
                <a href="{{ theme_wishlist_url() }}"
                   style="font-size:13px;color:var(--ms-muted);text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:color .2s;"
                   onmouseover="this.style.color='var(--ms-linen-d)'"
                   onmouseout="this.style.color='var(--ms-muted)'">
                    <i class="mdi mdi-heart-outline"></i> {{ __('View Wishlist') }}
                </a>
            </div>
            @endif

        </div>
    </div>
</section>
