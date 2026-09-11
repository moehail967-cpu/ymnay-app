{{-- GlowLab: Product list partial --}}
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
        <div style="display:flex;gap:20px;background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;box-shadow:var(--gl-shadow);transition:box-shadow .2s;"
             onmouseover="this.style.boxShadow='0 4px 20px rgba(184,150,90,.12)'" onmouseout="this.style.boxShadow='var(--gl-shadow)'">

            {{-- Image --}}
            <a href="{{ $product_url }}" style="flex-shrink:0;width:140px;height:140px;overflow:hidden;border-radius:calc(var(--gl-radius) - 2px);background:var(--gl-gold-pale);display:flex;align-items:center;justify-content:center;">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                @else
                    <span style="font-size:48px;">✨</span>
                @endif
            </a>

            {{-- Info --}}
            <div style="flex:1;min-width:0;display:flex;flex-direction:column;justify-content:space-between;">
                <div>
                    @if($discount)
                        <span style="display:inline-block;padding:3px 10px;background:var(--gl-dark);color:#fff;font-size:10px;font-weight:700;letter-spacing:.5px;border-radius:50px;margin-bottom:8px;">{{ $discount }}% {{ __('off') }}</span>
                    @elseif($campaign_name)
                        <span style="display:inline-block;padding:3px 10px;background:var(--gl-gold);color:#fff;font-size:10px;font-weight:700;letter-spacing:.5px;border-radius:50px;margin-bottom:8px;">{{ $campaign_name }}</span>
                    @elseif($product->badge?->name)
                        <span style="display:inline-block;padding:3px 10px;background:var(--gl-gold-pale);color:var(--gl-gold);font-size:10px;font-weight:700;letter-spacing:.5px;border-radius:50px;margin-bottom:8px;">{{ $product->badge->name }}</span>
                    @endif

                    <h3 style="font-size:15px;font-weight:400;color:var(--gl-dark);margin:0 0 6px;line-height:1.4;">
                        <a href="{{ $product_url }}" style="color:inherit;text-decoration:none;">{{ \Illuminate\Support\Str::words($product->name, 12) }}</a>
                    </h3>
                    <div style="margin-bottom:8px;">{!! theme_star_rating($product) !!}</div>
                    @if($product->summary)
                        <p style="font-size:13px;color:var(--gl-muted);margin:0 0 12px;line-height:1.6;">{{ \Illuminate\Support\Str::words($product->summary, 20) }}</p>
                    @endif
                </div>

                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <div>
                        <span style="font-size:18px;font-weight:600;color:var(--gl-dark);">{{ amount_with_currency_symbol($sale_price) }}</span>
                        @if($regular_price)
                            <span style="font-size:13px;color:var(--gl-muted);text-decoration:line-through;margin-left:6px;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        @endif
                    </div>
                    <button class="add-to-cart-btn"
                            data-product_id="{{ $product->id }}"
                            data-slug="{{ $product->slug }}"
                            style="padding:9px 20px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                            onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                        {{ __('Add to Cart') }}
                    </button>
                    <button class="add-to-wishlist-btn"
                            data-product_id="{{ $product->id }}"
                            style="width:38px;height:38px;border-radius:50%;border:1.5px solid var(--gl-border);background:transparent;color:var(--gl-muted);font-size:16px;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;"
                            onmouseover="this.style.borderColor='var(--gl-gold)';this.style.color='var(--gl-gold)'" onmouseout="this.style.borderColor='var(--gl-border)';this.style.color='var(--gl-muted)'"
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
            <button onclick="glFilterRequest({{ $page }})"
                    style="width:38px;height:38px;border-radius:50%;border:1.5px solid {{ $page == $current_page ? 'var(--gl-dark)' : 'var(--gl-border)' }};background:{{ $page == $current_page ? 'var(--gl-dark)' : '#fff' }};color:{{ $page == $current_page ? '#fff' : 'var(--gl-muted)' }};font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
