{{-- Brand Logos — global tenant widget, carousel fetched from Brands table --}}
@php $uid = 'gb-brand-' . substr(md5(uniqid()), 0, 8); @endphp
<section class="{{ $uid }}-section gb-brands-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        @if(!empty($title))
        <div class="gb-brands-head">
            <h2 class="gb-brands-title">{{ $title }}</h2>
        </div>
        @endif

        @if($slides->isNotEmpty())
        <div class="gb-brands-carousel" id="{{ $uid }}-carousel">

            {{-- Slides --}}
            <div class="gb-brands-slides-wrap" id="{{ $uid }}-slides">
                @foreach($slides as $si => $slide)
                <div class="gb-brands-slide{{ $si === 0 ? ' active' : '' }}" data-slide="{{ $si }}">
                    <div class="gb-brands-row">
                        @foreach($slide as $brand)
                        @php
                            $logoUrl = null;
                            if (!empty($brand->image_id)) {
                                $d = get_attachment_image_by_id($brand->image_id);
                                $logoUrl = $d['img_url'] ?? null;
                            }
                            $brandUrl = !empty($brand->url) ? $brand->url : '#';
                        @endphp
                        <a href="{{ $brandUrl }}" class="gb-brand-logo-item"
                           {{ $brandUrl !== '#' ? 'target="_blank" rel="noopener"' : '' }}>
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $brand->name ?? '' }}" class="gb-brand-logo-img" loading="lazy">
                            @else
                                <span class="gb-brand-logo-text">{{ $brand->name ?? '' }}</span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Arrows (only if more than 1 slide) --}}
            @if($slides->count() > 1)
            <button class="gb-brands-arr gb-brands-prev" id="{{ $uid }}-prev" aria-label="{{ __('Previous') }}">
                <i class="las la-angle-left"></i>
            </button>
            <button class="gb-brands-arr gb-brands-next" id="{{ $uid }}-next" aria-label="{{ __('Next') }}">
                <i class="las la-angle-right"></i>
            </button>

            {{-- Dots --}}
            <div class="gb-brands-dots" id="{{ $uid }}-dots">
                @foreach($slides as $si => $slide)
                <button class="gb-brands-dot{{ $si === 0 ? ' active' : '' }}"
                        data-index="{{ $si }}" aria-label="{{ __('Slide') }} {{ $si + 1 }}"></button>
                @endforeach
            </div>
            @endif

        </div>
        @else
        {{-- Placeholder --}}
        <div class="gb-brands-row">
            @foreach(range(1,6) as $i)
            <div class="gb-brand-logo-item gb-brand-ph">
                <span class="gb-brand-logo-text">Brand {{ $i }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<style>
.gb-brands-section {
    background: #fff;

}
.gb-brands-head { text-align: center; margin-bottom: 36px; }
.gb-brands-title { font-size: clamp(1.4rem, 2.5vw, 2rem); font-weight: 700; color: #1a1a1a; margin: 0; }

.gb-brands-carousel { position: relative; overflow: hidden; }
.gb-brands-slides-wrap { position: relative; min-height: 80px; }
.gb-brands-slide { display: none; }
.gb-brands-slide.active { display: block; }

.gb-brands-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    padding: 12px 0 48px;
}
.gb-brand-logo-item {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 18px 28px;
    background: #fff;
    border: 1px solid #f0ebe3;
    border-radius: 10px;
    text-decoration: none;
    transition: box-shadow .22s, border-color .22s;
    min-width: 120px;
    min-height: 72px;
}
.gb-brand-logo-item:hover {
    box-shadow: 0 4px 18px rgba(0,0,0,.09);
    border-color: #e0d9d0;
    text-decoration: none;
}
.gb-brand-logo-img {
    max-height: 48px;
    max-width: 110px;
    width: auto;
    object-fit: contain;
    filter: grayscale(100%);
    opacity: 0.55;
    transition: filter .22s, opacity .22s;
}
.gb-brand-logo-item:hover .gb-brand-logo-img { filter: grayscale(0%); opacity: 1; }
.gb-brand-logo-text { font-size: 13px; font-weight: 600; color: #bbb; }

/* Arrows */
.gb-brands-arr {
    position: absolute;
    top: calc(50% - 24px);
    transform: translateY(-50%);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1.5px solid #e0d9d0;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #555;
    font-size: 14px;
    z-index: 2;
    transition: background .2s, border-color .2s, color .2s;
    box-shadow: 0 1px 6px rgba(0,0,0,.08);
    padding: 0;
}
.gb-brands-arr:hover { background: var(--main-color-one, #3D8870); border-color: var(--main-color-one, #3D8870); color: #fff; }
.gb-brands-prev { left: 0; }
.gb-brands-next { right: 0; }

/* Dots */
.gb-brands-dots {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding-bottom: 4px;
}
.gb-brands-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    border: none;
    background: #d0c8c0;
    cursor: pointer;
    padding: 0;
    transition: background .2s, width .2s;
}
.gb-brands-dot.active { background: var(--main-color-one, #3D8870); width: 22px; border-radius: 4px; }

@media (max-width: 768px) {
    .gb-brand-logo-item { padding: 14px 18px; min-width: 90px; }
}
@media (max-width: 480px) {
    .gb-brand-logo-item { padding: 12px 14px; min-width: 76px; }
    .gb-brand-logo-img { max-height: 36px; }
}
</style>

<script>
(function(){
    var total = {{ $slides->count() }};
    if (total < 2) return;
    var current = 0;
    var slides  = document.querySelectorAll('#{{ $uid }}-slides .gb-brands-slide');
    var dots    = document.querySelectorAll('#{{ $uid }}-dots .gb-brands-dot');
    var prev    = document.getElementById('{{ $uid }}-prev');
    var next    = document.getElementById('{{ $uid }}-next');
    var timer   = null;

    function goTo(idx) {
        slides[current].classList.remove('active');
        if (dots[current]) dots[current].classList.remove('active');
        current = (idx + total) % total;
        slides[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
    }

    function startAuto() {
        timer = setInterval(function(){ goTo(current + 1); }, 4000);
    }

    if (prev) prev.addEventListener('click', function(){ clearInterval(timer); goTo(current - 1); startAuto(); });
    if (next) next.addEventListener('click', function(){ clearInterval(timer); goTo(current + 1); startAuto(); });

    dots.forEach(function(dot, i){
        dot.addEventListener('click', function(){ clearInterval(timer); goTo(i); startAuto(); });
    });

    startAuto();
})();
</script>
