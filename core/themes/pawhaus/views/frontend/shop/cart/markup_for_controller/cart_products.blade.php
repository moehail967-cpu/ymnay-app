@foreach($cart_data as $key => $data)
    @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
    <tr class="table-cart-row" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
        <td>
            <div class="d-flex align-items-center gap-3">
                <div class="ph-cart-img">
                    {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                </div>
                <div>
                    <a href="{{ theme_product_url($slug) }}"
                       style="font-size:14px;font-weight:700;color:var(--ph-dark);text-decoration:none;">
                        {{ $data->name }}
                    </a>
                    @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                    <div style="font-size:12px;color:var(--ph-muted);margin-top:3px;">
                        @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                        @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                        @if($data?->options?->attributes)
                            @foreach($data->options->attributes as $attrKey => $attrVal)
                                · {{ $attrKey }}: {{ $attrVal }}
                            @endforeach
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <span class="ph-price">{{ amount_with_currency_symbol($data->price) }}</span>
        </td>
        <td>
            <div class="ph-qty">
                @if(!$wishlist)
                    <button type="button" class="substract"><i class="las la-minus"></i></button>
                @endif
                <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1"
                       {{ $wishlist ? 'disabled readonly' : '' }}>
                @if(!$wishlist)
                    <button type="button" class="plus"><i class="las la-plus"></i></button>
                @endif
            </div>
        </td>
        <td>
            <span class="ph-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
        </td>
        <td class="ph-cart-action-cell {{ $wishlist ? 'ph-cart-action-wishlist' : '' }}"
            data-product_hash_id="{{ $data->rowId }}">
            @if($wishlist)
                <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;">
                    <i class="las la-cart-arrow-down" style="font-size:20px;color:var(--ph-sage);"></i>
                </div>
            @else
                @auth
                <button class="save-for-later-btn" type="button"
                        data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
                    <i class="las la-heart"></i>
                </button>
                @endauth
            @endif
            <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" style="cursor:pointer;">
                <button class="ph-remove-btn" type="button" title="{{ __('Remove') }}">
                    <i class="las la-times"></i>
                </button>
            </div>
        </td>
    </tr>
@endforeach
