{{-- Electro: Brand Logos --}}
@php $uid = 'elbrand' . substr(md5(uniqid()), 0, 8); @endphp
<section class="el-brands-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        @if($slides->isNotEmpty())
        <div class="el-brands-carousel">
            <div class="el-brands-slides-wrap" id="{{ $uid }}-slides">
                @foreach($slides as $si => $slide)
                <div class="el-brands-slide{{ $si === 0 ? ' active' : '' }}" data-slide="{{ $si }}">
                    <div class="el-brands-row">
                        @foreach($slide as $brand)
                        @php
                            $logoUrl  = null;
                            if (!empty($brand->image_id)) {
                                $logoData = get_attachment_image_by_id((int) $brand->image_id);
                                $logoUrl  = $logoData['img_url'] ?? null;
                            }
                            $brandUrl = !empty($brand->url) ? $brand->url : '#';
                        @endphp
                        <a href="{{ $brandUrl }}" class="el-brand-item" target="{{ $brandUrl !== '#' ? '_blank' : '_self' }}" rel="noopener">
                            @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $brand->name ?? '' }}" class="el-brand-logo" loading="lazy">
                            @else
                            <span class="el-brand-text">{{ $brand->name ?? '' }}</span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Dots --}}
            <div class="el-dots el-brands-dots" id="{{ $uid }}-dots">
                @foreach($slides as $si => $slide)
                <button class="el-dot{{ $si === 0 ? ' active' : '' }}" data-index="{{ $si }}" aria-label="{{ __('Slide') }} {{ $si + 1 }}"></button>
                @endforeach
            </div>
        </div>
        @else
        <p class="el-no-data">{{ __('No brands found.') }}</p>
        @endif
    </div>
</section>

<script>
(function(){
    var total   = {{ $slides->count() }};
    if (total < 2) return;
    var current = 0;
    var slides  = document.querySelectorAll('#{{ $uid }}-slides .el-brands-slide');
    var dots    = document.querySelectorAll('#{{ $uid }}-dots .el-dot');
    var timer   = null;

    function goTo(idx) {
        slides[current].classList.remove('active');
        dots[current] && dots[current].classList.remove('active');
        current = (idx + total) % total;
        slides[current].classList.add('active');
        dots[current] && dots[current].classList.add('active');
    }

    dots.forEach(function(dot, i){
        dot.addEventListener('click', function(){ clearInterval(timer); goTo(i); timer = setInterval(function(){ goTo(current + 1); }, 4000); });
    });

    if (total > 1) timer = setInterval(function(){ goTo(current + 1); }, 4000);
})();
</script>
