@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:#f8f9fa;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-center text-lg-start">
                @if(!empty($data['title']))
                    <h1 style="font-size:2.5rem; font-weight:700; color:#1a1a2e; line-height:1.2; margin-bottom:1rem;">
                        {{ $data['title'] }}
                    </h1>
                @endif
                @if(!empty($data['subtitle']))
                    <p style="font-size:1.125rem; color:#6c757d; margin-bottom:2rem;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif
                @if(!empty($data['button_text']))
                    <a href="{{ $data['button_url'] ?? '#' }}"
                       class="btn btn-primary btn-lg px-5"
                       style="border-radius:8px; font-weight:600;">
                        {{ $data['button_text'] }}
                    </a>
                @endif
            </div>
            @if(!empty($hero_img_url))
                <div class="col-lg-6 text-center">
                    <img src="{{ $hero_img_url }}"
                         alt="{{ $data['title'] ?? '' }}"
                         class="img-fluid"
                         style="border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.12); max-height:420px; object-fit:cover; width:100%;">
                </div>
            @endif
        </div>
    </div>
</section>
