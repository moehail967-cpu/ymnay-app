@foreach($cart_data as $key => $data)
@php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
<tr class="table-cart-row"
    data-product-id="{{ $key }}"
    data-variant-id="{{ $data->options->variant_id ?? '' }}"
    data-varinat-id="{{ $data->options->variant_id ?? '' }}">
    <td>
        <div class="d-flex align-items-center gap-3">
            <div class="vl-cart-thumb">
                {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
            </div>
            <div>
                <a href="{{ theme_product_url($slug) }}" class="vl-cart-name"
                   style="text-decoration:none;">{{ $data->name }}</a>
                @php
                    $metaParts = [];
                    if ($data?->options?->color_name) $metaParts[] = __('Color') . ': ' . $data->options->color_name;
                    if ($data?->options?->size_name)  $metaParts[] = __('Size')  . ': ' . $data->options->size_name;
                    foreach ((array)($data?->options?->attributes ?? []) as $attrK => $attrV) {
                        if ($attrV !== null && $attrV !== '') $metaParts[] = ucfirst($attrK) . ': ' . $attrV;
                    }
                @endphp
                @if(!empty($metaParts))
                <div class="vl-cart-meta">{{ implode(' · ', $metaParts) }}</div>
                @endif
            </div>
        </div>
    </td>
    <td class="vl-cart-price">{{ amount_with_currency_symbol($data->price) }}</td>
    <td>
        <div class="vl-qty-stepper">
            <button type="button" class="vl-qty-btn substract"><i class="las la-minus"></i></button>
            <input class="vl-qty-input quantity-input" type="number" value="{{ $data->qty }}" min="1">
            <button type="button" class="vl-qty-btn plus"><i class="las la-plus"></i></button>
        </div>
    </td>
    <td class="vl-cart-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</td>
    <td class="vl-cart-action-cell {{ $wishlist ? 'vl-cart-action-wishlist' : '' }}"
        data-product_hash_id="{{ $data->rowId }}">
        @if($wishlist)
            <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;color:var(--vl-champagne);">
                <i class="las la-cart-arrow-down" style="font-size:20px;"></i>
            </div>
        @else
            @auth
            <button class="save-for-later-btn" type="button"
                    data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
                <i class="las la-heart"></i>
            </button>
            @endauth
        @endif
        <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}">
            <button class="vl-remove-btn" type="button">
                <i class="las la-times"></i>
            </button>
        </div>
    </td>
</tr>
@endforeach
