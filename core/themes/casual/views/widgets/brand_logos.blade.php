{{-- Casual: Brand Logos --}}
@php $uid = 'csbrand' . substr(md5(uniqid()), 0, 8); @endphp
<section class="cs-brands-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        @if($slides->isNotEmpty())
        <div class="cs-brands-carousel" id="{{ $uid }}-carousel">
            <div class="cs-brands-slides-wrap" id="{{ $uid }}-slides">
                @foreach($slides as $si => $slide)
                <div class="cs-brands-slide{{ $si === 0 ? ' active' : '' }}" data-slide="{{ $si }}">
                    <div class="cs-brands-row">
                        @foreach($slide as $brand)
                        @php
                            $logoUrl = null;
                            if (!empty($brand->image_id)) {
                                $d = get_attachment_image_by_id($brand->image_id);
                                $logoUrl = $d['img_url'] ?? null;
                            }
                            $brandUrl = !empty($brand->url) ? $brand->url : '#';
                        @endphp
                        <a href="{{ $brandUrl }}" class="cs-brand-logo-item" target="{{ $brandUrl !== '#' ? '_blank' : '_self' }}" rel="noopener">
                            @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $brand->name ?? '' }}" class="cs-brand-logo-img" loading="lazy">
                            @else
                            <span class="cs-brand-logo-text">{{ $brand->name ?? '' }}</span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Arrows --}}
            <button class="cs-brands-arr cs-brands-prev" id="{{ $uid }}-prev" aria-label="{{ __('Previous') }}">
                <i class="las la-angle-left"></i>
            </button>
            <button class="cs-brands-arr cs-brands-next" id="{{ $uid }}-next" aria-label="{{ __('Next') }}">
                <i class="las la-angle-right"></i>
            </button>

            {{-- Dots --}}
            <div class="cs-brands-dots" id="{{ $uid }}-dots">
                @foreach($slides as $si => $slide)
                <button class="cs-brands-dot{{ $si === 0 ? ' active' : '' }}" data-index="{{ $si }}" aria-label="{{ __('Slide') }} {{ $si + 1 }}"></button>
                @endforeach
            </div>
        </div>
        @else
        <p class="cs-no-data">{{ __('No brands found.') }}</p>
        @endif
    </div>
</section>

<script>
(function(){
    var total  = {{ $slides->count() }};
    if (total < 1) return;
    var current = 0;
    var slides  = document.querySelectorAll('#{{ $uid }}-slides .cs-brands-slide');
    var dots    = document.querySelectorAll('#{{ $uid }}-dots .cs-brands-dot');
    var prev    = document.getElementById('{{ $uid }}-prev');
    var next    = document.getElementById('{{ $uid }}-next');
    var timer   = null;

    function goTo(idx) {
        slides[current].classList.remove('active');
        dots[current] && dots[current].classList.remove('active');
        current = (idx + total) % total;
        slides[current].classList.add('active');
        dots[current] && dots[current].classList.add('active');
    }

    function startAuto() {
        timer = setInterval(function(){ goTo(current + 1); }, 4000);
    }

    if (prev) prev.addEventListener('click', function(){ clearInterval(timer); goTo(current - 1); startAuto(); });
    if (next) next.addEventListener('click', function(){ clearInterval(timer); goTo(current + 1); startAuto(); });

    dots.forEach(function(dot, i){
        dot.addEventListener('click', function(){ clearInterval(timer); goTo(i); startAuto(); });
    });

    if (total > 1) startAuto();
})();
</script>
