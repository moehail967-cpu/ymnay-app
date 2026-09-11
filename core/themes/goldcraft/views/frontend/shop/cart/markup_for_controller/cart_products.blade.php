@foreach($cart_data as $key => $data)
    @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
    <tr class="table-cart-row" style="border-bottom:1px solid var(--gc-border);"
        data-product-id="{{ $key }}"
        data-variant-id="{{ $data->options->variant_id ?? '' }}"
        data-varinat-id="{{ $data->options->variant_id ?? '' }}">
        <td style="padding:16px 20px;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:64px;height:64px;border-radius:var(--gc-radius);overflow:hidden;border:1px solid var(--gc-border);flex-shrink:0;background:var(--gc-warm);">
                    {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                </div>
                <div>
                    <a href="{{ theme_product_url($slug) }}" style="font-size:14px;font-weight:400;color:var(--gc-dark);text-decoration:none;font-style:italic;">{{ $data->name }}</a>
                    @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                    <div style="font-size:11px;color:var(--gc-muted);margin-top:3px;">
                        @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                        @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                        @foreach((array)($data?->options?->attributes ?? []) as $attrK => $attrV)
                            @if($attrV !== null && $attrV !== '') · {{ ucfirst($attrK) }}: {{ $attrV }} @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </td>
        <td style="padding:16px;"><span class="gc-price" style="font-size:15px;">{{ amount_with_currency_symbol($data->price) }}</span></td>
        <td style="padding:16px;">
            <div style="display:flex;align-items:center;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;width:fit-content;">
                <button type="button" class="substract" style="width:32px;height:36px;border:0;background:var(--gc-warm);cursor:pointer;font-size:13px;"><i class="mdi mdi-minus"></i></button>
                <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1"
                       style="width:48px;height:36px;border:0;text-align:center;font-size:14px;font-weight:600;font-family:Georgia,serif;">
                <button type="button" class="plus" style="width:32px;height:36px;border:0;background:var(--gc-warm);cursor:pointer;font-size:13px;"><i class="mdi mdi-plus"></i></button>
            </div>
        </td>
        <td style="padding:16px;"><span class="gc-price" style="font-size:15px;">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
        <td class="gc-cart-action-cell" style="padding:16px;" data-product_hash_id="{{ $data->rowId }}">
            @auth
            <button class="save-for-later-btn" type="button"
                    data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}"
                    style="background:transparent;border:1.5px solid var(--gc-border);border-radius:50%;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--gc-muted);font-size:14px;margin-bottom:6px;transition:all .2s;">
                <i class="mdi mdi-heart-outline"></i>
            </button>
            @endauth
            <div class="close-table-cart" style="cursor:pointer;">
                <button type="button" class="gc-remove-btn"
                        style="width:30px;height:30px;border-radius:50%;border:1.5px solid var(--gc-border);background:transparent;cursor:pointer;color:var(--gc-muted);font-size:13px;display:flex;align-items:center;justify-content:center;transition:all .2s;">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
        </td>
    </tr>
@endforeach
