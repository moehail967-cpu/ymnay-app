<section class="dk-section-promo">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h2 class="dk-section-title dk-promo-title">{!! $title !!}</h2>
                @if($text)
                <p class="dk-promo-text">{{ $text }}</p>
                @endif
                <a href="{{ $button_url }}" class="dk-btn dk-btn-red">
                    <i class="las la-user-plus"></i> {!! $button_text !!}
                </a>
            </div>
            <div class="col-lg-5 text-center">
                @if(!empty($image) || !empty($promo_image))
                    <img src="{{ $promo_image ?? $image }}" alt="" class="img-fluid" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @else
                    <div class="dk-promo-icon-placeholder">
                        <i class="las la-wrench"></i>
                        <i class="las la-car"></i>
                        <i class="las la-cog"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
