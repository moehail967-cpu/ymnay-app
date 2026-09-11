<div class="col-xl-{{ $wishlist ? "10 mx-auto" : "8" }} mt-4">
    <div class="table-list-content table-cart-clear">
        <div class="table-responsive table-responsive--md">
            <table class="custom--table table-border radius-10">
                <thead class="head-bg">
                <tr>
                    <th> {{__('Product')}} </th>
                    <th> {{__('Price')}} </th>
                    <th> {{__('Quantity')}} </th>
                    <th> {{__('Subtotal')}} </th>
                    <th> {{__('Action')}} </th>
                </tr>
                </thead>
                <tbody id="cart_tbody">
                @foreach($cart_data as $key => $data)
                    <tr class="table-cart-row" data-product-id="{{$key}}" data-varinat-id="{{$data->variant_id}}">
                        <td class="ff-jost" data-label="Product">
                            <div class="product-name-table">
                                <div class="thumbs bg-image radius-10"
                                    {!! render_background_image_markup_by_attachment_id($data?->options?->image) !!}></div>
                                <div class="carts-contents">
                                    <a href="javascript:void(0)" class="name-title"> {{$data->name}} </a>
                                    @if($wishlist)
                                        @php $in_stock = ($stock_map ?? [])[(int)$data->id] ?? false; @endphp
                                        <span class="wl-stock-badge {{ $in_stock ? 'wl-in-stock' : 'wl-out-stock' }}">
                                            {{ $in_stock ? __('In Stock') : __('Out of Stock') }}
                                        </span>
                                    @endif
                                    <span class="name-subtitle d-block mt-2">
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
                        <td class="price-td" data-label="Price"> {{amount_with_currency_symbol($data->price)}} </td>
                        <td class="ff-jost" data-label="Quantity">
                            <div class="product-quantity">
                                @if(!$wishlist)
                                    <span class="substract">
                                        <i class="las la-minus"></i>
                                    </span>
                                @endif
                                <input class="quantity-input" {{ $wishlist ? "disabled='true' readonly='true'" : "" }} type="number" value="{{$data->qty}}">
                                @if(!$wishlist)
                                    <span class="plus">
                                        <i class="las la-plus"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                        @php
                            $subtotal = $data->price * $data->qty;
                        @endphp
                        <td class="price-td" data-label="Subtotal"> {{amount_with_currency_symbol($subtotal)}} </td>
                        <td class="ff-jost {{ $wishlist ? "wl-action-cell" : "" }}" data-label="Close" data-product_hash_id="{{$data->rowId}}">
                            @if($wishlist)
                                @php $in_stock_action = ($stock_map ?? [])[(int)$data->id] ?? false; @endphp
                                <button class="add-to-cart-btn wl-atc-btn {{ !$in_stock_action ? 'wl-atc-disabled' : '' }}"
                                    data-product_id="{{ $data->id }}"
                                    {{ !$in_stock_action ? 'disabled' : '' }}
                                    title="{{ $in_stock_action ? __('Add to Cart') : __('Out of Stock') }}">
                                    <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                                </button>
                            @else
                                <button class="save-for-later-btn btn-save-later" data-row_id="{{$data->rowId}}" title="{{ __('Save for Later') }}">
                                    <i class="lar la-heart"></i>
                                </button>
                            @endif

                            <div class="close-table-{{ $wishlist ? "wishlist" : "cart" }}">
                                <i class="las la-times"></i>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="table-update-btn margin-top-40">
            <a href="{{url('shop')}}" class="btn-update btn-table btn-border-1"> {{__('Continue Shopping')}} </a>

            @if(!$wishlist)
                <a href="javascript:void(0)" class="btn-clear btn-table clear-cart-btn"> {{__('Clear Cart')}} </a>
            @endif
        </div>
    </div>
</div>