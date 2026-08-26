{{--
    Aromatic product-options partial.
    Title / price / stock are rendered in product-details.blade.php.
    This partial only renders the interactive form elements so JS hooks work correctly.
--}}

{{-- Campaign notice (expired / not yet started) --}}
@if($campaign_product !== null && $campaign_product->status !== 'draft')
    @if(isset($campaign_active) && $campaign_active)
        <div class="global-timer mb-4"></div>
    @else
        <div class="ar-campaign-notice mb-4">
            {{ __('The Campaign Is Over Or Not Yet Started') }}
        </div>
    @endif
@endif

{{-- Sizes --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
    <div class="ar-attr-group mb-4">
        <span class="ar-attr-label">
            {{ __('Size:') }}
            <input readonly class="form--input value-size" name="size" type="text" value="">
            <input type="hidden" id="selected_size">
        </span>
        <ul class="size-lists ar-size-list {{ $quickView ? "quick-view-size-lists" : "" }}" data-type="Size">
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
    <div class="ar-attr-group mb-4">
        <span class="ar-attr-label">
            {{ __('Color:') }}
            <input readonly class="form--input value-size" name="color" type="text" value="">
            <input type="hidden" id="selected_color">
        </span>
        <ul class="size-lists color-list ar-color-list {{ $quickView ? "quick-view-size-lists" : "" }}" data-type="Color">
            @foreach($productColors as $product_color)
                @if(!empty($product_color))
                    <li style="background-color: {{ $product_color->color_code }}"
                        data-value="{{ optional($product_color)->id }}"
                        data-display-value="{{ optional($product_color)->name }}"></li>
                @endif
            @endforeach
        </ul>
    </div>
@endif

{{-- Custom attributes --}}
@foreach($available_attributes as $attribute => $options)
    <div class="ar-attr-group mb-4 {{ $quickView ? "quick-view-value-input-area" : "" }}">
        <span class="ar-attr-label">
            {{ $attribute }}
            <input readonly class="form--input value-size" type="text" value="">
            <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
        </span>
        <ul class="size-lists ar-size-list {{ $quickView ? "quick-view-size-lists" : "" }}" data-type="{{ $attribute }}">
            @foreach($options as $option)
                <li class="list" data-value="{{ $option }}" data-display-value="{{ $option }}">{{ $option }}</li>
            @endforeach
        </ul>
    </div>
@endforeach

{{-- Quantity --}}
<div class="mb-4">
    <div class="ar-qty-row">
        <span class="ar-qty-label">{{ __('Quantity:') }}</span>
        <div class="ar-qty">
            <button type="button" class="{{ $quickView ? "quick-view-" : "" }}substract substract">−</button>
            <input type="number" class="{{ $quickView ? "quick-view-" : "" }}quantity-input quantity-input qty_"
                   id="{{ $quickView ? "quick-view-" : "" }}quantity" name="quantity" value="1" min="1">
            <button type="button" class="{{ $quickView ? "quick-view-" : "" }}plus plus">+</button>
        </div>
        @php
            $text_color = $product?->inventory?->stock_count > 0 ? 'text-success' : 'text-danger';
            $qty_text   = $product?->inventory?->stock_count > 0
                ? __('Only!').' '.$product->inventory->stock_count.' '.__('Item Left')
                : __('No Item Left!');
        @endphp
        <span class="ar-items-left {{ $text_color }}"
              id="{{ $quickView ? "quick_view_" : "" }}item_left"
              data-stock-text="{{ $qty_text }}">
            {{ $qty_text }}
        </span>
    </div>

    @if($product?->inventory?->stock_count > 0)
        <div class="ar-cart-btns mt-3">
            <a href="javascript:void(0)"
               class="{{ $quickView ? "quick_view_add_to_cart" : "add_to_cart_single_page" }} cart-loading ar-btn ar-btn-red">
                <i class="mdi mdi-cart-outline"></i> {{ __('Add to Cart') }}
            </a>
            <a href="javascript:void(0)"
               class="{{ $quickView ? "quick_view_but_now" : "but_now_single_page" }} cart-loading ar-btn ar-btn-outline">
                {{ __('Buy Now') }}
            </a>
        </div>
    @endif
</div>

{{-- Delivery Options --}}
@if($product->product_delivery_option != null && $product->product_delivery_option->count())
<div class="ar-pd-delivery-wrap mt-3">
    @foreach($product->product_delivery_option as $option)
    <div class="ar-pd-delivery-item">
        <span class="ar-pd-delivery-icon"><i class="{{ $option->icon }}"></i></span>
        <div class="ar-pd-delivery-text">
            <strong>{{ $option->title }}</strong>
            <span>{{ $option->sub_title }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Wishlist / Compare --}}
<div class="ar-action-links">
    <button class="ar-action-link {{ $quickView ? "quick_view_add_to_wishlist" : "add_to_wishlist_single_page" }}">
        <i class="mdi mdi-heart-outline"></i> {{ __('Add to Wishlist') }}
    </button>
    <button class="ar-action-link {{ $quickView ? "quick-view-" : "" }}compare-btn" data-product_id="{{ $product->id }}">
        <i class="mdi mdi-compare-horizontal"></i> {{ __('Compare') }}
    </button>
</div>

{{-- Custom specifications — at the bottom --}}
@if(isset($custom_specifications) && $custom_specifications->isNotEmpty())
    <div class="ar-spec-wrap mb-4 mt-4">
        <table class="ar-spec-table">
            <thead>
                <tr>
                    <th>{{ __('Specification') }}</th>
                    <th>{{ __('Value') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($custom_specifications as $spec)
                    <tr>
                        <td>{{ $spec->title }}</td>
                        <td>{{ $spec->value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
