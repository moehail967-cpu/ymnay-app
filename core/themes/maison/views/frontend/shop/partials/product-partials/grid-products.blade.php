{{-- Maison: Product grid partial — initial render + AJAX filter/pagination --}}
@foreach($products as $product)
    @php
        $data          = theme_product_price($product);
        $regular_price = $data['regular_price'];
        $sale_price    = $data['sale_price'];
        $discount      = $data['discount'];
        $campaign_name = $data['campaign_name'];
        $img_data      = get_attachment_image_by_id($product->image_id ?? null, 'grid');
        $img_url       = $img_data['img_url'] ?? null;
        $badge         = $product->badge?->name ?? null;
        $product_url   = theme_product_url($product->slug);
    @endphp

    <div class="col-6 col-md-4">
        <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);overflow:hidden;box-shadow:var(--ms-shadow);transition:box-shadow .25s,transform .25s;position:relative;"
             onmouseover="this.style.boxShadow='0 18px 50px -10px rgba(200,184,154,.4)';this.style.transform='translateY(-3px)'"
             onmouseout="this.style.boxShadow='var(--ms-shadow)';this.style.transform='none'">

            {{-- Image --}}
            <div style="position:relative;aspect-ratio:1;overflow:hidden;background:var(--ms-warm);">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"
                             style="width:100%;height:100%;object-fit:cover;transition:transform .4s;"
                             onmouseover="this.style.transform='scale(1.06)'"
                             onmouseout="this.style.transform='scale(1)'">
                    </a>
                @else
                    <a href="{{ $product_url }}" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
                        <i class="mdi mdi-image-outline" style="font-size:52px;color:var(--ms-border);"></i>
                    </a>
                @endif

                @if($discount)
                    <span style="position:absolute;top:10px;left:10px;background:var(--ms-olive);color:#fff;font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:3px 10px;border-radius:2px;">
                        −{{ $discount }}%
                    </span>
                @elseif($badge)
                    <span style="position:absolute;top:10px;left:10px;background:var(--ms-linen);color:#fff;font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:3px 10px;border-radius:2px;">
                        {{ $badge }}
                    </span>
                @elseif($campaign_name)
                    <span style="position:absolute;top:10px;left:10px;background:var(--ms-charcoal);color:#fff;font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:3px 10px;border-radius:2px;">
                        {{ $campaign_name }}
                    </span>
                @endif

                <button class="ms-wishlist-btn add-to-wishlist-btn" data-product_id="{{ $product->id }}"
                        style="position:absolute;top:10px;right:10px;width:34px;height:34px;border-radius:50%;border:1px solid var(--ms-border);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--ms-muted);font-size:15px;transition:all .2s;"
                        onmouseover="this.style.borderColor='var(--ms-linen)';this.style.color='var(--ms-linen)'"
                        onmouseout="this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-muted)'"
                        aria-label="{{ __('Wishlist') }}">
                    <i class="las la-heart"></i>
                </button>
            </div>

            {{-- Body --}}
            <div style="padding:16px;">
                <div style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:4px;">
                    {{ $product->category?->name ?? '' }}
                </div>
                <div style="font-size:14px;font-weight:500;color:var(--ms-dark);margin-bottom:10px;line-height:1.4;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                    <a href="{{ $product_url }}" style="color:inherit;text-decoration:none;"
                       onmouseover="this.style.color='var(--ms-linen-d)'"
                       onmouseout="this.style.color='var(--ms-dark)'">
                        {{ \Illuminate\Support\Str::words($product->name, 7) }}
                    </a>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                    <div>
                        <span style="font-size:16px;font-weight:600;color:var(--ms-linen-d);">
                            {{ amount_with_currency_symbol($sale_price) }}
                        </span>
                        @if($regular_price)
                            <span style="font-size:12px;color:var(--ms-muted);text-decoration:line-through;margin-left:6px;">
                                {{ amount_with_currency_symbol($regular_price) }}
                            </span>
                        @endif
                    </div>
                    <button class="ms-atc-btn add-to-cart-btn"
                            data-product_id="{{ $product->id }}"
                            style="display:inline-flex;align-items:center;gap:5px;padding:7px 13px;background:var(--ms-dark);color:#fff;border:none;border-radius:var(--ms-radius);font-size:11px;font-weight:600;letter-spacing:.04em;cursor:pointer;transition:background .2s;white-space:nowrap;"
                            onmouseover="this.style.background='var(--ms-linen-d)'"
                            onmouseout="this.style.background='var(--ms-dark)'"
                            aria-label="{{ __('Add to cart') }}">
                        <i class="las la-shopping-bag" style="font-size:14px;"></i>
                        <span class="d-none d-md-inline">{{ __('Add') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div style="display:flex;justify-content:center;align-items:center;gap:6px;margin-top:32px;flex-wrap:wrap;">
        @foreach($links as $page => $url)
            <button style="width:36px;height:36px;border-radius:var(--ms-radius);border:1px solid {{ $page == $current_page ? 'var(--ms-linen)' : 'var(--ms-border)' }};background:{{ $page == $current_page ? 'var(--ms-linen)' : '#fff' }};color:{{ $page == $current_page ? '#fff' : 'var(--ms-charcoal)' }};font-size:13px;font-weight:{{ $page == $current_page ? '700' : '500' }};cursor:pointer;transition:all .2s;"
                    onclick="msFilterRequest({{ $page }})"
                    onmouseover="if({{ $page != $current_page ? 'true' : 'false' }}) { this.style.borderColor='var(--ms-linen)';this.style.color='var(--ms-linen-d)'; }"
                    onmouseout="if({{ $page != $current_page ? 'true' : 'false' }}) { this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-charcoal)'; }">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
