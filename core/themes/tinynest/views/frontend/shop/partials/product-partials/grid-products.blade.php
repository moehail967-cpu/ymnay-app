{{-- TinyNest: Product Grid Partial — receives $products, $links, $current_page from controller --}}
@php $products = $products ?? []; @endphp

@if(empty($products))
    <div class="tn-empty-state">
        <div class="tn-empty-icon"><i class="las la-baby"></i></div>
        <h4 class="tn-empty-title">{{ __('No Products Found') }}</h4>
        <p class="tn-empty-text">{{ __('Try adjusting your filters.') }}</p>
    </div>
@else
<div class="row g-4">
    @foreach($products as $product)
    @php
        $data     = theme_product_price($product);
        $img_url  = theme_product_image($product->image_id ?? null, 'grid');
        $badge         = $product->badge?->name ?? null;
        $discount      = $data['discount'];
        $campaign_name = $data['campaign_name'];
        $url           = theme_product_url($product->slug);
    @endphp
    <div class="col-6 col-md-4">
        <div class="tn-card h-100 d-flex flex-column">
            @if($discount)
                <span class="tn-card-badge">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="tn-card-badge">{{ $badge }}</span>
            @elseif($campaign_name)
                <span class="tn-card-badge">{{ $campaign_name }}</span>
            @endif
            <div class="tn-card-img">
                <a href="{{ $url }}">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy" class="tn-product-img">
                    @else
                        <div class="tn-product-placeholder"><i class="las la-baby"></i></div>
                    @endif
                </a>
                <div class="tn-card-actions">
                    <button class="tn-action-btn add-to-cart-btn" data-product_id="{{ $product->id }}"
                            title="{{ __('Add to Cart') }}">
                        <i class="las la-shopping-cart"></i>
                    </button>
                    <button class="tn-action-btn add-to-wishlist-btn" data-product_id="{{ $product->id }}"
                            title="{{ __('Wishlist') }}">
                        <i class="las la-heart"></i>
                    </button>
                </div>
            </div>
            <div class="tn-card-body d-flex flex-column flex-grow-1">
                <div class="tn-card-name flex-grow-1">
                    <a href="{{ $url }}" class="tn-card-name-link">
                        {{ \Illuminate\Support\Str::words($product->name, 7) }}
                    </a>
                </div>
                <div class="tn-stars mt-auto">
                    {!! theme_star_rating($product) !!}
                </div>
                <div class="tn-card-price">
                    <span class="tn-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                    @if($data['regular_price'] && $discount)
                        <span class="tn-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if(isset($links) && count($links) > 1)
<div class="tn-dp-pagination mt-4">
    @foreach($links as $page => $url)
        <button class="tn-dp-page-btn {{ $page == ($current_page ?? 1) ? 'active' : '' }}"
                onclick="window.tnFilterRequest({{ $page }})">
            {{ $page }}
        </button>
    @endforeach
</div>
@endif
@endif
