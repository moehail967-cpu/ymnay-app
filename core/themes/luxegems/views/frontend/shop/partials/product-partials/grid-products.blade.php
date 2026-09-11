{{-- LuxeGems: Product grid partial — matches lg-card design from featured_products widget --}}
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

        // Stars
        $reviews     = $product->reviews ?? [];
        $reviewCount = $reviews instanceof \Illuminate\Support\Collection ? $reviews->count() : count((array)$reviews);
        if ($reviewCount > 0) {
            $sum = $reviews instanceof \Illuminate\Support\Collection
                ? $reviews->sum('rating')
                : array_sum(array_column((array)$reviews, 'rating'));
            $avgRating = (int) round($sum / $reviewCount);
        } else {
            $avgRating = 5;
        }
        $stars = str_repeat('★', $avgRating) . str_repeat('☆', max(0, 5 - $avgRating));

        // Category label
        $tags     = $product->tags ?? [];
        $firstTag = $tags instanceof \Illuminate\Support\Collection
            ? $tags->first()?->name
            : (is_array($tags) ? ($tags[0] ?? null) : null);
        $catLabel = $cat ? strtoupper($cat) : null;
        if ($firstTag && $catLabel) {
            $catLabel .= ' · ' . strtoupper($firstTag);
        }
    @endphp

    <div class="col-6 col-md-4 col-lg-4">
        <div class="lg-card h-100 d-flex flex-column">
            {{-- Image --}}
            <div class="lg-card-img">
                <a href="{{ $product_url }}">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    @else
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:60px;background:var(--lg-surface);">💎</div>
                    @endif
                </a>
            </div>

            {{-- Badge --}}
            @if($discount)
                <div class="lg-card-badge">{{ $discount }}% {{ __('OFF') }}</div>
            @elseif($badge)
                <div class="lg-card-badge">{{ strtoupper($badge) }}</div>
            @elseif($campaign_name)
                <div class="lg-card-badge">{{ strtoupper($campaign_name) }}</div>
            @endif

            {{-- Wishlist --}}
            <button class="lg-card-wishlist add-to-wishlist-btn"
                    data-product_id="{{ $product->id }}"
                    aria-label="{{ __('Wishlist') }}">
                <i class="las la-heart"></i>
            </button>

            {{-- Body --}}
            <div class="lg-card-body d-flex flex-column flex-grow-1">
                @if($catLabel)
                    <div class="lg-card-cat">{{ $catLabel }}</div>
                @endif
                <a href="{{ $product_url }}" class="lg-card-name flex-grow-1">
                    {{ \Illuminate\Support\Str::words($product->name, 7) }}
                </a>

                <div class="lg-card-price">
                    <span class="lg-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price && $regular_price != $sale_price)
                        <span class="lg-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <button class="lg-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                    <i class="las la-shopping-bag"></i> {{ __('ADD TO BAG') }}
                </button>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="lg-pagination">
        @foreach($links as $page => $url)
            <button class="lg-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="luxegemsFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
