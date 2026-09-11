@foreach($cart_data as $key => $data)
    @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
    <tr class="table-cart-row" style="border-bottom:1px solid var(--tz-border);" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
        <td style="padding:14px 20px;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:60px;height:60px;border-radius:var(--tz-radius-sm);border:1px solid var(--tz-border);background:var(--tz-surface);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;padding:4px;">
                    {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                </div>
                <div>
                    <a href="{{ theme_product_url($slug) }}" style="font-size:13px;font-weight:600;color:var(--tz-text);text-decoration:none;" onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-text)'">{{ $data->name }}</a>
                    @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                    <div style="font-size:11px;color:var(--tz-muted);margin-top:3px;">
                        @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                        @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                        @if($data?->options?->attributes) @foreach($data->options->attributes as $attrKey => $attrVal)· {{ $attrKey }}: {{ $attrVal }} @endforeach @endif
                    </div>
                    @endif
                </div>
            </div>
        </td>
        <td style="padding:14px 16px;"><span style="font-size:14px;font-weight:700;color:var(--tz-blue);">{{ amount_with_currency_symbol($data->price) }}</span></td>
        <td style="padding:14px 16px;">
            <div style="display:flex;align-items:center;border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);overflow:hidden;width:fit-content;background:var(--tz-mid);">
                @if(!$wishlist)
                    <button type="button" class="substract" style="width:30px;height:34px;border:0;background:transparent;cursor:pointer;font-size:14px;color:var(--tz-muted);"><i class="mdi mdi-minus"></i></button>
                @endif
                <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1" {{ $wishlist ? 'disabled readonly' : '' }} style="width:44px;height:34px;border:0;background:transparent;text-align:center;font-size:13px;font-weight:600;font-family:var(--tz-font);color:var(--tz-text);outline:none;">
                @if(!$wishlist)
                    <button type="button" class="plus" style="width:30px;height:34px;border:0;background:transparent;cursor:pointer;font-size:14px;color:var(--tz-muted);"><i class="mdi mdi-plus"></i></button>
                @endif
            </div>
        </td>
        <td style="padding:14px 16px;"><span style="font-size:14px;font-weight:700;color:var(--tz-blue);">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
        <td style="padding:14px 16px;" class="tz-cart-action-cell {{ $wishlist ? 'tz-cart-action-wishlist' : '' }}"
            data-product_hash_id="{{ $data->rowId }}">
            @if($wishlist)
                <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;color:var(--tz-blue);font-size:18px;"><i class="mdi mdi-cart-arrow-down"></i></div>
            @else
                @auth
                <button class="save-for-later-btn" type="button"
                        data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}"
                        style="background:none;border:none;cursor:pointer;color:var(--tz-muted);font-size:16px;padding:0;line-height:1;">
                    <i class="mdi mdi-heart-outline"></i>
                </button>
                @endauth
            @endif
            <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" style="cursor:pointer;">
                <button type="button" title="{{ __('Remove') }}" style="width:28px;height:28px;border-radius:50%;border:1px solid var(--tz-border);background:transparent;cursor:pointer;color:var(--tz-muted);font-size:13px;display:flex;align-items:center;justify-content:center;"><i class="mdi mdi-close"></i></button>
            </div>
        </td>
    </tr>
@endforeach
