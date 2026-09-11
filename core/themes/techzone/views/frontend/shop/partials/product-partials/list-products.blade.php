{{-- TechZone: Product list partial — used when user switches to list view --}}
@foreach($products as $product)
    @php
        $data          = theme_product_price($product);
        $regular_price = $data['regular_price'];
        $sale_price    = $data['sale_price'];
        $discount      = $data['discount'];
        $campaign_name = $data['campaign_name'];
        $img_url       = theme_product_image($product->image_id ?? null, 'grid');
        $product_url   = theme_product_url($product->slug);
    @endphp

    <div class="col-12">
        <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);display:flex;gap:16px;padding:16px;transition:border-color .2s;"
             onmouseover="this.style.borderColor='var(--tz-blue)'" onmouseout="this.style.borderColor='var(--tz-border)'">
            <div style="width:120px;height:120px;flex-shrink:0;border-radius:var(--tz-radius-sm);overflow:hidden;background:var(--tz-surface);display:flex;align-items:center;justify-content:center;padding:8px;">
                @if($img_url)
                    <a href="{{ $product_url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy" style="max-width:100%;max-height:100%;object-fit:contain;"></a>
                @else
                    <a href="{{ $product_url }}" style="font-size:40px;color:var(--tz-blue);"><i class="las la-laptop"></i></a>
                @endif
            </div>

            <div style="flex:1;min-width:0;display:flex;flex-direction:column;justify-content:space-between;">
                <div>
                    <a href="{{ $product_url }}" style="font-size:15px;font-weight:600;color:var(--tz-text);text-decoration:none;display:block;margin-bottom:6px;transition:color .2s;"
                       onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-text)'">
                        {{ $product->name }}
                    </a>
                    <div style="margin-bottom:8px;">
                        {!! theme_star_rating($product) !!}
                    </div>
                    @if($product->short_description)
                    <p style="font-size:12px;color:var(--tz-muted);margin-bottom:0;line-height:1.6;">{{ \Illuminate\Support\Str::limit(strip_tags($product->short_description), 120) }}</p>
                    @endif
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-top:10px;">
                    <div>
                        <span style="font-size:18px;font-weight:700;color:var(--tz-blue);">{{ amount_with_currency_symbol($sale_price) }}</span>
                        @if($regular_price)
                            <span style="font-size:13px;color:var(--tz-muted);text-decoration:line-through;margin-left:6px;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        @endif
                        @if($discount)
                            <span style="font-size:11px;font-weight:700;background:var(--tz-green);color:#fff;padding:2px 8px;border-radius:var(--tz-radius-sm);margin-left:6px;">{{ $discount }}% {{ __('off') }}</span>
                        @elseif($campaign_name)
                            <span style="font-size:11px;font-weight:700;background:var(--tz-green);color:#fff;padding:2px 8px;border-radius:var(--tz-radius-sm);margin-left:6px;">{{ $campaign_name }}</span>
                        @endif
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="button" class="add-to-wishlist-btn" data-product_id="{{ $product->id }}"
                                style="width:36px;height:36px;border-radius:var(--tz-radius-sm);background:transparent;border:1px solid var(--tz-border);color:var(--tz-muted);cursor:pointer;font-size:16px;transition:all .2s;display:flex;align-items:center;justify-content:center;"
                                onmouseover="this.style.borderColor='var(--tz-blue)';this.style.color='var(--tz-blue)'" onmouseout="this.style.borderColor='var(--tz-border)';this.style.color='var(--tz-muted)'">
                            <i class="las la-heart"></i>
                        </button>
                        <button type="button" class="add-to-cart-btn" data-product_id="{{ $product->id }}"
                                style="display:flex;align-items:center;gap:6px;background:var(--tz-blue);color:#fff;border:0;padding:8px 16px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:600;cursor:pointer;font-family:var(--tz-font);transition:background .2s;"
                                onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                            <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="tz-pagination">
        @foreach($links as $page => $url)
            <button class="tz-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="tzFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
