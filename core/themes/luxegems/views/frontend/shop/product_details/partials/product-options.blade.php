{{-- LuxeGems: Product options (sizes, colors, qty, ATC, Buy Now, wishlist, compare) --}}
<form id="add-to-cart-form">
    @csrf

    {{-- Size options --}}
    @if(!empty($productSizes) && $productSizes->count() > 0 && !empty(current(current($productSizes))))
    <div class="mb-4">
        <span class="input-title">{{ __('Size') }}</span>
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
        <div class="value-input-area" data-type="Size"><input type="hidden" name="size" value=""></div>
    </div>
    @endif

    {{-- Color options --}}
    @if(!empty($productColors) && $productColors->count() > 0 && !empty(current(current($productColors))))
    <div class="mb-4">
        <span class="input-title">{{ __('Color') }}</span>
        <ul class="size-lists select-list" data-type="Color">
            @foreach($productColors as $color)
                @if(!empty($color))
                <li class="list"
                    data-value="{{ optional($color)->id }}"
                    data-display-value="{{ optional($color)->name }}">
                    {{ optional($color)->name }}
                </li>
                @endif
            @endforeach
        </ul>
        <div class="value-input-area" data-type="Color"><input type="hidden" name="color" value=""></div>
    </div>
    @endif

    {{-- Quantity --}}
    <div class="mb-4">
        <span class="input-title">{{ __('Quantity') }}</span>
        <div class="d-flex align-items-center gap-2" style="margin-top:8px;">
            <button type="button" class="substract lx-card-act-btn outline" style="padding:8px 14px;">
                <i class="las la-minus"></i>
            </button>
            <input type="number" name="quantity" id="quantity" value="1" min="1" class="quantity-input lg-form-control"
                   style="width:60px;text-align:center;">
            <button type="button" class="plus lx-card-act-btn outline" style="padding:8px 14px;">
                <i class="las la-plus"></i>
            </button>
        </div>
    </div>

    {{-- Hidden fields --}}
    <input type="hidden" name="product_id" value="{{ $product->id }}" class="form--input">
    <input type="hidden" name="variant_id" value="" class="form--input">
    <input type="hidden" name="campaign_id" value="{{ $campaign_product->campaign_id ?? '' }}" class="form--input">
    <input type="hidden" name="is_campaign" value="{{ empty($campaign_product) ? 0 : 1 }}" class="form--input">

    {{-- Action buttons --}}
    <div class="d-flex gap-3 flex-wrap">
        <button type="button" class="lx-btn lx-btn-primary add_to_cart_single_page">
            <i class="las la-shopping-bag"></i> {{ __('Add to Cart') }}
        </button>
        <button type="button" class="lx-btn lx-btn-outline but_now_single_page">
            <i class="las la-bolt"></i> {{ __('Buy Now') }}
        </button>
    </div>

    {{-- Delivery Options --}}
    @if($product->product_delivery_option != null && $product->product_delivery_option->count())
    <div class="mt-4 d-flex flex-column gap-2" style="padding:14px;background:#fafaf8;border:1px solid #e8e4dc;border-radius:4px;">
        @foreach($product->product_delivery_option as $option)
        <div class="d-flex align-items-start gap-3">
            <span style="font-size:18px;color:var(--lx-gold,#B8860B);flex-shrink:0;margin-top:2px;"><i class="{{ $option->icon }}"></i></span>
            <div>
                <strong style="display:block;font-size:12px;font-weight:700;color:var(--lx-dark,#1a1a1a);">{{ $option->title }}</strong>
                <span style="display:block;font-size:11px;color:var(--lx-muted,#888);margin-top:1px;">{{ $option->sub_title }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="d-flex gap-3 mt-3">
        <button type="button" class="lx-btn lx-btn-outline add_to_wishlist_single_page" style="padding:10px 20px;font-size:11px;">
            <i class="las la-heart"></i> {{ __('Wishlist') }}
        </button>
        <button type="button" class="lx-btn lx-btn-outline compare-btn" data-product_id="{{ $product->id }}" style="padding:10px 20px;font-size:11px;">
            <i class="las la-exchange-alt"></i> {{ __('Compare') }}
        </button>
    </div>
</form>
