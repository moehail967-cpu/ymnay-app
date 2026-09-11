@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url = route('tenant.shop');
@endphp

<section style="background:linear-gradient(135deg, var(--bk-cream) 0%, var(--bk-warm) 100%); padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="container">
        <div class="row align-items-center g-5 mt-4">

            {{-- Left: Text Content --}}
            <div class="col-lg-6">
                @if(!empty($data['badge_text']))
                    <div class="bk-section-tag d-inline-flex align-items-center gap-2 mb-3">
                        <span>🥐</span>
                        {{ $data['badge_text'] }}
                    </div>
                @endif

                @if(!empty($data['title']))
                    <h1 class="bk-section-title mb-3">
                        {!! nl2br(e($data['title'])) !!}
                    </h1>
                @endif

                @if(!empty($data['subtitle']))
                    <p class="bk-section-sub mb-4">{{ $data['subtitle'] }}</p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['button_text']))
                        <a href="{{ $data['button_url'] ?? $shop_url }}" class="bk-btn bk-btn-rose">
                            {{ $data['button_text'] }}
                        </a>
                    @endif
                    @if(!empty($data['button2_text']))
                        <a href="{{ $data['button2_url'] ?? '#' }}" class="bk-btn bk-btn-outline">
                            {{ $data['button2_text'] }}
                            <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Right: Hero Image --}}
            <div class="col-lg-6 text-center">
                @if(!empty($hero_img_url))
                    <img src="{{ $hero_img_url }}"
                         alt="{{ $data['title'] ?? '' }}"
                         style="max-width:100%; height:auto; border-radius:24px; object-fit:cover;"
                         loading="lazy">
                @else
                    <div style="background:var(--bk-rose-light); border-radius:24px; padding:60px 40px; display:inline-block;">
                        <span style="font-size:100px; line-height:1;">🥐</span>
                    </div>
                @endif
            </div>

        </div>
    </div>
</section>
