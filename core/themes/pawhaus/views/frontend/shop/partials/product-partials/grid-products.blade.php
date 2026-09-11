{{-- PawHaus: Product grid partial --}}
@foreach($products as $product)
    @php
        $data          = theme_product_price($product);
        $regular_price = $data['regular_price'];
        $sale_price    = $data['sale_price'];
        $discount      = $data['discount'];
        $campaign_name = $data['campaign_name'];
        $img_url       = theme_product_image($product->image_id ?? null, 'grid');
        $badge         = $product->badge?->name ?? null;
        $product_url   = theme_product_url($product->slug);
    @endphp

    <div class="col-6 col-md-4">
        <div class="ph-card h-100 d-flex flex-column">
            <div class="ph-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @else
                    <a href="{{ $product_url }}" style="font-size:48px;">🐾</a>
                @endif

                @if($discount)
                    <span class="ph-card-badge">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="ph-card-badge">{{ $badge }}</span>
                @elseif($campaign_name)
                    <span class="ph-card-badge ph-card-badge-new">{{ $campaign_name }}</span>
                @endif

                <button class="ph-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                    <i class="las la-heart"></i>
                </button>
            </div>

            <div class="ph-card-body d-flex flex-column flex-grow-1">
                <div class="ph-card-name flex-grow-1">
                    <a href="{{ $product_url }}">{{ \Illuminate\Support\Str::words($product->name, 8) }}</a>
                </div>
                <div class="ph-stars mt-auto">{!! theme_star_rating($product) !!}</div>
                <div class="ph-card-price">
                    <span class="ph-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span class="ph-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <button class="ph-card-atc add-to-cart-btn"
                        data-product_id="{{ $product->id }}"
                        aria-label="{{ __('Add to cart') }}">
                    <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                </button>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="ph-pagination">
        @foreach($links as $page => $url)
            <button class="ph-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="phFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
