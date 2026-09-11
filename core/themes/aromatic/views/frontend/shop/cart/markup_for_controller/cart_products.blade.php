@foreach($cart_data as $key => $data)
    @php
        if ($data->options->type == \App\Enums\ProductTypeEnum::DIGITAL) {
            $slug = \Modules\DigitalProduct\Entities\DigitalProduct::select('id', 'slug')->find($data->id)?->slug;
            $product_type = 'Digital';
        } else {
            $slug = \Modules\Product\Entities\Product::select('id', 'slug')->find($data->id)?->slug;
            $product_type = 'Normal';
        }
        $product_details_route = dynamicRoute($slug);
        $subtotal = calculatePrice($data->price, $data->options) * $data->qty;
    @endphp
    <tr class="table-cart-row ar-cart-row" data-product-id="{{ $key }}" data-varinat-id="{{ $data->variant_id }}">

        {{-- Product --}}
        <td data-label="{{ __('Product') }}">
            <div class="ar-cart-product">
                <div class="ar-cart-thumb bg-image"
                    {!! render_background_image_markup_by_attachment_id($data?->options?->image) !!}></div>
                <div class="ar-cart-info carts-contents">
                    <a href="{{ $product_details_route }}" class="ar-cart-name">{{ $data->name }}</a>
                    <span class="ar-cart-type-badge">{{ $product_type }}</span>
                    <span class="ar-cart-variants name-subtitle">
                        @if($data?->options?->color_name)
                            {{ __('Color:') }} {{ $data?->options?->color_name }}@if($data?->options?->size_name),@endif
                        @endif
                        @if($data?->options?->size_name)
                            {{ __('Size:') }} {{ $data?->options?->size_name }}
                        @endif
                        @if($data?->options?->attributes)
                            @foreach($data?->options?->attributes as $akey => $attribute)
                                {{ $akey.':' }} {{ $attribute }}{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        @endif
                    </span>
                </div>
            </div>
        </td>

        {{-- Price --}}
        <td class="price-td" data-label="{{ __('Price') }}">
            <span class="ar-cart-price">{{ amount_with_currency_symbol(calculatePrice($data->price, $data->options)) }}</span>
        </td>

        {{-- Quantity --}}
        <td data-label="{{ __('Quantity') }}">
            @if($data->options->type == \App\Enums\ProductTypeEnum::PHYSICAL)
                <div class="ar-qty-wrap product-quantity">
                    <button class="ar-qty-btn substract" type="button"><i class="las la-minus"></i></button>
                    <input class="ar-qty-input quantity-input" type="number" value="{{ $data->qty }}">
                    <button class="ar-qty-btn plus" type="button"><i class="las la-plus"></i></button>
                </div>
            @else
                <div class="ar-qty-wrap product-quantity">
                    <input class="quantity-input" type="hidden" value="1">
                    <span class="ar-cart-price">1</span>
                </div>
            @endif
        </td>

        {{-- Subtotal --}}
        <td class="price-td" data-label="{{ __('Subtotal') }}">
            <span class="ar-cart-price ar-cart-subtotal">{{ float_amount_with_currency_symbol($subtotal) }}</span>
        </td>

        {{-- Action --}}
        <td class="ff-jost" data-label="{{ __('Action') }}" data-product_hash_id="{{ $data->rowId }}">
            <button class="ar-cart-icon-btn ar-cart-wish-btn move-to-wishlist" title="{{ __('Save to Wishlist') }}">
                <i class="las la-heart"></i>
            </button>
            <button class="ar-cart-icon-btn ar-cart-remove close-table-cart" title="{{ __('Remove') }}">
                <i class="las la-times"></i>
            </button>
        </td>

    </tr>
@endforeach
