{{-- LuxeGems: Product list partial (AJAX list view) --}}
@foreach($products as $product)
@php
    $data          = theme_product_price($product);
    $sale_price    = $data['sale_price'];
    $reg_price     = $data['regular_price'];
    $discount      = $data['discount'];
    $campaign_name = $data['campaign_name'];
    $img_url       = theme_product_image($product->image_id ?? null, 'grid');
    $product_url   = theme_product_url($product->slug);
@endphp
<div class="col-12">
    <div class="lx-card" style="display:flex;flex-direction:row;overflow:hidden;">
        <div style="width:120px;min-width:120px;aspect-ratio:1;background:var(--lx-surface);overflow:hidden;">
            @if($img_url)
                <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"
                     style="width:100%;height:100%;object-fit:cover;">
            @else
                <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;color:var(--lx-gold);">
                    <i class="las la-gem"></i>
                </div>
            @endif
        </div>
        <div style="flex:1;padding:16px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div>
                <div class="lx-card-material">{{ $product->category?->name ?? '' }}</div>
                <div class="lx-card-name">
                    <a href="{{ $product_url }}" style="color:inherit;text-decoration:none;">{{ $product->name }}</a>
                </div>

            </div>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <div>
                    <span class="lx-price">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($discount > 0)
                        <span class="lx-price-old ms-2">{{ amount_with_currency_symbol($reg_price) }}</span>
                        <span style="font-size:11px;font-weight:700;background:var(--lx-gold);color:#000;padding:2px 8px;border-radius:4px;margin-left:6px;">{{ $discount }}% {{ __('off') }}</span>
                    @elseif($campaign_name)
                        <span style="font-size:11px;font-weight:700;background:var(--lx-gold);color:#000;padding:2px 8px;border-radius:4px;margin-left:6px;">{{ $campaign_name }}</span>
                    @endif
                </div>
                <button class="lx-card-act-btn add-to-cart-btn" data-product_id="{{ $product->id }}"
                        style="padding:10px 20px;font-size:11px;">
                    <i class="las la-plus"></i> {{ __('Add to Cart') }}
                </button>
                <button class="lx-card-act-btn outline add-to-wishlist-btn" data-product_id="{{ $product->id }}"
                        style="padding:10px 20px;font-size:11px;">
                    <i class="las la-heart"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12 mt-3">
    <div class="lg-pagination">
        @foreach($links as $page => $url)
            <button class="lg-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="luxegemsFilterRequest({{ $page }})">{{ $page }}</button>
        @endforeach
    </div>
</div>
@endif
