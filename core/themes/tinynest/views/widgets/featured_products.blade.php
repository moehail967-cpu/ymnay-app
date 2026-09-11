{{-- TinyNest: Featured Products Widget --}}
<section class="tn-section tn-fp-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        @if($title)
        <div class="d-flex align-items-end justify-content-between mb-4">
            <div>
                <span class="tn-section-tag tn-section-tag-pink">{{ __('Parent Favourites') }}</span>
                <h2 class="tn-section-title mb-0">{{ $title }}</h2>
                @if($subtitle)
                    <p class="tn-section-sub mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            <a href="{{ theme_shop_url() }}" class="tn-btn-view-all flex-shrink-0">
                {{ __('View All') }} <i class="mdi mdi-arrow-right"></i>
            </a>
        </div>
        @endif

        <div class="row g-3">
            @forelse($products as $product)
                @php
                    $pPrice     = theme_product_price($product);
                    $imgUrl     = theme_product_image($product->image_id ?? null);
                    $productUrl = theme_product_url($product->slug);
                    $badge      = $product->badge?->name ?? null;
                    $discount   = $pPrice['discount'];
                    $campaign   = $pPrice['campaign_name'];
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="tn-card">
                        <div class="tn-card-img" style="height:190px;overflow:hidden;">
                            <a href="{{ $productUrl }}">
                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <span class="tn-product-placeholder"><i class="mdi mdi-baby-carriage"></i></span>
                                @endif
                            </a>
                        </div>
                        {{-- Left badge: sale percentage or named badge --}}
                        @if($discount)
                            <div class="tn-card-badge">{{ $discount }}% {{ __('off') }}</div>
                        @elseif($badge)
                            <div class="tn-card-badge">{{ $badge }}</div>
                        @endif
                        {{-- Right badge: campaign name --}}
                        @if($campaign)
                            <div class="tn-card-safe">{{ $campaign }}</div>
                        @endif
                        <button class="tn-card-wishlist add-to-wishlist-btn" type="button"
                                data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                            <i class="mdi mdi-heart-outline"></i>
                        </button>
                        <div class="tn-card-body">
                            @if($product->category)
                                <div class="tn-card-cat">{{ $product->category->name }}</div>
                            @endif
                            <div class="tn-card-name">
                                <a href="{{ $productUrl }}">{{ \Illuminate\Support\Str::limit($product->name, 55) }}</a>
                            </div>
                            <div class="tn-stars">{!! theme_star_rating($product) !!}</div>
                            <div class="tn-card-price">
                                <span class="tn-price-sale">{{ amount_with_currency_symbol($pPrice['sale_price']) }}</span>
                                @if($pPrice['regular_price'] && $pPrice['regular_price'] !== $pPrice['sale_price'])
                                    <span class="tn-price-orig">{{ amount_with_currency_symbol($pPrice['regular_price']) }}</span>
                                @endif
                            </div>
                            <button type="button" class="tn-card-atc add-to-cart-btn"
                                    data-product_id="{{ $product->id }}">
                                <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="tn-empty-state">
                        <i class="mdi mdi-emoticon-sad-outline tn-empty-icon"></i>
                        <p class="tn-empty-title">{{ __('No products found.') }}</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
