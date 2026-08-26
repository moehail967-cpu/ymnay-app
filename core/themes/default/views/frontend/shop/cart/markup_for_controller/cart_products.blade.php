@foreach($cart_data as $key => $data)
    <tr class="table-cart-row" data-product-id="{{$key}}" data-varinat-id="{{$data->variant_id}}">
        <td class="ff-jost" data-label="Product">
            <div class="product-name-table">
                <div class="thumbs bg-image radius-10"
                    {!! render_background_image_markup_by_attachment_id($data?->options?->image) !!}></div>
                <div class="carts-contents">
                    @php
                        $slug = \Modules\Product\Entities\Product::select('id', 'slug')->find($data->id)?->slug;
                    @endphp

                    <a href="{{dynamicRoute($slug)}}" class="name-title"> {{$data->name}} </a>
                    @php $__meta = theme_cart_item_meta($data); @endphp
                    @if($__meta)<span class="name-subtitle d-block mt-2">{{ $__meta }}</span>@endif
                </div>
            </div>
        </td>
        <td class="price-td" data-label="Price"> {{amount_with_currency_symbol($data->price)}} </td>
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
            $subtotal = $data->price * $data->qty;
        @endphp
        <td class="price-td" data-label="Subtotal"> {{amount_with_currency_symbol($subtotal)}} </td>
        <td class="ff-jost" data-label="Close" data-product_hash_id="{{$data->rowId}}">
            <div class="close-table-cart">
                <i class="las la-times"></i>
            </div>
        </td>
    </tr>
@endforeach
