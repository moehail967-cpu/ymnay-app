<section class="gl-widget-products">
<div class="container">
    <div class="mb-5">
        @if(!empty($tag))
            <div class="gl-wp-tag">{{ $tag }}</div>
        @endif
        <div class="d-flex align-items-center justify-content-between mt-1">
            @if(!empty($title))
                <h2 class="gl-wp-section-title mb-0">{{ $title }}</h2>
            @endif
            @if(!empty($view_all_url))
                <a href="{{ $view_all_url }}" class="gl-view-all">{{ __('View All') }} →</a>
            @endif
        </div>
    </div>
    <div class="row g-4">
        @forelse($products as $product)
        @php
            $data     = theme_product_price($product);
            $img_url  = theme_product_image($product->image_id ?? null, 'grid');
            $url      = theme_product_url($product->slug);
            $discount = $data['discount'];
            $badge    = $product->badge?->name ?? null;
        @endphp
        <div class="col-6 col-md-4 col-lg-3 d-flex">
            <div class="gl-card w-100">
                <div class="gl-card-img">
                    @if($img_url)
                        <a href="{{ $url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"></a>
                    @else
                        <a href="{{ $url }}" style="font-size:56px;display:flex;align-items:center;justify-content:center;height:100%;">✨</a>
                    @endif
                </div>

                @if($discount)
                    <div class="gl-card-badge gl-badge-best">{{ $discount }}% {{ __('off') }}</div>
                @elseif($badge)
                    <div class="gl-card-badge gl-badge-best">{{ $badge }}</div>
                @endif

                <button class="gl-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                    <i class="las la-heart"></i>
                </button>

                <div class="gl-card-body">
                    <div class="gl-card-cat">{{ $product->category?->name ?? '' }}</div>
                    <a href="{{ $url }}" class="gl-card-name">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                    <div class="gl-stars">{!! theme_star_rating($product) !!}</div>
                    <div class="gl-card-price">
                        <span class="gl-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                        @if($data['regular_price'])
                            <span class="gl-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                        @endif
                    </div>
                    <button class="gl-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Add to cart') }}">
                        <i class="las la-shopping-cart"></i> {{ __('Add to Bag') }}
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-4">
            <i class="las la-box-open" style="font-size:48px;color:var(--gl-border)"></i>
            <p class="gl-section-sub mt-2">{{ __('No products found') }}</p>
        </div>
        @endforelse
    </div>
</div>
</section>
