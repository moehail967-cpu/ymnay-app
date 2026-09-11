@foreach($cart_data as $key => $data)
    @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
    <tr class="table-cart-row" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
        <td>
            <div class="d-flex align-items-center gap-3">
                <div class="bp-cart-img">{!! render_image_markup_by_attachment_id($data?->options?->image) !!}</div>
                <div>
                    <a href="{{ theme_product_url($slug) }}" class="bp-cart-item-name">{{ $data->name }}</a>
                    @php $__meta = theme_cart_item_meta($data); @endphp
                    @if($__meta)<div class="bp-cart-item-meta">{{ $__meta }}</div>@endif
                </div>
            </div>
        </td>
        <td><span class="bp-cart-price">{{ amount_with_currency_symbol($data->price) }}</span></td>
        <td>
            <div class="bp-qty">
                <button type="button" class="substract"><i class="las la-minus"></i></button>
                <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1">
                <button type="button" class="plus"><i class="las la-plus"></i></button>
            </div>
        </td>
        <td><span class="bp-cart-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
        <td class="ff-jost" data-product_hash_id="{{ $data->rowId }}">
            <div class="close-table-cart" style="cursor:pointer;">
                <button class="bp-remove-btn" type="button"><i class="las la-times"></i></button>
            </div>
        </td>
    </tr>
@endforeach
