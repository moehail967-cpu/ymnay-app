{{-- FitPeak: Product grid partial — used for initial render and AJAX filter response --}}
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

    <div class="col-6 col-md-4 col-lg-4 d-flex">
        <div class="fp-card w-100 h-100 d-flex flex-column">
            <div class="fp-card-img">
                <a href="{{ $product_url }}">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    @else
                        <div class="fp-img-ph"><i class="mdi mdi-dumbbell fp-img-ph-icon"></i></div>
                    @endif
                </a>
                @if($discount)
                    <span class="fp-card-badge">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="fp-card-badge">{{ $badge }}</span>
                @elseif($campaign_name)
                    <span class="fp-card-badge">{{ $campaign_name }}</span>
                @endif
                <button class="fp-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}" title="{{ __('Wishlist') }}">
                    <i class="mdi mdi-heart-outline"></i>
                </button>
            </div>
            <div class="fp-card-body d-flex flex-column flex-grow-1">
                @if($product->category)
                    <div class="fp-card-cat">{{ $product->category->name }}</div>
                @endif
                <a href="{{ $product_url }}" class="fp-card-title flex-grow-1">{{ \Illuminate\Support\Str::words($product->name, 8) }}</a>
                @if($product->summary)
                    <p class="fp-card-spec">{{ \Illuminate\Support\Str::words($product->summary, 10) }}</p>
                @endif
                <div class="fp-stars mt-auto">{!! theme_star_rating($product) !!}</div>
                <div class="fp-price-row">
                    <span class="fp-price">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span class="fp-price-old">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <button class="fp-card-atc-full add-to-cart-btn"
                        data-product_id="{{ $product->id }}"
                        data-name="{{ $product->name }}">
                    <i class="mdi mdi-cart-outline"></i> {{ __('Add to Cart') }}
                </button>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="fp-pagination">
        @foreach($links as $page => $url)
            <button class="fp-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="fpFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
