{{--
    Furnito product-options partial.
    Title, badge, stars, stock, price, and campaign countdown are already
    rendered in product-details.blade.php — not repeated here.
--}}

{{-- Product summary --}}
@if(!empty($product->summary))
    <p class="fn-pd-summary mb-4">{{ $product->summary }}</p>
@endif

{{-- Sizes / Colors / Custom Attributes --}}
<div class="value-input-area mt-2">
    @if($productSizes->count() > 0 && !empty(current(current($productSizes))))
        <div class="value-input-area single-input-list pt-2 size_list d-flex align-items-center justify-content-left {{ $quickView ? 'quick-view-value-input-area' : '' }}">
            <span class="input-title fw-500 color-heading">
                <strong class="color-light"> {{ __('Size:') }} </strong>
                <input readonly class="form--input value-size" name="size" type="text" value="">
                <input type="hidden" id="selected_size">
            </span>
            <ul class="size-lists select-list {{ $quickView ? 'quick-view-size-lists' : '' }}" data-type="Size">
                @foreach($productSizes as $product_size)
                    @if(!empty($product_size))
                        <li class="list"
                            data-value="{{ optional($product_size)->id }}"
                            data-display-value="{{ optional($product_size)->name }}"
                        > {{ optional($product_size)->size_code }} </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    @if($productColors->count() > 0 && current(current($productColors)))
        <div class="value-input-area single-input-list pt-2 color_list d-flex align-items-center justify-content-left {{ $quickView ? 'quick-view-value-input-area' : '' }}">
            <span class="input-title fw-500 color-heading">
                <strong class="color-light"> {{ __('Color:') }} </strong>
                <input readonly class="form--input value-size" name="color" type="text" value="">
                <input type="hidden" id="selected_color">
            </span>
            <ul class="size-lists color-list {{ $quickView ? 'quick-view-size-lists' : '' }}" data-type="Color">
                @foreach($productColors as $product_color)
                    @if(!empty($product_color))
                        <li style="background-color: {{$product_color->color_code}}"
                            data-value="{{ optional($product_color)->id }}"
                            data-display-value="{{ optional($product_color)->name }}"
                        ></li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    @foreach($available_attributes as $attribute => $options)
        <div class="value-input-area single-input-list pt-2 attribute_options_list d-flex align-items-center justify-content-left {{ $quickView ? 'quick-view-value-input-area' : '' }}">
            <span class="input-title fw-500 color-heading input-list">
                <strong class="color-light"> {{ $attribute }} </strong>
                <input readonly class="form--input value-size" type="text" value="">
                <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
            </span>
            <ul class="size-lists {{ $quickView ? 'quick-view-size-lists' : '' }}" data-type="{{ $attribute }}">
                @foreach($options as $option)
                    <li class="list"
                        data-value="{{ $option }}"
                        data-display-value="{{ $option }}"
                    > {{ $option }} </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>

{{-- Quantity + Add to Cart / Buy Now --}}
<div class="quantity-area pt-2">
    <div class="quantity-flex">
        <span class="quantity-title color-heading fw-500"> {{__('Quantity:')}} </span>
        <div class="product-quantity">
            <span class="{{ $quickView ? 'quick-view-' : '' }}substract substract"><i class="las la-minus"></i></span>
            <input class="{{ $quickView ? 'quick-view-' : '' }}quantity-input quantity-input qty_" type="number"
                   id="{{ $quickView ? 'quick-view-' : '' }}quantity" name="quantity" value="1">
            <span class="{{ $quickView ? 'quick-view-' : '' }}plus plus"><i class="las la-plus"></i></span>
        </div>
        @php
            if ($product?->inventory?->stock_count > 0) {
                $text_color = 'text-success';
                $text = __('Only!').' '.$product?->inventory?->stock_count.' '.__('Item Left');
            } else {
                $text_color = 'text-danger';
                $text = __('No Item Left!');
            }
        @endphp
        <a class="stock-available color-stock {{$text_color}}" href="javascript:void(0)"
           id="{{ $quickView ? 'quick_view_' : '' }}item_left" data-stock-text="{{$text}}"> {{$text}} </a>
    </div>

    @if($product?->inventory?->stock_count > 0)
        <div class="fn-pd-btn-group pt-4">
            <a href="javascript:void(0)"
               class="{{ $quickView ? 'quick_view_add_to_cart' : 'add_to_cart_single_page' }} fn-btn fn-btn-gold w-100 cart-loading">
                <i class="las la-shopping-cart"></i> {{__('Add to Cart')}}
            </a>
            <a href="javascript:void(0)"
               class="{{ $quickView ? 'quick_view_but_now' : 'but_now_single_page' }} fn-btn fn-btn-outline w-100 cart-loading">
                <i class="las la-bolt"></i> {{__('Buy Now')}}
            </a>
        </div>
    @endif
