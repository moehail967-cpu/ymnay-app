{{-- Maison: product options (variants, qty, ATC, Buy Now, wishlist) --}}

{{-- Size variants --}}
@if($productSizes->count() > 0 && !empty(current(current($productSizes))))
<div class="value-input-area" style="margin-bottom:20px;">
    <div style="font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:10px;">
        {{ __('Size') }}
    </div>
    <input type="hidden" name="size" id="selected_size">
    <ul class="ms-attr-list size-lists select-list" data-type="Size" style="list-style:none;margin:0;padding:0;display:flex;flex-wrap:wrap;gap:8px;">
        @foreach($productSizes as $size)
            @if(!empty($size))
            <li class="list"
                data-value="{{ optional($size)->id }}"
                data-display-value="{{ optional($size)->name }}"
                style="padding:6px 14px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:12px;font-weight:600;color:var(--ms-charcoal);cursor:pointer;transition:all .2s;background:#fff;"
                onmouseover="if(!this.classList.contains('selected')){this.style.borderColor='var(--ms-linen)';this.style.color='var(--ms-linen-d)';}"
                onmouseout="if(!this.classList.contains('selected')){this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-charcoal)';}">
                {{ optional($size)->size_code ?? optional($size)->name }}
            </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Color variants --}}
@if($productColors->count() > 0 && current(current($productColors)))
<div class="value-input-area" style="margin-bottom:20px;">
    <div style="font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:10px;">
        {{ __('Color') }}
    </div>
    <input type="hidden" name="color" id="selected_color">
    <ul class="ms-attr-list size-lists select-list color-list" data-type="Color" style="list-style:none;margin:0;padding:0;display:flex;flex-wrap:wrap;gap:10px;">
        @foreach($productColors as $color)
            @if(!empty($color))
            <li class="list"
                style="width:28px;height:28px;border-radius:50%;background-color:{{ $color->color_code }};border:2px solid var(--ms-border);cursor:pointer;transition:transform .2s,box-shadow .2s;position:relative;"
                data-value="{{ optional($color)->id }}"
                data-display-value="{{ optional($color)->name }}"
                title="{{ optional($color)->name }}"
                onmouseover="this.style.transform='scale(1.15)';this.style.boxShadow='0 0 0 3px var(--ms-linen)'"
                onmouseout="if(!this.classList.contains('selected')){this.style.transform='scale(1)';this.style.boxShadow='none';}">
            </li>
            @endif
        @endforeach
    </ul>
</div>
@endif

{{-- Custom attributes --}}
@foreach($available_attributes as $attribute => $options)
<div class="value-input-area" style="margin-bottom:20px;">
    <div style="font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:10px;">
        {{ $attribute }}
    </div>
    <ul class="ms-attr-list size-lists select-list" data-type="{{ $attribute }}" style="list-style:none;margin:0;padding:0;display:flex;flex-wrap:wrap;gap:8px;">
        @foreach($options as $option)
        <li class="list"
            data-value="{{ $option }}"
            data-display-value="{{ $option }}"
            style="padding:6px 14px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:12px;font-weight:600;color:var(--ms-charcoal);cursor:pointer;transition:all .2s;background:#fff;"
            onmouseover="if(!this.classList.contains('selected')){this.style.borderColor='var(--ms-linen)';this.style.color='var(--ms-linen-d)';}"
            onmouseout="if(!this.classList.contains('selected')){this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-charcoal)';}">
            {{ $option }}
        </li>
        @endforeach
    </ul>
</div>
@endforeach

{{-- Quantity --}}
<div style="margin-bottom:24px;">
    <div style="font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:10px;">
        {{ __('Quantity') }}
    </div>
    <div class="ms-qty-wrap">
        <button type="button" class="ms-qty-btn substract">
            <i class="mdi mdi-minus"></i>
        </button>
        <input type="number" id="quantity" class="ms-qty-input quantity-input" value="1" min="1">
        <button type="button" class="ms-qty-btn plus">
            <i class="mdi mdi-plus"></i>
        </button>
    </div>
</div>

{{-- ATC + Buy Now --}}
<div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px;">
    <button type="button" class="ms-btn-full ms-btn-dark add_to_cart_single_page">
        <i class="mdi mdi-cart-plus" style="margin-right:8px;font-size:18px;"></i>
        {{ __('Add to Cart') }}
    </button>
    <button type="button" class="ms-btn-full ms-btn-border but_now_single_page">
        <i class="mdi mdi-lightning-bolt" style="margin-right:8px;font-size:18px;"></i>
        {{ __('Buy Now') }}
    </button>
</div>

{{-- Delivery Options --}}
@if($product->product_delivery_option != null && $product->product_delivery_option->count())
<div style="display:flex;flex-direction:column;gap:10px;padding:14px;background:var(--ms-ivory,#fafaf8);border:1px solid var(--ms-border,#e8e4dc);border-radius:var(--ms-radius,4px);margin-bottom:20px;">
    @foreach($product->product_delivery_option as $option)
    <div style="display:flex;align-items:flex-start;gap:12px;">
        <span style="font-size:18px;color:var(--ms-linen-d,#8B7355);flex-shrink:0;margin-top:2px;"><i class="{{ $option->icon }}"></i></span>
        <div>
            <strong style="display:block;font-size:12px;font-weight:700;color:var(--ms-charcoal,#1a1a1a);letter-spacing:.02em;">{{ $option->title }}</strong>
            <span style="display:block;font-size:11px;color:var(--ms-muted,#888);margin-top:1px;">{{ $option->sub_title }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Action links --}}
<div style="display:flex;gap:16px;flex-wrap:wrap;">
    <button class="add_to_wishlist_single_page"
            style="background:transparent;border:0;padding:0;font-size:13px;color:var(--ms-muted);cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:color .2s;"
            onmouseover="this.style.color='var(--ms-linen-d)'"
            onmouseout="this.style.color='var(--ms-muted)'">
        <i class="mdi mdi-heart-outline"></i> {{ __('Add to Wishlist') }}
    </button>
    <button class="compare-btn"
            data-product_id="{{ $product->id }}"
            style="background:transparent;border:0;padding:0;font-size:13px;color:var(--ms-muted);cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:color .2s;"
            onmouseover="this.style.color='var(--ms-linen-d)'"
            onmouseout="this.style.color='var(--ms-muted)'">
        <i class="mdi mdi-compare-horizontal"></i> {{ __('Compare') }}
    </button>
</div>
