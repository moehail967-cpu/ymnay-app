<section class="pf-products-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="pf-section-row-head">
            <div>
                <h2 class="pf-section-h2">{{ $title }}</h2>
                @if(!empty($subtitle))
                    <p class="pf-section-hint">{{ $subtitle }}</p>
                @endif
            </div>
            <a href="{{ $view_all_url }}" class="pf-link-teal">
                {{ __('View All') }} <i class="las la-arrow-right"></i>
            </a>
        </div>

        <div class="row g-3">
            @foreach($products as $product)
            @php
                $pf_img   = theme_product_image($product->image_id ?? null, 'grid');
                $pf_price = theme_product_price($product);
                $pf_url   = theme_product_url($product->slug ?? '');
                $discount = $pf_price['discount'] ?? 0;
                $campaign = $pf_price['campaign_name'] ?? null;
                $badge    = $product->badge?->name ?? null;
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="pf-product-card">
                    @if($discount)
                        <div class="pf-product-badge pf-badge-sale">{{ $discount }}% OFF</div>
                    @elseif($campaign)
                        <div class="pf-product-badge pf-badge-deal">{{ $campaign }}</div>
                    @elseif($badge)
                        <div class="pf-product-badge pf-badge-tag">{{ $badge }}</div>
                    @endif

                    <div class="pf-product-img">
                        <a href="{{ $pf_url }}">
                            <img src="{{ $pf_img ?? theme_placeholder_image() }}" alt="{{ $product->name }}" loading="lazy">
                        </a>
                    </div>

                    <div class="pf-product-body">
                        @if($product->category)
                            <div class="pf-product-brand">{{ $product->category->name }}</div>
                        @endif
                        <h3 class="pf-product-name">
                            <a href="{{ $pf_url }}">{{ $product->name }}</a>
                        </h3>
                        @if($product->summary ?? null)
                            <div class="pf-card-generic">{{ \Illuminate\Support\Str::limit($product->summary, 60) }}</div>
                        @endif
                        @if($product->unit ?? null)
                            <div class="pf-product-meta">
                                <i class="las la-pills"></i> {{ $product->unit }}
                            </div>
                        @endif
                        <div class="pf-product-footer">
                            <div class="pf-product-price">
                                <span class="pf-price-current">{{ amount_with_currency_symbol($pf_price['sale_price']) }}</span>
                                @if(!empty($pf_price['regular_price']) && $pf_price['regular_price'] != $pf_price['sale_price'])
                                    <span class="pf-price-was">{{ amount_with_currency_symbol($pf_price['regular_price']) }}</span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <button class="pf-wish-btn add-to-wishlist-btn"
                                        data-product_id="{{ $product->id }}"
                                        aria-label="{{ __('Wishlist') }}">
                                    <i class="las la-heart"></i>
                                </button>
                                <button class="pf-add-btn add-to-cart-btn"
                                        data-product_id="{{ $product->id }}"
                                        aria-label="{{ __('Add to cart') }}">
                                    <i class="las la-cart-plus"></i>
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
