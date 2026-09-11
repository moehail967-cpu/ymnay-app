{{-- VelvetLux: Promo Section --}}
<section class="vl-promo py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h2>{!! $title !!}</h2>
                @if($subtitle)
                    <p>{{ $subtitle }}</p>
                @endif
                @if($button_text)
                    <a href="{{ $button_url }}" class="vl-btn vl-btn-outline-gold">{{ $button_text }}</a>
                @endif
            </div>
            <div class="col-lg-5 text-center">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="Promo" class="img-fluid rounded" style="width: 300px; height: 300px; object-fit: contain; margin: 0 auto; display: block;">
                @else
                    <div style="font-size:90px; line-height:1; letter-spacing:16px;">✂️🧵</div>
                @endif
            </div>
        </div>
    </div>
</section>
