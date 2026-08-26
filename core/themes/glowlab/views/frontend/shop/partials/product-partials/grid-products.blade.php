{{-- GlowLab: Product grid partial --}}
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

    <div class="col-6 col-md-4 d-flex">
        <div class="gl-card w-100 h-100 d-flex flex-column">
            <div class="gl-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @else
                    <a href="{{ $product_url }}" style="font-size:56px;display:flex;align-items:center;justify-content:center;height:100%;">✨</a>
                @endif
            </div>

            @if($discount)
                <div class="gl-card-badge gl-badge-best">{{ $discount }}% {{ __('off') }}</div>
            @elseif($badge)
                <div class="gl-card-badge gl-badge-best">{{ $badge }}</div>
            @elseif($campaign_name)
                <div class="gl-card-badge" style="background:var(--gl-sage);">{{ $campaign_name }}</div>
            @endif

            <button class="gl-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                <i class="las la-heart"></i>
            </button>

            <div class="gl-card-body d-flex flex-column flex-grow-1">
                <div class="gl-card-cat">{{ $product->category?->name ?? '' }}</div>
                <a href="{{ $product_url }}" class="gl-card-name flex-grow-1">{{ \Illuminate\Support\Str::words($product->name, 8) }}</a>
                <div class="gl-stars mt-auto">{!! theme_star_rating($product) !!}</div>
                <div class="gl-card-price">
                    <span class="gl-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span class="gl-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <button class="gl-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}" data-slug="{{ $product->slug }}">
                    <i class="las la-shopping-cart"></i> {{ __('Add to Bag') }}
                </button>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div style="display:flex;justify-content:center;gap:6px;margin-top:32px;flex-wrap:wrap;">
        @foreach($links as $page => $url)
            <button onclick="glFilterRequest({{ $page }})"
                    style="width:38px;height:38px;border-radius:50%;border:1.5px solid {{ $page == $current_page ? 'var(--gl-dark)' : 'var(--gl-border)' }};background:{{ $page == $current_page ? 'var(--gl-dark)' : '#fff' }};color:{{ $page == $current_page ? '#fff' : 'var(--gl-muted)' }};font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
