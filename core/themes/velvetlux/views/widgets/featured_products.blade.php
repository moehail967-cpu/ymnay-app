{{-- VelvetLux: Featured Products --}}
<section class="py-5" style="background: var(--vl-bg);">
    <div class="container">
        @if($section_title)
        <div class="d-flex align-items-end justify-content-between mb-5">
            <div>
                <span class="vl-section-tag">{!! $section_tag !!}</span>
                <h2 class="vl-section-title mb-0">{!! $section_title !!}</h2>
                <div class="vl-divider"></div>
            </div>
            <a href="{{ $view_all_url }}" class="vl-btn vl-btn-outline vl-btn-sm">
                {{ __('VIEW ALL') }} <i class="mdi mdi-arrow-right"></i>
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
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="vl-card">
                        <div class="vl-card-img">
                            <a href="{{ $productUrl }}" style="display:block;width:100%;height:100%;">
                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="vl-cat-placeholder"><i class="mdi mdi-hanger" style="font-size:64px;opacity:.3;"></i></div>
                                @endif
                            </a>
                        </div>
                        @if($badge)
                            <div class="vl-card-badge">{{ $badge }}</div>
                        @elseif($discount)
                            <div class="vl-card-badge">-{{ $discount }}%</div>
                        @endif
                        <button class="vl-card-wishlist add-to-wishlist-btn" type="button"
                                data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                            <i class="mdi mdi-heart-outline"></i>
                        </button>
                        <div class="vl-card-body">
                            @if($product->category)
                                <div class="vl-card-cat">{{ $product->category->name }}</div>
                            @endif
                            <div class="vl-card-name">
                                <a href="{{ $productUrl }}" style="color:inherit;text-decoration:none;">
                                    <em>{{ \Illuminate\Support\Str::limit($product->name, 50) }}</em>
                                </a>
                            </div>
                            <div class="vl-stars">{!! theme_star_rating($product) !!}</div>
                            <div class="vl-card-price">
                                <span class="vl-price-sale">{{ amount_with_currency_symbol($pPrice['sale_price']) }}</span>
                                @if($pPrice['regular_price'] && $pPrice['regular_price'] !== $pPrice['sale_price'])
                                    <span class="vl-price-orig">{{ amount_with_currency_symbol($pPrice['regular_price']) }}</span>
                                @endif
                            </div>
                            <button type="button" class="vl-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                                {{ __('ADD TO BAG') }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5" style="color:var(--vl-muted);">
                    {{ __('No products available.') }}
                </div>
            @endforelse
        </div>
    </div>
</section>
