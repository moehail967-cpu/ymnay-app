<section class="dk-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <span class="dk-section-tag"><i class="las la-cog"></i> {!! $tag !!}</span>
                <h1 class="dk-hero-title">{!! $title !!}</h1>
                @if($subtitle)
                <p class="dk-hero-sub">{!! $subtitle !!}</p>
                @endif
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ $button_url }}" class="dk-btn dk-btn-red">
                        <i class="las la-car"></i> {!! $button_text !!}
                    </a>
                    <a href="{{ $button2_url }}" class="dk-btn dk-btn-ghost">
                        <i class="las la-wrench"></i> {!! $button2_text !!}
                    </a>
                </div>
            </div>
            <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end">
                @if($hero_image)
                    <div class="dk-hero-img">
                        <img src="{{ $hero_image }}" alt="{{ strip_tags($tag) }}" style="width:100%;height:100%;object-fit:cover;display:block;border-radius:inherit;">
                    </div>
                @else
                    <div class="dk-hero-img">
                        <i class="las la-wrench dk-hero-placeholder-icon"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
