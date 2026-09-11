@php
    $style = '';
    if (!empty($bg_image_url)) {
        $style = "background-image:url('{$bg_image_url}'); background-size:cover; background-position:center;";
    } elseif (!empty($bg_color)) {
        $style = "background-color:{$bg_color};";
    }
@endphp

<section class="xgpb-hero-section" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px; {{$style}}">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="{{ !empty($hero_image_url) ? 'col-lg-6' : 'col-lg-8 mx-auto text-center' }}">
                @if(!empty($title))
                    <h1 class="xgpb-hero-title">{!! $title !!}</h1>
                @endif
                @if(!empty($subtitle))
                    <p class="xgpb-hero-subtitle">{{ $subtitle }}</p>
                @endif
                <div class="xgpb-hero-btns d-flex flex-wrap gap-3 mt-4 {{ empty($hero_image_url) ? 'justify-content-center' : '' }}">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="btn btn-primary btn-lg xgpb-btn-primary">{{ $button_text }}</a>
                    @endif
                    @if(!empty($secondary_button_text))
                        <a href="{{ $secondary_button_url }}" class="btn btn-outline-secondary btn-lg xgpb-btn-secondary">{{ $secondary_button_text }}</a>
                    @endif
                </div>
            </div>
            @if(!empty($hero_image_url))
                <div class="col-lg-6 text-center">
                    <img src="{{ $hero_image_url }}" alt="{{ strip_tags($title) }}"
                         class="img-fluid xgpb-hero-img"
                         style="border-radius:16px; max-height:480px; object-fit:cover; width:100%;">
                </div>
            @endif
        </div>
    </div>
</section>

<style>
.xgpb-hero-section { position: relative; }
.xgpb-hero-title { font-size: clamp(1.8rem, 4vw, 3rem); font-weight: 700; line-height: 1.2; margin-bottom: 1rem; color: inherit; }
.xgpb-hero-subtitle { font-size: 1.125rem; opacity: .85; margin-bottom: 0; }
</style>
