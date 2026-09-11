{{-- DriveKit: product options (variants, qty, ATC) --}}

{{-- Size variants --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
<div class="value-input-area mb-3">
    <label class="dk-form-label">{{ __('Size') }}</label>
    <input type="hidden" id="selected_size">
    <ul class="dk-size-list size-lists" data-type="Size">
        @foreach($productSizes as $size)
            @if(!empty($size))
            <li class="list" data-value="{{ $size->id }}" data-display-value="{{ $size->name }}">
                {{ $size->size_code }}
            </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Color variants --}}
@if($productColors->count() > 0 && current(current($productColors)))
<div class="value-input-area mb-3">
    <label class="dk-form-label">{{ __('Color') }}</label>
    <input type="hidden" id="selected_color">
    <ul class="dk-color-list size-lists color-list" data-type="Color">
        @foreach($productColors as $color)
            @if(!empty($color))
            <li style="background-color:{{ $color->color_code }};"
                data-value="{{ $color->id }}"
                data-display-value="{{ $color->name }}"
                title="{{ $color->name }}">
            </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Custom attributes --}}
@foreach($available_attributes as $attribute => $options)
<div class="value-input-area mb-3">
    <label class="dk-form-label">{{ $attribute }}</label>
    <ul class="dk-size-list size-lists" data-type="{{ $attribute }}">
        @foreach($options as $option)
        <li class="list" data-value="{{ $option }}" data-display-value="{{ $option }}">{{ $option }}</li>
        @endforeach
    </ul>
</div>
@endforeach

{{-- Quantity --}}
<div class="mb-3">
    <label class="dk-form-label">{{ __('Quantity') }}</label>
    <div class="dk-qty-wrap">
        <button type="button" class="dk-qty-btn substract"><i class="mdi mdi-minus"></i></button>
        <input type="number" id="quantity" class="dk-qty-input quantity-input" value="1" min="1">
        <button type="button" class="dk-qty-btn plus"><i class="mdi mdi-plus"></i></button>
    </div>
</div>

{{-- ATC + Buy Now --}}
<div class="d-flex flex-column gap-2 mb-3">
    <button type="button" class="dk-btn dk-btn-red dk-btn-block add_to_cart_single_page">
        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
    </button>
    <button type="button" class="dk-btn dk-btn-ghost dk-btn-block but_now_single_page">
        <i class="mdi mdi-lightning-bolt"></i> {{ __('Buy Now') }}
    </button>
</div>

{{-- Delivery Options --}}
@if($product->product_delivery_option != null && $product->product_delivery_option->count())
<div class="dk-delivery-wrap mb-3">
    @foreach($product->product_delivery_option as $option)
    <div class="dk-delivery-item">
        <span class="dk-delivery-icon"><i class="{{ $option->icon }}"></i></span>
        <div>
            <strong class="dk-delivery-title">{{ $option->title }}</strong>
            <span class="dk-delivery-sub">{{ $option->sub_title }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Action links --}}
<div class="dk-action-links">
    <button class="dk-action-link add_to_wishlist_single_page">
        <i class="mdi mdi-heart-outline"></i> {{ __('Add to Wishlist') }}
    </button>
    <button class="dk-action-link compare-btn" data-product_id="{{ $product->id }}">
        <i class="mdi mdi-compare"></i> {{ __('Compare') }}
    </button>
</div>
