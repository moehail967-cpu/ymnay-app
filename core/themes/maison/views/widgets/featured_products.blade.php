<section class="ms-widget-products">
    <div class="container">
        <div class="ms-widget-products-head">
            <div>
                @if(!empty($section_tag))
                    <div class="ms-section-tag">{{ $section_tag }}</div>
                @endif
                <h2 class="ms-section-title mb-0">{!! $title !!}</h2>
                @if(!empty($subtitle))
                    <p class="ms-section-sub mt-1 mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            <a href="{{ !empty($view_all_url) && $view_all_url !== '#' ? $view_all_url : theme_shop_url() }}" class="ms-btn ms-btn-border-dark ms-btn-sm">
                {{ __('View All') }} <i class="las la-arrow-right"></i>
            </a>
        </div>
        <div class="row g-3">
            @forelse($products as $product)
                @php
                    $data     = theme_product_price($product);
                    $img_url  = theme_product_image($product->image_id ?? null, 'grid');
                    $url      = theme_product_url($product->slug);
                    $badge    = $product->badge?->name ?? null;
                    $discount = $data['discount'];
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="ms-card">
                        <div class="ms-card-img" style="height:220px;overflow:hidden;">
                            @if($img_url)
                                <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;">
                            @endif
                        </div>
                        @if($discount)
                            <span class="ms-card-badge">{{ $discount }}% {{ __('off') }}</span>
                        @elseif($badge)
                            <span class="ms-card-badge">{{ $badge }}</span>
                        @endif
                        <button class="ms-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}">
                            <i class="mdi mdi-heart-outline"></i>
                        </button>
                        <div class="ms-card-body">
                            <div class="ms-card-cat">{{ $product->category?->name ?? '' }}</div>
                            <div class="ms-card-name">
                                <a href="{{ $url }}" class="ms-card-name-link">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                            </div>
                            <div class="ms-stars">{!! theme_star_rating($product) !!}</div>
                            <div class="ms-card-price">
                                <span class="ms-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                                @if($data['regular_price'])
                                    <span class="ms-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                                @endif
                            </div>
                            <button class="ms-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                                <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 ms-muted-text">{{ __('No products found.') }}</div>
            @endforelse
        </div>
    </div>
</section>
