{{-- TrailCo: Hero Section Widget --}}
<section class="tc-hero">
    <div class="container tc-hero-inner">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                @if($category_tag)
                    <span class="tc-hero-badge">
                        <i class="mdi mdi-image-filter-hdr"></i> {{ $category_tag }}
                    </span>
                @endif
                <h1 class="tc-hero-title">{!! $title !!}</h1>
                @if($subtitle)
                    <p class="tc-hero-sub">{{ $subtitle }}</p>
                @endif
                <div class="d-flex gap-3 flex-wrap">
                    @if($button_text)
                        <a href="{{ $button_url }}" class="tc-btn tc-btn-olive">
                            <i class="mdi mdi-map-marker-outline"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if($button2_text)
                        <a href="{{ $button2_url }}" class="tc-btn tc-btn-ghost">
                            {{ $button2_text }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end">
                <div class="tc-hero-photo-wrap">
                    @if($hero_image)
                        <img src="{{ $hero_image }}" alt="{{ __('Adventure') }}" class="tc-hero-photo">
                    @else
                        <img src="{{ global_asset('themes/trailco/assets/images/trailco-hero.jpg') }}"
                             alt="{{ __('Adventure') }}" class="tc-hero-photo">
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
