{{-- Pharmacy: product options (sizes, colors, attrs, qty, ATC, Buy Now, Wishlist, Compare) --}}

{{-- Sizes --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
<div class="value-input-area" style="margin-bottom:16px;">
    <span class="input-title" style="font-size:13px;font-weight:700;color:var(--pf-dark);display:block;margin-bottom:8px;">{{ __('Size') }}</span>
    <input type="hidden" name="size" id="selected_size">
    <ul class="size-lists select-list" data-type="Size">
        @foreach($productSizes as $size)
            @if(!empty($size))
            <li class="list"
                data-value="{{ optional($size)->id }}"
                data-display-value="{{ optional($size)->name }}">
                {{ optional($size)->size_code ?? optional($size)->name }}
            </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Colors --}}
@if($productColors->count() > 0 && current(current($productColors)))
<div class="value-input-area" style="margin-bottom:16px;">
    <span class="input-title" style="font-size:13px;font-weight:700;color:var(--pf-dark);display:block;margin-bottom:8px;">{{ __('Color') }}</span>
    <input type="hidden" name="color" id="selected_color">
    <ul class="size-lists select-list color-list" data-type="Color">
        @foreach($productColors as $color)
            @if(!empty($color))
            <li class="list"
                data-value="{{ optional($color)->id }}"
                data-display-value="{{ optional($color)->name }}"
                title="{{ optional($color)->name }}"
                style="background-color:{{ $color->color_code ?? 'transparent' }};">
            </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Custom attributes --}}
@foreach($available_attributes as $attribute => $options)
<div class="value-input-area" style="margin-bottom:16px;">
    <span class="input-title" style="font-size:13px;font-weight:700;color:var(--pf-dark);display:block;margin-bottom:8px;">{{ $attribute }}</span>
    <ul class="size-lists select-list" data-type="{{ $attribute }}">
        @foreach($options as $option)
        <li class="list" data-value="{{ $option }}" data-display-value="{{ $option }}">{{ $option }}</li>
        @endforeach
    </ul>
</div>
@endforeach

{{-- Quantity --}}
<div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
    <div class="pf-qty" style="display:flex;align-items:center;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);overflow:hidden;">
        <button type="button" class="substract" style="width:38px;height:40px;border:0;background:var(--pf-bg);color:var(--pf-dark);font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;">
            <i class="las la-minus"></i>
        </button>
        <input type="number" id="quantity" class="quantity-input"
               value="1" min="1"
               style="width:52px;height:40px;border:0;border-left:1.5px solid var(--pf-border);border-right:1.5px solid var(--pf-border);text-align:center;font-size:15px;font-weight:700;color:var(--pf-dark);font-family:var(--pf-font);outline:none;background:#fff;">
        <button type="button" class="plus" style="width:38px;height:40px;border:0;background:var(--pf-bg);color:var(--pf-dark);font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;">
            <i class="las la-plus"></i>
        </button>
    </div>
    <span id="item_left" data-stock-text="" style="font-size:12px;color:var(--pf-muted);font-weight:700;"></span>
</div>

{{-- Action Buttons --}}
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
    <button type="button" class="add_to_cart_single_page pf-btn pf-btn-teal" style="flex:1;justify-content:center;">
        <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
    </button>
    <button type="button" class="but_now_single_page pf-btn pf-btn-outline" style="flex:1;justify-content:center;">
        <i class="las la-bolt"></i> {{ __('Buy Now') }}
    </button>
</div>

{{-- Delivery Options --}}
@if($product->product_delivery_option != null && $product->product_delivery_option->count())
<div style="display:flex;flex-direction:column;gap:10px;padding:14px;background:var(--pf-bg,#f0f9f8);border:1px solid var(--pf-border,#d0e8e5);border-radius:var(--pf-radius,8px);margin-bottom:14px;">
    @foreach($product->product_delivery_option as $option)
    <div style="display:flex;align-items:flex-start;gap:12px;">
        <span style="font-size:18px;color:var(--pf-teal,#0EA5A0);flex-shrink:0;margin-top:2px;"><i class="{{ $option->icon }}"></i></span>
        <div>
            <strong style="display:block;font-size:12px;font-weight:700;color:var(--pf-dark,#1a1a1a);">{{ $option->title }}</strong>
            <span style="display:block;font-size:11px;color:var(--pf-muted,#888);margin-top:1px;">{{ $option->sub_title }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

<div style="display:flex;gap:10px;">
    <button type="button" class="add_to_wishlist_single_page pf-btn pf-btn-ghost pf-btn-sm">
        <i class="las la-heart"></i> {{ __('Wishlist') }}
    </button>
    <button type="button" class="compare-btn pf-btn pf-btn-ghost pf-btn-sm" data-product_id="{{ $product->id }}">
        <i class="las la-exchange-alt"></i> {{ __('Compare') }}
    </button>
</div>
