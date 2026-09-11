{{-- Medicom: Hero Section --}}
<section class="mc-hero-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="mc-hero-wrapper">
            {{-- Left: Text Content --}}
            <div class="mc-hero-content">
                @if(!empty($eyebrow))
                    <p class="mc-hero-eyebrow">{{ $eyebrow }}</p>
                @endif
                <h1 class="mc-hero-heading">
                    @if(!empty($title_black_1))<span class="text-black">{{ $title_black_1 }}</span><br>@endif
                    @if(!empty($title_blue))<span class="text-blue">{{ $title_blue }}</span>@endif
                    @if(!empty($title_black_2))<span class="text-black">{{ $title_black_2 }}</span>@endif
                </h1>

                @if(!empty($description))
                    <p class="mc-hero-description">{{ $description }}</p>
                @endif

                @if(!empty($button_text) && !empty($button_url))
                    <div class="mc-hero-btn-wrapper">
                        <a href="{{ $button_url }}" class="mc-btn-primary">{{ $button_text }}</a>
                    </div>
                @endif
            </div>

            {{-- Right: Visual / Image --}}
            <div class="mc-hero-image-wrapper">
                @if(!empty($hero_img))
                    <img src="{{ $hero_img }}" alt="{{ $title_black_1 }} {{ $title_blue }}" class="mc-hero-image" loading="lazy">
                @endif
            </div>
        </div>
    </div>
</section>


