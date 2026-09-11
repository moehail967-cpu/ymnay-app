@foreach($cart_data as $key => $data)
    @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
    <tr class="table-cart-row" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
        <td>
            <div class="d-flex align-items-center gap-3">
                <div class="fp-cart-img">
                    {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                </div>
                <div>
                    <a href="{{ theme_product_url($slug) }}"
                       style="font-size:14px;font-weight:700;color:var(--fp-white);text-decoration:none;">
                        {{ $data->name }}
                    </a>
                    @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                    <div style="font-size:12px;color:var(--fp-muted);margin-top:3px;">
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
            <span class="fp-price" style="font-size:15px;">{{ amount_with_currency_symbol($data->price) }}</span>
        </td>
        <td>
            <div class="fp-qty">
                <button type="button" class="substract"><i class="mdi mdi-minus"></i></button>
                <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1">
                <button type="button" class="plus"><i class="mdi mdi-plus"></i></button>
            </div>
        </td>
        <td>
            <span class="fp-price" style="font-size:15px;">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
        </td>
        <td class="fp-cart-action-cell" data-product_hash_id="{{ $data->rowId }}">
            <div class="close-table-cart" style="cursor:pointer;">
                <button class="fp-remove-btn" type="button">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            @auth
            <button class="save-for-later-btn fp-action-icon-btn" type="button"
                    data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
                <i class="mdi mdi-heart-outline"></i>
            </button>
            @endauth
        </td>
    </tr>
@endforeach
