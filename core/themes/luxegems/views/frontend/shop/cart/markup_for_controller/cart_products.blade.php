@foreach($cart_data as $key => $data)
    @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
    <tr class="table-cart-row"
        data-product-id="{{ $key }}"
        data-variant-id="{{ $data->options->variant_id ?? '' }}"
        data-varinat-id="{{ $data->options->variant_id ?? '' }}">
        <td>
            <div class="d-flex align-items-center gap-3">
                <div class="lg-cart-prod-img">
                    {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                </div>
                <div>
                    <a href="{{ theme_product_url($slug) }}" class="lg-cart-prod-name"
                       style="text-decoration:none;">{{ $data->name }}</a>
                    @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                    <div class="lg-cart-prod-meta">
                        @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                        @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                    </div>
                    @endif
                </div>
            </div>
        </td>
        <td class="lg-cart-price">{{ amount_with_currency_symbol($data->price) }}</td>
        <td>
            <div class="lg-qty-stepper">
                <button type="button" class="substract"><i class="las la-minus"></i></button>
                <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1">
                <button type="button" class="plus"><i class="las la-plus"></i></button>
            </div>
        </td>
        <td class="lg-cart-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</td>
        <td class="lg-cart-action-cell" data-product_hash_id="{{ $data->rowId }}">
            @auth
            <button class="save-for-later-btn" type="button"
                    data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}"
                    style="background:transparent;border:none;cursor:pointer;color:var(--lx-gold);font-size:18px;display:block;margin-bottom:4px;">
                <i class="las la-heart"></i>
            </button>
            @endauth
            <div class="close-table-cart">
                <button class="lg-remove-btn" type="button">
                    <i class="las la-times"></i>
                </button>
            </div>
        </td>
    </tr>
@endforeach
