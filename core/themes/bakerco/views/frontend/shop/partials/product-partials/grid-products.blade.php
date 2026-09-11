{{-- BakerCo: Product grid partial — used for initial render and AJAX filter response --}}
@foreach($products as $product)
    @php
        $data      = theme_product_price($product);
        $img_url   = theme_product_image($product->image_id ?? null, 'grid');
        $badge     = $product->badge?->name ?? null;
        $discount  = $data['discount'];
        $campaign_name = $data['campaign_name'];
        $url       = theme_product_url($product->slug);
    @endphp

    <div class="col-6 col-md-4">
        <div class="bk-card h-100 d-flex flex-column">
            <div class="bk-card-img">
                @if($img_url)
                    <a href="{{ $url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @else
                    <a href="{{ $url }}" class="bk-placeholder" style="height:220px;display:flex;align-items:center;justify-content:center;font-size:56px;">🥐</a>
                @endif

                @if($discount)
                    <span class="bk-card-badge">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="bk-card-badge">{{ $badge }}</span>
                @elseif($campaign_name)
                    <span class="bk-card-badge">{{ $campaign_name }}</span>
                @endif

                <button class="bk-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                    <i class="mdi mdi-heart-outline"></i>
                </button>
            </div>

            <div class="bk-card-body d-flex flex-column flex-grow-1">
                <div class="bk-card-cat">{{ $product->category?->name ?? '' }}</div>
                <div class="bk-card-name flex-grow-1">
                    <a href="{{ $url }}" style="color:inherit;text-decoration:none;">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                </div>
                <div class="bk-stars mt-auto">{!! theme_star_rating($product) !!}</div>
                <div class="bk-card-price">
                    <span class="bk-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                    @if($data['regular_price'])
                        <span class="bk-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                    @endif
                </div>
                <button class="bk-card-atc bk-atc-btn add-to-cart-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Add to Cart') }}">
                    <i class="mdi mdi-cart-outline"></i> {{ __('Add to Cart') }}
                </button>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="bk-pagination">
        @foreach($links as $page => $url)
            <button class="bk-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="bkFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
