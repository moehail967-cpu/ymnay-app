{{-- SportZone: Hero Section Widget --}}
<section class="sz-hero">

    <div class="container">
        <div class="row align-items-center">

            {{-- Text Column --}}
            <div class="col-lg-6 mb-5 mb-lg-0">

                @if(!empty($badge_text))
                    <span class="sz-section-tag"><i class="mdi mdi-lightning-bolt"></i> {{ $badge_text }}</span>
                @endif

                <h1 class="sz-hero-title">
                    {!! $title !!}@if(!empty($title_accent))<span class="sz-hero-accent"> {{ $title_accent }}</span>@endif
                </h1>

                @if(!empty($subtitle))
                    <p class="sz-hero-sub">{{ $subtitle }}</p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="sz-btn sz-btn-red">
                            <i class="mdi mdi-storefront-outline"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="sz-btn sz-btn-outline">
                            <i class="mdi mdi-calendar-clock"></i> {{ $button2_text }}
                        </a>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="sz-hero-stats">
                    <div class="sz-hero-stat">
                        <div class="sz-hero-stat-num">10K+</div>
                        <div class="sz-hero-stat-label">{{ __('Athletes Trust Us') }}</div>
                    </div>
                    <div class="sz-hero-stat-divider"></div>
                    <div class="sz-hero-stat">
                        <div class="sz-hero-stat-num">500+</div>
                        <div class="sz-hero-stat-label">{{ __('Products') }}</div>
                    </div>
                    <div class="sz-hero-stat-divider"></div>
                    <div class="sz-hero-stat">
                        <div class="sz-hero-stat-num">24/7</div>
                        <div class="sz-hero-stat-label">{{ __('Support') }}</div>
                    </div>
                </div>

            </div>

            {{-- Image Column --}}
            <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end">
                <div class="sz-hero-img-box">
                    @if(!empty($hero_image))
                        <img src="{{ $hero_image }}" alt="{{ __('Sports Hero') }}">
                    @else
                        <div class="sz-hero-img-placeholder">
                            <i class="mdi mdi-dumbbell"></i>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</section>
