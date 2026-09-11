@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 60;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 60;
    $shop_url = route('tenant.shop');
@endphp

<section style="background: linear-gradient(135deg, var(--ph-terra-light) 0%, var(--ph-sage-light) 100%); padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                @if(!empty($data['badge_text']))
                    <div style="display:inline-block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1.4px; color:var(--ph-terra); background:rgba(200,112,64,.12); padding:5px 14px; border-radius:20px; margin-bottom:14px;">
                        🐾 {{ $data['badge_text'] }}
                    </div>
                @endif
                @if(!empty($data['title']))
                    <h1 style="font-size:clamp(30px,4vw,46px); font-weight:800; color:var(--ph-dark); line-height:1.2; margin-bottom:16px;">{!! $data['title'] !!}</h1>
                @endif
                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:var(--ph-muted); margin-bottom:28px; max-width:480px; line-height:1.7;">{{ $data['subtitle'] }}</p>
                @endif
                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['button_text']))
                        <a href="{{ $data['button_url'] ?? $shop_url }}" style="display:inline-flex;align-items:center;gap:6px;background:var(--ph-terra);color:#fff;padding:13px 30px;border-radius:var(--ph-radius-xl);font-size:14px;font-weight:700;text-decoration:none;transition:background .2s;">
                            {{ $data['button_text'] }}
                        </a>
                    @endif
                    @if(!empty($data['button2_text']))
                        <a href="{{ $data['button2_url'] ?? '#' }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--ph-terra);border:2px solid var(--ph-terra);padding:11px 28px;border-radius:var(--ph-radius-xl);font-size:14px;font-weight:700;text-decoration:none;transition:all .2s;">
                            {{ $data['button2_text'] }} <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 text-center">
                @if(!empty($hero_img_url))
                    <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}" style="max-width:100%; max-height:420px; object-fit:contain; border-radius:var(--ph-radius);">
                @else
                    <div style="width:100%;max-width:420px;height:340px;margin:0 auto;background:rgba(200,112,64,.08);border-radius:var(--ph-radius);display:flex;align-items:center;justify-content:center;font-size:80px;border:2px dashed var(--ph-border);">🐾</div>
                @endif
            </div>
        </div>
    </div>
</section>
