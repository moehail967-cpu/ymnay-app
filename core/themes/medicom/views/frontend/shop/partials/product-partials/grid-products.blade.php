<div class="mc-shop-grid mt-4">
    @foreach($products as $product)
    @php
        $pd       = get_product_dynamic_price($product);
        $price    = $pd['sale_price'];
        $oldPrice = $pd['regular_price'];
        $discount = $pd['discount'];
        $pImg     = theme_product_image($product->image_id ?? null, 'grid');
        $pUrl     = theme_product_url($product->slug ?? $product->id);
    @endphp
    <div class="mc-card h-100 d-flex flex-column">
        <div class="mc-card-img-wrap">
            <a href="{{ $pUrl }}">
                @if($pImg)
                <img src="{{ $pImg }}" alt="{{ $product->name }}" class="mc-card-img" loading="lazy">
                @else
                <div class="mc-card-img-ph"><i class="las la-laptop"></i></div>
                @endif
            </a>
            <button class="add-to-wishlist-btn mc-card-wish" data-product_id="{{ $product->id }}" title="{{ __('Wishlist') }}">
                <i class="las la-heart"></i>
            </button>
            @if($discount)
            <span class="mc-card-badge">{{ $discount }}% {{ __('off') }}</span>
            @endif
        </div>
        <div class="mc-card-body d-flex flex-column flex-grow-1">
            <span class="mc-card-cat">{{ $product->category?->name ?? 'Category' }}</span>
            <a href="{{ $pUrl }}" class="mc-card-name flex-grow-1">{{ Str::limit($product->name, 45) }}</a>
            <div class="mc-card-prices">
                <span class="mc-card-price mt-auto">{{ amount_with_currency_symbol($price) }}</span>
                @if($oldPrice)
                <span class="mc-card-old">{{ amount_with_currency_symbol($oldPrice) }}</span>
                @endif
            </div>
            <button class="add-to-cart-btn mc-card-atc" data-product_id="{{ $product->id }}" title="{{ __('Add to Cart') }}">
                <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
            </button>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if(count($links) > 1)
<div class="mc-pagination mt-5">
    @foreach(generateDynamicPagination($current_page, count($links)) as $index => $link)
        @if($link === '...')
            <span class="mc-pg-btn mc-pg-dots">…</span>
        @else
            <button class="mc-pg-btn {{ $link === $current_page ? 'mc-pg-active' : '' }}"
                    onclick="elFilterRequest({{ $link }})">{{ $link }}</button>
        @endif
    @endforeach
</div>
@endif
