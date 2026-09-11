{{-- GoldCraft: Product grid partial --}}
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

    <div class="col-6 col-md-4">
        <div class="gc-card h-100 d-flex flex-column">
            <div class="gc-card-img">
                @if($img_url)
                    <a href="{{ $product_url }}">
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    </a>
                @else
                    <a href="{{ $product_url }}" style="font-size:48px;display:flex;align-items:center;justify-content:center;"><i class="las la-gem"></i></a>
                @endif

                @if($discount)
                    <span class="gc-card-badge gc-badge-new">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="gc-card-badge gc-badge-custom">{{ $badge }}</span>
                @elseif($campaign_name)
                    <span class="gc-card-badge" style="background:var(--gc-gold);color:var(--gc-dark);">{{ $campaign_name }}</span>
                @endif
            </div>

            <div class="gc-card-body d-flex flex-column flex-grow-1">
                <div class="gc-card-material">{{ __('Artisan') }}</div>
                <div class="gc-card-name flex-grow-1">
                    <a href="{{ $product_url }}" style="color:inherit;text-decoration:none;">{{ \Illuminate\Support\Str::words($product->name, 8) }}</a>
                </div>
                <div style="margin-bottom:8px;">{!! theme_star_rating($product) !!}</div>
                <div class="gc-card-footer">
                    <span class="gc-price">{{ amount_with_currency_symbol($sale_price) }}</span>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <button class="gc-cart-btn add-to-cart-btn" data-product_id="{{ $product->id }}"
                                style="display:inline-flex;align-items:center;justify-content:center;gap:5px;">
                            <i class="las la-shopping-bag" style="font-size:14px;"></i>
                            <span>{{ __('Add') }}</span>
                        </button>
                        <button class="add-to-wishlist-btn" data-product_id="{{ $product->id }}"
                                style="width:32px;height:32px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--gc-muted);font-size:14px;transition:all .2s;"
                                onmouseover="this.style.background='var(--gc-rose)';this.style.color='#fff';this.style.borderColor='var(--gc-rose)'"
                                onmouseout="this.style.background='transparent';this.style.color='var(--gc-muted)';this.style.borderColor='var(--gc-border)'"
                                aria-label="{{ __('Wishlist') }}">
                            <i class="las la-heart"></i>
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
