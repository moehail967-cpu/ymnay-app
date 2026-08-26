@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url = route('tenant.shop');
@endphp

<div class="container" style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="ch-hero">
        <div class="ch-hero-content">
            @if(!empty($data['badge_text']))
                <div class="ch-hero-tag">
                    <i class="mdi mdi-silverware-fork-knife"></i>
                    {{ $data['badge_text'] }}
                </div>
            @endif

            @if(!empty($data['title']))
                <h1 class="ch-hero-title">
                    {!! !empty($data['title_accent'])
                        ? str_replace(
                            $data['title_accent'],
                            '<span class="accent">'.$data['title_accent'].'</span>',
                            e($data['title'])
                          )
                        : e($data['title']) !!}
                </h1>
            @endif

            @if(!empty($data['subtitle']))
                <p class="ch-hero-sub">{{ $data['subtitle'] }}</p>
            @endif

            <div class="d-flex gap-3 flex-wrap">
                @if(!empty($data['button_text']))
                    <a href="{{ $data['button_url'] ?? $shop_url }}" class="ch-btn ch-btn-primary">
                        <i class="mdi mdi-silverware-fork-knife"></i>
                        {{ $data['button_text'] }}
                    </a>
                @endif
                @if(!empty($data['button2_text']))
                    <a href="{{ $data['button2_url'] ?? '#' }}" class="ch-btn ch-btn-outline">
                        {{ $data['button2_text'] }}
                        <i class="mdi mdi-arrow-right"></i>
                    </a>
                @endif
            </div>
        </div>

        @if(!empty($hero_img_url))
            <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}" class="ch-hero-dish">
        @endif
    </div>
</div>
