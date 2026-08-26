{{-- Pharmacy: Product list partial — horizontal card layout (AJAX list view) --}}
@foreach($products as $product)
    @php
        $data          = theme_product_price($product);
        $regular_price = $data['regular_price'];
        $sale_price    = $data['sale_price'];
        $discount      = $data['discount'];
        $campaign_name = $data['campaign_name'];
        $img_url       = theme_product_image($product->image_id ?? null, 'grid');
        $product_url   = theme_product_url($product->slug);
    @endphp

    <div class="col-12">
        <div style="background:var(--pf-white);border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);overflow:hidden;display:flex;gap:0;transition:box-shadow .2s;">
            {{-- Image --}}
            <div style="width:140px;min-width:140px;height:140px;overflow:hidden;background:var(--pf-teal-light);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                @if($img_url)
                    <a href="{{ $product_url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;" loading="lazy"></a>
                @else
                    <a href="{{ $product_url }}" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
                        <i class="las la-capsules" style="font-size:42px;color:var(--pf-teal);"></i>
                    </a>
                @endif
            </div>

            {{-- Info --}}
            <div style="padding:16px 20px;flex:1;display:flex;flex-direction:column;justify-content:space-between;gap:8px;">
                <div>
                    <div style="font-size:15px;font-weight:700;color:var(--pf-dark);margin-bottom:4px;">
                        <a href="{{ $product_url }}" style="color:inherit;text-decoration:none;">{{ $product->name }}</a>
                    </div>
                    <div style="margin-bottom:6px;">{!! theme_star_rating($product) !!}</div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:17px;font-weight:800;color:var(--pf-teal);">{{ amount_with_currency_symbol($sale_price) }}</span>
                        @if($regular_price)
                            <span style="font-size:13px;color:var(--pf-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        @endif
                        @if($discount)
                            <span style="font-size:11px;background:var(--pf-teal-light);color:var(--pf-teal);padding:2px 8px;border-radius:20px;font-weight:700;">{{ $discount }}% {{ __('off') }}</span>
                        @elseif($campaign_name)
                            <span style="font-size:11px;background:var(--pf-teal-light);color:var(--pf-teal);padding:2px 8px;border-radius:20px;font-weight:700;">{{ $campaign_name }}</span>
                        @endif
                    </div>
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button class="add-to-cart-btn pf-btn pf-btn-teal pf-btn-sm"
                            data-product_id="{{ $product->id }}">
                        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                    </button>
                    <button class="add-to-wishlist-btn pf-btn pf-btn-outline pf-btn-sm"
                            data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                        <i class="las la-heart"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="pf-pagination">
        @foreach($links as $page => $url)
            <button class="page-link {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="pfFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
