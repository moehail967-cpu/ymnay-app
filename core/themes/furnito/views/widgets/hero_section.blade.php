{{-- Furnito: Hero Section — multi-slide carousel, left text + right product image --}}
@php $uid = 'fn-hero-' . substr(md5(json_encode($slides)), 0, 8); @endphp

<section class="fn-hero" id="{{ $uid }}" style="min-height:{{ $min_height }}px;">

    {{-- Slides --}}
    <div class="fn-hero-track">
        @foreach($slides as $i => $slide)
        <div class="fn-hero-slide{{ $i === 0 ? ' active' : '' }}">
            <div class="fn-hero-inner">

                {{-- Left: text --}}
                <div class="fn-hero-content">
                    <h1 class="fn-hero-title">{!! nl2br(e($slide['title'])) !!}</h1>
                    @if(!empty($slide['description']))
                    <p class="fn-hero-desc">{{ $slide['description'] }}</p>
                    @endif
                    <a href="{{ $slide['button_url'] }}" class="fn-hero-btn">{{ $slide['button_text'] }}</a>
                </div>

                {{-- Right: image --}}
                <div class="fn-hero-visual">
                    @if(!empty($slide['hero_img']))
                        <img src="{{ $slide['hero_img'] }}" alt="{{ $slide['title'] }}" class="fn-hero-img" loading="eager">
                    @else
                        <div class="fn-hero-placeholder">
                            <i class="las la-couch"></i>
                        </div>
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    </div>

    {{-- Arrows (only render when > 1 slide) --}}
    @if(count($slides) > 1)
    <button class="fn-hero-arrow fn-hero-prev" aria-label="Previous slide">
        <i class="las la-angle-left"></i>
    </button>
    <button class="fn-hero-arrow fn-hero-next" aria-label="Next slide">
        <i class="las la-angle-right"></i>
    </button>

    {{-- Dots --}}
    <div class="fn-hero-dots">
        @foreach($slides as $i => $slide)
        <button class="fn-hero-dot{{ $i === 0 ? ' active' : '' }}" data-index="{{ $i }}" aria-label="Go to slide {{ $i + 1 }}"></button>
        @endforeach
    </div>
    @endif

</section>

<style>
/* ── Furnito Hero Section ── */
.fn-hero {
    background: #EEF3F7;
    overflow: hidden;
    position: relative;
}
.fn-hero-track {
    position: relative;
    width: 100%;
    height: 100%;
}
.fn-hero-slide {
    display: none;
    animation: fnFadeIn .45s ease;
}
.fn-hero-slide.active {
    display: block;
}
@keyframes fnFadeIn {
    from { opacity: 0; transform: translateX(18px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* Inner two-column layout */
.fn-hero-inner {
    display: flex;
    align-items: center;
    min-height: {{ $min_height }}px;
    max-width: 1320px;
    margin: 0 auto;
    padding: 80px 80px;
}

/* Left text column */
.fn-hero-content {
    flex: 0 0 46%;
    max-width: 46%;
    padding-right: 48px;
    padding-top: 60px;
    padding-bottom: 60px;
    z-index: 2;
}
.fn-hero-title {
    font-size: clamp(2.2rem, 3.8vw, 3.4rem);
    font-weight: 800;
    line-height: 1.12;
    color: #1f1f1f;
    margin: 0 0 20px;
    letter-spacing: -0.5px;
}
.fn-hero-desc {
    font-size: 15px;
    line-height: 1.75;
    color: #666;
    margin: 0 0 36px;
    max-width: 440px;
}
.fn-hero-btn {
    display: inline-block;
    padding: 14px 36px;
    background: #3D8870;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    letter-spacing: 0.4px;
    border-radius: 0;
    transition: background .2s;
}
.fn-hero-btn:hover {
    background: #2f7060;
    color: #fff;
    text-decoration: none;
}

/* Right image column */
.fn-hero-visual {
    flex: 1;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    align-self: stretch;
    padding-top: 24px;
    overflow: visible;
}
.fn-hero-img {
    display: block;
    max-height: {{ $min_height - 20 }}px;
    width: auto;
    max-width: 100%;
    object-fit: contain;
    object-position: bottom center;
    position: relative;
    z-index: 1;
}
.fn-hero-placeholder {
    width: 100%;
    height: {{ $min_height - 80 }}px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9rem;
    color: #3D8870;
    opacity: 0.25;
}

/* Arrows */
.fn-hero-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 38px;
    height: 38px;
    background: rgba(255,255,255,0.85);
    border: 1px solid #dde4ea;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #444;
    z-index: 10;
    transition: background .18s, color .18s, border-color .18s;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.fn-hero-arrow:hover {
    background: #3D8870;
    color: #fff;
    border-color: #3D8870;
}
.fn-hero-prev { left: 18px; }
.fn-hero-next { right: 18px; }
.fn-hero-arrow i { font-size: 14px; line-height: 1; }

/* Dots */
.fn-hero-dots {
    position: absolute;
    bottom: 18px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 7px;
    z-index: 10;
}
.fn-hero-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #b8c8d4;
    border: none;
    cursor: pointer;
    padding: 0;
    transition: background .2s, transform .2s;
}
.fn-hero-dot.active {
    background: #3D8870;
    transform: scale(1.25);
}

/* Responsive */
@media (max-width: 992px) {
    .fn-hero-inner {
        flex-direction: column;
        padding: 40px 24px;
        min-height: auto;
    }
    .fn-hero-content {
        flex: none;
        max-width: 100%;
        padding-right: 0;
        padding-top: 0;
        padding-bottom: 28px;
        text-align: center;
    }
    .fn-hero-desc { margin-left: auto; margin-right: auto; }
    .fn-hero-visual {
        width: 100%;
        align-self: auto;
        justify-content: center;
    }
    .fn-hero-img { max-height: 320px; }
    .fn-hero-placeholder { height: 280px; }
    .fn-hero-prev { left: 8px; }
    .fn-hero-next { right: 8px; }
}
@media (max-width: 576px) {
    .fn-hero-title { font-size: 2rem; }
    .fn-hero-img { max-height: 220px; }
}
</style>

<script>
(function () {
    var section = document.getElementById('{{ $uid }}');
    if (!section) return;

    var slides = section.querySelectorAll('.fn-hero-slide');
    var dots   = section.querySelectorAll('.fn-hero-dot');
    var prev   = section.querySelector('.fn-hero-prev');
    var next   = section.querySelector('.fn-hero-next');
    var total  = slides.length;
    var current = 0;
    var timer;

    if (total <= 1) return;

    function goTo(idx) {
        slides[current].classList.remove('active');
        if (dots[current]) dots[current].classList.remove('active');
        current = (idx + total) % total;
        slides[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
    }

    function startTimer() {
        clearInterval(timer);
        timer = setInterval(function () { goTo(current + 1); }, 5000);
    }

    if (prev) prev.addEventListener('click', function () { goTo(current - 1); startTimer(); });
    if (next) next.addEventListener('click', function () { goTo(current + 1); startTimer(); });

    dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () { goTo(i); startTimer(); });
    });

    startTimer();
})();
</script>
