{{-- Medicom: Our Store (Redesigned) --}}
@php $uid = 'mcstore' . substr(md5(uniqid()), 0, 8); @endphp
<section class="mc-store-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <h2 class="mc-center-title">{{ $title }}</h2>

        {{-- Category filter tabs --}}
        <div class="mc-store-tabs" id="{{ $uid }}-tabs">
            <button class="mc-store-tab active" data-cat="all" data-target="{{ $uid }}">{{ __('All') }}</button>
            @foreach($categories as $cat)
            <button class="mc-store-tab" data-cat="{{ $cat->slug ?? $cat->id }}" data-target="{{ $uid }}">{{ $cat->name }}</button>
            @endforeach
        </div>

        {{-- Product grid --}}
        <div class="mc-store-grid" id="{{ $uid }}">
            @forelse($products as $product)
            @php
                $pImg     = theme_product_image($product->image_id ?? null, 'grid');
                $pd       = get_product_dynamic_price($product);
                $price    = $pd['sale_price'];
                $oldPrice = $pd['regular_price'];
                $discount = $pd['discount'];
                $catSlug  = $product->category?->slug ?? '';
                $catName  = $product->category?->name ?? 'Category';
                $pUrl     = theme_product_url($product->slug ?? $product->id);
            @endphp
            <div class="mc-card" data-cat="{{ $catSlug }}">
                <div class="mc-card-img-wrap">
                    <a href="{{ $pUrl }}">
                        @if($pImg)
                        <img src="{{ $pImg }}" alt="{{ $product->name }}" class="mc-card-img" loading="lazy">
                        @else
                        <div class="mc-card-img-ph"><i class="las la-laptop"></i></div>
                        @endif
                    </a>
                    <button class="add-to-wishlist-btn mc-card-wish" data-product_id="{{ $product->id }}" title="{{ __('Wishlist') }}">
                        <i class="las la-heart"></i>
                    </button>
                    @if($discount)
                    <span class="mc-card-badge">{{ $discount }}% {{ __('off') }}</span>
                    @endif
                </div>
                <div class="mc-card-body">
                    <span class="mc-card-cat">{{ $catName }}</span>
                    <a href="{{ $pUrl }}" class="mc-card-name">{{ Str::limit($product->name, 45) }}</a>
                    <div class="mc-card-prices">
                        <span class="mc-card-price">{{ amount_with_currency_symbol($price) }}</span>
                        @if($oldPrice)
                        <span class="mc-card-old">{{ amount_with_currency_symbol($oldPrice) }}</span>
                        @endif
                    </div>
                    <button class="add-to-cart-btn mc-card-atc" data-product_id="{{ $product->id }}" title="{{ __('Add to Cart') }}">
                        <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                    </button>
                </div>
            </div>
            @empty
            <p class="mc-no-data">{{ __('No products found.') }}</p>
            @endforelse
        </div>
    </div>
</section>

<script>
(function(){
    var tabs = document.querySelectorAll('[data-target="{{ $uid }}"]');
    var grid = document.getElementById('{{ $uid }}');
    if (!grid || !tabs.length) return;
    tabs.forEach(function(tab){
        tab.addEventListener('click', function(){
            tabs.forEach(function(t){ t.classList.remove('active'); });
            tab.classList.add('active');
            var cat = tab.getAttribute('data-cat');
            grid.querySelectorAll('.mc-card').forEach(function(card){
                card.style.display = (cat === 'all' || card.getAttribute('data-cat') === cat) ? '' : 'none';
            });
        });
    });
})();
</script>
