@foreach($cart_data as $key => $data)
    @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
    <tr class="table-cart-row" style="border-bottom:1px solid var(--gl-border);" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
        <td style="padding:16px 20px;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:64px;height:64px;border-radius:var(--gl-radius);overflow:hidden;border:1px solid var(--gl-border);flex-shrink:0;background:var(--gl-blush);">
                    {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                </div>
                <div>
                    <a href="{{ theme_product_url($slug) }}" style="font-size:14px;font-weight:600;color:var(--gl-dark);text-decoration:none;">{{ $data->name }}</a>
                    @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                    <div style="font-size:12px;color:var(--gl-muted);margin-top:3px;">
                        @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                        @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                        @if($data?->options?->attributes)
                            @foreach($data->options->attributes as $attrKey => $attrVal)· {{ $attrKey }}: {{ $attrVal }} @endforeach
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </td>
        <td style="padding:16px;"><span style="font-size:15px;font-weight:700;color:var(--gl-dark);">{{ amount_with_currency_symbol($data->price) }}</span></td>
        <td style="padding:16px;">
            <div style="display:flex;align-items:center;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;width:fit-content;">
                <button type="button" class="substract" style="width:32px;height:36px;border:0;background:var(--gl-gold-pale);cursor:pointer;font-size:14px;color:var(--gl-dark);"><i class="mdi mdi-minus"></i></button>
                <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1"
                       style="width:48px;height:36px;border:0;text-align:center;font-size:14px;font-weight:600;font-family:inherit;">
                <button type="button" class="plus" style="width:32px;height:36px;border:0;background:var(--gl-gold-pale);cursor:pointer;font-size:14px;color:var(--gl-dark);"><i class="mdi mdi-plus"></i></button>
            </div>
        </td>
        <td style="padding:16px;"><span style="font-size:15px;font-weight:700;color:var(--gl-dark);">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
        <td style="padding:16px;" class="gl-cart-action-cell" data-product_hash_id="{{ $data->rowId }}">
            <div class="close-table-cart" style="cursor:pointer;">
                <button type="button" style="width:30px;height:30px;border-radius:50%;border:1.5px solid var(--gl-border);background:transparent;cursor:pointer;color:var(--gl-muted);font-size:13px;display:flex;align-items:center;justify-content:center;">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            @auth
            <button class="save-for-later-btn gl-action-icon-btn" type="button"
                    data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}"
                    style="width:30px;height:30px;border-radius:50%;border:1.5px solid var(--gl-border);background:transparent;cursor:pointer;color:var(--gl-muted);font-size:13px;display:flex;align-items:center;justify-content:center;margin-top:6px;">
                <i class="mdi mdi-heart-outline"></i>
            </button>
            @endauth
        </td>
    </tr>
@endforeach
