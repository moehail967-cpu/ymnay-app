@foreach($cart_data as $key => $data)
    @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
    <tr class="table-cart-row" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
        <td>
            <div class="d-flex align-items-center gap-3">
                <div class="bk-cart-img">
                    {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                </div>
                <div>
                    <a href="{{ $slug ? theme_product_url($slug) : 'javascript:void(0)' }}"
                       style="font-size:14px;font-weight:700;color:var(--bk-dark);text-decoration:none;">
                        {{ $data->name }}
                    </a>
                    @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                    <div style="font-size:12px;color:var(--bk-muted);margin-top:3px;">
                        @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                        @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                        @if(!empty($data?->options?->attributes))
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
            <span style="font-size:15px;font-weight:600;color:var(--bk-rose);">{{ amount_with_currency_symbol($data->price) }}</span>
        </td>
        <td>
            <div class="bk-qty">
                <button type="button" class="substract"><i class="mdi mdi-minus"></i></button>
                <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1">
                <button type="button" class="plus"><i class="mdi mdi-plus"></i></button>
            </div>
        </td>
        <td>
            <span style="font-size:15px;font-weight:600;color:var(--bk-rose);">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
        </td>
        <td class="bk-cart-action-cell" data-product_hash_id="{{ $data->rowId }}">
            @auth
            <button class="save-for-later-btn bk-action-icon-btn" type="button"
                    data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
                <i class="mdi mdi-heart-outline"></i>
            </button>
            @endauth
            <div class="close-table-cart bk-cart-action-btn">
                <button class="bk-remove-btn bk-action-icon-btn" type="button" title="{{ __('Remove') }}">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
        </td>
    </tr>
@endforeach
