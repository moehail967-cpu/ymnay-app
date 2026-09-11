{{-- VelvetLux product options partial: attributes + qty + cart buttons only --}}
{{-- Title / stock / price are already rendered by the parent product-details.blade.php --}}

{{-- Sizes --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
<div class="value-input-area mb-4">
    <span class="vl-product-form .input-title">{{ __('Size') }}</span>
    <input readonly class="form--input value-size" name="size" type="text" value="">
    <input type="hidden" id="selected_size">
    <ul class="size-lists" data-type="Size" style="margin-top:10px;">
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
<div class="value-input-area mb-4">
    <span class="vl-product-form .input-title">{{ __('Color') }}</span>
    <input readonly class="form--input value-size" name="color" type="text" value="">
    <input type="hidden" id="selected_color">
    <ul class="size-lists color-list" data-type="Color" style="margin-top:10px;">
        @foreach($productColors as $product_color)
            @if(!empty($product_color))
                <li style="background-color:{{ $product_color->color_code }};width:28px;height:28px;border-radius:50%;"
                    data-value="{{ optional($product_color)->id }}"
                    data-display-value="{{ optional($product_color)->name }}">
                </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Custom attribute axes (RAM, Material, etc.) — skip Color and Size since they have dedicated sections above --}}
@foreach($available_attributes as $attribute => $options)
    @if(in_array(strtolower($attribute), ['color', 'size'])) @continue @endif
<div class="value-input-area mb-4">
    <span class="vl-product-form .input-title">{{ $attribute }}</span>
    <input readonly class="form--input value-size" type="text" value="">
    <ul class="size-lists" data-type="{{ $attribute }}" style="margin-top:10px;">
        @foreach($options as $option)
            <li class="list" data-value="{{ $option }}" data-display-value="{{ $option }}">{{ $option }}</li>
        @endforeach
    </ul>
</div>
@endforeach

{{-- Quantity + stock left --}}
<div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px;">
    <span style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);font-family:'Inter',sans-serif;">{{ __('Quantity') }}</span>
    <div class="vl-qty-stepper">
        <button type="button" class="vl-qty-btn substract"><i class="las la-minus"></i></button>
        <input class="vl-qty-input quantity-input" id="quantity" name="quantity" type="number" value="1" min="1">
        <button type="button" class="vl-qty-btn plus"><i class="las la-plus"></i></button>
    </div>
    @php
        $text_color = $product?->inventory?->stock_count > 0 ? 'text-success' : 'text-danger';
        $text = $product?->inventory?->stock_count > 0
            ? __('Only!').' '.$product?->inventory?->stock_count.' '.__('Item Left!')
            : __('No Item Left!');
    @endphp
    <span id="item_left" class="{{ $text_color }}"
          data-stock-text="{{ $text }}"
          style="font-size:12px;font-family:'Inter',sans-serif;">{{ $text }}</span>
</div>

{{-- Add to Cart / Buy Now --}}
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
    <button type="button"
            class="add_to_cart_single_page vl-btn vl-btn-primary"
            style="flex:1;justify-content:center;min-width:160px;">
        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
    </button>
    <button type="button"
            class="but_now_single_page vl-btn vl-btn-outline"
            style="flex:1;justify-content:center;min-width:160px;">
        {{ __('Buy Now') }}
    </button>
</div>

{{-- Delivery options --}}
@if($product->product_delivery_option?->count())
<div style="margin-bottom:20px;border-top:1px solid var(--vl-border);padding-top:20px;display:flex;flex-direction:column;gap:12px;">
    @foreach($product->product_delivery_option as $option)
    <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-size:20px;color:var(--vl-champagne);"><i class="{{ $option->icon }}"></i></span>
        <div>
            <div style="font-size:12px;font-weight:600;color:var(--vl-ivory);">{{ $option->title }}</div>
            <div style="font-size:11px;color:var(--vl-muted);">{{ $option->sub_title }}</div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Wishlist / Compare --}}
<div style="display:flex;gap:10px;margin-bottom:24px;">
    <button type="button"
            class="add_to_wishlist_single_page"
            style="width:40px;height:40px;background:transparent;border:1px solid var(--vl-border);color:var(--vl-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:18px;transition:all .2s;"
            onmouseover="this.style.borderColor='var(--vl-champagne)';this.style.color='var(--vl-champagne)'"
            onmouseout="this.style.borderColor='var(--vl-border)';this.style.color='var(--vl-muted)'">
        <i class="lar la-heart"></i>
    </button>
    <button type="button"
            class="compare-btn"
            data-product_id="{{ $product->id }}"
            style="width:40px;height:40px;background:transparent;border:1px solid var(--vl-border);color:var(--vl-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:18px;transition:all .2s;"
            onmouseover="this.style.borderColor='var(--vl-champagne)';this.style.color='var(--vl-champagne)'"
            onmouseout="this.style.borderColor='var(--vl-border)';this.style.color='var(--vl-muted)'">
        <i class="las la-retweet"></i>
    </button>
</div>

{{-- Meta: Category / SKU / Unit --}}
<div style="border-top:1px solid var(--vl-border);padding-top:20px;">
    @if($product?->category?->name)
    <div style="display:flex;gap:12px;font-size:12px;margin-bottom:8px;color:var(--vl-muted);">
        <span style="font-family:'Inter',sans-serif;text-transform:uppercase;letter-spacing:1px;min-width:60px;">{{ __('Category') }}</span>
        <span style="color:var(--vl-champagne);">{{ $product->category->name }}</span>
    </div>
    @endif
    @if($product?->uom)
    <div style="display:flex;gap:12px;font-size:12px;margin-bottom:8px;color:var(--vl-muted);">
        <span style="font-family:'Inter',sans-serif;text-transform:uppercase;letter-spacing:1px;min-width:60px;">{{ __('Unit') }}</span>
        <span><strong>{{ $product->uom->quantity }}</strong> {{ $product->uom->uom_details?->name }}</span>
    </div>
    @endif
    @if($product?->inventory?->sku)
    <div style="display:flex;gap:12px;font-size:12px;margin-bottom:8px;color:var(--vl-muted);">
        <span style="font-family:'Inter',sans-serif;text-transform:uppercase;letter-spacing:1px;min-width:60px;">{{ __('SKU') }}</span>
        <span><strong>{{ $product->inventory->sku }}</strong></span>
    </div>
    @endif
</div>

