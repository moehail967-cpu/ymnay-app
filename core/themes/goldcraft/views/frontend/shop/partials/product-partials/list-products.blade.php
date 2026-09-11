{{-- GoldCraft: Product list partial --}}
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
        <div style="display:flex;gap:20px;background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;box-shadow:var(--gc-shadow);transition:box-shadow .2s;"
             onmouseover="this.style.boxShadow='0 4px 24px rgba(200,169,110,.18)'" onmouseout="this.style.boxShadow='var(--gc-shadow)'">

            <a href="{{ $product_url }}" style="flex-shrink:0;width:130px;height:130px;overflow:hidden;border-radius:var(--gc-radius);background:var(--gc-warm);display:flex;align-items:center;justify-content:center;padding:10px;">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" style="max-width:100%;max-height:100%;object-fit:contain;" loading="lazy">
                @else
                    <i class="las la-gem" style="font-size:42px;"></i>
                @endif
            </a>

            <div style="flex:1;min-width:0;display:flex;flex-direction:column;justify-content:space-between;">
                <div>
                    @if($discount)
                        <span style="display:inline-block;padding:2px 10px;background:var(--gc-rose);color:#fff;font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;border-radius:3px;margin-bottom:8px;">{{ $discount }}% {{ __('off') }}</span>
                    @elseif($campaign_name)
                        <span style="display:inline-block;padding:2px 10px;background:var(--gc-gold);color:var(--gc-dark);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;border-radius:3px;margin-bottom:8px;">{{ $campaign_name }}</span>
                    @endif

                    <h3 style="font-size:15px;font-weight:400;color:var(--gc-dark);margin:0 0 6px;font-style:italic;">
                        <a href="{{ $product_url }}" style="color:inherit;text-decoration:none;">{{ \Illuminate\Support\Str::words($product->name, 12) }}</a>
                    </h3>
                    <div style="margin-bottom:8px;">{!! theme_star_rating($product) !!}</div>
                    @if($product->summary)
                        <p style="font-size:13px;color:var(--gc-muted);margin:0 0 12px;line-height:1.65;font-style:italic;">{{ \Illuminate\Support\Str::words($product->summary, 20) }}</p>
                    @endif
                </div>

                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <div>
                        <span style="font-size:18px;font-weight:700;color:var(--gc-rose);">{{ amount_with_currency_symbol($sale_price) }}</span>
                        @if($regular_price)
                            <span style="font-size:13px;color:var(--gc-muted);text-decoration:line-through;margin-left:6px;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        @endif
                    </div>
                    <button class="add-to-cart-btn"
                            data-product_id="{{ $product->id }}"
                            data-slug="{{ $product->slug }}"
                            style="padding:8px 20px;background:var(--gc-rose);color:#fff;border:none;border-radius:var(--gc-radius);font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                            onmouseover="this.style.background='var(--gc-rose-d)'" onmouseout="this.style.background='var(--gc-rose)'">
                        {{ __('Add to Cart') }}
                    </button>
                    <button class="add-to-wishlist-btn"
                            data-product_id="{{ $product->id }}"
                            style="width:36px;height:36px;border-radius:50%;border:1.5px solid var(--gc-border);background:transparent;color:var(--gc-muted);font-size:15px;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;"
                            onmouseover="this.style.borderColor='var(--gc-rose)';this.style.color='var(--gc-rose)'" onmouseout="this.style.borderColor='var(--gc-border)';this.style.color='var(--gc-muted)'"
                            aria-label="{{ __('Wishlist') }}">
                        <i class="las la-heart"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div style="display:flex;justify-content:center;gap:6px;margin-top:32px;flex-wrap:wrap;">
        @foreach($links as $page => $url)
            <button onclick="gcFilterRequest({{ $page }})"
                    style="width:36px;height:36px;border-radius:3px;border:1.5px solid {{ $page == $current_page ? 'var(--gc-rose)' : 'var(--gc-border)' }};background:{{ $page == $current_page ? 'var(--gc-rose)' : 'transparent' }};color:{{ $page == $current_page ? '#fff' : 'var(--gc-muted)' }};font-size:12px;font-weight:700;cursor:pointer;transition:all .2s;font-family:Georgia,serif;">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
