{{-- FitPeak: Product list partial — used for AJAX filter list view response --}}
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
        <div class="fp-card" style="display:flex;flex-direction:row;gap:0;align-items:stretch;">
            <div style="width:160px;flex-shrink:0;background:var(--fp-mid);position:relative;">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                    </a>
                @else
                    <a href="{{ $product_url }}" style="display:flex;align-items:center;justify-content:center;height:100%;font-size:48px;">
                        <i class="mdi mdi-dumbbell" style="color:var(--fp-green);opacity:.3;"></i>
                    </a>
                @endif
                @if($discount)
                    <span class="fp-card-badge">{{ $discount }}% {{ __('off') }}</span>
                @elseif($campaign_name)
                    <span class="fp-card-badge">{{ $campaign_name }}</span>
                @endif
            </div>
            <div class="fp-card-body" style="flex:1;display:flex;flex-direction:column;justify-content:space-between;gap:10px;">
                <div>
                    <div class="fp-card-title" style="font-size:18px;">
                        <a href="{{ $product_url }}">{{ $product->name }}</a>
                    </div>
                    <div class="fp-card-meta" style="margin-top:4px;">
                        {!! theme_star_rating($product) !!}
                    </div>
                    @if($product->short_description)
                        <p style="font-size:13px;color:var(--fp-muted);margin-top:8px;line-height:1.5;">{{ \Illuminate\Support\Str::limit(strip_tags($product->short_description), 120) }}</p>
                    @endif
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div>
                        <div class="fp-price">{{ amount_with_currency_symbol($sale_price) }}</div>
                        @if($reg_price)
                            <div class="fp-price-old">{{ amount_with_currency_symbol($reg_price) }}</div>
                        @endif
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button class="fp-atc-btn add-to-cart-btn fp-btn-sm"
                                data-product_id="{{ $product->id }}"
                                aria-label="{{ __('Add to cart') }}">
                            <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                        <button class="fp-card-wishlist add-to-wishlist-btn"
                                data-product_id="{{ $product->id }}"
                                style="position:static;width:36px;height:36px;"
                                aria-label="{{ __('Wishlist') }}">
                            <i class="mdi mdi-heart-outline"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="fp-pagination">
        @foreach($links as $page => $url)
            <button class="fp-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="fpFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
