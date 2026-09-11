{{-- Casual: Product grid partial — initial render (uses $products paginator) --}}
@foreach($products as $product)
    @php
        $data      = theme_product_price($product);
        $img_url   = theme_product_image($product->image_id ?? null, 'grid');
        $badge     = $product->badge?->name ?? null;
        $discount  = $data['discount'];
        $camp_name = $data['campaign_name'] ?? null;
        $url       = theme_product_url($product->slug);
    @endphp

    <div class="col-6 col-md-4">
        <div class="casual-new-product-card h-100 d-flex flex-column">
            <div class="casual-new-product-card-thumb">
                @if($img_url)
                    <a href="{{ $url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"></a>
                @else
                    <a href="{{ $url }}" class="casual-new-thumb-placeholder">
                        <i class="las la-tshirt"></i>
                    </a>
                @endif

                @if($discount)
                    <span class="casual-new-badge">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="casual-new-badge">{{ $badge }}</span>
                @elseif($camp_name)
                    <span class="casual-new-badge">{{ $camp_name }}</span>
                @endif

                <button class="casual-new-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}">
                    <i class="lar la-heart"></i>
                </button>
            </div>

            <div class="casual-new-product-card-contents">
                <div class="casual-new-product-category">{{ $product?->category?->name ?? '' }}</div>
                <h5 class="casual-new-product-title">
                    <a href="{{ $url }}">{{ \Illuminate\Support\Str::words($product->name, 15) }}</a>
                </h5>
                <div class="casual-new-stars mt-auto">{!! theme_star_rating($product) !!}</div>
                <div class="casual-new-product-price">
                    <span class="casual-new-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                    @if(!empty($data['regular_price']))
                        <span class="casual-new-price-regular">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                    @endif
                </div>
                <a href="{{ $url }}" data-product_id="{{ $product->id }}" class="add-to-cart-btn casual-new-add-to-cart">
                    <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                </a>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(isset($links) && count($links) > 1)
<div class="col-12">
    <div class="cs-pagination">
        @foreach(generateDynamicPagination($current_page, count($links)) as $link)
            @if($link === '...')
                <span class="cs-page-ellipsis">…</span>
            @else
                <button class="cs-page-btn {{ $link == $current_page ? 'active' : '' }}"
                        data-page="{{ $link }}"
                        onclick="csFilterRequest({{ $link }})">{{ $link }}</button>
            @endif
        @endforeach
    </div>
</div>
@endif
