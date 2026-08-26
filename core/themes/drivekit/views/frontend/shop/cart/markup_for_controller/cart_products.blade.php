@foreach($cart_data as $key => $data)
@php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
<tr class="dk-cart-row table-cart-row" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
    <td class="dk-cart-td">
        <div class="dk-cart-product">
            <div class="dk-cart-thumb">
                {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
            </div>
            <div>
                <a href="{{ theme_product_url($slug) }}" class="dk-cart-product-link">{{ $data->name }}</a>
                @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                <div class="dk-cart-variant">
                    @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                    @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                    @if($data?->options?->attributes) @foreach($data->options->attributes as $attrKey => $attrVal) · {{ $attrKey }}: {{ $attrVal }} @endforeach @endif
                </div>
                @endif
            </div>
        </div>
    </td>
    <td class="dk-cart-td"><span class="dk-cart-price">{{ amount_with_currency_symbol($data->price) }}</span></td>
    <td class="dk-cart-td">
        <div class="dk-qty-control">
            @if(!$wishlist)
                <button type="button" class="substract dk-qty-btn"><i class="mdi mdi-minus"></i></button>
            @endif
            <input class="quantity-input dk-qty-input" type="number" value="{{ $data->qty }}" min="1" {{ $wishlist ? 'disabled readonly' : '' }}>
            @if(!$wishlist)
                <button type="button" class="plus dk-qty-btn"><i class="mdi mdi-plus"></i></button>
            @endif
        </div>
    </td>
    <td class="dk-cart-td"><span class="dk-cart-total-val">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
    <td class="dk-cart-td dk-cart-action-cell {{ $wishlist ? 'dk-cart-action-wishlist' : '' }}" data-product_hash_id="{{ $data->rowId }}">
        @if($wishlist)
            <button type="button" class="move-to-wishlist dk-btn dk-btn-ghost dk-btn-sm" title="{{ __('Move to Cart') }}" style="gap:4px;padding:6px 10px;">
                <i class="mdi mdi-cart-arrow-down"></i> {{ __('Add to Cart') }}
            </button>
        @else
            @auth
            <button class="save-for-later-btn" type="button"
                    data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
                <i class="mdi mdi-heart-outline"></i>
            </button>
            @endauth
        @endif
        <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}">
            <button type="button" class="dk-remove-btn"><i class="mdi mdi-close"></i></button>
        </div>
    </td>
</tr>
@endforeach
