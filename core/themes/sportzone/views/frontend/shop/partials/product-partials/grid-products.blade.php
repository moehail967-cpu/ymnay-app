{{-- SportZone: Product grid partial --}}
@foreach($products as $product)
    @php
        $data          = theme_product_price($product);
        $regular_price = $data['regular_price'];
        $sale_price    = $data['sale_price'];
        $discount      = $data['discount'];
        $campaign_name = $data['campaign_name'];
        $img_url       = theme_product_image($product->image_id ?? null, 'grid');
        $badge         = $product->badge?->name ?? null;
        $product_url   = theme_product_url($product->slug);
    @endphp

    <div class="col-6 col-md-4">
        <div class="sz-card h-100 d-flex flex-column">
            <div class="sz-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @else
                    <a href="{{ $product_url }}" class="sz-img-ph">
                        <i class="mdi mdi-dumbbell" style="font-size:48px;color:var(--sz-red);opacity:.35;"></i>
                    </a>
                @endif

                @if($discount)
                    <span class="sz-card-badge sz-badge-sale">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="sz-card-badge sz-badge-hot">{{ $badge }}</span>
                @elseif($campaign_name)
                    <span class="sz-card-badge" style="background:var(--sz-navy);">{{ $campaign_name }}</span>
                @endif

                <button class="sz-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}" style="position:absolute;top:10px;right:10px;width:32px;height:32px;background:rgba(255,255,255,.9);border:none;border-radius:var(--sz-radius);cursor:pointer;font-size:16px;color:var(--sz-muted);transition:all .2s;">
                    <i class="las la-heart"></i>
                </button>
            </div>

            <div class="sz-card-body d-flex flex-column flex-grow-1">
                @if($product->category)
                    <div class="sz-card-cat">{{ $product->category->name }}</div>
                @endif
                <div class="sz-card-name flex-grow-1">
                    <a href="{{ $product_url }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                </div>
                <div class="sz-stars mt-auto">{!! theme_star_rating($product) !!}</div>
                <div class="sz-card-price">
                    <span class="sz-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span class="sz-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <button class="sz-card-atc sz-atc-btn add-to-cart-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Add to cart') }}">
                    <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                </button>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div style="display:flex;gap:6px;justify-content:center;margin-top:16px;flex-wrap:wrap;">
        @foreach($links as $page => $url)
            <button class="sz-btn sz-btn-sm {{ $page == $current_page ? 'sz-btn-red' : 'sz-btn-outline' }}"
                    style="min-width:36px;"
                    data-page="{{ $page }}"
                    onclick="szFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
