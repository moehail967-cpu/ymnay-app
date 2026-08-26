<section class="dk-widget-products">
    <div class="container">
        {{-- Section header --}}
        <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
            <div>
                @if($tag)
                <span class="dk-section-tag">{{ $tag }}</span>
                @endif
                @if($title)
                <h2 class="dk-section-title mb-0">
                    {!! preg_replace('/(\S+)\s*$/', '<span>$1</span>', e($title)) !!}
                </h2>
                @endif
            </div>
            <a href="{{ theme_shop_url() }}" class="dk-btn dk-btn-outline dk-btn-sm">
                {{ __('View All') }} <i class="las la-arrow-right"></i>
            </a>
        </div>

        {{-- Product grid --}}
        <div class="row g-3">
            @forelse($products as $product)
            @php
                $img_url  = theme_product_image($product->image_id ?? null, 'grid');
                $pdata    = theme_product_price($product);
                $badge    = $product->badge?->name ?? null;
                $discount = $pdata['discount'];
                $prod_url = theme_product_url($product->slug);
                $sku      = $product->product_code ?? null;
                $summary  = $product->summary ?? null;
                $sku_line = $sku ? ('SKU: ' . $sku) : ($summary ? \Illuminate\Support\Str::limit(strip_tags($summary), 40) : null);
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="dk-card">
                    {{-- Image --}}
                    <div class="dk-card-img">
                        @if($img_url)
                            <a href="{{ $prod_url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"></a>
                        @else
                            <a href="{{ $prod_url }}" class="dk-card-no-img"><i class="las la-wrench"></i></a>
                        @endif
                        {{-- Left badge: discount > badge name > nothing --}}
                        @if($discount)
                            <span class="dk-card-badge dk-badge-sale">{{ __('Sale') }}</span>
                        @elseif($badge)
                            <span class="dk-card-badge dk-badge-hot">{{ $badge }}</span>
                        @elseif($product->category)
                            <span class="dk-card-badge dk-badge-sale">{{ $product->category->name }}</span>
                        @endif
                    </div>
                    {{-- Body --}}
                    <div class="dk-card-body">
                        @if($product->category)
                            <div class="dk-card-cat">{{ $product->category->name }}</div>
                        @endif
                        <a href="{{ $prod_url }}" class="dk-card-name">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                        @if($sku_line)
                        <div class="dk-card-sku">{{ $sku_line }}</div>
                        @endif
                        <div class="dk-stars">{!! theme_star_rating($product) !!}</div>
                        <div class="dk-card-price">
                            <span class="dk-price-sale">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                            @if($pdata['regular_price'])
                                <span class="dk-price-orig">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                            @endif
                        </div>
                        <div class="dk-card-actions">
                            <button class="dk-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                                <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                            </button>
                            <button class="dk-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                                <i class="las la-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5" style="color:var(--dk-silver);">{{ __('No products found.') }}</div>
            @endforelse
        </div>
    </div>
</section>
