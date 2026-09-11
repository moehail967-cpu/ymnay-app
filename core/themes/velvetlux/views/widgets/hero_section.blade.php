{{-- VelvetLux: Hero Section --}}
<section class="vl-hero">
    <div class="container">
        <div class="row align-items-center g-0 vl-hero-row">
            <div class="col-lg-6 d-flex align-items-center">
                <div class="vl-hero-content">
                    @if($season_text)
                        <span class="vl-hero-eyebrow">{{ $season_text }}</span>
                    @endif
                    <h1 class="vl-hero-title">{!! $title !!}</h1>
                    <div class="vl-divider"></div>
                    @if($subtitle)
                        <p class="vl-hero-sub">{{ $subtitle }}</p>
                    @endif
                    <div class="d-flex gap-3 flex-wrap mt-4">
                        @if($button_text)
                            <a href="{{ $button_url }}" class="vl-btn vl-btn-plum">{{ $button_text }}</a>
                        @endif
                        @if($button2_text)
                            <a href="{{ $button2_url }}" class="vl-btn vl-btn-outline">{{ $button2_text }}</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-flex justify-content-end align-items-start pt-5 pt-lg-0">
                <div class="vl-hero-img-wrap">
                    @if($hero_image)
                        <img src="{{ $hero_image }}" alt="{{ strip_tags($title) }}" class="vl-hero-editorial-img">
                    @else
                        <div class="vl-hero-img-placeholder">
                            <i class="mdi mdi-hanger"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
