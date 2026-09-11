{{--
    GlowLab product-options partial.
    Title / price / stock are rendered by product-details.blade.php.
    This partial renders the interactive form controls.
--}}

{{-- Campaign countdown / expired notice --}}
@if($campaign_product !== null && $campaign_product->status !== 'draft')
    @if(isset($campaign_active) && $campaign_active)
        <div class="global-timer mb-4"></div>
    @else
        <div style="font-size:12px;color:var(--gl-muted);background:var(--gl-blush);border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:10px 14px;margin-bottom:16px;">
            {{ __('The Campaign Is Over Or Not Yet Started') }}
        </div>
    @endif
@endif

{{-- Sizes --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
    <div class="value-input-area single-input-list mb-3 size_list">
        <span class="input-title" style="display:block;font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:8px;">
            {{ __('Size:') }}
            <input readonly class="form--input value-size" name="size" type="text" value="">
            <input type="hidden" id="selected_size">
        </span>
        <ul class="size-lists select-list" data-type="Size" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;">
            @foreach($productSizes as $product_size)
                @if(!empty($product_size))
                    <li class="list"
                        style="border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;"
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
    <div class="value-input-area single-input-list mb-3 color_list">
        <span class="input-title" style="display:block;font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:8px;">
            {{ __('Color:') }}
            <input readonly class="form--input value-size" name="color" type="text" value="">
            <input type="hidden" id="selected_color">
        </span>
        <ul class="size-lists color-list" data-type="Color" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;">
            @foreach($productColors as $product_color)
                @if(!empty($product_color))
                    <li style="background-color:{{ $product_color->color_code }};width:28px;height:28px;border-radius:50%;border:2px solid var(--gl-border);cursor:pointer;transition:transform .2s;"
                        data-value="{{ optional($product_color)->id }}"
                        data-display-value="{{ optional($product_color)->name }}"></li>
                @endif
            @endforeach
        </ul>
    </div>
@endif

{{-- Custom attributes --}}
@foreach($available_attributes as $attribute => $options)
    <div class="value-input-area single-input-list mb-3 attribute_options_list">
        <span class="input-title" style="display:block;font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:8px;">
            {{ $attribute }}
            <input readonly class="form--input value-size" type="text" value="">
            <input type="hidden" id="selected_attribute_option" name="selected_attribute_option">
        </span>
        <ul class="size-lists" data-type="{{ $attribute }}" style="display:flex;flex-wrap:wrap;gap:8px;list-style:none;padding:0;margin:0;">
            @foreach($options as $option)
                <li class="list"
                    style="border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;"
                    data-value="{{ $option }}" data-display-value="{{ $option }}">{{ $option }}</li>
            @endforeach
        </ul>
    </div>
@endforeach

{{-- Quantity + ATC --}}
<div class="mb-3" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
    {{-- Qty stepper --}}
    <div style="display:flex;align-items:center;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;">
        <button type="button" class="substract"
                style="width:38px;height:42px;border:none;background:var(--gl-blush);font-size:16px;cursor:pointer;color:var(--gl-dark);">−</button>
        <input type="number" class="quantity-input qty_"
               id="quantity" name="quantity" value="1" min="1"
               style="width:52px;height:42px;border:none;border-left:1px solid var(--gl-border);border-right:1px solid var(--gl-border);text-align:center;font-weight:600;font-size:14px;background:#fff;">
        <button type="button" class="plus"
                style="width:38px;height:42px;border:none;background:var(--gl-blush);font-size:16px;cursor:pointer;color:var(--gl-dark);">+</button>
    </div>
    @php
        $text_color = $product?->inventory?->stock_count > 0 ? '#27AE60' : '#E53935';
        $qty_text   = $product?->inventory?->stock_count > 0
            ? __('Only!').' '.$product->inventory->stock_count.' '.__('Item Left')
            : __('No Item Left!');
    @endphp
    <span id="item_left" data-stock-text="{{ $qty_text }}"
          style="font-size:12px;font-weight:600;color:{{ $text_color }};">
        {{ $qty_text }}
    </span>
</div>

<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
    <a href="javascript:void(0)"
       class="add_to_cart_single_page cart-loading gl-btn gl-btn-primary">
        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
    </a>
    <a href="javascript:void(0)"
       class="but_now_single_page cart-loading gl-btn gl-btn-outline">
        {{ __('Buy Now') }}
    </a>
</div>

{{-- Delivery Options --}}
@if($product->product_delivery_option != null && $product->product_delivery_option->count())
<div style="display:flex;flex-direction:column;gap:10px;padding:14px;background:var(--gl-blush,#fdf5f0);border-radius:var(--gl-radius,8px);border:1px solid var(--gl-border,#e8e0d8);margin-bottom:16px;">
    @foreach($product->product_delivery_option as $option)
    <div style="display:flex;align-items:flex-start;gap:12px;">
        <span style="font-size:18px;color:var(--gl-gold,#B8860B);flex-shrink:0;margin-top:2px;"><i class="{{ $option->icon }}"></i></span>
        <div>
            <strong style="display:block;font-size:12px;font-weight:700;color:var(--gl-dark,#1a1a1a);">{{ $option->title }}</strong>
            <span style="display:block;font-size:11px;color:var(--gl-muted,#888);margin-top:1px;">{{ $option->sub_title }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Wishlist / Compare --}}
<div style="display:flex;gap:16px;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--gl-border);">
    <a href="javascript:void(0)"
       class="add_to_wishlist_single_page gl-icon-btn"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;">
        <i class="las la-heart" style="font-size:18px;"></i> {{ __('Wishlist') }}
    </a>
    <a href="javascript:void(0)"
       class="compare-btn gl-icon-btn"
       data-product_id="{{ $product->id }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;">
        <i class="las la-retweet" style="font-size:18px;"></i> {{ __('Compare') }}
    </a>
    <a href="javascript:void(0)"
       class="share-icon gl-icon-btn social_share_parent"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;position:relative;">
        <i class="las la-share-alt" style="font-size:18px;"></i> {{ __('Share') }}
        @php
            $product_primary_image = get_attachment_image_by_id($product->image_id);
            $product_primary_image = $product_primary_image ? $product_primary_image['img_url'] : '';
        @endphp
        <ul class="social_share_wrapper_item">
            {!! single_post_share(URL::current(), $product->name, $product_primary_image) !!}
        </ul>
    </a>
</div>

{{-- SKU / Category --}}
<div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--gl-border);font-size:12px;color:var(--gl-muted);">
    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:6px;">
        @if($product?->category?->name)
        <li>
            <span style="font-weight:700;color:var(--gl-dark);">{{ __('Category:') }}</span>
            <a href="{{ theme_category_url($product->category->slug) }}" style="color:var(--gl-gold);text-decoration:none;">{{ $product->category->name }}</a>
            @if($product?->subCategory?->slug)
                | <a href="{{ theme_category_url($product->subCategory->slug) }}" style="color:var(--gl-gold);text-decoration:none;">{{ $product->subCategory->name }}</a>
            @endif
        </li>
        @endif
        @if($product?->inventory?->sku)
        <li>
            <span style="font-weight:700;color:var(--gl-dark);">{{ __('SKU:') }}</span>
            {{ $product->inventory->sku }}
        </li>
        @endif
    </ul>
</div>
