{{-- TinyNest: Hero Section Widget --}}
<section class="tn-hero" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                @if($badge_text)
                    <div class="tn-hero-badge">
                        <span style="color:var(--tn-pink);">💗</span> {{ $badge_text }}
                    </div>
                @endif
                <h1 class="tn-hero-title">
                    @php
                        $plain = $title_accent
                            ? str_replace(e($title_accent), '<span class="tn-title-accent">'.e($title_accent).'</span>', e($title))
                            : e($title);
                    @endphp
                    {!! $plain !!}
                </h1>
                @if($subtitle)
                    <p class="tn-hero-sub">{{ $subtitle }}</p>
                @endif
                <div class="d-flex gap-3 flex-wrap">
                    @if($button_text)
                        <a href="{{ $button_url }}" class="tn-btn tn-btn-blush">
                            <i class="mdi mdi-baby-carriage"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if($button2_text)
                        <a href="{{ $button2_url }}" class="tn-btn tn-btn-mint">
                            <i class="mdi mdi-gift-outline"></i> {{ $button2_text }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end">
                @if($hero_image)
                    <div class="tn-hero-circle-wrap">
                        <img src="{{ $hero_image }}" alt="{{ $title }}" class="tn-hero-circle-img">
                    </div>
                @else
                    <div class="tn-hero-circle-wrap tn-hero-circle-placeholder">
                        <i class="mdi mdi-baby-carriage"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
