@php
    $uid1 = 'bpfeat'   . substr(md5(uniqid()), 0, 8);
    $uid2 = 'bprecent' . substr(md5(uniqid()), 0, 8);
@endphp

{{-- Section 1: Featured Books --}}
<section class="bp-featured-section" style="padding-top:{{ $padding_top }}px;padding-bottom:40px;">
    <div class="container">
        <div class="bp-slider-head">
            <h2 class="bp-section-title">{{ $featured_title }}</h2>
            <div class="bp-slider-arrows">
                <button class="bp-arrow" id="{{ $uid1 }}-prev" aria-label="{{ __('Previous') }}"><i class="las la-angle-left"></i></button>
                <button class="bp-arrow" id="{{ $uid1 }}-next" aria-label="{{ __('Next') }}"><i class="las la-angle-right"></i></button>
            </div>
        </div>
        <div class="bp-slider-wrap" id="{{ $uid1 }}">
            <div class="bp-feat-track">
                @forelse($featured_products as $product)
                @php
                    $data     = theme_product_price($product);
                    $img_url  = theme_product_image($product->image_id ?? null, 'grid');
                    $url      = theme_product_url($product->slug);
                    $discount = $data['discount'];
                    $badge    = $product->badge?->name ?? null;
                @endphp
                <div class="bp-feat-item">
                    <a href="{{ $url }}" class="bp-catgrid-card">
                        <div class="bp-catgrid-img">
                            @if($img_url)
                                <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="bp-catgrid-placeholder"><i class="las la-book"></i></div>
                            @endif
                        </div>
                        <div class="bp-catgrid-info">
                            <div class="bp-catgrid-name">{{ \Illuminate\Support\Str::words($product->name, 5) }}</div>
                            <div class="bp-catgrid-cat">{{ $product->category?->name ?? '' }}</div>
                            <div class="bp-catgrid-price">{{ amount_with_currency_symbol($data['sale_price']) }}</div>
                        </div>
                    </a>
                </div>
                @empty
                <p class="text-muted py-3">{{ __('No products found.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

{{-- Section 2: Recently Sold --}}
<section class="bp-recent-section" style="padding-top:0;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="bp-slider-head">
            <h2 class="bp-section-title">{{ $recent_title }}</h2>
            <div class="bp-slider-arrows">
                <button class="bp-arrow" id="{{ $uid2 }}-prev" aria-label="{{ __('Previous') }}"><i class="las la-angle-left"></i></button>
                <button class="bp-arrow" id="{{ $uid2 }}-next" aria-label="{{ __('Next') }}"><i class="las la-angle-right"></i></button>
            </div>
        </div>
        <div class="bp-slider-wrap" id="{{ $uid2 }}">
            <div class="bp-recent-track">
                @forelse($recent_products as $product)
                @php
                    $data     = theme_product_price($product);
                    $img_url  = theme_product_image($product->image_id ?? null, 'grid');
                    $url      = theme_product_url($product->slug);
                    $discount = $data['discount'];
                    $badge    = $product->badge?->name ?? null;
                @endphp
                <div class="bp-recent-item">
                    <a href="{{ $url }}" class="bp-catgrid-card">
                        <div class="bp-catgrid-img">
                            @if($img_url)
                                <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="bp-catgrid-placeholder"><i class="las la-book"></i></div>
                            @endif
                        </div>
                        <div class="bp-catgrid-info">
                            <div class="bp-catgrid-name">{{ \Illuminate\Support\Str::words($product->name, 5) }}</div>
                            <div class="bp-catgrid-cat">{{ $product->category?->name ?? '' }}</div>
                            <div class="bp-catgrid-price">{{ amount_with_currency_symbol($data['sale_price']) }}</div>
                        </div>
                    </a>
                </div>
                @empty
                <p class="text-muted py-3">{{ __('No products found.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
<script>
(function(){
    function makeSlider(uid, scrollAmt) {
        var wrap = document.getElementById(uid);
        var prev = document.getElementById(uid + '-prev');
        var next = document.getElementById(uid + '-next');
        if (!wrap) return;
        if (prev) prev.addEventListener('click', function(){ wrap.scrollBy({ left: -scrollAmt, behavior: 'smooth' }); });
        if (next) next.addEventListener('click', function(){ wrap.scrollBy({ left:  scrollAmt, behavior: 'smooth' }); });
    }
    makeSlider('{{ $uid1 }}', 400);
    makeSlider('{{ $uid2 }}', 230);
})();
</script>
