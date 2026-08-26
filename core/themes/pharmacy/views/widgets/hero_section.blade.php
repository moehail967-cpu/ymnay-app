<div class="pf-hero" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="pf-hero-badge">
                    <i class="las la-shield-alt"></i> {{ $eyebrow }}
                </span>
                <h1 class="pf-hero-heading">{!! $title !!}</h1>
                @if($subtitle)
                    <p class="pf-hero-sub">{{ $subtitle }}</p>
                @endif
                <div class="pf-hero-btns">
                    <a href="{{ $button_url }}" class="pf-btn pf-btn-teal pf-btn-pill pf-btn-lg">
                        {{ $button_text }} <i class="las la-arrow-right"></i>
                    </a>
                    @if($button2_text)
                        <a href="{{ $button2_url }}" class="pf-btn pf-btn-dark-outline pf-btn-pill pf-btn-lg">
                            {{ $button2_text }}
                        </a>
                    @endif
                </div>
                <div class="pf-hero-divider"></div>
                <div class="pf-hero-trust-inline">
                    <span><i class="las la-check-circle"></i> {{ __('100% Genuine') }}</span>
                    <span><i class="las la-check-circle"></i> {{ __('Pharmacist Verified') }}</span>
                    <span><i class="las la-check-circle"></i> {{ __('Cold Chain Assured') }}</span>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                @if($hero_image)
                    <div class="pf-hero-img-wrap">
                        <img src="{{ $hero_image }}" alt="{{ $eyebrow }}" class="pf-hero-img">
                    </div>
                @elseif($product_image)
                    <div class="pf-hero-img-wrap">
                        <img src="{{ $product_image }}" alt="{{ $eyebrow }}" class="pf-hero-img">
                    </div>
                @else
                    <div class="pf-hero-img-wrap pf-hero-img-ph">
                        <i class="las la-pills"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
{{-- Trust bar below hero (dynamic) --}}
<div class="pf-hero-trust-bar">
    <div class="container">
        <div class="row g-0">
            @foreach($trust_items as $item)
            <div class="col-6 col-md-3">
                <div class="pf-trust-item">
                    <i class="{{ $item['icon'] }} pf-trust-icon"></i>
                    <div>
                        <div class="pf-trust-title">{{ $item['title'] }}</div>
                        <div class="pf-trust-sub">{{ $item['sub'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
