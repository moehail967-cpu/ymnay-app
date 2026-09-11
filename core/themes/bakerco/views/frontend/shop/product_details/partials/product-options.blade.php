{{--
    BakerCo product-options partial.
    Title / price / stock are already rendered in product-details.blade.php.
    This partial only renders the interactive form elements so JS hooks work correctly.
--}}

{{-- Campaign notice (not yet started / expired) --}}
@if($campaign_product !== null && $campaign_product->status !== 'draft')
    @if(isset($campaign_active) && $campaign_active)
        <div class="global-timer mb-4"></div>
    @else
        <div class="alert mb-4" style="background:#FEFCE8;border:1px solid #FEF08A;color:#713F12;border-radius:var(--bk-radius);padding:14px 18px;">
            {{ __('The Campaign Is Over Or Not Yet Started') }}
        </div>
    @endif
@endif

{{-- Sizes --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
    <div class="mb-4">
        <span class="input-title" style="font-size:13px;font-weight:700;color:var(--bk-dark);display:block;margin-bottom:8px;">
            {{ __('Size:') }}
            <input readonly class="form--input value-size" name="size" type="text" value="" style="display:none;">
            <input type="hidden" id="selected_size">
        </span>
        <ul class="size-lists" data-type="Size">
            @foreach($productSizes as $product_size)
                @if(!empty($product_size))
                    <li class="list"
                        data-value="{{ optional($product_size)->id }}"
                        data-display-value="{{ optional($product_size)->name }}">
                        {{ optional($product_size)->size_code }}
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endif

{{-- Colors --}}
@if($productColors->count() > 0 && current(current($productColors)))
    <div class="mb-4">
        <span class="input-title" style="font-size:13px;font-weight:700;color:var(--bk-dark);display:block;margin-bottom:8px;">
            {{ __('Color:') }}
            <input readonly class="form--input value-size" name="color" type="text" value="" style="display:none;">
            <input type="hidden" id="selected_color">
        </span>
        <ul class="size-lists color-list" data-type="Color">
            @foreach($productColors as $product_color)
                @if(!empty($product_color))
                    <li style="background-color:{{ $product_color->color_code }}"
                        data-value="{{ optional($product_color)->id }}"
                        data-display-value="{{ optional($product_color)->name }}"></li>
                @endif
            @endforeach
        </ul>
    </div>
@endif

{{-- Custom attributes --}}
@foreach($available_attributes as $attribute => $options)
    <div class="mb-4">
        <span class="input-title" style="font-size:13px;font-weight:700;color:var(--bk-dark);display:block;margin-bottom:8px;">
            {{ $attribute }}
            <input readonly class="form--input value-size" type="text" value="" style="display:none;">
            <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
        </span>
        <ul class="size-lists" data-type="{{ $attribute }}">
            @foreach($options as $option)
                <li class="list" data-value="{{ $option }}" data-display-value="{{ $option }}">{{ $option }}</li>
            @endforeach
        </ul>
    </div>
@endforeach

{{-- Quantity + Add to Cart / Buy Now --}}
<div class="mb-4">
    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
        <span style="font-size:13px;font-weight:700;color:var(--bk-dark);">{{ __('Quantity:') }}</span>
        <div class="bk-qty">
            <button type="button" class="substract">−</button>
            <input type="number" class="quantity-input qty_" id="quantity" name="quantity" value="1" min="1">
            <button type="button" class="plus">+</button>
        </div>
        @php
            $text_color = $product?->inventory?->stock_count > 0 ? 'color:#10B981;' : 'color:#EF4444;';
            $qty_text   = $product?->inventory?->stock_count > 0
                ? __('Only!').' '.$product->inventory->stock_count.' '.__('Item Left')
                : __('No Item Left!');
        @endphp
        <span style="font-size:13px;font-weight:600;{{ $text_color }}" id="item_left" data-stock-text="{{ $qty_text }}">
            {{ $qty_text }}
        </span>
    </div>

    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="javascript:void(0)"
           class="add_to_cart_single_page cart-loading bk-btn bk-btn-rose"
           style="flex:1;min-width:160px;text-align:center;">
            <i class="mdi mdi-cart-outline"></i> {{ __('Add to Cart') }}
        </a>
        <a href="javascript:void(0)"
           class="but_now_single_page cart-loading bk-btn bk-btn-outline"
           style="flex:1;min-width:160px;text-align:center;">
            {{ __('Buy Now') }}
        </a>
    </div>
</div>

{{-- Delivery Options --}}
@if($product->product_delivery_option != null && $product->product_delivery_option->count())
<div class="bk-delivery-wrap mt-3">
    @foreach($product->product_delivery_option as $option)
    <div class="bk-delivery-item">
        <span class="bk-delivery-icon"><i class="{{ $option->icon }}"></i></span>
        <div>
            <strong class="bk-delivery-title">{{ $option->title }}</strong>
            <span class="bk-delivery-sub">{{ $option->sub_title }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Wishlist / Compare --}}
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:4px;">
    <button class="bk-wishlist-link add_to_wishlist_single_page">
        <i class="mdi mdi-heart-outline"></i> {{ __('Add to Wishlist') }}
    </button>
    <button class="bk-wishlist-link compare-btn" data-product_id="{{ $product->id }}">
        <i class="mdi mdi-compare-horizontal"></i> {{ __('Compare') }}
    </button>
</div>
