<section class="lg-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="lg-line"></div>
                <h1 class="lg-hero-title">
                    {!! $title !!}
                    @if(!empty($title_accent))
                        <br><span>{{ $title_accent }}</span>
                    @endif
                </h1>
                @if(!empty($subtitle))
                    <p class="lg-hero-sub">{{ $subtitle }}</p>
                @endif
                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="lg-btn lg-btn-gold">
                            <i class="las la-gem"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="lg-btn lg-btn-outline">{{ $button2_text }}</a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end">
                <div class="lg-hero-img">
                    @if(!empty($product_image))
                        <img src="{{ $product_image }}" alt="{{ strip_tags($title) }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                    @else
                        <span style="font-size:130px;line-height:1;">💎</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
