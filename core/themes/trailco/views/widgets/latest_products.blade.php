{{-- TrailCo: Latest Products Widget (grid card layout matching HTML design) --}}
<section class="tr-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        @if($title)
        <div class="d-flex align-items-end justify-content-between mb-4">
            <div>
                <span class="tr-section-tag"><i class="mdi mdi-new-box"></i> {{ __('New Arrivals') }}</span>
                <h2 class="tr-section-title mb-0">{{ $title }}</h2>
                @if($subtitle)
                    <p class="tr-section-sub mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            <a href="{{ $view_all_url }}" class="tr-btn tr-btn-outline tr-btn-sm">
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
                    <div class="tr-card">
                        <div class="tr-card-img">
                            <a href="{{ $productUrl }}">
                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <span class="tr-card-placeholder"><i class="mdi mdi-hiking"></i></span>
                                @endif
                            </a>
                            @if($discount)
                                <span class="tr-card-badge tr-badge-sale">-{{ $discount }}%</span>
                            @elseif($badge)
                                <span class="tr-card-badge tr-badge-new">{{ $badge }}</span>
                            @endif
                            @if($campaign)
                                <span class="tr-card-tag">{{ $campaign }}</span>
                            @endif
                            <button class="tr-card-wishlist add-to-wishlist-btn" type="button"
                                    data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                                <i class="mdi mdi-heart-outline"></i>
                            </button>
                        </div>
                        <div class="tr-card-body">
                            @if($product->category)
                                <div class="tr-card-brand">{{ $product->category->name }}</div>
                            @endif
                            <div class="tr-card-name">
                                <a href="{{ $productUrl }}" class="tr-card-name-link">
                                    {{ \Illuminate\Support\Str::limit($product->name, 55) }}
                                </a>
                            </div>
                            <div class="tr-stars">{!! theme_star_rating($product) !!}</div>
                            <div class="tr-card-price">
                                <span class="tr-price-sale">{{ amount_with_currency_symbol($pPrice['sale_price']) }}</span>
                                @if($pPrice['regular_price'] && $pPrice['regular_price'] !== $pPrice['sale_price'])
                                    <span class="tr-price-orig">{{ amount_with_currency_symbol($pPrice['regular_price']) }}</span>
                                @endif
                            </div>
                            <button type="button" class="tr-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                                <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5" style="color:var(--tr-stone);">
                    <i class="mdi mdi-emoticon-sad-outline" style="font-size:48px;opacity:.35;display:block;margin-bottom:8px;"></i>
                    {{ __('No products available.') }}
                </div>
            @endforelse
        </div>
    </div>
</section>
