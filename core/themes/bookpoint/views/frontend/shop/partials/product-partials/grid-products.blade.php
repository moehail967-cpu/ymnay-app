{{-- BookPoint: Product grid partial — initial render + AJAX filter response --}}
@foreach($products as $product)
    @php
        $data          = theme_product_price($product);
        $img_url       = theme_product_image($product->image_id ?? null, 'grid');
        $badge         = $product->badge?->name ?? null;
        $discount      = $data['discount'];
        $campaign_name = $data['campaign_name'];
        $url           = theme_product_url($product->slug);
    @endphp

    <div class="col-6 col-md-4 col-lg-4">
        <div class="bp-card h-100 d-flex flex-column">
            <div class="bp-card-img">
                @if($img_url)
                    <a href="{{ $url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @else
                    <a href="{{ $url }}" class="bp-card-img-placeholder">
                        <i class="las la-book"></i>
                    </a>
                @endif

                @if($discount)
                    <span class="bp-card-badge">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="bp-card-badge">{{ $badge }}</span>
                @elseif($campaign_name)
                    <span class="bp-card-badge">{{ $campaign_name }}</span>
                @endif

                <button class="bp-card-wishlist add-to-wishlist-btn"
                        data-product_id="{{ $product->id }}"
                        aria-label="{{ __('Wishlist') }}">
                    <i class="las la-heart"></i>
                </button>
            </div>

            <div class="bp-card-body d-flex flex-column flex-grow-1">
                <div class="bp-card-cat">{{ $product->category?->name ?? '' }}</div>
                <div class="bp-card-name flex-grow-1">
                    <a href="{{ $url }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                </div>
                <div class="bp-card-stars mt-auto">{!! theme_star_rating($product) !!}</div>
                <div class="bp-card-price">
                    <span class="bp-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                    @if($data['regular_price'])
                        <span class="bp-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                    @endif
                </div>
                <button class="bp-card-atc add-to-cart-btn"
                        data-product_id="{{ $product->id }}"
                        aria-label="{{ __('Add to Cart') }}">
                    <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                </button>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="bp-pagination">
        @foreach($links as $page => $url)
            <button class="bp-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="bpFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
