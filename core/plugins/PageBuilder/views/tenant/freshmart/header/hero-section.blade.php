@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url = route('tenant.shop');
@endphp

<section style="background:linear-gradient(135deg, var(--fm-green-light) 0%, var(--fm-bg) 100%); padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="container">
        <div class="row align-items-center g-5">

            {{-- Left: Text Content --}}
            <div class="col-lg-6">
                @if(!empty($data['badge_text']))
                    <div class="fm-badge d-inline-flex align-items-center gap-2 mb-3"
                         style="background:var(--fm-green-light); color:var(--fm-green); border:1px solid var(--fm-border); padding:6px 16px; border-radius:50px; font-size:13px; font-weight:600;">
                        <span>🥦</span>
                        {{ $data['badge_text'] }}
                    </div>
                @endif

                @if(!empty($data['title']))
                    <h1 class="fm-hero-title mb-3"
                        style="font-size:clamp(28px,4vw,52px); font-weight:900; color:var(--fm-dark); font-family:var(--fm-font-head); line-height:1.15;">
                        {!! nl2br(e($data['title'])) !!}
                    </h1>
                @endif

                @if(!empty($data['subtitle']))
                    <p class="fm-hero-sub mb-4"
                       style="font-size:16px; color:var(--fm-muted); line-height:1.7; max-width:480px;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['button_text']))
                        <a href="{{ $data['button_url'] ?? $shop_url }}"
                           class="fm-btn fm-btn-green"
                           style="display:inline-flex; align-items:center; gap:8px; background:var(--fm-green); color:#fff; padding:14px 30px; border-radius:50px; font-weight:700; text-decoration:none; transition:background .2s;"
                           onmouseover="this.style.background='var(--fm-green-deep)'" onmouseout="this.style.background='var(--fm-green)'">
                            <i class="mdi mdi-cart-outline"></i>
                            {{ $data['button_text'] }}
                        </a>
                    @endif
                    @if(!empty($data['button2_text']))
                        <a href="{{ $data['button2_url'] ?? '#' }}"
                           class="fm-btn fm-btn-outline"
                           style="display:inline-flex; align-items:center; gap:8px; border:2px solid var(--fm-green); color:var(--fm-green); padding:14px 28px; border-radius:50px; font-weight:700; text-decoration:none; transition:all .2s;"
                           onmouseover="this.style.background='var(--fm-green-light)'" onmouseout="this.style.background='transparent'">
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
                    <div style="background:var(--fm-green-light); border-radius:24px; padding:60px 40px; display:inline-block; border:2px dashed var(--fm-border);">
                        <span style="font-size:100px; line-height:1;">🥦</span>
                    </div>
                @endif
            </div>

        </div>
    </div>
</section>
