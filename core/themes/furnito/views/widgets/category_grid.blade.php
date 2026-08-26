{{-- Furnito: Category Grid — horizontal image-card slider --}}
@php $uid = 'fn-catg-' . substr(md5(uniqid()), 0, 6); @endphp

<section class="fn-catg-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="fn-catg-container">
        @if($title)
        <div class="fn-catg-head">
            <h2 class="fn-catg-title">{{ $title }}</h2>
            @if(!empty($subtitle))<p class="fn-catg-sub">{{ $subtitle }}</p>@endif
        </div>
        @endif

        <div class="fn-catg-slider-wrap" id="{{ $uid }}">
            {{-- Prev arrow --}}
            <button class="fn-catg-arr fn-catg-arr-prev" aria-label="{{ __('Previous') }}">
                <i class="las la-angle-left"></i>
            </button>

            {{-- Overflow mask --}}
            <div class="fn-catg-overflow">
                {{-- Slider track --}}
                <div class="fn-catg-track">
                    @forelse($categories as $category)
                    @php
                        $has_img = !empty($category->image_id);
                        $img_url = $has_img ? theme_category_image($category) : null;
                        $url     = theme_category_url($category->slug);
                        $count   = $category->product_count ?? 0;
                    @endphp
                    <a href="{{ $url }}" class="fn-catg-card"
                       @if($has_img) style="background-image:url('{{ $img_url }}')" @endif>
                        <div class="fn-catg-overlay"></div>
                        <div class="fn-catg-info">
                            <div class="fn-catg-name">{{ $category->name }}</div>
                            <div class="fn-catg-count">{{ $count }} {{ __('Items') }}</div>
                        </div>
                    </a>
                    @empty
                    <p class="text-muted py-3">{{ __('No categories found.') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- Next arrow --}}
            <button class="fn-catg-arr fn-catg-arr-next" aria-label="{{ __('Next') }}">
                <i class="las la-angle-right"></i>
            </button>
        </div>
    </div>
</section>

<style>
.fn-catg-section { background: #fff; }
.fn-catg-container { max-width: 1320px; margin: 0 auto; padding: 0 40px; }
.fn-catg-head { text-align: center; margin-bottom: 40px; }
.fn-catg-title { font-size: clamp(1.6rem, 3vw, 2.2rem); font-weight: 800; color: #1a1a1a; margin: 0; }
.fn-catg-sub { font-size: 15px; color: #888; margin-top: 8px; margin-bottom: 0; }

/* Slider wrapper */
.fn-catg-slider-wrap { display: flex; align-items: center; gap: 16px; }
.fn-catg-overflow { flex: 1; overflow: hidden; padding: 8px 2px; }
.fn-catg-track { display: flex; gap: 14px; transition: transform .38s cubic-bezier(.4,0,.2,1); will-change: transform; }

/* Individual card */
.fn-catg-card {
    flex: 0 0 calc((100% - 14px * 5) / 6);
    min-width: 0;
    aspect-ratio: 1 / 1.1;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    text-decoration: none;
    display: block;
    background-color: #2c3a30;
    background-size: cover;
    background-position: center;
    transition: transform .28s ease, box-shadow .28s ease;
}
.fn-catg-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(0,0,0,.18); text-decoration: none; }

/* Gradient overlay */
.fn-catg-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.72) 0%, rgba(0,0,0,.22) 50%, rgba(0,0,0,.04) 100%);
    border-radius: 12px;
    transition: background .28s;
}
.fn-catg-card:hover .fn-catg-overlay { background: linear-gradient(to top, rgba(0,0,0,.82) 0%, rgba(0,0,0,.32) 55%, rgba(0,0,0,.06) 100%); }

/* Card text */
.fn-catg-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 14px 14px 16px;
    text-align: center;
    z-index: 1;
}
.fn-catg-name { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.3; margin-bottom: 3px; }
.fn-catg-count { font-size: 12px; color: rgba(255,255,255,.80); font-weight: 400; }

/* Arrow buttons */
.fn-catg-arr {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    background: #fff;
    border: 1.5px solid #ddd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 14px;
    color: #555;
    transition: border-color .2s, color .2s, box-shadow .2s;
    box-shadow: 0 1px 6px rgba(0,0,0,.10);
    padding: 0;
}
.fn-catg-arr:hover { border-color: #3D8870; color: #3D8870; box-shadow: 0 2px 10px rgba(61,136,112,.22); }
.fn-catg-arr:disabled { opacity: .3; pointer-events: none; }

/* Responsive */
@media (max-width: 1100px) { .fn-catg-card { flex: 0 0 calc((100% - 14px * 3) / 4); } }
@media (max-width: 768px)  { .fn-catg-card { flex: 0 0 calc((100% - 14px * 2) / 3); } }
@media (max-width: 480px)  { .fn-catg-card { flex: 0 0 calc((100% - 14px) / 2); } .fn-catg-container { padding: 0 16px; } }
</style>

<script>
(function () {
    var wrap  = document.getElementById('{{ $uid }}');
    if (!wrap) return;

    var overflow = wrap.querySelector('.fn-catg-overflow');
    var track    = wrap.querySelector('.fn-catg-track');
    var prev     = wrap.querySelector('.fn-catg-arr-prev');
    var next     = wrap.querySelector('.fn-catg-arr-next');
    var cards    = track.querySelectorAll('.fn-catg-card');
    if (!cards.length) return;

    var idx = 0;

    function visibleCount() {
        var w = overflow.offsetWidth;
        if (w < 400) return 2;
        if (w < 640) return 3;
        if (w < 900) return 4;
        return 6;
    }

    function maxIdx() { return Math.max(0, cards.length - visibleCount()); }

    function cardStep() {
        if (!cards[0]) return 0;
        return cards[0].offsetWidth + 14;
    }

    function update() {
        track.style.transform = 'translateX(-' + (idx * cardStep()) + 'px)';
        prev.disabled = idx === 0;
        next.disabled = idx >= maxIdx();
    }

    prev.addEventListener('click', function () { if (idx > 0) { idx--; update(); } });
    next.addEventListener('click', function () { if (idx < maxIdx()) { idx++; update(); } });

    update();
    window.addEventListener('resize', function () { idx = Math.min(idx, maxIdx()); update(); });
})();
</script>
