<section class="fm-hero-widget">
    <div class="container">
        <div class="row align-items-center g-5">

            {{-- Left: Content --}}
            <div class="col-lg-6">
                @if(!empty($eyebrow))
                    <div class="fm-hero-badge mb-4">
                        <i class="las la-leaf"></i> {{ $eyebrow }}
                    </div>
                @endif

                <h1 class="fm-hero-title">{!! $title !!}</h1>

                @if(!empty($subtitle))
                    <p class="fm-hero-sub">{{ $subtitle }}</p>
                @endif

                <div class="d-flex gap-3 flex-wrap mb-4">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="fm-btn fm-btn-green">
                            <i class="las la-shopping-cart"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="fm-btn fm-btn-outline">
                            <i class="las la-calendar-alt"></i> {{ $button2_text }}
                        </a>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="fm-hero-stats">
                    <div class="fm-hero-stat">
                        <div class="fm-hero-stat-value">{{ $stat1_value }}</div>
                        <div class="fm-hero-stat-label">{{ $stat1_label }}</div>
                    </div>
                    <div class="fm-hero-stat">
                        <div class="fm-hero-stat-value">{{ $stat2_value }}</div>
                        <div class="fm-hero-stat-label">{{ $stat2_label }}</div>
                    </div>
                    <div class="fm-hero-stat">
                        <div class="fm-hero-stat-value">{{ $stat3_value }}</div>
                        <div class="fm-hero-stat-label">{{ $stat3_label }}</div>
                    </div>
                </div>
            </div>

            {{-- Right: Image --}}
            <div class="col-lg-6 text-center">
                <div class="fm-hero-img-wrap">
                    <div class="fm-hero-deco"></div>
                    @if(!empty($hero_img_url))
                        <div class="fm-hero-img-circle">
                            <img src="{{ $hero_img_url }}" alt="{{ strip_tags($title) }}">
                        </div>
                    @elseif(!empty($hero_image))
                        <div class="fm-hero-img-circle">
                            <img src="{{ $hero_image }}" alt="{{ strip_tags($title) }}">
                        </div>
                    @else
                        <div class="fm-hero-img-circle fm-hero-img-placeholder">
                            <i class="mdi mdi-basket"></i>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>
