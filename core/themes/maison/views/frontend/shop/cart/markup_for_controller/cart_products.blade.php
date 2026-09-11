@foreach($cart_data as $key => $data)
    @php $slug = \Modules\Product\Entities\Product::select('id','slug')->find($data->id)?->slug; @endphp
    <tr class="table-cart-row" style="border-bottom:1px solid var(--ms-border);"
        data-product-id="{{ $key }}"
        data-variant-id="{{ $data->options->variant_id ?? '' }}"
        data-varinat-id="{{ $data->options->variant_id ?? '' }}">
        <td style="padding:18px 20px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:70px;height:70px;border-radius:var(--ms-radius);overflow:hidden;border:1px solid var(--ms-border);flex-shrink:0;background:var(--ms-warm);">
                    {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                </div>
                <div>
                    <a href="{{ theme_product_url($slug) }}" style="font-size:14px;font-weight:500;color:var(--ms-dark);text-decoration:none;display:block;margin-bottom:4px;"
                       onmouseover="this.style.color='var(--ms-linen-d)'"
                       onmouseout="this.style.color='var(--ms-dark)'">
                        {{ $data->name }}
                    </a>
                    @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                    <div style="font-size:11px;color:var(--ms-muted);letter-spacing:.03em;">
                        @if($data?->options?->color_name) {{ $data->options->color_name }} @endif
                        @if($data?->options?->size_name) · {{ $data->options->size_name }} @endif
                        @if($data?->options?->attributes)
                            @foreach($data->options->attributes as $attrKey => $attrVal) · {{ $attrKey }}: {{ $attrVal }} @endforeach
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </td>
        <td style="padding:18px 16px;">
            <span style="font-size:15px;font-weight:500;color:var(--ms-linen-d);">{{ amount_with_currency_symbol($data->price) }}</span>
        </td>
        <td style="padding:18px 16px;">
            <div style="display:flex;align-items:center;border:1px solid var(--ms-border);border-radius:var(--ms-radius);overflow:hidden;width:fit-content;">
                <button type="button" class="substract"
                        style="width:32px;height:36px;border:0;background:var(--ms-warm);cursor:pointer;font-size:14px;color:var(--ms-charcoal);transition:background .2s;"
                        onmouseover="this.style.background='var(--ms-surface)'"
                        onmouseout="this.style.background='var(--ms-warm)'">
                    <i class="mdi mdi-minus"></i>
                </button>
                <input class="quantity-input" type="number" value="{{ $data->qty }}" min="1"
                       style="width:48px;height:36px;border:0;border-left:1px solid var(--ms-border);border-right:1px solid var(--ms-border);text-align:center;font-size:14px;font-weight:600;font-family:inherit;color:var(--ms-dark);">
                <button type="button" class="plus"
                        style="width:32px;height:36px;border:0;background:var(--ms-warm);cursor:pointer;font-size:14px;color:var(--ms-charcoal);transition:background .2s;"
                        onmouseover="this.style.background='var(--ms-surface)'"
                        onmouseout="this.style.background='var(--ms-warm)'">
                    <i class="mdi mdi-plus"></i>
                </button>
            </div>
        </td>
        <td style="padding:18px 16px;">
            <span style="font-size:15px;font-weight:600;color:var(--ms-dark);">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
        </td>
        <td style="padding:18px 16px;" class="ms-cart-action-cell" data-product_hash_id="{{ $data->rowId }}">
            @auth
            <button class="save-for-later-btn" type="button"
                    data-row_id="{{ $data->rowId }}" title="{{ __('Save for Later') }}"
                    style="background:transparent;border:1px solid var(--ms-border);border-radius:50%;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--ms-muted);font-size:14px;margin-bottom:6px;transition:all .2s;">
                <i class="mdi mdi-heart-outline"></i>
            </button>
            @endauth
            <div class="close-table-cart" style="cursor:pointer;">
                <button type="button" class="ms-remove-btn"
                        style="width:30px;height:30px;border-radius:50%;border:1px solid var(--ms-border);background:transparent;cursor:pointer;color:var(--ms-muted);font-size:13px;display:flex;align-items:center;justify-content:center;transition:all .2s;"
                        onmouseover="this.style.borderColor='#C0392B';this.style.color='#C0392B'"
                        onmouseout="this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-muted)'">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
        </td>
    </tr>
@endforeach
