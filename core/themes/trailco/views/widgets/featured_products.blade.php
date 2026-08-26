{{-- TrailCo: Featured Products Widget --}}
<section class="py-5" style="background:var(--tc-bg);">
    <div class="container">
        @if($title)
        <div class="d-flex align-items-end justify-content-between mb-4">
            <div>
                <span class="tc-section-tag">{{ __('Trail Tested') }}</span>
                <h2 class="tc-section-title mb-0">{!! $title !!}</h2>
            </div>
            <a href="{{ $view_all_url }}" class="tc-btn tc-btn-outline tc-btn-sm">
                {{ __('View All') }} <i class="mdi mdi-arrow-right"></i>
            </a>
        </div>
        @endif

        <div class="row g-3">
            @forelse($products as $product)
                @php
                    $pPrice     = theme_product_price($product);
                    $imgUrl     = theme_product_image($product->image_id ?? null);
                    $productUrl = theme_product_url($product->slug);
                    $badge      = $product->badge?->name ?? null;
                    $discount   = $pPrice['discount'];
                    $campaign   = $pPrice['campaign_name'];
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="tc-card">
                        <div class="tc-card-img">
                            <a href="{{ $productUrl }}">
                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <span class="tc-card-placeholder"><i class="mdi mdi-image-outline"></i></span>
                                @endif
                            </a>
                        </div>
                        {{-- Left badge: terra background --}}
                        @if($badge)
                            <div class="tc-card-badge">{{ $badge }}</div>
                        @elseif($discount)
                            <div class="tc-card-badge">{{ __('Sale') }}</div>
                        @endif
                        {{-- Right tag: olive background --}}
                        @if($campaign)
                            <div class="tc-card-tag">{{ $campaign }}</div>
                        @elseif($discount)
                            <div class="tc-card-tag">-{{ $discount }}%</div>
                        @endif
                        <div class="tc-card-body">
                            @if($product->category)
                                <div class="tc-card-cat">{{ $product->category->name }}</div>
                            @endif
                            <div class="tc-card-name">
                                <a href="{{ $productUrl }}" class="tc-card-name-link">
                                    {{ \Illuminate\Support\Str::limit($product->name, 55) }}
                                </a>
                            </div>
                            <div class="tc-stars">{!! theme_star_rating($product) !!}</div>
                            <div class="tc-card-price">
                                <span class="tc-price-sale">{{ amount_with_currency_symbol($pPrice['sale_price']) }}</span>
                                @if($pPrice['regular_price'] && $pPrice['regular_price'] !== $pPrice['sale_price'])
                                    <span class="tc-price-orig">{{ amount_with_currency_symbol($pPrice['regular_price']) }}</span>
                                @endif
                            </div>
                            <div class="tc-card-actions">
                                <button type="button" class="tc-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                                    <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                                </button>
                                <button type="button" class="tc-card-wishlist add-to-wishlist-btn"
                                        data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p style="color:var(--tc-muted);">{{ __('No products available.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
