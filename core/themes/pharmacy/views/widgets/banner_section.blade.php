<section class="pf-banner-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="pf-banner-rx-watermark">Rx</div>
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="pf-banner-badge">
                    <i class="las la-prescription-bottle-alt"></i>
                    {{ strtoupper($eyebrow) }}
                </span>
                <h2 class="pf-banner-title">{{ $title }}</h2>
                @if($subtitle)
                    <p class="pf-banner-sub">{{ $subtitle }}</p>
                @endif
                <div class="pf-banner-btns">
                    <a href="{{ $button_url }}" class="pf-btn pf-btn-teal pf-btn-pill">
                        <i class="las la-upload"></i> {{ $button_text }}
                    </a>
                    @if($button2_text)
                        <a href="{{ $button2_url }}" class="pf-btn pf-btn-dark-outline pf-btn-pill">
                            {{ $button2_text }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-5 text-center">
                @if($banner_image)
                    <div class="pf-banner-img-wrap">
                        <img src="{{ $banner_image }}" alt="{{ $eyebrow }}" class="pf-banner-img">
                    </div>
                @else
                    <div class="pf-banner-steps">
                        <div class="pf-step-box">
                            <div class="pf-step-icon"><i class="las la-file-prescription"></i></div>
                            <div class="pf-step-label">{{ __('UPLOAD') }}</div>
                        </div>
                        <div class="pf-step-arrow"><i class="las la-arrow-right"></i></div>
                        <div class="pf-step-box">
                            <div class="pf-step-icon"><i class="las la-user-md"></i></div>
                            <div class="pf-step-label">{{ __('VERIFIED') }}</div>
                        </div>
                        <div class="pf-step-arrow"><i class="las la-arrow-right"></i></div>
                        <div class="pf-step-box">
                            <div class="pf-step-icon"><i class="las la-truck"></i></div>
                            <div class="pf-step-label">{{ __('DELIVERED') }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
