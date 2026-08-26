@foreach($cart_data as $key => $data)
    <tr class="table-cart-row" data-product-id="{{$key}}" data-varinat-id="{{$data->variant_id}}">
        <td class="ff-jost" data-label="Product">
            <div class="product-name-table">
                <div class="thumbs bg-image radius-10"
                    {!! render_background_image_markup_by_attachment_id($data?->options?->image) !!}></div>
                <div class="carts-contents">
                    @php
                        $slug = \Modules\Product\Entities\Product::select('id', 'slug')->find($data->id)?->slug;

                        if ($data->options->type == \App\Enums\ProductTypeEnum::DIGITAL)
                        {
                            $product = \Modules\DigitalProduct\Entities\DigitalProduct::find($data->id)->select('id', 'slug')->first();
                            $product_details_route = theme_product_url($slug);
                            $product_type = 'Digital';
                        } else {
                            $product = \Modules\Product\Entities\Product::find($data->id)->select('id', 'slug')->first();
                            $product_details_route = theme_product_url($slug);
                            $product_type = 'Normal';
                        }
                    @endphp

                    <a href="{{theme_product_url($slug)}}" class="name-title"> {{$data->name}} </a>
                    <span class="cs-cart-type-badge">{{ $product_type }}</span>

                    @php $__meta = theme_cart_item_meta($data); @endphp
                    @if($__meta)<span class="name-subtitle d-block mt-2">{{ $__meta }}</span>@endif
                </div>
            </div>
        </td>
        <td class="price-td" data-label="Price"> {{amount_with_currency_symbol(calculatePrice($data->price, $data->options))}} </td>
        <td class="ff-jost" data-label="Quantity">
            <div class="product-quantity">
                                    <span class="substract">
                                        <i class="las la-minus"></i>
                                    </span>
                <input class="quantity-input" type="number" value="{{$data->qty}}">
                <span class="plus">
                                        <i class="las la-plus"></i>
                                    </span>
            </div>
        </td>
        @php
            $subtotal = calculatePrice($data->price * $data->qty, $data->options);
        @endphp
        <td class="price-td" data-label="Subtotal"> {{amount_with_currency_symbol($subtotal)}} </td>
        <td class="ff-jost" data-label="Close" data-product_hash_id="{{$data->rowId}}">
            <div class="close-table-cart">
                <i class="las la-times"></i>
            </div>
        </td>
    </tr>
@endforeach
