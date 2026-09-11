{{-- Furnito: Shop list products --}}
<div class="d-flex flex-column gap-3">
    @foreach($products as $product)
        @php
            $data    = theme_product_price($product);
            $img_url = theme_product_image($product->image_id ?? null, 'grid');
            $url     = theme_product_url($product->slug);
            $badge   = $product->badge?->name ?? null;
        @endphp

        <div class="fn-list-card">
            <div class="fn-list-card-img">
                <a href="{{ $url }}">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                    @else
                        <div style="width:100%;height:180px;display:flex;align-items:center;justify-content:center;font-size:48px;color:#3D8870;opacity:.25;">
                            <i class="las la-couch"></i>
                        </div>
                    @endif
                </a>
                @if($data['discount'])
                    <span class="fn-card-badge">{{ $data['discount'] }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="fn-card-badge">{{ $badge }}</span>
                @endif
            </div>

            <div class="fn-list-card-body">
                @if($product->category?->name)
                    <div class="fn-card-cat">{{ $product->category->name }}</div>
                @endif
                <div class="fn-list-card-name">
                    <a href="{{ $url }}">{{ \Illuminate\Support\Str::words($product->name, 10) }}</a>
                </div>
                <div>{!! theme_star_rating($product) !!}</div>
                <div class="fn-list-card-price">
                    <span class="fn-price-sale">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                    @if($data['regular_price'])
                        <span class="fn-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                    @endif
                </div>
                @if($product->summary)
                    <p class="fn-list-card-desc">{{ \Illuminate\Support\Str::words($product->summary, 20) }}</p>
                @endif
                <div class="fn-list-card-actions">
                    <button class="fn-list-card-atc add-to-cart-btn" data-product_id="{{ $product->id }}">
                        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                    </button>
                    <button class="fn-list-card-wish add-to-wishlist-btn" data-product_id="{{ $product->id }}">
                        <i class="las la-heart"></i> {{ __('Wishlist') }}
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if(count($links) > 1)
<div class="fn-pagination">
    @foreach($links as $page => $url)
        <a href="{{ $url }}" class="fn-page-btn {{ $page == $current_page ? 'active' : '' }}"
           data-page="{{ $page }}">{{ $page }}</a>
    @endforeach
</div>
@endif
