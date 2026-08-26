<section class="fp-hero-section">
    <div class="container">
        <div class="row align-items-center">

            {{-- Left: Content --}}
            <div class="col-lg-6 mb-5 mb-lg-0">

                @if(!empty($badge_text))
                    <div class="fp-hero-badge">
                        <i class="mdi mdi-lightning-bolt"></i> {{ $badge_text }}
                    </div>
                @endif

                <h1 class="fp-hero-title">
                    {!! $title !!}
                    @if(!empty($title_accent))
                        <span class="fp-hero-accent">{{ $title_accent }}</span>
                    @endif
                </h1>

                @if(!empty($subtitle))
                    <p class="fp-hero-sub">{{ $subtitle }}</p>
                @endif

                <div class="d-flex gap-3 flex-wrap mb-4">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="fp-btn fp-btn-primary">
                            <i class="mdi mdi-shopping"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="fp-btn fp-btn-outline">
                            <i class="mdi mdi-layers"></i> {{ $button2_text }}
                        </a>
                    @endif
                </div>

                {{-- Stats Row --}}
                @if(!empty($stat1_value) || !empty($stat2_value) || !empty($stat3_value))
                    <div class="fp-hero-stats">
                        @if(!empty($stat1_value))
                            <div class="fp-hero-stat">
                                <div class="fp-stat-value">{{ $stat1_value }}</div>
                                <div class="fp-stat-label">{{ $stat1_label }}</div>
                            </div>
                        @endif
                        @if(!empty($stat2_value))
                            <div class="fp-hero-stat">
                                <div class="fp-stat-value">{{ $stat2_value }}</div>
                                <div class="fp-stat-label">{{ $stat2_label }}</div>
                            </div>
                        @endif
                        @if(!empty($stat3_value))
                            <div class="fp-hero-stat">
                                <div class="fp-stat-value">{{ $stat3_value }}</div>
                                <div class="fp-stat-label">{{ $stat3_label }}</div>
                            </div>
                        @endif
                    </div>
                @endif

            </div>

            {{-- Right: Image --}}
            <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end">
                @if(!empty($hero_image))
                    <div class="fp-hero-img-frame">
                        <img src="{{ $hero_image }}" alt="{{ strip_tags($title) }}">
                    </div>
                @else
                    <div class="fp-hero-img-frame fp-hero-img-empty">
                        <i class="mdi mdi-dumbbell"></i>
                    </div>
                @endif
            </div>

        </div>
    </div>
</section>
