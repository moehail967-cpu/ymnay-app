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

    <div class="col-12">
        <div class="dk-card dk-card-list d-flex align-items-center gap-3">
            <div class="dk-card-list-img flex-shrink-0">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy" style="width:110px;height:110px;object-fit:cover;border-radius:var(--dk-radius);">
                    </a>
                @else
                    <a href="{{ $product_url }}" class="dk-card-no-img" style="width:110px;height:110px;font-size:32px;">
                        <i class="mdi mdi-package-variant-closed"></i>
                    </a>
                @endif
            </div>
            <div class="dk-card-body flex-grow-1" style="padding:0;">
                @if($product->category)
                    <div class="dk-card-cat">{{ $product->category->name }}</div>
                @endif
                <a href="{{ $product_url }}" class="dk-card-name">{{ \Illuminate\Support\Str::words($product->name, 10) }}</a>
                <div class="dk-stars">{!! theme_star_rating($product) !!}</div>
                <div class="d-flex align-items-center gap-3 flex-wrap mt-1">
                    <div class="dk-card-price" style="margin:0;">
                        <span class="dk-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                        @if($regular_price)
                            <span class="dk-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                        @endif
                        @if($discount)
                            <span class="dk-card-badge dk-badge-sale" style="position:static;display:inline-block;">{{ $discount }}% {{ __('off') }}</span>
                        @endif
                    </div>
                    <button class="dk-card-atc dk-atc-btn add-to-cart-btn" data-product_id="{{ $product->id }}">
                        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                    </button>
                    <button class="dk-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" style="position:static;" aria-label="{{ __('Wishlist') }}">
                        <i class="mdi mdi-heart-outline"></i>
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
