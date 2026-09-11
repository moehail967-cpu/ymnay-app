{{-- TechZone: Hero Section --}}
<section class="tz-hero-widget">

    <div class="container">
        <div class="row align-items-center tz-hero-row">

            {{-- Left: Text --}}
            <div class="col-lg-6 mb-5 mb-lg-0">

                @if(!empty($eyebrow))
                    <span class="tz-hero-badge"><span class="tz-hero-badge-plus">+</span> {{ strtoupper($eyebrow) }}</span>
                @endif

                <h1 class="tz-hero-title">{!! $title !!}</h1>

                @if(!empty($subtitle))
                    <p class="tz-hero-sub">{{ $subtitle }}</p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="tz-btn tz-btn-blue">
                            <i class="mdi mdi-monitor"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="tz-btn tz-btn-ghost">
                            <i class="mdi mdi-tag-outline"></i> {{ $button2_text }}
                        </a>
                    @endif
                </div>

            </div>

            {{-- Right: Image --}}
            <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end">
                <div class="tz-hero-media">
                    @if(!empty($hero_image))
                        <img src="{{ $hero_image }}" alt="{{ strip_tags($title) }}" loading="eager">

                    @elseif(!empty($product_image))
                        <img src="{{ $product_image }}" alt="{{ strip_tags($title) }}" loading="eager">
                    @else
                        <div class="tz-hero-media-placeholder">
                            <i class="mdi mdi-cpu-64-bit"></i>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</section>
