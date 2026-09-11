@php
    $data    = theme_product_price($product);
    $img_url = theme_product_image($product->image_id ?? null, 'grid');
    $url     = theme_product_url($product->slug);
    $badge   = $product->badge?->name ?? null;
@endphp
<div class="fn-card">
    <div class="fn-card-img">
        <a href="{{ $url }}">
            @if($img_url)
                <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
            @else
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:56px;color:#3D8870;opacity:.25;">
                    <i class="las la-couch"></i>
                </div>
            @endif
        </a>

        @if($data['discount'])
            <span class="fn-card-badge">{{ $data['discount'] }}% {{ __('off') }}</span>
        @elseif($badge)
            <span class="fn-card-badge">{{ $badge }}</span>
        @elseif($data['campaign_name'])
            <span class="fn-card-badge">{{ $data['campaign_name'] }}</span>
        @endif

        <button class="fn-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
            <i class="las la-heart"></i>
        </button>
    </div>

    <div class="fn-card-body">
        @if($product->category?->name)
            <div class="fn-card-cat">{{ $product->category->name }}</div>
        @endif
        <div class="fn-card-name">
            <a href="{{ $url }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
        </div>
        <div class="fn-card-price">
            <span class="fn-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
            @if($data['regular_price'])
                <span class="fn-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
            @endif
        </div>
        <button class="fn-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Add to Cart') }}">
            <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
        </button>
    </div>
</div>
