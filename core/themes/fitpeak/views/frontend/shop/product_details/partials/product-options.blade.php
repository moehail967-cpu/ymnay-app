{{-- FitPeak: product-options — sizes, colors, qty, ATC/Buy Now, wishlist/compare --}}

{{-- Sizes --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
<div class="mb-4">
    <span class="input-title">{{ __('Size:') }}</span>
    <input readonly class="form--input value-size" name="size" type="text" value="">
    <input type="hidden" id="selected_size">
    <ul class="size-lists select-list" data-type="Size">
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
    <span class="input-title">{{ __('Color:') }}</span>
    <input readonly class="form--input value-size" name="color" type="text" value="">
    <input type="hidden" id="selected_color">
    <ul class="size-lists color-list" data-type="Color">
        @foreach($productColors as $product_color)
            @if(!empty($product_color))
                <li style="background-color:{{ $product_color->color_code }}"
                    data-value="{{ optional($product_color)->id }}"
                    data-display-value="{{ optional($product_color)->name }}">
                </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Custom Attributes --}}
@foreach($available_attributes as $attribute => $options)
<div class="mb-4">
    <span class="input-title">{{ $attribute }}</span>
    <input readonly class="form--input value-size" type="text" value="">
    <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
    <ul class="size-lists attribute_options_list" data-type="{{ $attribute }}">
        @foreach($options as $option)
            <li class="list" data-value="{{ $option }}" data-display-value="{{ $option }}">{{ $option }}</li>
        @endforeach
    </ul>
</div>
@endforeach

{{-- Quantity --}}
<div class="mb-4">
    <span class="input-title">{{ __('Quantity:') }}</span>
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="d-flex align-items-center" style="border:1px solid var(--fp-border);border-radius:var(--fp-radius);overflow:hidden;">
            <button type="button" class="substract" style="width:38px;height:38px;background:var(--fp-surface);border:none;color:var(--fp-text);font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <i class="mdi mdi-minus"></i>
            </button>
            <input class="quantity-input qty_" type="number" id="quantity" name="quantity" value="1" min="1"
                   style="width:52px;height:38px;text-align:center;background:var(--fp-card);border:none;color:var(--fp-text);font-weight:700;font-size:15px;">
            <button type="button" class="plus" style="width:38px;height:38px;background:var(--fp-surface);border:none;color:var(--fp-text);font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <i class="mdi mdi-plus"></i>
            </button>
        </div>
        <span class="stock-available"
              id="item_left"
              data-stock-text="{{ $product?->inventory?->stock_count > 0 ? __('Only!').' '.$product->inventory->stock_count.' '.__('Item Left') : __('No Item Left!') }}"
              style="font-size:13px;color:var(--fp-green);font-weight:700;">
            {{ $product?->inventory?->stock_count > 0 ? __('Only!').' '.$product->inventory->stock_count.' '.__('Item Left') : __('No Item Left!') }}
        </span>
    </div>
</div>

{{-- ATC + Buy Now --}}
<div class="d-flex gap-3 flex-wrap mb-4">
    <button type="button" class="add_to_cart_single_page fp-btn fp-btn-primary cart-loading"
            style="flex:1;min-width:140px;padding:14px 24px;font-size:15px;">
        <i class="mdi mdi-cart-outline"></i> {{ __('Add to Cart') }}
    </button>
    <button type="button" class="but_now_single_page fp-btn fp-btn-outline cart-loading"
            style="flex:1;min-width:140px;padding:14px 24px;font-size:15px;">
        {{ __('Buy Now') }}
    </button>
</div>

{{-- Delivery Options --}}
@if($product->product_delivery_option?->isNotEmpty())
<div style="margin-bottom:16px;display:flex;flex-direction:column;gap:10px;">
    @foreach($product->product_delivery_option as $option)
    <div class="d-flex align-items-start gap-3">
        <i class="{{ $option->icon }}" style="font-size:20px;color:var(--fp-green);flex-shrink:0;margin-top:2px;"></i>
        <div>
            <div style="font-size:13px;font-weight:700;color:var(--fp-text);">{{ $option->title }}</div>
            <div style="font-size:12px;color:var(--fp-muted);">{{ $option->sub_title }}</div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Wishlist + Compare --}}
<div class="d-flex gap-3 align-items-center mb-4">
    <a href="javascript:void(0)" class="add_to_wishlist_single_page fp-icon-btn" title="{{ __('Add to Wishlist') }}">
        <i class="mdi mdi-heart-outline"></i>
    </a>
    <a href="javascript:void(0)" class="compare-btn fp-icon-btn"
       data-product_id="{{ $product->id }}" title="{{ __('Add to Compare') }}">
        <i class="mdi mdi-compare-horizontal"></i>
    </a>
</div>

{{-- SKU / Category --}}
<div style="font-size:12px;color:var(--fp-muted);display:flex;flex-direction:column;gap:4px;border-top:1px solid var(--fp-border);padding-top:16px;">
    @if($product?->inventory?->sku)
        <div><span style="color:var(--fp-green);font-weight:700;text-transform:uppercase;letter-spacing:1px;">{{ __('SKU') }}:</span> {{ $product->inventory->sku }}</div>
    @endif
    @if($product?->category?->name)
        <div><span style="color:var(--fp-green);font-weight:700;text-transform:uppercase;letter-spacing:1px;">{{ __('Category') }}:</span> {{ $product->category->name }}</div>
    @endif
</div>

