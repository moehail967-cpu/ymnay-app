<div class="hf-cart-items {{ $wishlist ? 'hf-cart-items--full' : '' }}">
    <div class="hf-cart-table-wrap">
        <table class="hf-cart-table">
            <thead>
                <tr>
                    <th>{{__('Product')}}</th>
                    <th>{{__('Price')}}</th>
                    <th>{{__('Quantity')}}</th>
                    <th>{{__('Subtotal')}}</th>
                    <th>{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody id="cart_tbody">
            @foreach($cart_data as $key => $data)
                @php
                    if ($data->options->type == \App\Enums\ProductTypeEnum::DIGITAL)
                        {
                            $slug = \Modules\DigitalProduct\Entities\DigitalProduct::select('id', 'slug')->find($data->id)?->slug;
                        } else {
                            $slug = \Modules\Product\Entities\Product::select('id', 'slug')->find($data->id)?->slug;
                        }
                @endphp
                <tr class="hf-cart-row" data-product-id="{{$key}}" data-varinat-id="{{$data->variant_id}}">
                    <td data-label="{{__('Product')}}">
                        <div class="hf-cart-product">
                            <div class="hf-cart-thumb"
                                {!! render_background_image_markup_by_attachment_id($data?->options?->image) !!}></div>
                            <div>
                                @php
                                    if ($data->options->type == \App\Enums\ProductTypeEnum::DIGITAL)
                                    {
                                        $product = \Modules\DigitalProduct\Entities\DigitalProduct::find($data->id)->select('id', 'slug')->first();
                                        $product_details_route = dynamicRoute($slug);
                                        $product_type = 'Digital';
                                    } else {
                                        $product = \Modules\Product\Entities\Product::find($data->id)->select('id', 'slug')->first();
                                        $product_details_route = dynamicRoute($slug);
                                        $product_type = 'Normal';
                                    }
                                @endphp
                                <a href="{{$product_details_route}}" class="hf-cart-name">{{$data->name}}</a>
                                <span class="hf-cart-type">{{$product_type}}</span>
                                <span class="hf-cart-meta d-block mt-1">
                                    @if($data?->options?->color_name)
                                        {{__('Color:')}} {{$data?->options?->color_name}} ,
                                    @endif
                                    @if($data?->options?->size_name)
                                        {{__('Size:')}} {{$data?->options?->size_name}}
                                    @endif
                                    @if($data?->options?->attributes)
                                        <br>
                                        @foreach($data?->options?->attributes as $key => $attribute)
                                            {{$key.':'}} {{$attribute}}{{!$loop->last ? ',' : ''}}
                                        @endforeach
                                    @endif
                                </span>
                            </div>
                        </div>
                    </td>
                    <td data-label="{{__('Price')}}">{{amount_with_currency_symbol(calculatePrice($data->price, $data->options))}}</td>
                    <td data-label="{{__('Quantity')}}">
                        @if($data->options->type == \App\Enums\ProductTypeEnum::PHYSICAL)
                            <div class="hf-qty">
                                @if(!$wishlist)
                                <span class="substract"><i class="las la-minus"></i></span>
                                @endif
                                <input class="quantity-input" {{ $wishlist ? "disabled='true' readonly='true'" : "" }} type="number" value="{{$data->qty}}">
                                @if(!$wishlist)
                                <span class="plus"><i class="las la-plus"></i></span>
                                @endif
                            </div>
                        @else
                            <div class="hf-qty">
                                <input class="quantity-input" type="hidden" value="1">
                                <span style="padding:0 12px;font-size:14px;">1</span>
                            </div>
                        @endif
                    </td>
                    @php $subtotal = calculatePrice($data->price, $data->options) * $data->qty; @endphp
                    <td data-label="{{__('Subtotal')}}">{{float_amount_with_currency_symbol($subtotal)}}</td>
                    <td data-label="{{__('Action')}}" class="hf-cart-action" data-product_hash_id="{{$data->rowId}}">
                        <div class="hf-cart-action-inner">
                            @if($wishlist)
                                <div class="move-to-wishlist" title="{{__('Move to Cart')}}">
                                    <i class="las la-cart-arrow-down"></i>
                                </div>
                            @endif
                            <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" title="{{__('Remove')}}">
                                <i class="las la-times"></i>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="hf-cart-actions mt-4">
        <a href="{{theme_shop_url()}}" class="hf-btn hf-btn-outline">{{__('Continue Shopping')}}</a>
        @if(!$wishlist)
            <a href="javascript:void(0)" class="hf-btn hf-btn-ghost clear-cart-btn">{{__('Clear Cart')}}</a>
        @endif
    </div>
</div>
