<div class="col-xl-{{ $wishlist ? "10 mx-auto" : "8" }} mt-4">
    <div class="ar-cart-table-wrap table-list-content table-cart-clear">
        <div class="table-responsive">
            <table class="ar-cart-table">
                <thead>
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th style="text-align:right">{{ __('Price') }}</th>
                    <th style="text-align:center">{{ __('Quantity') }}</th>
                    <th style="text-align:right">{{ __('Subtotal') }}</th>
                    <th style="text-align:center">{{ __('Action') }}</th>
                </tr>
                </thead>
                <tbody id="cart_tbody">
                @foreach($cart_data as $key => $data)
                    @php
                        if ($data->options->type == \App\Enums\ProductTypeEnum::DIGITAL) {
                            $slug = \Modules\DigitalProduct\Entities\DigitalProduct::select('id', 'slug')->find($data->id)?->slug;
                        } else {
                            $slug = \Modules\Product\Entities\Product::select('id', 'slug')->find($data->id)?->slug;
                        }
                    @endphp
                    <tr class="table-cart-row ar-cart-row" data-product-id="{{ $key }}" data-varinat-id="{{ $data->variant_id }}">

                        {{-- Product --}}
                        <td data-label="{{ __('Product') }}">
                            <div class="ar-cart-product">
                                <div class="ar-cart-thumb bg-image"
                                    {!! render_background_image_markup_by_attachment_id($data?->options?->image) !!}></div>
                                <div class="ar-cart-info carts-contents">
                                    @php
                                        if ($data->options->type == \App\Enums\ProductTypeEnum::DIGITAL) {
                                            $product = \Modules\DigitalProduct\Entities\DigitalProduct::select('id', 'slug')->find($data->id);
                                            $product_details_route = dynamicRoute($slug);
                                            $product_type = 'Digital';
                                        } else {
                                            $product = \Modules\Product\Entities\Product::select('id', 'slug')->find($data->id);
                                            $product_details_route = dynamicRoute($slug);
                                            $product_type = 'Normal';
                                        }
                                    @endphp
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
                            @if($wishlist)
                                <input class="quantity-input" type="hidden" value="{{ $data->qty }}">
                                <span class="ar-cart-price">{{ $data->qty }}</span>
                            @elseif($data->options->type == \App\Enums\ProductTypeEnum::PHYSICAL)
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
                        @php $subtotal = calculatePrice($data->price, $data->options) * $data->qty; @endphp
                        <td class="price-td" data-label="{{ __('Subtotal') }}">
                            <span class="ar-cart-price ar-cart-subtotal">{{ float_amount_with_currency_symbol($subtotal) }}</span>
                        </td>

                        {{-- Actions --}}
                        <td class="ff-jost"
                            data-label="{{ __('Action') }}"
                            data-product_hash_id="{{ $data->rowId }}">
                            @if($wishlist)
                                <button class="ar-cart-icon-btn ar-cart-wish-btn move-to-wishlist" title="{{ __('Move to Cart') }}">
                                    <i class="las la-cart-arrow-down"></i>
                                </button>
                            @else
                                <button class="ar-cart-icon-btn ar-cart-wish-btn move-to-wishlist" title="{{ __('Save to Wishlist') }}">
                                    <i class="las la-heart"></i>
                                </button>
                            @endif
                            <button class="ar-cart-icon-btn ar-cart-remove close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" title="{{ __('Remove') }}">
                                <i class="las la-times"></i>
                            </button>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="ar-cart-footer-btns">
            <a href="{{ theme_shop_url() }}" class="ar-btn ar-btn-outline">
                <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
            </a>
            @if(!$wishlist)
                <a href="javascript:void(0)" class="ar-btn ar-cart-clear-link clear-cart-btn">
                    <i class="las la-trash-alt"></i> {{ __('Clear Cart') }}
                </a>
            @endif
        </div>
    </div>
</div>
