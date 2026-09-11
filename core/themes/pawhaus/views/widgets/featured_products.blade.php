{{-- PawHaus: Featured Products --}}
<section class="ph-featured-products-widget">
    <div class="container">
        <div class="ph-products-head">
            <div>
                @if(!empty($section_tag))
                    <div class="ph-products-tag">{{ $section_tag }}</div>
                @endif
                <h2 class="ph-products-title">{!! $title !!}</h2>
            </div>
            <a href="{{ !empty($view_all_url) && $view_all_url !== '#' ? $view_all_url : theme_shop_url() }}"
               class="ph-btn ph-btn-outline ph-btn-sm ph-btn-pill">
                {{ __('View All') }} <i class="las la-arrow-right"></i>
            </a>
        </div>

        <div class="row g-3">
            @forelse($products as $product)
                @php
                    $data        = theme_product_price($product);
                    $sale_price  = $data['sale_price'];
                    $reg_price   = $data['regular_price'];
                    $discount    = $data['discount'];
                    $campaign    = $data['campaign_name'] ?? null;
                    $img_url     = theme_product_image($product->image_id ?? null, 'grid');
                    $product_url = theme_product_url($product->slug);
                    $badge       = $product->badge?->name ?? null;
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="ph-card">
                        <div class="ph-card-img">
                            @if($img_url)
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>
                            @else
                                <a href="{{ $product_url }}" class="ph-card-img-ph">
                                    <i class="las la-paw"></i>
                                </a>
                            @endif

                            @if($discount)
                                <span class="ph-card-badge">-{{ $discount }}%</span>
                            @elseif($campaign)
                                <span class="ph-card-badge ph-badge-campaign">{{ $campaign }}</span>
                            @elseif($badge)
                                <span class="ph-card-badge ph-badge-tag">{{ $badge }}</span>
                            @endif

                            <button class="ph-card-wishlist add-to-wishlist-btn"
                                    data-product_id="{{ $product->id }}"
                                    aria-label="{{ __('Wishlist') }}">
                                <i class="las la-heart"></i>
                            </button>
                        </div>

                        <div class="ph-card-body">
                            <div class="ph-card-cat">{{ $product->category?->name ?? '' }}</div>
                            <div class="ph-card-name">
                                <a href="{{ $product_url }}">{{ \Illuminate\Support\Str::words($product->name, 8) }}</a>
                            </div>
                            <div class="ph-stars">{!! theme_star_rating($product) !!}</div>
                            <div class="ph-card-price">
                                <span class="ph-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                                @if($reg_price)
                                    <span class="ph-price-orig">{{ amount_with_currency_symbol($reg_price) }}</span>
                                @endif
                            </div>
                            <button class="ph-card-atc add-to-cart-btn"
                                    data-product_id="{{ $product->id }}"
                                    aria-label="{{ __('Add to cart') }}">
                                <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="las la-box-open" style="font-size:48px;color:var(--ph-border);"></i>
                    <p class="ph-section-sub mt-2">{{ __('No products found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
