@foreach($products as $product)
    @php
        $data          = get_digital_product_dynamic_price($product);
        $sale_price    = $data['sale_price']    ?? $data['price']    ?? 0;
        $regular_price = $data['regular_price'] ?? $data['old_price'] ?? null;
        $discount      = $data['discount']      ?? null;
        $img_url       = theme_product_image($product->image_id ?? null, 'grid');
        $product_url   = theme_product_url($product->slug);
        $cat_name      = $product->category?->name ?? null;
    @endphp

    <div class="col">
        <div class="pf-product-card h-100 d-flex flex-column">

            {{-- Badge --}}
            @if($discount)
                <div class="pf-product-badge pf-badge-sale">{{ $discount }}% {{ __('OFF') }}</div>
            @endif

            {{-- Image --}}
            <a href="{{ $product_url }}" class="pf-product-img">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                @else
                    <i class="las la-file-download" style="font-size:56px;color:var(--pf-teal);"></i>
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
                    <a href="{{ $product_url }}" class="pf-add-btn" title="{{ __('View Product') }}">
                        <i class="las la-eye"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach

@if(count($links) > 1)
<div class="col-12">
    <div class="pf-pagination" style="justify-content:center;margin-top:28px;">
        @foreach($links as $page => $url)
            <button class="pf-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    onclick="pfDpFilter({{ $page }})">{{ $page }}</button>
        @endforeach
    </div>
</div>
@endif
