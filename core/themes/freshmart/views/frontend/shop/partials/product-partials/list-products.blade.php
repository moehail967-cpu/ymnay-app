{{-- FreshMart: Product list partial — used when list view is selected --}}
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

    <div class="col-12">
        <div class="fm-list-card">
            <div class="fm-list-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @else
                    <a href="{{ $product_url }}" class="fm-img-ph">🥦</a>
                @endif

                @if($discount)
                    <span class="fm-card-badge">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="fm-card-badge">{{ $badge }}</span>
                @endif
            </div>

            <div class="fm-list-card-body">
                <div class="fm-card-name mb-1">
                    <a href="{{ $product_url }}">{{ $product->name }}</a>
                </div>
                <div class="fm-stars mb-2">{!! theme_star_rating($product) !!}</div>
                @if($product->description)
                <p class="fm-list-card-desc">{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 120) }}</p>
                @endif
                <div class="fm-card-price mb-3">
                    <span class="fm-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span class="fm-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="fm-card-atc add-to-cart-btn"
                            data-product_id="{{ $product->id }}"
                            aria-label="{{ __('Add to cart') }}">
                        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                    </button>
                    <button class="fm-icon-btn add-to-wishlist-btn"
                            data-product_id="{{ $product->id }}"
                            aria-label="{{ __('Wishlist') }}">
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
    <div class="fm-pagination">
        @foreach($links as $page => $url)
            <button class="fm-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="fmFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
