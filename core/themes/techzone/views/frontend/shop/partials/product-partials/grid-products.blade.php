{{-- TechZone: Product grid partial — used for initial render and AJAX filter response --}}
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
        <div class="tz-card h-100 d-flex flex-column">
            <div class="tz-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @else
                    <a href="{{ $product_url }}" class="tz-card-img-placeholder">
                        <i class="las la-laptop"></i>
                    </a>
                @endif
            </div>

            @if($discount)
                <div class="tz-card-badge">{{ $discount }}% {{ __('off') }}</div>
            @elseif($badge)
                <div class="tz-card-badge tz-card-badge-new">{{ $badge }}</div>
            @elseif($campaign_name)
                <div class="tz-card-badge tz-card-badge-new">{{ $campaign_name }}</div>
            @endif

            <button class="tz-card-wishlist add-to-wishlist-btn" type="button"
                    data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                <i class="mdi mdi-heart-outline"></i>
            </button>

            <div class="tz-card-body d-flex flex-column flex-grow-1">
                @if($product->category)
                    <div class="tz-card-cat">{{ $product->category->name ?? '' }}</div>
                @endif
                <div class="tz-card-name flex-grow-1">
                    <a href="{{ $product_url }}">{{ \Illuminate\Support\Str::words($product->name, 8) }}</a>
                </div>
                <div class="tz-stars mt-auto">
                    {!! theme_star_rating($product) !!}
                </div>
                <div class="tz-card-price">
                    <span class="tz-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span class="tz-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <button type="button" class="tz-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                    <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                </button>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="tz-pagination">
        @foreach($links as $page => $url)
            <button class="tz-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="tzFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
