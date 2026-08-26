{{-- BakerCo: list view — rendered same as grid (no separate list design) --}}
@foreach($products as $product)
@php
    $data          = theme_product_price($product);
    $discount      = $data['discount'];
    $campaign_name = $data['campaign_name'];
    $badge         = $product->badge?->name ?? null;
    $img           = get_attachment_image_by_id($product->image_id ?? null, 'grid');
    $img_url       = $img['img_url'] ?? null;
@endphp
<div class="col-12">
    <div class="bk-card" style="flex-direction:row;height:auto;">
        <div class="bk-card-img" style="width:120px;flex-shrink:0;height:120px;position:relative;">
            @if($img_url)
                <a href="{{ theme_product_url($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                </a>
            @else
                <a href="{{ theme_product_url($product->slug) }}" class="bk-placeholder" style="height:120px;font-size:36px;">
                    <i class="mdi mdi-hanger"></i>
                </a>
            @endif
            @if($discount)
                <span class="bk-card-badge">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="bk-card-badge">{{ $badge }}</span>
            @elseif($campaign_name)
                <span class="bk-card-badge">{{ $campaign_name }}</span>
            @endif
        </div>
        <div class="bk-card-body" style="padding:16px;">
            <div class="bk-card-name mb-1">
                <a href="{{ theme_product_url($product->slug) }}" style="color:inherit;">{{ $product->name }}</a>
            </div>
            <div class="bk-card-price mb-2">
                <span class="bk-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                @if($data['discount'] > 0)
                    <span class="bk-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                @endif
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <button class="bk-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}" style="display:inline-flex;width:auto;padding:8px 18px;" aria-label="{{ __('Add to Cart') }}">
                    <i class="mdi mdi-cart-outline"></i> {{ __('Add to Cart') }}
                </button>
                <button class="add-to-wishlist-btn" data-product_id="{{ $product->id }}" style="width:36px;height:36px;border:1px solid var(--bk-border,#e5e5e5);border-radius:4px;background:#fff;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;" aria-label="{{ __('Wishlist') }}">
                    <i class="mdi mdi-heart-outline"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12 mt-3">
    <div class="bk-pagination">
        @foreach($links as $page => $link)
            <a href="{{ $link }}" class="bk-page-btn {{ $page === $current_page ? 'active' : '' }}"
               data-page="{{ $page }}">{{ $page }}</a>
        @endforeach
    </div>
</div>
@endif
