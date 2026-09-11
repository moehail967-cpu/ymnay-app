{{-- SportZone: Product list partial (horizontal card AJAX view) --}}
@foreach($products as $product)
    @php
        $data          = theme_product_price($product);
        $regular_price = $data['regular_price'];
        $sale_price    = $data['sale_price'];
        $discount      = $data['discount'];
        $campaign_name = $data['campaign_name'];
        $img_url       = theme_product_image($product->image_id ?? null, 'grid');
        $badge         = $product->badge?->name ?? null;
        $product_url   = theme_product_url($product->slug);
    @endphp

    <div class="col-12" style="margin-bottom:0;">
        <div style="background:var(--sz-white);border:2px solid var(--sz-border);display:flex;align-items:center;gap:0;transition:border-color .2s;"
             onmouseover="this.style.borderColor='var(--sz-red)'"
             onmouseout="this.style.borderColor='var(--sz-border)'">

            {{-- Image --}}
            <div style="width:150px;flex-shrink:0;position:relative;background:var(--sz-bg);overflow:hidden;aspect-ratio:1/1;">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"
                             style="width:100%;height:100%;object-fit:cover;transition:transform .3s;"
                             onmouseover="this.style.transform='scale(1.05)'"
                             onmouseout="this.style.transform='scale(1)'">
                    </a>
                @else
                    <a href="{{ $product_url }}" style="display:flex;align-items:center;justify-content:center;height:100%;text-decoration:none;">
                        <i class="mdi mdi-dumbbell" style="font-size:40px;color:var(--sz-red);opacity:.3;"></i>
                    </a>
                @endif

                @if($discount)
                    <span style="position:absolute;top:8px;left:8px;background:var(--sz-red);color:#fff;font-family:var(--sz-font-head);font-size:10px;padding:3px 8px;text-transform:uppercase;letter-spacing:1px;">
                        {{ $discount }}% {{ __('off') }}
                    </span>
                @elseif($badge)
                    <span style="position:absolute;top:8px;left:8px;background:var(--sz-navy);color:#fff;font-family:var(--sz-font-head);font-size:10px;padding:3px 8px;text-transform:uppercase;letter-spacing:1px;">
                        {{ $badge }}
                    </span>
                @elseif($campaign_name)
                    <span style="position:absolute;top:8px;left:8px;background:var(--sz-navy-mid);color:#fff;font-family:var(--sz-font-head);font-size:10px;padding:3px 8px;text-transform:uppercase;letter-spacing:1px;">
                        {{ $campaign_name }}
                    </span>
                @endif
            </div>

            {{-- Body --}}
            <div style="flex:1;min-width:0;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div style="flex:1;min-width:180px;">
                    @if($product->category)
                        <div style="font-family:var(--sz-font-head);font-size:11px;color:var(--sz-red);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:4px;">
                            {{ $product->category->name }}
                        </div>
                    @endif
                    <div style="font-family:var(--sz-font-head);font-size:16px;font-weight:700;color:var(--sz-dark);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                        <a href="{{ $product_url }}" style="color:inherit;text-decoration:none;transition:color .2s;"
                           onmouseover="this.style.color='var(--sz-red)'"
                           onmouseout="this.style.color='var(--sz-dark)'">
                            {{ \Illuminate\Support\Str::words($product->name, 8) }}
                        </a>
                    </div>
                    <div class="sz-stars" style="margin-bottom:8px;">{!! theme_star_rating($product) !!}</div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span class="sz-price-sale" style="font-size:18px;">{{ amount_with_currency_symbol($sale_price) }}</span>
                        @if($regular_price)
                            <span class="sz-price-orig" style="font-size:14px;">{{ amount_with_currency_symbol($regular_price) }}</span>
                        @endif
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                    <button class="add-to-wishlist-btn"
                            data-product_id="{{ $product->id }}"
                            aria-label="{{ __('Wishlist') }}"
                            style="width:38px;height:38px;border:2px solid var(--sz-border);background:transparent;cursor:pointer;font-size:18px;color:var(--sz-muted);display:flex;align-items:center;justify-content:center;transition:all .2s;"
                            onmouseover="this.style.borderColor='var(--sz-red)';this.style.color='var(--sz-red)'"
                            onmouseout="this.style.borderColor='var(--sz-border)';this.style.color='var(--sz-muted)'">
                        <i class="mdi mdi-heart-outline"></i>
                    </button>
                    <button class="add-to-cart-btn sz-btn sz-btn-red sz-btn-sm"
                            data-id="{{ $product->id }}"
                            data-product_id="{{ $product->id }}"
                            data-slug="{{ $product->slug }}"
                            aria-label="{{ __('Add to cart') }}">
                        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                    </button>
                </div>
            </div>

        </div>
    </div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div style="display:flex;gap:6px;justify-content:center;margin-top:20px;flex-wrap:wrap;">
        @foreach($links as $page => $url)
            <button class="sz-btn sz-btn-sm {{ $page == $current_page ? 'sz-btn-red' : 'sz-btn-outline' }}"
                    style="min-width:36px;"
                    data-page="{{ $page }}"
                    onclick="szFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
