@foreach($product_object as $product)
@php
    $data          = get_product_dynamic_price($product);
    $campaign_name = $data['campaign_name'];
    $regular_price = $data['regular_price'];
    $sale_price    = $data['sale_price'];
    $discount      = $data['discount'];
    $hasDiscount   = $discount !== null;
    $pImg = null;
    if (!empty($product->image_id)) {
        $pd   = get_attachment_image_by_id($product->image_id, 'grid');
        $pImg = !empty($pd) ? $pd['img_url'] : null;
    }
    $pUrl = theme_product_url($product->slug);
@endphp

<div class="col-sm-12 mt-4">
    <div class="cs-list-card">
        <a href="{{ $pUrl }}" class="cs-list-card-thumb">
            @if($pImg)
                <img src="{{ $pImg }}" alt="{{ $product->name }}" loading="lazy">
            @else
                <div class="casual-new-thumb-placeholder"><i class="las la-book"></i></div>
            @endif
        </a>
        <div class="cs-list-card-body">
            @if($hasDiscount || !empty($product->badge) || !is_null($campaign_name))
            <div class="cs-product-badges cs-list-card-badges">
                @if($hasDiscount)
                    <span class="cs-product-badge cs-product-badge-sale">{{ $discount }}% {{ __('off') }}</span>
                @endif
                @if(!empty($product->badge))
                    <span class="cs-product-badge cs-product-badge-new">{{ $product->badge->name }}</span>
                @endif
                @if(!is_null($campaign_name))
                    <span class="cs-product-badge cs-product-badge-new">{{ $campaign_name }}</span>
                @endif
            </div>
            @endif

            <h5 class="cs-list-card-title">
                <a href="{{ $pUrl }}">{{ Str::words($product->name, 10) }}</a>
            </h5>

            @if($product->summary)
            <p class="cs-list-card-desc">{{ Str::words($product->summary, 20) }}</p>
            @endif

            <div class="cs-list-card-price">
                <span class="cs-list-card-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($hasDiscount && $regular_price)
                    <span class="cs-list-card-regular">{{ amount_with_currency_symbol($regular_price) }}</span>
                @endif
            </div>

            <div class="cs-list-card-actions">
                <a href="javascript:void(0)" class="cs-list-card-btn add-to-cart-btn cart-loading"
                   data-product_id="{{ $product->id }}">
                    <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                </a>
                <a href="javascript:void(0)" class="cs-list-card-icon add-to-wishlist-btn cart-loading"
                   data-product_id="{{ $product->id }}" title="{{ __('Add to Wishlist') }}">
                    <i class="lar la-heart"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

{{ $product_object->links() }}
