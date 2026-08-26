{{-- TrailCo: product-options — sizes, colors, qty, ATC, Buy Now, wishlist, compare --}}

{{-- Sizes --}}
@if(isset($productSizes) && $productSizes->count() > 0 && !empty(current(current($productSizes))))
<div class="value-input-area mb-4">
    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);margin-bottom:10px;">
        {{ __('Size:') }}
        <input readonly class="value-size" name="size" type="text" value=""
               style="border:none;background:transparent;font-weight:700;color:var(--tr-olive);width:60px;">
        <input type="hidden" id="selected_size">
    </div>
    <ul class="size-lists select-list" data-type="Size" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;">
        @foreach($productSizes as $product_size)
            @if(!empty($product_size))
            <li class="list"
                data-value="{{ optional($product_size)->id }}"
                data-display-value="{{ optional($product_size)->name }}"
                style="padding:6px 16px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);cursor:pointer;font-size:13px;font-weight:700;color:var(--tr-bark);transition:all .15s;">
                {{ optional($product_size)->size_code }}
            </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Colors --}}
@if(isset($productColors) && $productColors->count() > 0 && current(current($productColors)))
<div class="value-input-area mb-4">
    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);margin-bottom:10px;">
        {{ __('Color:') }}
        <input readonly class="value-size" name="color" type="text" value=""
               style="border:none;background:transparent;font-weight:700;color:var(--tr-olive);width:80px;">
        <input type="hidden" id="selected_color">
    </div>
    <ul class="size-lists color-list" data-type="Color" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;">
        @foreach($productColors as $product_color)
            @if(!empty($product_color))
            <li style="background-color:{{ optional($product_color)->color_code }};width:32px;height:32px;border-radius:50%;cursor:pointer;border:2.5px solid var(--tr-border);"
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
@if(isset($available_attributes))
@foreach($available_attributes as $attribute => $options)
<div class="value-input-area mb-4">
    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);margin-bottom:10px;">
        {{ $attribute }}:
        <input readonly class="value-size" type="text" value=""
               style="border:none;background:transparent;font-weight:700;color:var(--tr-olive);width:80px;">
        <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
    </div>
    <ul class="size-lists" data-type="{{ $attribute }}" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;">
        @foreach($options as $option)
        <li class="list"
            data-value="{{ $option }}"
            data-display-value="{{ $option }}"
            style="padding:6px 16px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);cursor:pointer;font-size:13px;font-weight:700;color:var(--tr-bark);">
            {{ $option }}
        </li>
        @endforeach
    </ul>
</div>
@endforeach
@endif

{{-- Quantity + stock --}}
<div class="d-flex align-items-center gap-4 mb-4 flex-wrap">
    <div style="display:flex;align-items:center;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;">
        <button type="button" class="substract"
                style="width:38px;height:42px;border:0;background:var(--tr-cream);cursor:pointer;font-size:16px;color:var(--tr-bark);font-weight:900;">−</button>
        <input class="quantity-input" type="number" id="quantity" name="quantity" value="1" min="1"
               style="width:52px;height:42px;border:0;text-align:center;font-size:15px;font-weight:700;color:var(--tr-bark);outline:none;">
        <button type="button" class="plus"
                style="width:38px;height:42px;border:0;background:var(--tr-cream);cursor:pointer;font-size:16px;color:var(--tr-bark);font-weight:900;">+</button>
    </div>
    <span id="item_left" style="font-size:12px;color:var(--tr-stone);"
          data-stock-text="@php echo ($stock_count ?? 0) > 0 ? __('Only!').' '.($stock_count ?? 0).' '.__('Item Left') : __('No Item Left!'); @endphp">
        @if(($stock_count ?? 0) > 0)
            <span class="text-success"><i class="mdi mdi-check-circle"></i> {{ __('Only!') }} {{ $stock_count }} {{ __('Item Left') }}</span>
        @else
            <span class="text-danger"><i class="mdi mdi-close-circle"></i> {{ __('No Item Left!') }}</span>
        @endif
    </span>
</div>

{{-- Action buttons --}}
<div class="d-flex gap-2 flex-wrap mb-3">
    <a href="javascript:void(0)" class="add_to_cart_single_page tr-btn tr-btn-primary">
        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
    </a>
    <a href="javascript:void(0)" class="but_now_single_page tr-btn" style="background:var(--tr-terra);color:#fff;border-color:var(--tr-terra);">
        <i class="mdi mdi-lightning-bolt"></i> {{ __('Buy Now') }}
    </a>
</div>

{{-- Delivery Options --}}
@if($product->product_delivery_option != null && $product->product_delivery_option->count())
<div class="mb-3" style="display:flex;flex-direction:column;gap:10px;padding:14px;background:var(--tr-cream,#f9f5ef);border:1.5px solid var(--tr-border,#ddd8cc);border-radius:var(--tr-radius,6px);">
    @foreach($product->product_delivery_option as $option)
    <div style="display:flex;align-items:flex-start;gap:12px;">
        <span style="font-size:18px;color:var(--tr-olive,#6B7C3B);flex-shrink:0;margin-top:2px;"><i class="{{ $option->icon }}"></i></span>
        <div>
            <strong style="display:block;font-size:12px;font-weight:700;color:var(--tr-bark,#3D2B1A);">{{ $option->title }}</strong>
            <span style="display:block;font-size:11px;color:var(--tr-stone,#888);margin-top:1px;">{{ $option->sub_title }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="javascript:void(0)" class="add_to_wishlist_single_page" style="display:flex;align-items:center;gap:4px;font-size:13px;color:var(--tr-stone);text-decoration:none;font-weight:600;">
        <i class="mdi mdi-heart-outline" style="font-size:18px;"></i> {{ __('Wishlist') }}
    </a>
    <a href="javascript:void(0)" class="compare-btn" data-product_id="{{ $product->id }}"
       style="display:flex;align-items:center;gap:4px;font-size:13px;color:var(--tr-stone);text-decoration:none;font-weight:600;">
        <i class="mdi mdi-swap-horizontal" style="font-size:18px;"></i> {{ __('Compare') }}
    </a>
</div>

{{-- SKU / Category --}}
<div style="font-size:13px;color:var(--tr-stone);line-height:2;border-top:1px solid var(--tr-border);padding-top:14px;">
    @if($product?->inventory?->sku)
    <div><span style="font-weight:700;color:var(--tr-bark);">{{ __('SKU:') }}</span> {{ $product->inventory->sku }}</div>
    @endif
    @if($product?->category)
    <div>
        <span style="font-weight:700;color:var(--tr-bark);">{{ __('Category:') }}</span>
        <a href="{{ theme_category_url($product->category->slug) }}" style="color:var(--tr-olive);">{{ $product->category->name }}</a>
    </div>
    @endif
</div>
