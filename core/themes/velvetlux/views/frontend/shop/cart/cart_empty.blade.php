@php $is_wishlist = $wishlist ?? false; @endphp
<div style="text-align:center;padding:100px 24px;">
    <div style="font-size:64px;color:var(--vl-champagne);opacity:.3;margin-bottom:24px;">{{ $is_wishlist ? '♡' : '◈' }}</div>
    <div style="font-size:10px;letter-spacing:5px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:12px;">{{ $is_wishlist ? __('Wishlist Empty') : __('Cart Empty') }}</div>
    <h2 style="font-size:28px;font-weight:300;color:var(--vl-ivory);font-family:'Cormorant Garamond',serif;margin-bottom:16px;letter-spacing:1px;">{{ $is_wishlist ? __('Your wishlist is empty') : __('Your bag is empty') }}</h2>
    <p style="font-size:14px;color:var(--vl-muted);margin-bottom:40px;">{{ $is_wishlist ? __('Save pieces you love and return when ready.') : __('Discover our curated collections and add pieces to your bag.') }}</p>
    <a href="{{ theme_shop_url() }}" class="vl-btn vl-btn-primary">{{ __('Explore Collection') }}</a>
</div>
