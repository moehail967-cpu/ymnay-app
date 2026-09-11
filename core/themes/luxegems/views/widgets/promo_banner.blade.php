<section class="lg-promo">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="lg-line"></div>
                <h2>{!! $title !!}</h2>
                @if(!empty($text))
                    <p>{{ $text }}</p>
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
            <div class="col-lg-5 text-center d-none d-lg-block" style="height: 300px; width: 300px; margin:auto;">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="{{ strip_tags($title) }}" class="lg-promo-img">
                @else
                    <div style="font-size:100px;line-height:1;filter:drop-shadow(0 0 20px rgba(201,168,76,.3));">{{ $promo_icon }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
