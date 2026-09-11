{{-- VelvetLux: Product grid partial — used for initial render and AJAX filter response --}}
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
        <div class="vl-card h-100 d-flex flex-column">
            <div class="vl-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @endif

                {{-- Badge top-left --}}
                @if($discount)
                    <span class="vl-card-badge">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="vl-card-badge">{{ $badge }}</span>
                @elseif($campaign_name)
                    <span class="vl-card-badge">{{ $campaign_name }}</span>
                @endif

                {{-- Wishlist heart — top-right --}}
                <button class="vl-card-wishlist add-to-wishlist-btn"
                        data-product_id="{{ $product->id }}"
                        aria-label="{{ __('Wishlist') }}">
                    <i class="mdi mdi-heart-outline"></i>
                </button>
            </div>

            <div class="vl-card-body d-flex flex-column flex-grow-1">
                <div class="vl-card-cat">{{ $product->category?->name ?? '' }}</div>

                <div class="vl-card-name flex-grow-1">
                    <a href="{{ $product_url }}" style="color:inherit;text-decoration:none;">
                        {{ \Illuminate\Support\Str::words($product->name, 7) }}
                    </a>
                </div>

                <div class="vl-card-price mt-auto">
                    <span class="vl-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price && $regular_price != $sale_price)
                        <span class="vl-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>

                <button class="vl-card-atc add-to-cart-btn"
                        data-product_id="{{ $product->id }}"
                        aria-label="{{ __('Add to cart') }}">
                    <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                </button>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="vl-pagination">
        @foreach($links as $page => $url)
            <button class="vl-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="velvetluxFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
