{{-- PawHaus: Digital product grid partial --}}
@foreach($products as $product)
    @php
        $data      = theme_product_price($product);
        $sale_price = $data['sale_price'];
        $regular_price = $data['regular_price'];
        $discount  = $data['discount'];
        $img_url   = theme_product_image($product->image_id ?? null, 'grid');
        $product_url = theme_product_url($product->slug);
    @endphp
    <div class="col-6 col-md-4">
        <div class="ph-card h-100 d-flex flex-column">
            <div class="ph-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"></a>
                @else
                    <a href="{{ $product_url }}" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
                        <i class="las la-file-download" style="font-size:56px;color:var(--ph-terra);"></i>
                    </a>
                @endif
                @if($discount)
                    <span class="ph-card-badge">{{ $discount }}% {{ __('off') }}</span>
                @endif
            </div>
            <div class="ph-card-body d-flex flex-column flex-grow-1">
                <div class="ph-card-name flex-grow-1">
                    <a href="{{ $product_url }}">{{ \Illuminate\Support\Str::words($product->name, 8) }}</a>
                </div>
                <div class="ph-stars mt-auto">{!! theme_star_rating($product) !!}</div>
                <div class="ph-card-price">
                    <span class="ph-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price)
                        <span class="ph-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>
                <a href="{{ $product_url }}" class="ph-card-atc">
                    <i class="las la-download"></i> {{ __('Get Now') }}
                </a>
            </div>
        </div>
    </div>
@endforeach

@if(count($links) > 1)
<div class="col-12">
    <div class="ph-pagination">
        @foreach($links as $page => $url)
            <button class="ph-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    onclick="phDpFilter({{ $page }})">{{ $page }}</button>
        @endforeach
    </div>
</div>
@endif
