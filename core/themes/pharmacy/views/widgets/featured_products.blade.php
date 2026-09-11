<section class="pf-products-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="pf-section-row-head">
            <div>
                <h2 class="pf-section-h2">{{ $title }}</h2>
                @if(!empty($subtitle ?? ''))
                    <p class="pf-section-hint">{{ $subtitle }}</p>
                @endif
            </div>
            <a href="{{ $view_all_url }}" class="pf-link-teal">
                {{ __('View All') }} <i class="las la-arrow-right"></i>
            </a>
        </div>

        <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-lg-4">
            @foreach($products as $product)
            @php
                $pf_img   = theme_product_image($product->image_id ?? null, 'grid');
                $pf_price = theme_product_price($product);
                $pf_url   = theme_product_url($product->slug ?? '');
                $discount = $pf_price['discount'] ?? 0;
                $campaign = $pf_price['campaign_name'] ?? null;
                $badge    = $product->badge?->name ?? null;
            @endphp
            <div class="col">
                <div class="pf-prod-card">
                    <div class="pf-prod-img">
                        <a href="{{ $pf_url }}">
                            <img src="{{ $pf_img ?? theme_placeholder_image() }}" alt="{{ $product->name }}" loading="lazy">
                        </a>
                        @if($discount)
                            <span class="pf-prod-badge pf-badge-sale">{{ $discount }}% OFF</span>
                        @elseif($campaign)
                            <span class="pf-prod-badge pf-badge-deal">{{ $campaign }}</span>
                        @elseif($badge)
                            <span class="pf-prod-badge pf-badge-tag">{{ $badge }}</span>
                        @endif
                    </div>
                    <div class="pf-prod-body">
                        @if($product->category)
                            <div class="pf-prod-brand">{{ $product->category->name }}</div>
                        @endif
                        <div class="pf-prod-name">
                            <a href="{{ $pf_url }}">{{ $product->name }}</a>
                        </div>
                        @if($product->summary ?? null)
                            <p class="pf-prod-desc">{{ \Illuminate\Support\Str::limit($product->summary, 55) }}</p>
                        @endif
                        <div class="pf-prod-footer">
                            <div class="pf-prod-price">
                                <span class="pf-prod-price-sale">{{ amount_with_currency_symbol($pf_price['sale_price']) }}</span>
                                @if(!empty($pf_price['regular_price']) && $pf_price['regular_price'] != $pf_price['sale_price'])
                                    <span class="pf-prod-price-orig">{{ amount_with_currency_symbol($pf_price['regular_price']) }}</span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <button class="pf-prod-wish add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                                    <i class="las la-heart"></i>
                                </button>
                                <button class="pf-prod-atc add-to-cart-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Add to cart') }}">
                                    <i class="las la-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
