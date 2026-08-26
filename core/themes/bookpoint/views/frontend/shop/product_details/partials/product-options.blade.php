{{--
    BookPoint product-options partial.
    Title / price / stock are rendered by product-details.blade.php (when $quickView=false)
    and by quick-view.blade.php (when $quickView=true).
    This partial only renders interactive form elements so JS hooks work correctly.
--}}

{{-- Campaign notice (expired / not yet started) --}}
@if($campaign_product !== null && $campaign_product->status !== 'draft')
    @if(isset($campaign_active) && $campaign_active)
        <div class="global-timer mb-4"></div>
    @else
        <div class="mb-4" style="background:#fff3cd;border:1px solid #ffc107;color:#856404;border-radius:6px;padding:12px 16px;font-size:13px;">
            {{ __('The Campaign Is Over Or Not Yet Started') }}
        </div>
    @endif
@endif

{{-- Sizes --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
    <div class="mb-4 size_list {{ $quickView ? 'quick-view-value-input-area' : '' }}">
        <span class="bp-opt-label">{{ __('Size:') }}
            <input readonly class="form--input value-size" name="size" type="text" value="" style="display:none;">
            <input type="hidden" id="selected_size">
        </span>
        <ul class="size-lists {{ $quickView ? 'quick-view-size-lists' : '' }}" data-type="Size">
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
    <div class="mb-4 color_list {{ $quickView ? 'quick-view-value-input-area' : '' }}">
        <span class="bp-opt-label">{{ __('Color:') }}
            <input readonly class="form--input value-size" name="color" type="text" value="" style="display:none;">
            <input type="hidden" id="selected_color">
        </span>
        <ul class="size-lists color-list {{ $quickView ? 'quick-view-size-lists' : '' }}" data-type="Color">
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
    <div class="mb-4 attribute_options_list {{ $quickView ? 'quick-view-value-input-area' : '' }}">
        <span class="bp-opt-label">{{ $attribute }}
            <input readonly class="form--input value-size" type="text" value="" style="display:none;">
            <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
        </span>
        <ul class="size-lists {{ $quickView ? 'quick-view-size-lists' : '' }}" data-type="{{ $attribute }}">
            @foreach($options as $option)
                <li class="list" data-value="{{ $option }}" data-display-value="{{ $option }}">{{ $option }}</li>
            @endforeach
        </ul>
    </div>
@endforeach

{{-- Quantity + buttons --}}
<div class="mb-4">
    <div class="bp-opt-qty-row">
        <span class="bp-opt-label">{{ __('Quantity:') }}</span>
        <div class="bp-qty">
            <button type="button" class="{{ $quickView ? 'quick-view-' : '' }}substract substract">−</button>
            <input type="number" class="{{ $quickView ? 'quick-view-' : '' }}quantity-input quantity-input qty_"
                   id="{{ $quickView ? 'quick-view-' : '' }}quantity" name="quantity" value="1" min="1">
            <button type="button" class="{{ $quickView ? 'quick-view-' : '' }}plus plus">+</button>
        </div>
        @php
            $qty_text_color = $product?->inventory?->stock_count > 0 ? 'color:#118668;' : 'color:#e94560;';
            $qty_text = $product?->inventory?->stock_count > 0
                ? __('Only!').' '.$product->inventory->stock_count.' '.__('Item Left')
                : __('No Item Left!');
        @endphp
        <span class="stock-available" style="font-size:13px;font-weight:600;{{ $qty_text_color }}"
              id="{{ $quickView ? 'quick_view_' : '' }}item_left"
              data-stock-text="{{ $qty_text }}">{{ $qty_text }}</span>
    </div>

    @if($product?->inventory?->stock_count > 0 || !$product?->inventory)
    <div class="bp-opt-btn-row">
        <a href="javascript:void(0)"
           class="{{ $quickView ? 'quick_view_add_to_cart' : 'add_to_cart_single_page' }} cart-loading bp-btn bp-btn-green">
            <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
        </a>
        <a href="javascript:void(0)"
           class="{{ $quickView ? 'quick_view_but_now' : 'but_now_single_page' }} cart-loading bp-btn bp-btn-outline">
            {{ __('Buy Now') }}
        </a>
    </div>
    @endif
</div>

{{-- Delivery options — right after CTA --}}
@if($product->product_delivery_option?->isNotEmpty())
    <div class="bp-opt-delivery mt-4">
        @foreach($product->product_delivery_option as $option)
            <div class="bp-delivery-item">
                <i class="{{ $option->icon }}"></i>
                <div>
                    <div class="bp-delivery-title">{{ $option->title }}</div>
                    <div class="bp-delivery-sub">{{ $option->sub_title }}</div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Payment icons — right after delivery --}}
@php
    $payment_gateway_images = \App\Models\PaymentGateway::where('status', 1)->permittedPaymentGateway()->get('image')->pluck('image');
@endphp
@if($payment_gateway_images->isNotEmpty())
    <div class="bp-opt-payment mt-4">
        <span class="bp-opt-label d-block mb-2">{{ __('Guaranteed Safe Checkout') }}</span>
        <div class="bp-payment-icons">
            @foreach($payment_gateway_images as $image)
                {!! render_image_markup_by_attachment_id($image) !!}
            @endforeach
        </div>
    </div>
@endif

{{-- Wishlist / Compare --}}
<div class="bp-opt-actions">
    <button class="bp-opt-action-btn {{ $quickView ? 'quick_view_add_to_wishlist' : 'add_to_wishlist_single_page' }}">
        <i class="lar la-heart"></i> {{ __('Add to Wishlist') }}
    </button>
    <button class="bp-opt-action-btn {{ $quickView ? 'quick-view-' : '' }}compare-btn" data-product_id="{{ $product->id }}">
        <i class="las la-retweet"></i> {{ __('Compare') }}
    </button>
</div>

{{-- Social share --}}
@php
    $product_primary_image = get_attachment_image_by_id($product->image_id);
    $product_primary_image_url = $product_primary_image ? $product_primary_image['img_url'] : '';
@endphp
<div class="bp-opt-social mt-4">
    {!! single_post_share($product->slug, $product->name, $product_primary_image_url) !!}
</div>

{{-- Custom Specifications --}}
@if(!empty($custom_specifications) && $custom_specifications->isNotEmpty())
    <div class="bp-opt-specs mt-4">
        <div class="bp-opt-label mb-2">{{ __('Product Details:') }}</div>
        <table class="bp-spec-table">
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
