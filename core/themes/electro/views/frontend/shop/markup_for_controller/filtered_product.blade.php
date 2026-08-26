@foreach($product_object as $product)
@php
    $pd       = get_product_dynamic_price($product);
    $price    = $pd['sale_price'];
    $oldPrice = $pd['regular_price'];
    $discount = $pd['discount'];
    $pImg     = theme_product_image($product->image_id ?? null, 'grid');
    $pUrl     = theme_product_url($product->slug ?? $product->id);
@endphp
<div class="el-card">
    <div class="el-card-img-wrap">
        <a href="{{ $pUrl }}">
            @if($pImg)
            <img src="{{ $pImg }}" alt="{{ $product->name }}" class="el-card-img" loading="lazy">
            @else
            <div class="el-card-img-ph"><i class="las la-laptop"></i></div>
            @endif
        </a>
        <button class="add-to-wishlist-btn el-card-wish" data-product_id="{{ $product->id }}" title="{{ __('Wishlist') }}">
            <i class="las la-heart"></i>
        </button>
        @if($discount)
        <span class="el-card-badge">{{ $discount }}% {{ __('off') }}</span>
        @endif
        @if(!empty($product->badge))
        <span class="el-card-badge el-card-badge-new" style="top:36px">{{ $product->badge->name }}</span>
        @endif
    </div>
    <div class="el-card-body">
        <a href="{{ $pUrl }}" class="el-card-name">{{ Str::limit($product->name, 45) }}</a>
        <div class="el-card-footer">
            <div class="el-card-prices">
                <span class="el-card-price">{{ amount_with_currency_symbol($price) }}</span>
                @if($oldPrice)
                <span class="el-card-old">{{ amount_with_currency_symbol($oldPrice) }}</span>
                @endif
            </div>
            <button class="add-to-cart-btn el-card-atc" data-product_id="{{ $product->id }}" title="{{ __('Add to Cart') }}">
                <i class="las la-plus"></i>
            </button>
        </div>
    </div>
</div>
@endforeach

{{ $product_object->links() }}
