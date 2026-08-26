<section class="kv-featured-products-widget py-5">
    <div class="container">
        <div class="kv-sec-header mb-4">
            <div>
                @if(!empty($tag))
                    <span class="kv-sec-tag">{{ $tag }}</span>
                @endif
                <h2 class="kv-sec-title mt-2">{{ $title }}</h2>
            </div>
            @if(!empty($view_all_url) && $view_all_url !== '#')
                <a href="{{ $view_all_url }}" class="kv-view-all-btn">{{ __('View All') }} →</a>
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
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="kv-card">

                        {{-- Badge top-left --}}
                        @if($data['discount'])
                            <span class="kv-card-badge kv-badge-sale">{{ $data['discount'] }}% {{ __('off') }}</span>
                        @elseif($badge)
                            <span class="kv-card-badge kv-badge-hot">{{ $badge }}</span>
                        @endif

                        {{-- Image inset --}}
                        <a href="{{ $url }}" class="kv-card-img-wrap">
                            @if($img_url)
                                <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy" class="kv-card-img">
                            @else
                                <div class="kv-img-ph">🧸</div>
                            @endif
                        </a>

                        <div class="kv-card-body">
                            @if($cat)
                                <div class="kv-card-cat">{{ $cat }}</div>
                            @endif
                            <div class="kv-card-name">
                                <a href="{{ $url }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                            </div>
                            <div class="kv-stars">{!! theme_star_rating($product) !!}</div>
                            <div class="kv-card-price">
                                <span class="kv-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                                @if($data['regular_price'])
                                    <span class="kv-price-old">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                                @endif
                            </div>
                            <div class="kv-card-footer-row">
                                <button class="add-to-cart-btn kv-card-atc" data-product_id="{{ $product->id }}">
                                    <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                                </button>
                                <button class="add-to-wishlist-btn kv-card-wishlist" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                                    <i class="las la-heart"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div style="font-size:48px">🧸</div>
                    <p class="kv-muted mt-2">{{ __('No products found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
