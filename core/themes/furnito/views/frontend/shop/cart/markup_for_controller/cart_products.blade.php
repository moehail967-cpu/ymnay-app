@foreach($cart_data as $key => $data)
    @php
        $is_digital = $data->options->type == \App\Enums\ProductTypeEnum::DIGITAL;
        $slug = $is_digital
            ? \Modules\DigitalProduct\Entities\DigitalProduct::select('id','slug')->find($data->id)?->slug
            : \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug;
        $product_url = dynamicRoute($slug);
        $img_data    = $data?->options?->image ? get_attachment_image_by_id($data->options->image, 'thumbnail') : null;
        $img_url     = $img_data['img_url'] ?? theme_placeholder_image();
        $unit_price  = calculatePrice($data->price, $data->options);
        $subtotal    = $unit_price * $data->qty;
        $meta        = theme_cart_item_meta($data);
    @endphp
    <tr class="fn-cart-row" data-product-id="{{ $key }}" data-variant-id="{{ $data->variant_id ?? '' }}">
        <td>
            <div class="fn-cart-img-wrap">
                <img src="{{ $img_url }}" alt="{{ $data->name }}" class="fn-cart-img">
                <div>
                    <a href="{{ $product_url }}" class="fn-cart-name">{{ $data->name }}</a>
                    @if($is_digital)
                        <span class="fn-cart-type">{{ __('Digital') }}</span>
                    @endif
                    @if($meta)
                        <div class="fn-cart-meta">{{ $meta }}</div>
                    @endif
                </div>
            </div>
        </td>
        <td class="fn-cart-price">{{ amount_with_currency_symbol($unit_price) }}</td>
        <td>
            @if(!$is_digital)
                <div class="fn-qty">
                    <button class="fn-qty-btn fn-qty-minus" type="button"><i class="las la-minus"></i></button>
                    <input class="fn-qty-input" type="number" value="{{ $data->qty }}">
                    <button class="fn-qty-btn fn-qty-plus" type="button"><i class="las la-plus"></i></button>
                </div>
            @else
                <span class="fn-cart-price">1</span>
            @endif
        </td>
        <td class="fn-cart-price">{{ float_amount_with_currency_symbol($subtotal) }}</td>
        <td>
            <div class="fn-cart-td-actions">
                <button class="fn-cart-remove" type="button"
                        data-hash="{{ $data->rowId }}"
                        title="{{ __('Remove') }}">
                    <i class="las la-times"></i>
                </button>
            </div>
        </td>
    </tr>
@endforeach
