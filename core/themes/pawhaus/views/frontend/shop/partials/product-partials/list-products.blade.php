{{-- PawHaus: Product list partial (AJAX list view) --}}
@foreach($products as $product)
    @php
        $data          = theme_product_price($product);
        $sale_price    = $data['sale_price'];
        $regular_price = $data['regular_price'];
        $discount      = $data['discount'];
        $campaign_name = $data['campaign_name'];
        $img_url       = theme_product_image($product->image_id ?? null, 'grid');
        $product_url   = theme_product_url($product->slug);
    @endphp
    <div class="col-12">
        <div style="background:var(--ph-white);border:2px solid var(--ph-border);border-radius:var(--ph-radius);padding:16px;display:flex;gap:16px;align-items:center;transition:all .25s;">
            <div style="width:90px;height:90px;border-radius:var(--ph-radius-sm);overflow:hidden;background:var(--ph-terra-light);flex-shrink:0;">
                @if($img_url)
                    <a href="{{ $product_url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;"></a>
                @else
                    <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;"><i class="las la-paw" style="color:var(--ph-terra);"></i></div>
                @endif
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:15px;font-weight:800;color:var(--ph-dark);margin-bottom:4px;">
                    <a href="{{ $product_url }}" style="color:inherit;text-decoration:none;">{{ $product->name }}</a>
                </div>
                <div style="font-size:12px;color:var(--ph-muted);margin-bottom:8px;">{!! theme_star_rating($product) !!}</div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:17px;font-weight:800;color:var(--ph-terra);">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span style="font-size:13px;color:var(--ph-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                    @if($discount)
                        <span style="font-size:11px;background:var(--ph-sage-light);color:var(--ph-sage);padding:2px 8px;border-radius:20px;font-weight:700;">{{ $discount }}% {{ __('off') }}</span>
                    @elseif($campaign_name)
                        <span style="font-size:11px;background:var(--ph-sage-light);color:var(--ph-sage);padding:2px 8px;border-radius:20px;font-weight:700;">{{ $campaign_name }}</span>
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-shrink:0;">
                <button class="add-to-wishlist-btn ph-icon-btn" data-product_id="{{ $product->id }}" title="{{ __('Wishlist') }}">
                    <i class="las la-heart"></i>
                </button>
                <button class="add-to-cart-btn ph-btn ph-btn-terra ph-btn-sm" data-product_id="{{ $product->id }}">
                    <i class="las la-cart-plus"></i> {{ __('Add') }}
                </button>
            </div>
        </div>
    </div>
@endforeach

@if(count($links) > 1)
<div class="col-12">
    <div class="ph-pagination">
        @foreach($links as $page => $url)
            <button class="ph-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="phFilterRequest({{ $page }})">{{ $page }}</button>
        @endforeach
    </div>
</div>
@endif
