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
        $cat_name      = $product->category?->name ?? null;
    @endphp

    <div class="col">
        <div class="pf-product-card h-100 d-flex flex-column">

            {{-- Badge --}}
            @if($discount)
                <div class="pf-product-badge pf-badge-sale">{{ $discount }}% {{ __('OFF') }}</div>
            @elseif($badge)
                <div class="pf-product-badge pf-badge-new">{{ $badge }}</div>
            @elseif($campaign_name)
                <div class="pf-product-badge pf-badge-rx">{{ $campaign_name }}</div>
            @endif

            {{-- Image --}}
            <a href="{{ $product_url }}" class="pf-product-img">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                @else
                    <i class="las la-pills" style="font-size:56px;color:var(--pf-teal);"></i>
                @endif
            </a>

            {{-- Body --}}
            <div class="pf-product-body">
                @if($cat_name)
                    <div class="pf-product-brand">{{ $cat_name }}</div>
                @endif
                <div class="pf-product-name">
                    <a href="{{ $product_url }}">{{ \Illuminate\Support\Str::words($product->name, 8) }}</a>
                </div>
                <div class="pf-product-meta">
                    {!! theme_star_rating($product) !!}
                </div>
                <div class="pf-product-footer">
                    <div>
                        <span class="pf-price-current">{{ amount_with_currency_symbol($sale_price) }}</span>
                        @if($regular_price)
                            <span class="pf-price-was">{{ amount_with_currency_symbol($regular_price) }}</span>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <button class="pf-wish-btn add-to-wishlist-btn"
                                data-product_id="{{ $product->id }}"
                                title="{{ __('Wishlist') }}">
                            <i class="las la-heart"></i>
                        </button>
                        <button class="pf-add-btn add-to-cart-btn"
                                data-product_id="{{ $product->id }}"
                                title="{{ __('Add to Cart') }}">
                            <i class="las la-cart-plus"></i>
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
    <div class="pf-pagination" style="justify-content:center;margin-top:28px;">
        @foreach($links as $page => $url)
            <button class="pf-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    onclick="pfFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
