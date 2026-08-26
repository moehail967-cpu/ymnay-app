{{-- TechZone: Digital product grid partial --}}
@foreach($products as $product)
    @php
        $data          = theme_product_price($product);
        $sale_price    = $data['sale_price'];
        $regular_price = $data['regular_price'];
        $discount      = $data['discount'];
        $img_url       = theme_product_image($product->image_id ?? null, 'grid');
        $product_url   = theme_product_url($product->slug);
    @endphp

    <div class="col-6 col-md-4">
        <div class="tz-card h-100 d-flex flex-column">
            <div class="tz-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @else
                    <a href="{{ $product_url }}" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;font-size:48px;color:var(--tz-blue);">
                        <i class="las la-compact-disc"></i>
                    </a>
                @endif
                @if($discount)
                    <span class="tz-card-badge">{{ $discount }}% {{ __('off') }}</span>
                @endif
                <button class="tz-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                    <i class="las la-heart"></i>
                </button>
            </div>

            <div class="tz-card-body d-flex flex-column flex-grow-1">
                <div class="tz-card-name flex-grow-1">
                    <a href="{{ $product_url }}">{{ \Illuminate\Support\Str::words($product->name, 8) }}</a>
                </div>
                <div class="tz-stars mt-auto">
                    {!! theme_star_rating($product) !!}
                </div>
                <div class="tz-card-price">
                    <span class="tz-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span class="tz-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <button type="button" class="tz-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                    <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                </button>
            </div>
        </div>
    </div>
@endforeach

@if(isset($links) && count($links) > 1)
<div class="col-12">
    <div class="tz-pagination">
        @foreach($links as $page => $url)
            <button class="tz-page-btn {{ $page == ($current_page ?? 1) ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="tzDpFilter({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
