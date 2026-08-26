@foreach($cart_data as $key => $data)
    <tr class="hf-cart-row" data-product-id="{{$key}}" data-varinat-id="{{$data->variant_id}}">
        <td data-label="{{__('Product')}}">
            <div class="hf-cart-product">
                <div class="hf-cart-thumb"
                    {!! render_background_image_markup_by_attachment_id($data?->options?->image) !!}></div>
                <div>
                    @php
                        $slug = \Modules\Product\Entities\Product::select('id', 'slug')->find($data->id)?->slug;
                        if ($data->options->type == \App\Enums\ProductTypeEnum::DIGITAL) {
                            $product = \Modules\DigitalProduct\Entities\DigitalProduct::find($data->id)->select('id', 'slug')->first();
                            $product_details_route = dynamicRoute($slug);
                            $product_type = 'Digital';
                        } else {
                            $product = \Modules\Product\Entities\Product::find($data->id)->select('id', 'slug')->first();
                            $product_details_route = dynamicRoute($slug);
                            $product_type = 'Normal';
                        }
                    @endphp
                    <a href="{{dynamicRoute($slug)}}" class="hf-cart-name">{{$data->name}}</a>
                    <span class="hf-cart-type">{{$product_type}}</span>
                    @php $__meta = theme_cart_item_meta($data); @endphp
                    @if($__meta)<span class="hf-cart-meta d-block mt-1">{{ $__meta }}</span>@endif
                </div>
            </div>
        </td>
        <td data-label="{{__('Price')}}">{{amount_with_currency_symbol(calculatePrice($data->price, $data->options))}}</td>
        <td data-label="{{__('Quantity')}}">
            <div class="hf-qty">
                <span class="substract"><i class="las la-minus"></i></span>
                <input class="quantity-input" type="number" value="{{$data->qty}}">
                <span class="plus"><i class="las la-plus"></i></span>
            </div>
        </td>
        @php $subtotal = calculatePrice($data->price * $data->qty, $data->options); @endphp
        <td data-label="{{__('Subtotal')}}">{{amount_with_currency_symbol($subtotal)}}</td>
        <td data-label="{{__('Action')}}" class="hf-cart-action" data-product_hash_id="{{$data->rowId}}">
            <div class="hf-cart-action-inner">
                <div class="close-table-cart" title="{{__('Remove')}}">
                    <i class="las la-times"></i>
                </div>
            </div>
        </td>
    </tr>
@endforeach
