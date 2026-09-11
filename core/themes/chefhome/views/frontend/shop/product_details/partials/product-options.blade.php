{{--
    ChefHome product-options override.
    Title, stars, stock, price, and campaign countdown are already
    rendered by product-details.blade.php — only form controls below.
--}}
<div class="value-input-area">
    @if($productSizes->count() > 0 && !empty(current(current($productSizes))))
        <div class="value-input-area single-input-list mt-4 size_list">
            <span class="input-title">
                <strong>{{ __('Size:') }}</strong>
                <input readonly class="form--input value-size" name="size" type="text" value="">
                <input type="hidden" id="selected_size">
            </span>
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

    @if($productColors->count() > 0 && current(current($productColors)))
        <div class="value-input-area single-input-list mt-4 color_list">
            <span class="input-title">
                <strong>{{ __('Color:') }}</strong>
                <input readonly class="form--input value-size" name="color" type="text" value="">
                <input type="hidden" id="selected_color">
            </span>
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

    @foreach($available_attributes as $attribute => $options)
        <div class="value-input-area single-input-list mt-4 attribute_options_list">
            <span class="input-title">
                <strong>{{ $attribute }}</strong>
                <input readonly class="form--input value-size" type="text" value="">
                <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
            </span>
            <ul class="size-lists" data-type="{{ $attribute }}">
                @foreach($options as $option)
                    <li class="list" data-value="{{ $option }}" data-display-value="{{ $option }}">{{ $option }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>

<div class="quantity-area mt-4">
    <div class="quantity-flex">
        <span class="quantity-title color-heading fw-500">{{ __('Quantity:') }}</span>
        <div class="product-quantity">
            <span class="substract"><i class="las la-minus"></i></span>
            <input class="quantity-input qty_" type="number" id="quantity" name="quantity" value="1">
            <span class="plus"><i class="las la-plus"></i></span>
        </div>
        @php
            $text_color = $product?->inventory?->stock_count > 0 ? 'text-success' : 'text-danger';
            $text       = $product?->inventory?->stock_count > 0
                ? __('Only!').' '.$product->inventory->stock_count.' '.__('Item Left')
                : __('No Item Left!');
        @endphp
        <a class="stock-available color-stock {{ $text_color }}" href="javascript:void(0)"
           id="item_left" data-stock-text="{{ $text }}">{{ $text }}</a>
    </div>
    <div class="quantity-btn mt-4">
        <div class="btn-wrapper">
            <a href="javascript:void(0)"
               class="add_to_cart_single_page ch-btn ch-btn-primary w-100 justify-content-center cart-loading">
                {{ __('Add to Cart') }}
            </a>
        </div>
        <div class="btn-wrapper">
            <a href="javascript:void(0)"
               class="but_now_single_page ch-btn ch-btn-outline w-100 justify-content-center cart-loading">
                {{ __('Buy Now') }}
            </a>
        </div>
    </div>
</div>

@if($product->product_delivery_option != null)
<div class="delivery-options delivery-parent mt-4">
    @foreach($product->product_delivery_option as $option)
    <div class="delivery-item d-flex gap-2 mb-2">
        <i class="{{ $option->icon }}" style="font-size:20px;color:var(--ch-red);flex-shrink:0;"></i>
        <div>
            <div style="font-weight:700;font-size:13px;color:var(--ch-dark);">{{ $option->title }}</div>
            <div style="font-size:12px;color:var(--ch-muted);">{{ $option->sub_title }}</div>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="wishlist-compare mt-4">
    <div class="wishlist-compare-btn">
        <a href="javascript:void(0)" class="add_to_wishlist_single_page ch-icon-btn" title="{{ __('Wishlist') }}">
            <i class="lar la-heart"></i>
        </a>
        <a href="javascript:void(0)" class="compare-btn ch-icon-btn"
           data-product_id="{{ $product->id }}" title="{{ __('Compare') }}">
            <i class="las la-retweet"></i>
        </a>
        <a href="javascript:void(0)" class="share-icon ch-icon-btn social_share_parent" title="{{ __('Share') }}">
            <i class="las la-share-alt"></i>
            @php
                $product_primary_image = get_attachment_image_by_id($product->image_id);
                $product_primary_image = $product_primary_image ? $product_primary_image['img_url'] : '';
            @endphp
            <ul class="social_share_wrapper_item">
                {!! single_post_share(URL::current(), $product->name, $product_primary_image) !!}
            </ul>
        </a>
    </div>
</div>

<div class="shop-details-stock shop-border-top pt-4 mt-4">
    <ul class="stock-category">
        <li class="category-list">
            <span class="list-item fw-600">
                @if($product?->category?->slug)
                    <a href="{{ theme_category_url($product->category->slug) }}">{{ $product?->category?->name }}</a>
                @endif
                @if($product?->subCategory?->slug)
                    | <a href="{{ theme_category_url($product->subCategory->slug) }}">{{ $product->subCategory->name }}</a>
                @endif
                @foreach($product->childCategory ?? [] as $child_category)
                    @if($child_category?->slug)
                        | <a href="{{ theme_category_url($child_category->slug) }}">{{ $child_category->name }}</a>
                    @endif
                @endforeach
            </span>
        </li>
        @if($product->uom != null)
        <li class="category-list">
            <span>{{ __('Unit:') }}</span>
            <span class="list-item fw-600">{{ $product?->uom?->quantity }} {{ $product?->uom?->uom_details?->name }}</span>
        </li>
        @endif
        <li class="category-list">
            <span>{{ __('SKU:') }}</span>
            <span class="list-item fw-600">{{ $product?->inventory?->sku }}</span>
        </li>
    </ul>
</div>
