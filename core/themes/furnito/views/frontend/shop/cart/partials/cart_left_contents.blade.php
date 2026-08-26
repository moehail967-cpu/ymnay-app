@php $is_wishlist = $wishlist ?? false; @endphp

<div class="{{ $is_wishlist ? 'col-12' : 'col-lg-8' }}">
    <div class="fn-cart-box">
        <div class="table-responsive">
            <table class="fn-cart-table">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Subtotal') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="fn-cart-tbody">
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
                        {{-- Product --}}
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

                        {{-- Unit price --}}
                        <td class="fn-cart-price">{{ amount_with_currency_symbol($unit_price) }}</td>

                        {{-- Quantity --}}
                        <td>
                            @if(!$is_digital)
                                <div class="fn-qty">
                                    @if(!$is_wishlist)
                                        <button class="fn-qty-btn fn-qty-minus" type="button"><i class="las la-minus"></i></button>
                                    @endif
                                    <input class="fn-qty-input" type="number"
                                           value="{{ $data->qty }}"
                                           {{ $is_wishlist ? 'disabled readonly' : '' }}>
                                    @if(!$is_wishlist)
                                        <button class="fn-qty-btn fn-qty-plus" type="button"><i class="las la-plus"></i></button>
                                    @endif
                                </div>
                            @else
                                <span class="fn-cart-price">1</span>
                            @endif
                        </td>

                        {{-- Subtotal --}}
                        <td class="fn-cart-price">{{ float_amount_with_currency_symbol($subtotal) }}</td>

                        {{-- Actions --}}
                        <td>
                            <div class="fn-cart-td-actions">
                                @if($is_wishlist)
                                    <button class="fn-cart-move" type="button"
                                            data-hash="{{ $data->rowId }}"
                                            title="{{ __('Move to Cart') }}">
                                        <i class="las la-shopping-cart"></i>
                                    </button>
                                    <button class="fn-wish-remove" type="button"
                                            data-hash="{{ $data->rowId }}"
                                            title="{{ __('Remove') }}">
                                        <i class="las la-times"></i>
                                    </button>
                                @else
                                    <button class="fn-cart-remove" type="button"
                                            data-hash="{{ $data->rowId }}"
                                            title="{{ __('Remove') }}">
                                        <i class="las la-times"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer actions --}}
        <div class="fn-cart-footer">
            <a href="{{ theme_shop_url() }}" class="fn-btn fn-btn-outline fn-btn-sm">
                <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
            </a>
            @if(!$is_wishlist)
                <button type="button" class="fn-btn fn-btn-sm fn-clear-cart-btn">
                    <i class="las la-trash"></i> {{ __('Clear Cart') }}
                </button>
            @endif
        </div>
    </div>
</div>
