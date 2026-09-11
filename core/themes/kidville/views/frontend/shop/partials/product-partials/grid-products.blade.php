{{-- KidVille: Product grid partial — used for initial render and AJAX filter response --}}
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
        $cat           = $product->category?->name ?? null;
    @endphp

    <div class="col-6 col-md-4">
        <div class="kv-card h-100 d-flex flex-column">

            {{-- Badge top-left --}}
            @if($discount)
                <span class="kv-card-badge kv-badge-sale">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="kv-card-badge kv-badge-hot">{{ $badge }}</span>
            @elseif($campaign_name)
                <span class="kv-card-badge kv-badge-new">{{ $campaign_name }}</span>
            @endif

            {{-- Image inset --}}
            <a href="{{ $product_url }}" class="kv-card-img-wrap">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy" class="kv-card-img">
                @else
                    <div class="kv-img-ph">🧸</div>
                @endif
            </a>

            <div class="kv-card-body d-flex flex-column flex-grow-1">
                @if($cat)
                    <div class="kv-card-cat">{{ $cat }}</div>
                @endif
                <div class="kv-card-name flex-grow-1">
                    <a href="{{ $product_url }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                </div>
                <div class="kv-stars mt-auto">{!! theme_star_rating($product) !!}</div>
                <div class="kv-card-price">
                    <span class="kv-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span class="kv-price-old">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <div class="kv-card-footer-row">
                    <button class="add-to-cart-btn kv-card-atc" data-product_id="{{ $product->id }}">
                        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                    </button>
                    <button class="add-to-wishlist-btn kv-card-wishlist" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                        <i class="las la-heart"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="kv-pagination">
        @foreach($links as $page => $url)
            <button class="kv-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="kvFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
