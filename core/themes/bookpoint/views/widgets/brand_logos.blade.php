@php $uid = 'bpbrand' . substr(md5(uniqid()), 0, 8); @endphp
<section class="bp-brand-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="bp-brand-slider" id="{{ $uid }}">
            @forelse($chunks as $ci => $chunk)
            <div class="bp-brand-slide{{ $ci === 0 ? ' active' : '' }}" data-slide="{{ $ci }}">
                @foreach($chunk as $brand)
                @php
                    $logoUrl  = null;
                    if (!empty($brand->image_id)) {
                        $att     = get_attachment_image_by_id($brand->image_id);
                        $logoUrl = $att['img_url'] ?? null;
                    }
                    $brandUrl = !empty($brand->url) ? $brand->url : '#';
                @endphp
                <div class="bp-brand-item">
                    <a href="{{ $brandUrl }}" {{ $brandUrl !== '#' ? 'target="_blank" rel="noopener"' : '' }}>
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $brand->name }}" loading="lazy">
                        @else
                            <span class="bp-brand-name">{{ $brand->name }}</span>
                        @endif
                    </a>
                </div>
                @endforeach
            </div>
            @empty
            <div class="bp-brand-slide active">
                <p class="text-muted" style="text-align:center;width:100%;">{{ __('No brands added yet.') }}</p>
            </div>
            @endforelse
        </div>
        @if($chunks->count() > 1)
        <div class="bp-brand-dots" id="{{ $uid }}-dots">
            @foreach($chunks as $ci => $chunk)
                <button class="bp-brand-dot{{ $ci === 0 ? ' active' : '' }}" data-slide="{{ $ci }}" aria-label="{{ __('Slide') }} {{ $ci + 1 }}"></button>
            @endforeach
        </div>
        @endif
    </div>
</section>
<script>
(function(){
    var slider  = document.getElementById('{{ $uid }}');
    var dotsEl  = document.getElementById('{{ $uid }}-dots');
    if (!slider) return;
    var slides  = slider.querySelectorAll('.bp-brand-slide');
    var current = 0;
    function goTo(index) {
        slides[current].classList.remove('active');
        current = index;
        slides[current].classList.add('active');
        if (dotsEl) {
            dotsEl.querySelectorAll('.bp-brand-dot').forEach(function(d, i){
                d.classList.toggle('active', i === current);
            });
        }
    }
    if (dotsEl) {
        dotsEl.addEventListener('click', function(e){
            var dot = e.target.closest('.bp-brand-dot');
            if (dot) goTo(parseInt(dot.dataset.slide, 10));
        });
    }
    if (slides.length > 1) {
        setInterval(function(){ goTo((current + 1) % slides.length); }, 4000);
    }
})();
</script>
