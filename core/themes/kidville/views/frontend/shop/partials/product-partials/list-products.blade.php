{{-- KidVille: Product list partial --}}
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
        <div style="display:flex;gap:20px;background:#fff;border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:20px;box-shadow:var(--kv-shadow);transition:box-shadow .2s;"
             onmouseover="this.style.boxShadow='0 12px 40px -8px rgba(244,67,54,.18)';this.style.borderColor='var(--kv-red)'"
             onmouseout="this.style.boxShadow='var(--kv-shadow)';this.style.borderColor='var(--kv-border)'">

            <a href="{{ $product_url }}" style="flex-shrink:0;width:130px;height:130px;overflow:hidden;border-radius:var(--kv-radius);background:var(--kv-light);display:flex;align-items:center;justify-content:center;padding:10px;">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" style="max-width:100%;max-height:100%;object-fit:contain;" loading="lazy">
                @else
                    <i class="las la-box-open" style="font-size:42px;color:var(--kv-red);"></i>
                @endif
            </a>

            <div style="flex:1;min-width:0;display:flex;flex-direction:column;justify-content:space-between;">
                <div>
                    @if($discount)
                        <span style="display:inline-block;padding:3px 10px;background:var(--kv-red);color:#fff;font-size:10px;font-weight:800;letter-spacing:.5px;border-radius:var(--kv-radius-sm);margin-bottom:8px;">{{ $discount }}% {{ __('off') }}</span>
                    @elseif($campaign_name)
                        <span style="display:inline-block;padding:3px 10px;background:var(--kv-yellow);color:var(--kv-dark);font-size:10px;font-weight:800;letter-spacing:.5px;border-radius:var(--kv-radius-sm);margin-bottom:8px;">{{ $campaign_name }}</span>
                    @endif

                    <h3 style="font-size:15px;font-weight:700;color:var(--kv-dark);margin:0 0 6px;">
                        <a href="{{ $product_url }}" style="color:inherit;text-decoration:none;">{{ \Illuminate\Support\Str::words($product->name, 12) }}</a>
                    </h3>
                    <div style="margin-bottom:8px;">{!! theme_star_rating($product) !!}</div>
                    @if($product->summary)
                        <p style="font-size:13px;color:var(--kv-muted);margin:0 0 12px;line-height:1.65;">{{ \Illuminate\Support\Str::words($product->summary, 20) }}</p>
                    @endif
                </div>

                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <div>
                        <span style="font-size:18px;font-weight:800;color:var(--kv-red);">{{ amount_with_currency_symbol($sale_price) }}</span>
                        @if($regular_price)
                            <span style="font-size:13px;color:var(--kv-muted);text-decoration:line-through;margin-left:6px;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        @endif
                    </div>
                    <button class="add-to-cart-btn kv-btn kv-btn-red kv-btn-sm"
                            data-product_id="{{ $product->id }}"
                            data-slug="{{ $product->slug }}">
                        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                    </button>
                    <button class="add-to-wishlist-btn"
                            data-product_id="{{ $product->id }}"
                            style="width:38px;height:38px;border-radius:50%;border:2px solid var(--kv-border);background:transparent;color:var(--kv-muted);font-size:16px;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;"
                            onmouseover="this.style.borderColor='var(--kv-red)';this.style.color='var(--kv-red)'"
                            onmouseout="this.style.borderColor='var(--kv-border)';this.style.color='var(--kv-muted)'"
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
    <div class="kv-pagination" style="margin-top:32px;">
        @foreach($links as $page => $url)
            <button class="kv-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    onclick="kvFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
