@foreach($cart_data as $key => $data)
@php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
<tr class="table-cart-row" style="border-bottom:1px solid var(--tr-border);" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
    <td style="padding:16px 20px;">
        <div class="d-flex align-items-center gap-3">
            <div style="width:64px;height:64px;border-radius:var(--tr-radius);overflow:hidden;border:1px solid var(--tr-border);flex-shrink:0;background:var(--tr-cream);">
                {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
            </div>
            <div>
                <a href="{{ theme_product_url($slug) }}" style="font-size:14px;font-weight:700;color:var(--tr-bark);text-decoration:none;">{{ $data->name }}</a>
                @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                <div style="font-size:12px;color:var(--tr-stone);margin-top:3px;">
                    @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                    @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                    @if($data?->options?->attributes) @foreach($data->options->attributes as $attrKey => $attrVal) · {{ $attrKey }}: {{ $attrVal }} @endforeach @endif
                </div>
                @endif
            </div>
        </div>
    </td>
    <td style="padding:16px;"><span style="font-size:15px;font-weight:900;color:var(--tr-bark);">{{ amount_with_currency_symbol($data->price) }}</span></td>
    <td style="padding:16px;">
        <div style="display:flex;align-items:center;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;width:fit-content;">
            <button type="button" class="substract" style="width:32px;height:36px;border:0;background:var(--tr-cream);cursor:pointer;font-size:14px;"><i class="mdi mdi-minus"></i></button>
            <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1"
                   style="width:48px;height:36px;border:0;text-align:center;font-size:14px;font-weight:700;">
            <button type="button" class="plus" style="width:32px;height:36px;border:0;background:var(--tr-cream);cursor:pointer;font-size:14px;"><i class="mdi mdi-plus"></i></button>
        </div>
    </td>
    <td style="padding:16px;"><span style="font-size:15px;font-weight:900;color:var(--tr-bark);">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
    <td style="padding:16px;" class="tr-cart-action-cell {{ $wishlist ? 'tr-cart-action-wishlist' : '' }}" data-product_hash_id="{{ $data->rowId }}">
        @if($wishlist)
            <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;color:var(--tr-olive);font-size:20px;"><i class="mdi mdi-cart-arrow-down"></i></div>
        @else
            @auth
            <button class="save-for-later-btn" type="button"
                    data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}">
                <i class="mdi mdi-heart-outline"></i>
            </button>
            @endauth
        @endif
        <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" style="cursor:pointer;">
            <button type="button" style="width:32px;height:32px;border-radius:50%;border:1.5px solid var(--tr-border);background:transparent;cursor:pointer;color:var(--tr-stone);font-size:14px;display:flex;align-items:center;justify-content:center;">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
    </td>
</tr>
@endforeach
