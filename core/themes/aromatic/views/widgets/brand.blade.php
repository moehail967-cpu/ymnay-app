@php $brand_uid = 'brand' . substr(md5(uniqid()), 0, 8); @endphp
<section class="ar-brand-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="ar-brand-slider" id="{{ $brand_uid }}">
            <div class="ar-brand-track">
                @forelse($chunks as $ci => $chunk)
                <div class="ar-brand-slide{{ $ci === 0 ? ' active' : '' }}" data-slide="{{ $ci }}">
                    @foreach($chunk as $brand)
                    @php
                        $logoData = !empty($brand->image_id) ? get_attachment_image_by_id($brand->image_id) : null;
                        $logoUrl  = $logoData['img_url'] ?? null;
                        $brandUrl = !empty($brand->url) ? $brand->url : '#';
                    @endphp
                    <div class="ar-brand-item">
                        @if($logoUrl)
                            <a href="{{ $brandUrl }}"
                               {{ $brandUrl !== '#' ? 'target="_blank" rel="noopener"' : '' }}>
                                <img src="{{ $logoUrl }}" alt="{{ $brand->name ?? '' }}" loading="lazy">
                            </a>
                        @else
                            <a href="{{ $brandUrl }}"
                               {{ $brandUrl !== '#' ? 'target="_blank" rel="noopener"' : '' }}
                               class="ar-brand-item-name">
                                {{ $brand->name ?? '' }}
                            </a>
                        @endif
                    </div>
                    @endforeach
                </div>
                @empty
                <div class="ar-brand-slide active">
                    <p style="color:var(--ar-body);font-size:14px;">{{ __('No brands added yet.') }}</p>
                </div>
                @endforelse
            </div>
        </div>

        @if($chunks->count() > 1)
        <div class="ar-brand-dots" id="{{ $brand_uid }}-dots">
            @foreach($chunks as $ci => $chunk)
                <button class="ar-brand-dot{{ $ci === 0 ? ' active' : '' }}" data-slide="{{ $ci }}" aria-label="{{ __('Slide') }} {{ $ci + 1 }}"></button>
            @endforeach
        </div>
        @endif
    </div>
</section>

<script>
(function () {
    var slider  = document.getElementById('{{ $brand_uid }}');
    var dotsEl  = document.getElementById('{{ $brand_uid }}-dots');
    if (!slider) return;

    var slides  = slider.querySelectorAll('.ar-brand-slide');
    var current = 0;

    function goTo(index) {
        slides[current].classList.remove('active');
        current = index;
        slides[current].classList.add('active');
        if (dotsEl) {
            dotsEl.querySelectorAll('.ar-brand-dot').forEach(function (d, i) {
                d.classList.toggle('active', i === current);
            });
        }
    }

    if (dotsEl) {
        dotsEl.addEventListener('click', function (e) {
            var dot = e.target.closest('.ar-brand-dot');
            if (dot) goTo(parseInt(dot.dataset.slide, 10));
        });
    }

    if (slides.length > 1) {
        setInterval(function () {
            goTo((current + 1) % slides.length);
        }, 4000);
    }
})();
</script>
