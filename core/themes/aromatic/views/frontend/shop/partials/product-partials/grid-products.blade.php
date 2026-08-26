@foreach($products as $product)
@php
    $data       = theme_product_price($product);
    $img_url    = theme_product_image($product->image_id ?? null, 'grid');
    $badge      = $product->badge?->name ?? null;
    $discount   = $data['discount'];
    $camp_name  = $data['campaign_name'] ?? null;
    $url        = theme_product_url($product->slug);
@endphp
<div class="col-6 col-md-4 col-lg-4">
    <div class="ar-card h-100 d-flex flex-column">
        <div class="ar-card-img">
            @if($img_url)
                <a href="{{ $url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"></a>
            @else
                <a href="{{ $url }}" class="ar-card-placeholder"><i class="las la-flask"></i></a>
            @endif

            @if($discount)
                <span class="ar-card-badge">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="ar-card-badge">{{ $badge }}</span>
            @elseif($camp_name)
                <span class="ar-card-badge">{{ $camp_name }}</span>
            @endif

            <button class="ar-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}">
                <i class="mdi mdi-heart-outline"></i>
            </button>
        </div>
        <div class="ar-card-body d-flex flex-column flex-grow-1">
            <div class="ar-card-cat">{{ $product->category?->name ?? '' }}</div>
            <div class="ar-card-name flex-grow-1">
                <a href="{{ $url }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
            </div>
            <div class="ar-stars mt-auto">{!! theme_star_rating($product) !!}</div>
            <div class="ar-card-price">
                <span class="ar-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                @if(!empty($data['regular_price']))
                    <span class="ar-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                @endif
            </div>
            <a href="{{ $url }}" class="ar-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
            </a>
        </div>
    </div>
</div>
@endforeach

@if(isset($links) && count($links) > 1)
<div class="col-12">
    <div class="ar-pagination">
        @foreach($links as $page => $url)
            <button class="ar-page-btn {{ $page == ($current_page ?? 1) ? 'active' : '' }}"
                    onclick="arFilterRequest({{ $page }})">{{ $page }}</button>
        @endforeach
    </div>
</div>
@endif
