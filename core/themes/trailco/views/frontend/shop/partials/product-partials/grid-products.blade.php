{{-- TrailCo: Product grid partial --}}
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
        <div class="tc-card h-100 d-flex flex-column">
            <div class="tc-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @else
                    <a href="{{ $product_url }}" class="tc-card-placeholder">
                        <i class="las la-tree"></i>
                    </a>
                @endif

                @if($discount)
                    <span class="tc-card-badge">-{{ $discount }}%</span>
                @elseif($campaign_name)
                    <span class="tc-card-badge" style="background:var(--tc-olive-deep);">{{ $campaign_name }}</span>
                @elseif($badge)
                    <span class="tc-card-tag">{{ $badge }}</span>
                @endif
            </div>

            <button class="tc-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}"
                    aria-label="{{ __('Wishlist') }}">
                <i class="las la-heart"></i>
            </button>

            <div class="tc-card-body d-flex flex-column flex-grow-1">
                @if($product->category)
                    <div class="tc-card-cat">{{ $product->category->name }}</div>
                @endif
                <div class="tc-card-name flex-grow-1">
                    <a href="{{ $product_url }}" class="tc-card-name-link">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                </div>
                <div class="tc-card-price mt-auto">
                    <span class="tc-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span class="tc-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <div class="tc-card-actions">
                    <button class="tc-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Add to cart') }}">
                        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if(count($links) > 1)
<div class="col-12">
    <div style="display:flex;gap:6px;justify-content:center;margin-top:16px;flex-wrap:wrap;">
        @foreach($links as $page => $url)
            <button style="padding:7px 14px;border:1.5px solid {{ $page == $current_page ? 'var(--tc-olive)' : 'var(--tc-border)' }};background:{{ $page == $current_page ? 'var(--tc-olive)' : '#fff' }};color:{{ $page == $current_page ? '#fff' : 'var(--tc-dark)' }};border-radius:var(--tc-radius);font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;"
                    data-page="{{ $page }}"
                    onclick="tcFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
