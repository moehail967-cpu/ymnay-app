@foreach($cart_data as $key => $data)
    @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
    <tr class="table-cart-row" style="border-bottom:1px solid var(--sz-border);" data-product-id="{{ $key }}" data-variant-id="{{ $data->options->variant_id ?? '' }}" data-varinat-id="{{ $data->options->variant_id ?? '' }}">
        <td style="padding:16px 20px;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:64px;height:64px;border-radius:var(--sz-radius);overflow:hidden;border:2px solid var(--sz-border);flex-shrink:0;background:var(--sz-bg);">
                    {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                </div>
                <div>
                    <a href="{{ theme_product_url($slug) }}" style="font-family:var(--sz-font-head);font-size:14px;font-weight:400;color:var(--sz-dark);text-decoration:none;text-transform:uppercase;letter-spacing:.5px;">{{ $data->name }}</a>
                    @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                    <div style="font-size:12px;color:var(--sz-muted);margin-top:3px;">
                        @if($data?->options?->color_name) {{ __('Color:') }} {{ $data->options->color_name }} @endif
                        @if($data?->options?->size_name) · {{ __('Size:') }} {{ $data->options->size_name }} @endif
                        @if($data?->options?->attributes)
                            @foreach($data->options->attributes as $attrKey => $attrVal) · {{ $attrKey }}: {{ $attrVal }} @endforeach
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </td>
        <td style="padding:16px;"><span class="sz-price-sale" style="font-size:16px;">{{ amount_with_currency_symbol($data->price) }}</span></td>
        <td style="padding:16px;">
            <div style="display:flex;align-items:center;border:2px solid var(--sz-border);border-radius:var(--sz-radius);overflow:hidden;width:fit-content;">
                @if(!$wishlist)
                    <button type="button" class="substract" style="width:32px;height:36px;border:0;background:var(--sz-bg);cursor:pointer;font-size:14px;color:var(--sz-dark);"><i class="mdi mdi-minus"></i></button>
                @endif
                <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1"
                       {{ $wishlist ? 'disabled readonly' : '' }}
                       style="width:48px;height:36px;border:0;text-align:center;font-family:var(--sz-font-head);font-size:14px;font-weight:600;">
                @if(!$wishlist)
                    <button type="button" class="plus" style="width:32px;height:36px;border:0;background:var(--sz-bg);cursor:pointer;font-size:14px;color:var(--sz-dark);"><i class="mdi mdi-plus"></i></button>
                @endif
            </div>
        </td>
        <td style="padding:16px;"><span class="sz-price-sale" style="font-size:16px;">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span></td>
        <td style="padding:16px;" class="sz-cart-action-cell {{ $wishlist ? 'sz-cart-action-wishlist' : '' }}"
            data-product_hash_id="{{ $data->rowId }}">
            @if($wishlist)
                <div class="move-to-wishlist" title="{{ __('Move to Cart') }}" style="cursor:pointer;color:var(--sz-red);font-size:20px;"><i class="mdi mdi-cart-arrow-down"></i></div>
            @else
                @auth
                <button class="save-for-later-btn" type="button"
                        data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}"
                        style="background:none;border:none;cursor:pointer;color:var(--sz-muted);font-size:18px;padding:0;line-height:1;">
                    <i class="mdi mdi-heart-outline"></i>
                </button>
                @endauth
            @endif
            <div class="close-table-{{ $wishlist ? 'wishlist' : 'cart' }}" style="cursor:pointer;">
                <button type="button" title="{{ __('Remove') }}" style="width:32px;height:32px;border-radius:50%;border:2px solid var(--sz-border);background:transparent;cursor:pointer;color:var(--sz-muted);font-size:14px;display:flex;align-items:center;justify-content:center;">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
        </td>
    </tr>
@endforeach
