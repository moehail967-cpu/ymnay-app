<div class="col-lg-{{ $wishlist ? '12' : '8' }}">
    <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
        <div class="table-responsive">
            <table class="bp-cart-table">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Subtotal') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cart_tbody">
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
                                @if(!$wishlist)<button type="button" class="substract"><i class="las la-minus"></i></button>@endif
                                <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1" {{ $wishlist ? 'disabled readonly' : '' }}>
                                @if(!$wishlist)<button type="button" class="plus"><i class="las la-plus"></i></button>@endif
                            </div>
                        </td>
                        <td><span class="bp-cart-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
                        <td class="ff-jost {{ $wishlist ? 'd-flex justify-content-around align-items-center' : '' }}" data-product_hash_id="{{ $data->rowId }}">
                            @if($wishlist)
                                <div class="move-to-wishlist" style="cursor:pointer;" title="{{ __('Move to Cart') }}">
                                    <i class="las la-shopping-cart" style="font-size:20px;color:var(--bp-accent);"></i>
                                </div>
                            @endif
                            <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" style="cursor:pointer;">
                                <button class="bp-remove-btn" type="button"><i class="las la-times"></i></button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mt-4">
        <a href="{{ theme_shop_url() }}" class="bp-btn bp-btn-outline"><i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}</a>
        @if(!$wishlist)
        <a href="javascript:void(0)" class="bp-btn bp-btn-danger clear-cart-btn"><i class="las la-trash"></i> {{ __('Clear Cart') }}</a>
        @endif
    </div>
</div>
