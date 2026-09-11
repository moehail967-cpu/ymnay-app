<style>
/* ── Brand Logos ── */
.hf-brands-section { background:#fff; }
.hf-brands-carousel { position:relative; overflow:hidden; }
.hf-brands-slides-wrap { position:relative; min-height:100px; }
.hf-brands-slide { display:none; }
.hf-brands-slide.active { display:block; }
.hf-brands-row { display:flex; align-items:center; justify-content:center; gap:32px; flex-wrap:wrap; padding:16px 0 48px; }
.hf-brand-logo-item { display:flex; align-items:center; justify-content:center; padding:16px 24px; background:#fff; border-radius:10px; transition:box-shadow .2s; text-decoration:none; }
.hf-brand-logo-item:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); }
.hf-brand-logo-img { max-height:48px; max-width:120px; width:auto; object-fit:contain; filter:grayscale(1); opacity:.6; transition:filter .2s,opacity .2s; }
.hf-brand-logo-item:hover .hf-brand-logo-img { filter:grayscale(0); opacity:1; }
.hf-brand-logo-text { font-size:13px; font-weight:600; color:#888; }
.hf-brands-arr { position:absolute; top:50%; transform:translateY(-50%); width:36px; height:36px; border-radius:50%; border:2px solid #e0d8d0; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#333; font-size:14px; z-index:2; transition:background .2s,border-color .2s,color .2s; }
.hf-brands-arr:hover { background:#ff7857; border-color:#ff7857; color:#fff; }
.hf-brands-prev { left:0; }
.hf-brands-next { right:0; }
.hf-brands-dots { display:flex; align-items:center; justify-content:center; gap:8px; padding-bottom:8px; }
.hf-brands-dot { width:8px; height:8px; border-radius:50%; border:none; background:#d0c8c0; cursor:pointer; padding:0; transition:background .2s,width .2s; }
.hf-brands-dot.active { background:#ff7857; width:22px; border-radius:4px; }
</style>
{{-- Casual: Brand Logos --}}
@php $uid = 'csbrand' . substr(md5(uniqid()), 0, 8); @endphp
<section class="hf-brands-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        @if($slides->isNotEmpty())
        <div class="hf-brands-carousel" id="{{ $uid }}-carousel">
            <div class="hf-brands-slides-wrap" id="{{ $uid }}-slides">
                @foreach($slides as $si => $slide)
                <div class="hf-brands-slide{{ $si === 0 ? ' active' : '' }}" data-slide="{{ $si }}">
                    <div class="hf-brands-row">
                        @foreach($slide as $brand)
                        @php
                            $logoUrl = null;
                            if (!empty($brand->image_id)) {
                                $d = get_attachment_image_by_id($brand->image_id);
                                $logoUrl = $d['img_url'] ?? null;
                            }
                            $brandUrl = !empty($brand->url) ? $brand->url : '#';
                        @endphp
                        <a href="{{ $brandUrl }}" class="hf-brand-logo-item" target="{{ $brandUrl !== '#' ? '_blank' : '_self' }}" rel="noopener">
                            @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $brand->name ?? '' }}" class="hf-brand-logo-img" loading="lazy">
                            @else
                            <span class="hf-brand-logo-text">{{ $brand->name ?? '' }}</span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Arrows --}}
            <button class="hf-brands-arr hf-brands-prev" id="{{ $uid }}-prev" aria-label="{{ __('Previous') }}">
                <i class="las la-angle-left"></i>
            </button>
            <button class="hf-brands-arr hf-brands-next" id="{{ $uid }}-next" aria-label="{{ __('Next') }}">
                <i class="las la-angle-right"></i>
            </button>

            {{-- Dots --}}
            <div class="hf-brands-dots" id="{{ $uid }}-dots">
                @foreach($slides as $si => $slide)
                <button class="hf-brands-dot{{ $si === 0 ? ' active' : '' }}" data-index="{{ $si }}" aria-label="{{ __('Slide') }} {{ $si + 1 }}"></button>
                @endforeach
            </div>
        </div>
        @else
        <p class="hf-no-data">{{ __('No brands found.') }}</p>
        @endif
    </div>
</section>

<script>
(function(){
    var total  = {{ $slides->count() }};
    if (total < 1) return;
    var current = 0;
    var slides  = document.querySelectorAll('#{{ $uid }}-slides .hf-brands-slide');
    var dots    = document.querySelectorAll('#{{ $uid }}-dots .hf-brands-dot');
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
