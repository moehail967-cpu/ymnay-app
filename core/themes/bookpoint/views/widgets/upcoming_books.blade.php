@php $uid = 'bpupc' . substr(md5(uniqid()), 0, 8); @endphp
<section class="bp-upcoming-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="bp-slider-head">
            <h2 class="bp-section-title">{{ $title }}</h2>
            <div class="bp-slider-arrows">
                <button class="bp-arrow" id="{{ $uid }}-prev" aria-label="{{ __('Previous') }}"><i class="las la-angle-left"></i></button>
                <button class="bp-arrow" id="{{ $uid }}-next" aria-label="{{ __('Next') }}"><i class="las la-angle-right"></i></button>
            </div>
        </div>
        <div class="bp-slider-wrap" id="{{ $uid }}">
            <div class="bp-upcoming-track">
                @forelse($products as $product)
                @php
                    $data     = theme_product_price($product);
                    $img_url  = theme_product_image($product->image_id ?? null, 'grid');
                    $url      = theme_product_url($product->slug);
                    $discount = $data['discount'];
                    $badge    = $product->badge?->name ?? null;
                @endphp
                <div class="bp-upcoming-item">
                    <div class="bp-upcoming-card">
                        <div class="bp-upcoming-img-wrap">
                            <a href="{{ $url }}">
                                @if($img_url)
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="bp-upcoming-placeholder"><i class="las la-book"></i></div>
                                @endif
                            </a>
                            @if($discount)
                                <span class="bp-badge-chip bp-badge-green">{{ $discount }}% {{ __('off') }}</span>
                            @elseif($badge)
                                <span class="bp-badge-chip bp-badge-green">{{ $badge }}</span>
                            @endif
                        </div>
                        <div class="bp-upcoming-info">
                            <div class="bp-upcoming-name">
                                <a href="{{ $url }}">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                            </div>
                            <div class="bp-upcoming-author">{{ __('by') }} {{ $product->brand?->name ?? '' }}</div>
                            <div class="bp-upcoming-stars">{!! theme_star_rating($product) !!}</div>
                            <div class="bp-upcoming-price-row">
                                <span class="bp-price-main">{{ amount_with_currency_symbol($data['sale_price']) }}</span>
                                @if($data['regular_price'])
                                    <span class="bp-price-orig">{{ amount_with_currency_symbol($data['regular_price']) }}</span>
                                @endif
                                <a href="{{ $url }}" class="bp-preorder-link">{{ __('Pre-Order') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted">{{ __('No upcoming books.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
<script>
(function(){
    var wrap = document.getElementById('{{ $uid }}');
    var prev = document.getElementById('{{ $uid }}-prev');
    var next = document.getElementById('{{ $uid }}-next');
    if (!wrap) return;
    var scrollAmt = 460;
    if (prev) prev.addEventListener('click', function(){ wrap.scrollBy({ left: -scrollAmt, behavior: 'smooth' }); });
    if (next) next.addEventListener('click', function(){ wrap.scrollBy({ left:  scrollAmt, behavior: 'smooth' }); });
})();
</script>
