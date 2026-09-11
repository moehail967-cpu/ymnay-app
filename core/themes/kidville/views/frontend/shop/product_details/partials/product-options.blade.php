{{-- KidVille: product options partial --}}

{{-- Sizes --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
<div class="value-input-area" style="margin-bottom:18px;">
    <div style="font-size:11px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:var(--kv-muted);margin-bottom:8px;">
        {{ __('Size:') }} <input readonly class="value-size" name="size" type="text" value=""
               style="border:none;background:transparent;font-weight:800;color:var(--kv-red);width:60px;">
        <input type="hidden" id="selected_size">
    </div>
    <ul class="size-lists select-list" data-type="Size" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;">
        @foreach($productSizes as $product_size)
            @if(!empty($product_size))
            <li class="list"
                data-value="{{ optional($product_size)->id }}"
                data-display-value="{{ optional($product_size)->name }}"
                style="padding:6px 16px;border:2px solid var(--kv-border);border-radius:var(--kv-radius-sm);cursor:pointer;font-size:13px;font-weight:700;background:#fff;color:var(--kv-dark);transition:all .2s;">
                {{ optional($product_size)->size_code }}
            </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Colors --}}
@if($productColors->count() > 0 && current(current($productColors)))
<div class="value-input-area" style="margin-bottom:18px;">
    <div style="font-size:11px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:var(--kv-muted);margin-bottom:8px;">
        {{ __('Color:') }} <input readonly class="value-size" name="color" type="text" value=""
               style="border:none;background:transparent;font-weight:800;color:var(--kv-red);width:80px;">
        <input type="hidden" id="selected_color">
    </div>
    <ul class="size-lists color-list" data-type="Color" style="display:flex;flex-wrap:wrap;gap:10px;list-style:none;padding:0;margin:0;">
        @foreach($productColors as $product_color)
            @if(!empty($product_color))
            <li style="background-color:{{ $product_color->color_code }};width:28px;height:28px;border-radius:50%;cursor:pointer;border:3px solid var(--kv-border);"
                data-value="{{ optional($product_color)->id }}"
                data-display-value="{{ optional($product_color)->name }}"
                title="{{ optional($product_color)->name }}">
            </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Custom attributes --}}
@foreach($available_attributes as $attribute => $options)
<div class="value-input-area" style="margin-bottom:18px;">
    <div style="font-size:11px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:var(--kv-muted);margin-bottom:8px;">
        {{ $attribute }}: <input readonly class="value-size" type="text" value=""
               style="border:none;background:transparent;font-weight:800;color:var(--kv-red);width:80px;">
    </div>
    <ul class="size-lists" data-type="{{ $attribute }}" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;">
        @foreach($options as $option)
        <li class="list"
            data-value="{{ $option }}"
            data-display-value="{{ $option }}"
            style="padding:6px 16px;border:2px solid var(--kv-border);border-radius:var(--kv-radius-sm);cursor:pointer;font-size:13px;font-weight:700;background:#fff;color:var(--kv-dark);transition:all .2s;">
            {{ $option }}
        </li>
        @endforeach
    </ul>
</div>
@endforeach

{{-- Quantity --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;border:2px solid var(--kv-border);border-radius:var(--kv-radius);overflow:hidden;background:#fff;">
        <button type="button" class="substract" style="width:40px;height:44px;border:none;background:transparent;font-size:20px;font-weight:700;cursor:pointer;color:var(--kv-red);display:flex;align-items:center;justify-content:center;">−</button>
        <input class="quantity-input" type="number" id="quantity" name="quantity" value="1" min="1"
               style="width:50px;height:44px;border:none;text-align:center;font-size:16px;font-weight:800;outline:none;color:var(--kv-dark);">
        <button type="button" class="plus" style="width:40px;height:44px;border:none;background:transparent;font-size:20px;font-weight:700;cursor:pointer;color:var(--kv-red);display:flex;align-items:center;justify-content:center;">+</button>
    </div>
    <span id="item_left" style="font-size:13px;color:var(--kv-muted);">
        @if($stock_count > 0)
            <span style="color:var(--kv-green);font-weight:700;"><i class="las la-check-circle"></i> {{ __('Only!') }} {{ $stock_count }} {{ __('left') }}</span>
        @else
            <span style="color:var(--kv-red);font-weight:700;"><i class="las la-times-circle"></i> {{ __('Out of stock') }}</span>
        @endif
    </span>
</div>

{{-- Action Buttons --}}
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
    <button class="add_to_cart_single_page kv-btn kv-btn-red" style="flex:1;min-width:140px;justify-content:center;">
        <i class="las la-shopping-bag"></i> {{ __('Add to Cart') }}
    </button>
    <button class="but_now_single_page kv-btn kv-btn-outline" style="flex:1;min-width:140px;justify-content:center;">
        <i class="las la-bolt"></i> {{ __('Buy Now') }}
    </button>
</div>
{{-- Delivery Options --}}
@if($product->product_delivery_option != null && $product->product_delivery_option->count())
<div style="display:flex;flex-direction:column;gap:10px;padding:14px;background:var(--kv-yellow-lt,#fffde7);border-radius:var(--kv-radius,10px);border:2px solid var(--kv-border,#e8e0d0);margin-bottom:12px;">
    @foreach($product->product_delivery_option as $option)
    <div style="display:flex;align-items:flex-start;gap:12px;">
        <span style="font-size:18px;color:var(--kv-red,#E8603C);flex-shrink:0;margin-top:2px;"><i class="{{ $option->icon }}"></i></span>
        <div>
            <strong style="display:block;font-size:12px;font-weight:800;color:var(--kv-dark,#1a1a1a);">{{ $option->title }}</strong>
            <span style="display:block;font-size:11px;color:var(--kv-muted,#888);margin-top:1px;">{{ $option->sub_title }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif
<div style="display:flex;gap:10px;flex-wrap:wrap;">
    <button class="add_to_wishlist_single_page kv-btn kv-btn-ghost" style="flex:1;justify-content:center;">
        <i class="las la-heart"></i> {{ __('Wishlist') }}
    </button>
    <button class="compare-btn kv-btn kv-btn-ghost" style="flex:1;justify-content:center;">
        <i class="las la-exchange-alt"></i> {{ __('Compare') }}
    </button>
</div>
