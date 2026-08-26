<section class="ar-nc-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="ar-section-head">
            @if(!empty($title))
                <h2 class="ar-section-title">{{ $title }}</h2>
            @endif
            @if($show_line)
                <div class="ar-section-line"></div>
            @endif
        </div>
        <div class="ar-prod-grid">
            @forelse($products as $product)
            @php
                $data      = theme_product_price($product);
                $img_url   = theme_product_image($product->image_id ?? null, 'grid');
                $url       = theme_product_url($product->slug);
                $discount  = $data['discount'] ?? 0;
                $badge     = $product->badge?->name ?? null;
                $camp_name = $data['campaign_name'] ?? null;
            @endphp
            <div class="ar-prod-grid-item">
                <div class="ar-card">
                    <div class="ar-card-img">
                        @if($img_url)
                            <a href="{{ $url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"></a>
                        @else
                            <a href="{{ $url }}" class="ar-card-placeholder"><i class="las la-flask"></i></a>
                        @endif

                        @if($discount)
                            <span class="ar-card-badge">{{ $discount }}% {{ __('off') }}</span>
                        @elseif($badge)
                            <span class="ar-card-badge">{{ $badge }}</span>
                        @elseif($camp_name)
                            <span class="ar-card-badge">{{ $camp_name }}</span>
                        @endif

                        <button class="ar-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                            <i class="mdi mdi-heart-outline"></i>
                        </button>
                    </div>
                    <div class="ar-card-body">
                        <div class="ar-card-cat">{{ $product->category?->name ?? '' }}</div>
                        <div class="ar-card-name">
                            <a href="{{ $url }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                        </div>
                        <div class="ar-stars">{!! theme_star_rating($product) !!}</div>
                        <div class="ar-card-price">
                            <span class="ar-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                            @if(!empty($data['regular_price']) && $data['regular_price'] != $data['sale_price'])
                                <span class="ar-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                            @endif
                        </div>
                        <a href="{{ $url }}" class="ar-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                            <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="ar-empty-state" style="grid-column:1/-1;text-align:center;padding:40px 0;">
                <i class="las la-box-open" style="font-size:48px;color:var(--ar-muted);"></i>
                <p style="margin-top:8px;color:var(--ar-muted);">{{ __('No products found') }}</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
