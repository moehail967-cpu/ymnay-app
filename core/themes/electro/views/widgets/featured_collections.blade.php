{{-- Electro: Featured Collections --}}
@php $uid = 'elfc' . substr(md5(uniqid()), 0, 8); @endphp
<section class="el-featured-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <h2 class="el-center-title">{{ $title }}</h2>

        @if($slides->isNotEmpty())
        <div class="el-featured-slider" id="{{ $uid }}-slider">
            @foreach($slides as $si => $slide)
            <div class="el-featured-slide{{ $si === 0 ? ' active' : '' }}" data-slide="{{ $si }}">
                <div class="el-featured-grid">
                    @foreach($slide as $product)
                    @php
                        $pImg     = theme_product_image($product->image_id ?? null, 'grid');
                        $pd       = get_product_dynamic_price($product);
                        $price    = $pd['sale_price'];
                        $oldPrice = $pd['regular_price'];
                        $discount = $pd['discount'];
                        $pUrl     = theme_product_url($product->slug ?? $product->id);
                    @endphp
                    <div class="el-card">
                        <div class="el-card-img-wrap">
                            <a href="{{ $pUrl }}">
                                @if($pImg)
                                <img src="{{ $pImg }}" alt="{{ $product->name }}" class="el-card-img" loading="lazy">
                                @else
                                <div class="el-card-img-ph"><i class="las la-laptop"></i></div>
                                @endif
                            </a>
                            <button class="add-to-wishlist-btn el-card-wish" data-product_id="{{ $product->id }}" title="{{ __('Wishlist') }}">
                                <i class="las la-heart"></i>
                            </button>
                            @if($discount)
                            <span class="el-card-badge">{{ $discount }}% {{ __('off') }}</span>
                            @endif
                        </div>
                        <div class="el-card-body">
                            <a href="{{ $pUrl }}" class="el-card-name">{{ Str::limit($product->name, 45) }}</a>
                            <div class="el-card-footer">
                                <div class="el-card-prices">
                                    <span class="el-card-price">{{ amount_with_currency_symbol($price) }}</span>
                                    @if($oldPrice)
                                    <span class="el-card-old">{{ amount_with_currency_symbol($oldPrice) }}</span>
                                    @endif
                                </div>
                                <button class="add-to-cart-btn el-card-atc" data-product_id="{{ $product->id }}" title="{{ __('Add to Cart') }}">
                                    <i class="las la-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Dots --}}
        @if($slides->count() > 1)
        <div class="el-dots" id="{{ $uid }}-dots">
            @foreach($slides as $si => $slide)
            <button class="el-dot{{ $si === 0 ? ' active' : '' }}" data-index="{{ $si }}" aria-label="{{ __('Slide') }} {{ $si + 1 }}"></button>
            @endforeach
        </div>
        @endif

        @else
        <p class="el-no-data">{{ __('No products found.') }}</p>
        @endif
    </div>
</section>

<script>
(function(){
    var total   = {{ $slides->count() }};
    if (total < 2) return;
    var current = 0;
    var slides  = document.querySelectorAll('#{{ $uid }}-slider .el-featured-slide');
    var dots    = document.querySelectorAll('#{{ $uid }}-dots .el-dot');

    function goTo(idx) {
        slides[current].classList.remove('active');
        dots[current] && dots[current].classList.remove('active');
        current = (idx + total) % total;
        slides[current].classList.add('active');
        dots[current] && dots[current].classList.add('active');
    }

    dots.forEach(function(dot, i){
        dot.addEventListener('click', function(){ goTo(i); });
    });
})();
</script>
