{{-- DriveKit: Product grid partial --}}
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
        
        $sku      = $product->product_code ?? null;
        $summary  = $product->summary ?? null;
        $sku_line = $sku ? ('SKU: ' . $sku) : ($summary ? \Illuminate\Support\Str::limit(strip_tags($summary), 40) : null);
    @endphp

    <div class="col-6 col-md-4">
        <div class="dk-card h-100 d-flex flex-column">
            {{-- Image --}}
            <div class="dk-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"></a>
                @else
                    <a href="{{ $product_url }}" class="dk-card-no-img"><i class="las la-wrench"></i></a>
                @endif
                {{-- Left badge: discount > badge name > nothing --}}
                @if($discount)
                    <span class="dk-card-badge dk-badge-sale">{{ __('Sale') }}</span>
                @elseif($badge)
                    <span class="dk-card-badge dk-badge-hot">{{ $badge }}</span>
                @elseif($product->category)
                    <span class="dk-card-badge dk-badge-sale">{{ $product->category->name }}</span>
                @endif
            </div>
            {{-- Body --}}
            <div class="dk-card-body d-flex flex-column flex-grow-1">
                @if($product->category)
                    <div class="dk-card-cat">{{ $product->category->name }}</div>
                @endif
                <a href="{{ $product_url }}" class="dk-card-name flex-grow-1">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                @if($sku_line)
                <div class="dk-card-sku">{{ $sku_line }}</div>
                @endif
                <div class="dk-stars mt-auto">{!! theme_star_rating($product) !!}</div>
                <div class="dk-card-price">
                    <span class="dk-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span class="dk-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <div class="dk-card-actions">
                    <button class="dk-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                        <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                    </button>
                    <button class="dk-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
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
    <div class="dk-pagination">
        @foreach($links as $page => $url)
            <button onclick="dkFilterRequest({{ $page }})" class="dk-page-btn {{ $page == $current_page ? 'active' : '' }}">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
