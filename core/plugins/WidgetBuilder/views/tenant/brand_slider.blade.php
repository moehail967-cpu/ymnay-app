@php $uid = 'xgpb_brand_' . uniqid(); @endphp

<section class="xgpb-brand-slider" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px; background:{{ $bg_color }};">
    <div class="container">
        @if(!empty($title))
            <div class="text-center mb-4">
                <h2 class="xgpb-section-title">{{ $title }}</h2>
            </div>
        @endif

        @if(!empty($brands))
            <div class="xgpb-brand-track" id="{{ $uid }}">
                @foreach($brands as $brand)
                    <div class="xgpb-brand-item">
                        @if(!empty($brand['url']))
                            <a href="{{ $brand['url'] }}" target="_blank" rel="noopener noreferrer">
                        @endif
                            @if(!empty($brand['logo_url']))
                                <img src="{{ $brand['logo_url'] }}"
                                     alt="{{ $brand['name'] }}"
                                     class="xgpb-brand-logo {{ $grayscale ? 'xgpb-grayscale' : '' }}"
                                     loading="lazy">
                            @else
                                <span class="xgpb-brand-text-fallback">{{ $brand['name'] }}</span>
                            @endif
                        @if(!empty($brand['url']))</a>@endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<script>
(function() {
    var el = document.getElementById("{{ $uid }}");
    if (!el || typeof jQuery === 'undefined' || !jQuery.fn.slick) return;
    jQuery(el).slick({
        slidesToShow: {{ $items_per_row }},
        slidesToScroll: 1,
        autoplay: {{ $autoplay ? 'true' : 'false' }},
        autoplaySpeed: {{ $autoplay_speed }},
        arrows: false, dots: false, infinite: true,
        responsive: [
            { breakpoint: 1200, settings: { slidesToShow: Math.min({{ $items_per_row }}, 4) } },
            { breakpoint: 768,  settings: { slidesToShow: 3 } },
            { breakpoint: 576,  settings: { slidesToShow: 2 } }
        ]
    });
})();
</script>

<style>
.xgpb-brand-item { padding: 0 20px; display: flex; align-items: center; justify-content: center; }
.xgpb-brand-logo { max-height: 60px; max-width: 140px; object-fit: contain; transition: filter .3s, opacity .3s; }
.xgpb-grayscale { filter: grayscale(100%); opacity: .6; }
.xgpb-grayscale:hover { filter: grayscale(0); opacity: 1; }
.xgpb-brand-text-fallback { font-weight: 600; font-size: 1.1rem; color: #6c757d; }
</style>
