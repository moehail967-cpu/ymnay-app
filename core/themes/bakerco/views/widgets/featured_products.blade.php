<section style="padding-top:{{ $padding_top }}px; padding-bottom:{{ $padding_bottom }}px; background:var(--bk-cream);">
<div class="container">
    <div class="text-center mb-4">
        @if(!empty($section_tag))<div class="bk-section-tag">{{ $section_tag }}</div>@endif
        @if(!empty($title))<h2 class="bk-section-title">{{ $title }}</h2>@endif
        @if(!empty($subtitle))<p class="bk-section-sub">{{ $subtitle }}</p>@endif
    </div>
    <div class="row g-3">
        @foreach($products as $product)
        @php
            $data      = theme_product_price($product);
            $img_url   = theme_product_image($product->image_id ?? null, 'grid');
            $url       = theme_product_url($product->slug);
            $discount  = $data['discount'];
            $badge     = $product->badge?->name ?? null;
        @endphp
        <div class="col-6 col-md-4 col-lg-3">
            <div class="bk-card">
                <div class="bk-card-img">
                    @if($img_url)
                        <a href="{{ $url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"></a>
                    @else
                        <a href="{{ $url }}" class="bk-placeholder" style="height:220px;">🥐</a>
                    @endif
                    @if($discount)
                        <span class="bk-card-badge">{{ $discount }}% {{ __('off') }}</span>
                    @elseif($badge)
                        <span class="bk-card-badge">{{ $badge }}</span>
                    @endif
                    <button class="bk-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                        <i class="mdi mdi-heart-outline"></i>
                    </button>
                </div>
                <div class="bk-card-body">
                    <div class="bk-card-cat">{{ $product->category?->name ?? '' }}</div>
                    <div class="bk-card-name">
                        <a href="{{ $url }}" style="color:inherit;text-decoration:none;">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                    </div>
                    <div class="bk-stars">{!! theme_star_rating($product) !!}</div>
                    <div class="bk-card-price">
                        <span class="bk-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                        @if($data['regular_price'])
                            <span class="bk-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                        @endif
                    </div>
                    <a href="{{ $url }}" class="bk-card-atc bk-atc-btn">
                        <i class="mdi mdi-cart-outline"></i> {{ __('Add to Cart') }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
</section>
