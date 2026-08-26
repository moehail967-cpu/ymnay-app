{{-- ChefHome: Product grid partial — used for initial render and AJAX filter response --}}
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
        <div class="ch-card h-100 d-flex flex-column">
            <div class="ch-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @else
                    <a href="{{ $product_url }}" class="ch-img-ph"><i class="las la-utensils"></i></a>
                @endif

                @if($discount)
                    <span class="ch-card-badge ch-badge-sale">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="ch-card-badge ch-badge-hot">{{ $badge }}</span>
                @elseif($campaign_name)
                    <span class="ch-card-badge ch-badge-new">{{ $campaign_name }}</span>
                @endif

                <button class="ch-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                    <i class="las la-heart"></i>
                </button>
            </div>

            <div class="ch-card-body d-flex flex-column flex-grow-1">
                <div class="ch-card-title flex-grow-1">
                    <a href="{{ $product_url }}">{{ \Illuminate\Support\Str::words($product->name, 8) }}</a>
                </div>
                <div class="ch-card-meta">
                    {!! theme_star_rating($product) !!}
                </div>
                <div class="ch-card-footer">
                    <div>
                        <div class="ch-price">{{ amount_with_currency_symbol($sale_price) }}</div>
                        @if($regular_price)
                            <div class="ch-price-old">{{ amount_with_currency_symbol($regular_price) }}</div>
                        @endif
                    </div>
                    <button class="ch-add-btn add-to-cart-btn"
                            data-product_id="{{ $product->id }}"
                            aria-label="{{ __('Add to cart') }}">
                        <i class="las la-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="ch-pagination">
        @foreach($links as $page => $url)
            <button class="ch-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="chFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
