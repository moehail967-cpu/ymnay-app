{{-- SportZone: Featured Products Widget --}}
<section class="sz-featured-section">
    <div class="container">

        {{-- Section Heading --}}
        <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <span class="sz-section-tag">{{ __('Top Picks') }}</span>
                <h2 class="sz-section-title mb-0">{{ $title }}</h2>
            </div>
            <a href="{{ $view_all_url }}" class="sz-btn sz-btn-outline-dark sz-btn-sm">
                {{ __('View All') }} <i class="mdi mdi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-3">
            @forelse($products as $product)
                @php
                    $data       = theme_product_price($product);
                    $sale_price = $data['sale_price'];
                    $reg_price  = $data['regular_price'];
                    $discount   = $data['discount'];
                    $img        = get_attachment_image_by_id($product->image_id ?? null, 'grid');
                    $imgUrl     = $img['img_url'] ?? null;
                    $badge      = $product->badge?->name ?? null;
                    $inStock    = theme_product_in_stock($product);
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="sz-card">

                        {{-- Image area --}}
                        <div class="sz-card-img">
                            <a href="{{ theme_product_url($product->slug) }}">
                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="sz-img-ph">
                                        <i class="mdi mdi-dumbbell sz-img-ph-icon"></i>
                                    </div>
                                @endif
                            </a>
                        </div>

                        {{-- Badge (positioned absolute on .sz-card) --}}
                        @if($discount)
                            <div class="sz-card-badge">{{ $discount }}% {{ __('OFF') }}</div>
                        @elseif($badge)
                            <div class="sz-card-badge">{{ $badge }}</div>
                        @elseif(!$inStock)
                            <div class="sz-card-badge" style="background:var(--sz-muted);">{{ __('Sold Out') }}</div>
                        @endif

                        {{-- Wishlist (positioned absolute on .sz-card) --}}
                        <button class="sz-card-wishlist add-to-wishlist-btn"
                                data-product_id="{{ $product->id }}"
                                aria-label="{{ __('Add to Wishlist') }}"
                                type="button">
                            <i class="mdi mdi-heart-outline"></i>
                        </button>

                        {{-- Body --}}
                        <div class="sz-card-body">
                            @if($product->category)
                                <div class="sz-card-cat">{{ $product->category->name }}</div>
                            @endif

                            <div class="sz-card-name">
                                <a href="{{ theme_product_url($product->slug) }}">
                                    {{ \Illuminate\Support\Str::words($product->name, 6) }}
                                </a>
                            </div>

                            <div class="sz-stars">
                                {!! theme_star_rating($product) !!}
                            </div>

                            <div class="sz-card-price">
                                <span class="sz-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                                @if($reg_price && $reg_price > $sale_price)
                                    <span class="sz-price-orig">{{ amount_with_currency_symbol($reg_price) }}</span>
                                @endif
                            </div>

                            @if($inStock)
                                <button class="sz-card-atc add-to-cart-btn"
                                        data-product_id="{{ $product->id }}"
                                        type="button">
                                    <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                                </button>
                            @else
                                <button class="sz-card-atc" disabled type="button" style="background:var(--sz-muted);cursor:not-allowed;">
                                    {{ __('Out of Stock') }}
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 sz-empty-state">
                    <i class="mdi mdi-package-variant-closed" style="font-size:48px;opacity:.25;"></i>
                    <p class="mt-3" style="opacity:.4;">{{ __('No products available.') }}</p>
                </div>
            @endforelse
        </div>

    </div>
</section>
