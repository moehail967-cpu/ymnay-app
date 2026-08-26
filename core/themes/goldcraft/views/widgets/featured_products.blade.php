<section class="gc-widget-products">
<div class="container">
    <div class="gc-wp-header mb-3">
        <div>
            @if(!empty($tag))
                <div class="gc-section-tag" style="font-style:italic;font-family:Georgia,serif;letter-spacing:0;">{{ $tag }}</div>
            @endif
            @if(!empty($title))
                <h2 class="gc-section-head mb-0">{{ $title }}</h2>
            @endif
            <div class="gc-section-divider-left"></div>
        </div>
        @if(!empty($view_all_url))
            <a href="{{ $view_all_url }}" class="gc-wp-view-all">{{ __('View All') }} →</a>
        @endif
    </div>

    <div class="row g-3">
        @forelse($products as $product)
        @php
            $data     = theme_product_price($product);
            $img_url  = theme_product_image($product->image_id ?? null, 'grid');
            $url      = theme_product_url($product->slug);
            $discount = $data['discount'];
            $badge    = $product->badge?->name ?? null;
        @endphp
        <div class="col-6 col-md-4 col-lg-3">
            <div class="gc-card">
                <div class="gc-card-img">
                    @if($img_url)
                        <a href="{{ $url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"></a>
                    @else
                        <a href="{{ $url }}" class="gc-card-img-ph"><i class="las la-gem"></i></a>
                    @endif
                </div>
                @if($discount)
                    <div class="gc-card-badge">{{ $discount }}% {{ __('off') }}</div>
                @elseif($badge)
                    <div class="gc-card-badge">{{ $badge }}</div>
                @endif
                <button class="gc-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                    <i class="mdi mdi-heart-outline"></i>
                </button>
                <div class="gc-card-body">
                    <div class="gc-card-cat">{{ $product->category?->name ?? '' }}</div>
                    <div class="gc-card-name"><a href="{{ $url }}"><em>{{ \Illuminate\Support\Str::words($product->name, 7) }}</em></a></div>
                    <div class="gc-stars">{!! theme_star_rating($product) !!}</div>
                    <div class="gc-card-price">
                        <span class="gc-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                        @if($data['regular_price'])
                            <span class="gc-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                        @endif
                    </div>
                    <button class="gc-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Add to cart') }}">
                        <i class="mdi mdi-cart-outline"></i> {{ __('Add to Bag') }}
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-4">
            <i class="las la-gem" style="font-size:48px;color:var(--gc-border)"></i>
            <p class="gc-section-sub mt-2">{{ __('No products found') }}</p>
        </div>
        @endforelse
    </div>
</div>
</section>
