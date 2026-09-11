@foreach($cart_data as $key => $data)
    @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
    <tr class="table-cart-row" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
        <td>
            <div class="d-flex align-items-center gap-3">
                <div class="fm-cart-img">
                    {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                </div>
                <div>
                    <a href="{{ theme_product_url($slug) }}"
                       class="fm-cart-name" style="text-decoration:none;">
                        {{ $data->name }}
                    </a>
                    @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                    <div class="fm-cart-variant">
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
            <span class="fm-price-sale" style="font-size:15px;">{{ amount_with_currency_symbol($data->price) }}</span>
        </td>
        <td>
            <div class="fm-qty-wrap">
                <button type="button" class="fm-qty-btn substract"><i class="las la-minus"></i></button>
                <input class="fm-qty-input quantity-input" type="number" value="{{ $data->qty }}" min="1">
                <button type="button" class="fm-qty-btn plus"><i class="las la-plus"></i></button>
            </div>
        </td>
        <td>
            <span class="fm-price-sale" style="font-size:15px;">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
        </td>
        <td class="fm-cart-action-cell" data-product_hash_id="{{ $data->rowId }}">
            <div class="close-table-cart">
                <button class="fm-remove-btn" type="button" title="{{ __('Remove') }}">
                    <i class="las la-times"></i>
                </button>
            </div>
            @auth
            <button class="save-for-later-btn fm-action-icon-btn" type="button"
                    data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
                <i class="las la-heart"></i>
            </button>
            @endauth
        </td>
    </tr>
@endforeach
