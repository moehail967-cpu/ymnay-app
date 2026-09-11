@php
    $img_data = get_attachment_image_by_id($product->image);
    $img_url  = !empty($img_data) ? $img_data['img_url'] : global_asset('assets/common/img/placeholder.jpg');
    $price    = $product->sale_price ?? 0;
    $regular  = $product->regular_price ?? $price;
@endphp
<div class="{{ $col_class }}">
    <div class="xgpb-product-card card h-100 border-0">
        <a href="{{ route('tenant.products.single-quick-view', $product->slug) }}"
           class="xgpb-product-img-wrap d-block overflow-hidden">
            <img src="{{ $img_url }}" alt="{{ $product->name }}"
                 class="card-img-top w-100 xgpb-product-img" loading="lazy">
        </a>
        <div class="card-body p-3">
            <a href="{{ route('tenant.products.single-quick-view', $product->slug) }}"
               class="xgpb-product-name d-block mb-2">{{ $product->name }}</a>
            <div class="xgpb-product-price d-flex align-items-center gap-2">
                <span class="xgpb-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                @if($regular > $price)
                    <span class="xgpb-price-regular text-decoration-line-through text-muted small">
                        {{ amount_with_currency_symbol($regular) }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
