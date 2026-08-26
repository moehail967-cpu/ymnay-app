<section class="py-5" style="background:var(--lg-bg);">
    <div class="container">
        <div class="d-flex align-items-end justify-content-between mb-5 flex-wrap gap-3">
            <div>
                <div class="lg-line"></div>
                <h2 class="lg-section-title mb-0">{{ $title }}</h2>
            </div>
            @if(!empty($view_all_url) && $view_all_url !== '#')
                <a href="{{ $view_all_url }}" class="lg-btn lg-btn-outline lg-btn-sm">
                    {{ __('View All') }} <i class="las la-arrow-right"></i>
                </a>
            @endif
        </div>

        <div class="row g-3">
            @forelse($products as $product)
                @php
                    $data    = theme_product_price($product);
                    $img_url = theme_product_image($product->image_id ?? null, 'grid');
                    $url     = theme_product_url($product->slug);
                    $cat     = $product->category?->name ?? null;
                    $badge   = $product->badge?->name ?? null;

                    // Stars
                    $reviews = $product->reviews ?? [];
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

                    // Category · first tag
                    $tags = $product->tags ?? [];
                    $firstTag = $tags instanceof \Illuminate\Support\Collection
                        ? $tags->first()?->name
                        : (is_array($tags) ? ($tags[0] ?? null) : null);
                    $catLabel = $cat ? strtoupper($cat) : null;
                    if ($firstTag && $catLabel) {
                        $catLabel .= ' · ' . strtoupper($firstTag);
                    }
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="lg-card">
                        {{-- Image --}}
                        <div class="lg-card-img">
                            <a href="{{ $url }}">
                                @if($img_url)
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:60px;background:var(--lg-surface);">💎</div>
                                @endif
                            </a>
                        </div>

                        {{-- Badge (outside image div, absolute on card) --}}
                        @if($data['discount'])
                            <div class="lg-card-badge">{{ $data['discount'] }}% {{ __('OFF') }}</div>
                        @elseif($badge)
                            <div class="lg-card-badge">{{ strtoupper($badge) }}</div>
                        @endif

                        {{-- Wishlist (outside image div, absolute on card) --}}
                        <button class="lg-card-wishlist add-to-wishlist-btn"
                                data-product_id="{{ $product->id }}"
                                aria-label="{{ __('Wishlist') }}">
                            <i class="las la-heart"></i>
                        </button>

                        {{-- Body --}}
                        <div class="lg-card-body">
                            @if($catLabel)
                                <div class="lg-card-cat">{{ $catLabel }}</div>
                            @endif
                            <a href="{{ $url }}" class="lg-card-name">
                                {{ \Illuminate\Support\Str::words($product->name, 7) }}
                            </a>

                            <div class="lg-card-price">
                                <span class="lg-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                                @if($data['regular_price'])
                                    <span class="lg-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                                @endif
                            </div>
                            <button class="lg-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                                <i class="las la-shopping-bag"></i> {{ __('ADD TO BAG') }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5" style="color:var(--lg-muted);">{{ __('No products found.') }}</div>
            @endforelse
        </div>
    </div>
</section>
