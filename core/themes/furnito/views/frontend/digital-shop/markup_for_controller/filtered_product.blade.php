@foreach($product_object as $product)
    @php
        $dp      = get_digital_product_dynamic_price($product);
        $sale    = $dp['sale_price'];
        $orig    = $dp['regular_price'];
        $discount = $dp['discount'];
        $price   = $sale > 0 ? $sale : $orig;
        $img     = get_attachment_image_by_id($product->image_id ?? null, 'grid');
        $img_url = $img['img_url'] ?? null;
        $badge   = $product->additionalFields?->badge?->name ?? null;
        $author  = $product->additionalFields?->author?->name ?? null;
        $url     = dynamicRoute($product->slug);
    @endphp

    <div class="col-6 col-md-4 col-xl-3">
        <div class="fn-card">
            <div class="fn-card-img">
                <a href="{{ $url }}">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:56px;color:#3D8870;opacity:.25;">
                            <i class="las la-file-alt"></i>
                        </div>
                    @endif
                </a>

                @if($discount > 0)
                    <span class="fn-card-badge">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="fn-card-badge">{{ $badge }}</span>
                @endif
            </div>

            <div class="fn-card-body">
                @if($author)
                    <div class="fn-card-cat">{{ $author }}</div>
                @endif
                <div class="fn-card-name">
                    <a href="{{ $url }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                </div>
                <div class="fn-card-price">
                    <span class="fn-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                    @if($discount > 0 && $orig)
                        <span class="fn-price-orig">{{ amount_with_currency_symbol($orig) }}</span>
                    @endif
                </div>
                <a href="{{ $url }}" class="fn-card-atc">
                    <i class="las la-eye"></i> {{ __('View Product') }}
                </a>
            </div>
        </div>
    </div>
@endforeach

{{ $product_object->links() }}
