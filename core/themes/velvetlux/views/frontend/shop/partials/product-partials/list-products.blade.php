{{-- VelvetLux: Product list partial — horizontal layout --}}
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

    <div class="col-12">
        <div class="vl-list-card">
            {{-- Image --}}
            <a href="{{ $product_url }}" class="vl-list-img">
                @if($img_url)
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                @else
                    <div class="vl-cat-placeholder">◆</div>
                @endif

                @if($discount)
                    <span class="vl-card-badge">{{ $discount }}% {{ __('off') }}</span>
                @elseif($badge)
                    <span class="vl-card-badge">{{ $badge }}</span>
                @elseif($campaign_name)
                    <span class="vl-card-badge">{{ $campaign_name }}</span>
                @endif
            </a>

            {{-- Details --}}
            <div class="vl-list-body">
                <div>
                    <div class="vl-card-cat">{{ $product->category?->name ?? '' }}</div>
                    <h3 class="vl-card-name">
                        <a href="{{ $product_url }}" style="color:inherit;text-decoration:none;">{{ $product->name }}</a>
                    </h3>
                    @if($product->short_description)
                        <p class="vl-list-desc">{{ \Illuminate\Support\Str::words(strip_tags($product->short_description), 18) }}</p>
                    @endif
                </div>

                <div class="vl-list-footer">
                    <div class="vl-card-price">
                        <span class="vl-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                        @if($regular_price && $regular_price != $sale_price)
                            <span class="vl-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                        @endif
                    </div>
                    <div class="vl-list-actions">
                        <button class="vl-btn vl-btn-sm vl-btn-outline add-to-wishlist-btn"
                                data-product_id="{{ $product->id }}"
                                aria-label="{{ __('Wishlist') }}">
                            <i class="mdi mdi-heart-outline"></i>
                        </button>
                        <button class="vl-btn vl-btn-sm vl-btn-plum add-to-cart-btn"
                                data-product_id="{{ $product->id }}"
                                aria-label="{{ __('Add to cart') }}">
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
    <div class="vl-pagination">
        @foreach($links as $page => $url)
            <button class="vl-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="velvetluxFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
