{{-- TinyNest uses a card-based layout, not a table --}}
{{-- This file is rendered server-side and returned as data.markup for #tn_cart_items_wrap --}}
{{-- NOTE: TinyNest cart does NOT use AJAX table replacement — it has its own qty update JS.
     This file exists for consistency but TinyNest qty updates are handled inline via
     JSON response (res.subtotal / res.cart_total / res.cart_subtotal). --}}
@foreach($cart_data as $key => $data)
@php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
<div class="tn-cart-row table-cart-row" data-row-id="{{ $data->rowId }}" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
    <div class="tn-cart-product">
        <div class="tn-cart-img">
            {!! render_image_markup_by_attachment_id($data->options->image ?? null, 'tn-cart-thumb') !!}
        </div>
        <div>
            <div class="tn-cart-name">
                <a href="{{ theme_product_url($slug) }}" style="text-decoration:none;color:inherit;">{{ $data->name }}</a>
            </div>
            @php
                $tn_meta_parts = [];
                if ($data?->options?->color_name) $tn_meta_parts[] = __('Color') . ': ' . $data->options->color_name;
                if ($data?->options?->size_name)  $tn_meta_parts[] = __('Size')  . ': ' . $data->options->size_name;
                foreach ((array)($data?->options?->attributes ?? []) as $attrK => $attrV) {
                    if ($attrV !== null && $attrV !== '') $tn_meta_parts[] = ucfirst($attrK) . ': ' . $attrV;
                }
                $tn_meta = implode(' · ', $tn_meta_parts);
            @endphp
            @if($tn_meta)<small class="tn-cart-meta">{{ $tn_meta }}</small>@endif
        </div>
    </div>
    <div class="tn-cart-cell text-center">
        {{ amount_with_currency_symbol($data->price) }}
    </div>
    <div class="tn-cart-cell text-center">
        <div class="tn-qty-ctrl">
            <button class="tn-qty-btn tn-cart-qty-minus" data-row="{{ $data->rowId }}">–</button>
            <input type="number" class="tn-qty-input tn-cart-qty-input" value="{{ $data->qty }}"
                   data-row="{{ $data->rowId }}" min="1">
            <button class="tn-qty-btn tn-cart-qty-plus" data-row="{{ $data->rowId }}">+</button>
        </div>
    </div>
    <div class="tn-cart-cell text-center tn-cart-subtotal" data-row="{{ $data->rowId }}">
        {{ amount_with_currency_symbol($data->subtotal()) }}
    </div>
    <div class="tn-cart-cell text-center">
        @auth
        <button class="save-for-later-btn" type="button"
                data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
            <i class="las la-heart"></i>
        </button>
        @endauth
        <button class="tn-remove-btn tn-cart-remove-btn" data-row="{{ $data->rowId }}"
                title="{{ __('Remove') }}">
            <i class="las la-times"></i>
        </button>
    </div>
</div>
@endforeach
