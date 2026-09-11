{{-- Casual: Product options (sizes, colors, attributes, qty, ATC) --}}

{{-- Campaign countdown --}}
@if($campaign_product !== null && $campaign_product->status !== 'draft')
<div class="cs-pd-campaign mb-3">
    <h5 class="cs-pd-campaign-name">{{ $campaign_name }}</h5>
    @if($campaign_active)
        <div class="global-timer cs-pd-timer"></div>
    @else
        <p class="cs-pd-campaign-over">{{ __('The Campaign is over or not yet started') }}</p>
    @endif
</div>
@endif

{{-- Sizes --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
<div class="cs-pd-option-group size_list">
    <label class="cs-pd-option-label">
        {{ __('Size:') }}
        <input readonly class="cs-pd-option-val value-size" name="size" type="text" value="">
        <input type="hidden" id="selected_size">
    </label>
    <ul class="cs-pd-option-list size-lists select-list" data-type="Size">
        @foreach($productSizes as $product_size)
            @if(!empty($product_size))
            <li class="cs-pd-option-item list"
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
<div class="cs-pd-option-group color_list">
    <label class="cs-pd-option-label">
        {{ __('Color:') }}
        <input readonly class="cs-pd-option-val value-size" name="color" type="text" value="">
        <input type="hidden" id="selected_color">
    </label>
    <ul class="cs-pd-option-list cs-pd-color-list size-lists color-list" data-type="Color">
        @foreach($productColors as $product_color)
            @if(!empty($product_color))
            <li class="cs-pd-color-swatch"
                style="background-color: {{ $product_color->color_code }}"
                data-value="{{ optional($product_color)->id }}"
                data-display-value="{{ optional($product_color)->name }}">
            </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Custom attributes --}}
@foreach($available_attributes as $attribute => $options)
<div class="cs-pd-option-group attribute_options_list">
    <label class="cs-pd-option-label">
        {{ $attribute }}
        <input readonly class="cs-pd-option-val value-size" type="text" value="">
        <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
    </label>
    <ul class="cs-pd-option-list size-lists" data-type="{{ $attribute }}">
        @foreach($options as $option)
        <li class="cs-pd-option-item list"
            data-value="{{ $option }}"
            data-display-value="{{ $option }}">
            {{ $option }}
        </li>
        @endforeach
    </ul>
</div>
@endforeach

{{-- Custom specifications --}}
@if($custom_specifications->isNotEmpty())
<div class="cs-pd-specs">
    <table class="cs-pd-specs-table specification-table">
        <thead>
            <tr>
                <th>{{ __('Custom Specifications') }}</th>
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

{{-- Quantity + item left --}}
<div class="cs-pd-qty-row">
    <label class="cs-pd-option-label">{{ __('Quantity:') }}</label>
    <div class="cs-pd-qty">
        <button type="button" class="substract cs-qty-btn"><i class="las la-minus"></i></button>
        <input class="quantity-input cs-qty-input" type="number" id="quantity" name="quantity" value="1" min="1">
        <button type="button" class="plus cs-qty-btn"><i class="las la-plus"></i></button>
    </div>
    @php
        $item_left_text = $product?->inventory?->stock_count > 0
            ? __('Only!') . ' ' . $product?->inventory?->stock_count . ' ' . __('Item Left')
            : __('No Item Left!');
        $item_left_cls  = $product?->inventory?->stock_count > 0 ? 'text-success' : 'text-danger';
    @endphp
    <span class="cs-pd-item-left stock-available {{ $item_left_cls }}"
          id="item_left"
          data-stock-text="{{ $item_left_text }}">
        {{ $item_left_text }}
    </span>
</div>

{{-- Action buttons --}}
@if($product?->inventory?->stock_count > 0)
<div class="cs-pd-actions">
    <a href="javascript:void(0)"
       class="add_to_cart_single_page cs-pd-atc-btn cart-loading">
        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
    </a>
    <a href="javascript:void(0)"
       class="but_now_single_page cs-pd-buy-btn cart-loading">
        {{ __('Buy Now') }}
    </a>
    <a href="javascript:void(0)"
       class="add_to_wishlist_single_page cs-pd-wish-btn btn-wishlist"
       data-product_id="{{ $product->id }}">
        <i class="lar la-heart"></i>
    </a>
    <a href="javascript:void(0)"
       class="compare-btn cs-pd-compare-btn"
       data-product_id="{{ $product->id }}"
       title="{{ __('Add to Compare') }}">
        <i class="las la-retweet"></i>
    </a>
</div>
@else
<p class="cs-pd-out-of-stock-msg text-danger">{{ __('This product is currently out of stock.') }}</p>
@endif

{{-- Delivery Options --}}
@if($product->product_delivery_option != null && $product->product_delivery_option->count())
<div class="cs-pd-delivery-wrap">
    @foreach($product->product_delivery_option as $option)
    <div class="cs-pd-delivery-item">
        <i class="{{ $option->icon }}"></i>
        <div>
            <strong>{{ $option->title }}</strong>
            <span>{{ $option->sub_title }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif
