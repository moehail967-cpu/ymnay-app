{{-- FreshMart: product-options partial — skips name/stars/price (rendered in parent) --}}

{{-- Sizes --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
<div class="value-input-area mb-3">
    <div style="font-size:13px;font-weight:700;color:var(--fm-dark);margin-bottom:8px;">
        {{ __('Size:') }} <input readonly class="value-size" name="size" type="text" value=""
               style="border:none;background:transparent;font-weight:600;color:var(--fm-green);width:60px;">
        <input type="hidden" id="selected_size">
    </div>
    <ul class="size-lists select-list" data-type="Size" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;">
        @foreach($productSizes as $product_size)
            @if(!empty($product_size))
            <li class="list"
                data-value="{{ optional($product_size)->id }}"
                data-display-value="{{ optional($product_size)->name }}"
                style="padding:6px 14px;border:1px solid var(--fm-border);border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;">
                {{ optional($product_size)->size_code }}
            </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Colors --}}
@if($productColors->count() > 0 && current(current($productColors)))
<div class="value-input-area mb-3">
    <div style="font-size:13px;font-weight:700;color:var(--fm-dark);margin-bottom:8px;">
        {{ __('Color:') }} <input readonly class="value-size" name="color" type="text" value=""
               style="border:none;background:transparent;font-weight:600;color:var(--fm-green);width:80px;">
        <input type="hidden" id="selected_color">
    </div>
    <ul class="size-lists color-list" data-type="Color" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;">
        @foreach($productColors as $product_color)
            @if(!empty($product_color))
            <li style="background-color:{{ $product_color->color_code }};width:28px;height:28px;border-radius:50%;cursor:pointer;border:2px solid var(--fm-border);"
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
<div class="value-input-area mb-3">
    <div style="font-size:13px;font-weight:700;color:var(--fm-dark);margin-bottom:8px;">
        {{ $attribute }}: <input readonly class="value-size" type="text" value=""
               style="border:none;background:transparent;font-weight:600;color:var(--fm-green);width:80px;">
        <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
    </div>
    <ul class="size-lists" data-type="{{ $attribute }}" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;">
        @foreach($options as $option)
        <li class="list"
            data-value="{{ $option }}"
            data-display-value="{{ $option }}"
            style="padding:6px 14px;border:1px solid var(--fm-border);border-radius:6px;cursor:pointer;font-size:13px;font-weight:600;">
            {{ $option }}
        </li>
        @endforeach
    </ul>
</div>
@endforeach

{{-- Quantity + ATC --}}
<div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
    <div class="fm-qty-wrap">
        <button type="button" class="substract fm-qty-btn"><i class="las la-minus"></i></button>
        <input class="quantity-input fm-qty-input" type="number" id="quantity" name="quantity" value="1" min="1">
        <button type="button" class="plus fm-qty-btn"><i class="las la-plus"></i></button>
    </div>
    <span id="item_left" style="font-size:12px;color:var(--fm-muted);"
          data-stock-text="@php echo $stock_count > 0 ? __('Only!').' '.$stock_count.' '.__('Item Left') : __('No Item Left!'); @endphp">
        @if($stock_count > 0)
            <span class="text-success">{{ __('Only!') }} {{ $stock_count }} {{ __('Item Left') }}</span>
        @else
            <span class="text-danger">{{ __('No Item Left!') }}</span>
        @endif
    </span>
</div>

<div class="d-flex gap-2 flex-wrap mb-4">
    <a href="javascript:void(0)" class="add_to_cart_single_page fm-btn fm-btn-green">
        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
    </a>
    <a href="javascript:void(0)" class="but_now_single_page fm-btn fm-btn-outline">
        <i class="las la-bolt"></i> {{ __('Buy Now') }}
    </a>
</div>

{{-- Delivery options --}}
@if($product->product_delivery_option?->isNotEmpty())
<div class="mb-4" style="border-top:1px solid var(--fm-border);padding-top:12px;">
    @foreach($product->product_delivery_option as $option)
    <div class="d-flex align-items-start gap-2 mb-2">
        <i class="{{ $option->icon }}" style="font-size:20px;color:var(--fm-green);margin-top:2px;"></i>
        <div>
            <div style="font-size:13px;font-weight:700;color:var(--fm-dark);">{{ $option->title }}</div>
            <div style="font-size:12px;color:var(--fm-muted);">{{ $option->sub_title }}</div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Wishlist / Compare --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="javascript:void(0)" class="add_to_wishlist_single_page fm-icon-btn" title="{{ __('Add to Wishlist') }}">
        <i class="lar la-heart"></i> {{ __('Wishlist') }}
    </a>
    <a href="javascript:void(0)" class="compare-btn fm-icon-btn"
       data-product_id="{{ $product->id }}" title="{{ __('Add to Compare') }}">
        <i class="las la-retweet"></i> {{ __('Compare') }}
    </a>
</div>

{{-- SKU / Category --}}
<div style="font-size:13px;color:var(--fm-muted);line-height:2;">
    @if($product?->inventory?->sku)
    <div><span style="font-weight:700;color:var(--fm-dark);">{{ __('SKU:') }}</span> {{ $product->inventory->sku }}</div>
    @endif
    @if($product?->category)
    <div>
        <span style="font-weight:700;color:var(--fm-dark);">{{ __('Category:') }}</span>
        <a href="{{ theme_category_url($product->category->slug) }}" style="color:var(--fm-green);">{{ $product->category->name }}</a>
        @if($product?->subCategory)
            / <a href="{{ theme_category_url($product->subCategory->slug) }}" style="color:var(--fm-green);">{{ $product->subCategory->name }}</a>
        @endif
    </div>
    @endif
</div>