</div>

{{-- Delivery Options --}}
@if($product->product_delivery_option?->count())
    <div class="delivery-options delivery-parent mt-4">
        @foreach($product->product_delivery_option as $option)
            <div class="delivery-item d-flex">
                <div class="icon"><i class="{{ $option->icon }}"></i></div>
                <div class="content">
                    <h6 class="title">{{ $option->title }}</h6>
                    <p>{{ $option->sub_title }}</p>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Safe Checkout --}}
<div class="details-checkout-shop shop-border-top pt-4 mt-4">
    <span class="guaranteed-checkout fw-500 color-heading"> {{__('Guaranteed Safe Checkout')}} </span>
    @php
        $payment_gateway_images = \App\Models\PaymentGateway::where('status', 1)->permittedPaymentGateway()->get('image')->pluck('image');
    @endphp
    <ul class="payment-list mt-3">
        @foreach($payment_gateway_images as $image)
            <li class="single-list">
                <a href="javascript:void(0)">{!! render_image_markup_by_attachment_id($image) !!}</a>
            </li>
        @endforeach
    </ul>
</div>

{{-- Wishlist / Compare / Share --}}
<div class="wishlist-compare mt-4">
    <div class="wishlist-compare-btn">
        <a href="javascript:void(0)"
           class="{{ $quickView ? 'quick_view_add_to_wishlist' : 'add_to_wishlist_single_page' }} btn-wishlist share-icon fw-500">
            <span class="icon"><i class="lar la-heart"></i></span>
        </a>
        <a href="javascript:void(0)"
           class="btn-wishlist share-icon fw-500 {{ $quickView ? 'quick-view-' : '' }}compare-btn"
           data-product_id="{{$product->id}}"
           data-bs-toggle="tooltip" data-bs-placement="top"
           title="{{__('Add to Compare')}}">
            <span class="icon"><i class="las la-retweet"></i></span>
        </a>
    </div>
    <div class="wishlist-share social_share_parent">
        <a href="javascript:void(0)" class="share-icon fw-500">
            <span class="icon"><i class="las la-share-alt"></i></span>
        </a>
        @php
            $product_primary_image = get_attachment_image_by_id($product->image_id);
            $product_primary_image = $product_primary_image ? $product_primary_image['img_url'] : '';
        @endphp
        <ul class="social_share_wrapper_item">
            {!! single_post_share($product->slug, $product->name, $product_primary_image) !!}
        </ul>
    </div>
</div>

{{-- Custom Specifications --}}
@if($custom_specifications->isNotEmpty())
    <div class="quantity-area pt-4">
        <div class="quantity-flex">
            <span class="quantity-title color-heading fw-500"> {{__('Product Details:')}} </span>
        </div>
        <div class="specification-table-wrapper mt-3">
            <table class="specification-table">
                <thead>
                    <tr>
                        <th class="spec-title">{{ __('Custom Specifications') }}</th>
                        <th class="spec-value">{{ __('Value') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($custom_specifications as $spec)
                        <tr>
                            <td class="spec-title">{{ $spec->title }}</td>
                            <td class="spec-value">{{ $spec->value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
