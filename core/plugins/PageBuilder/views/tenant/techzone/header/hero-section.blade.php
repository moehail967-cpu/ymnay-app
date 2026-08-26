@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 60;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 60;
    $shop_url = route('tenant.shop');
@endphp

<section style="background: var(--tz-bg); border-bottom:1px solid var(--tz-border); padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-50%;right:-10%;width:600px;height:600px;background:var(--tz-blue-glow);border-radius:50%;pointer-events:none;"></div>
    <div class="container" style="position:relative;z-index:1;">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                @if(!empty($data['badge_text']))
                    <div style="display:inline-block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1.4px; color:var(--tz-blue); background:var(--tz-blue-glow); border:1px solid rgba(0,102,255,.2); padding:5px 14px; border-radius:var(--tz-radius); margin-bottom:14px;">
                        {{ $data['badge_text'] }}
                    </div>
                @endif
                @if(!empty($data['title']))
                    <h1 style="font-size:clamp(28px,4vw,46px); font-weight:900; color:var(--tz-text); line-height:1.15; margin-bottom:16px;">{!! $data['title'] !!}</h1>
                @endif
                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:var(--tz-muted); margin-bottom:28px; max-width:480px; line-height:1.7;">{{ $data['subtitle'] }}</p>
                @endif
                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['button_text']))
                        <a href="{{ $data['button_url'] ?? $shop_url }}" style="display:inline-flex;align-items:center;gap:6px;background:var(--tz-blue);color:#fff;padding:13px 30px;border-radius:var(--tz-radius);font-size:14px;font-weight:700;text-decoration:none;transition:opacity .2s;">
                            {{ $data['button_text'] }}
                        </a>
                    @endif
                    @if(!empty($data['button2_text']))
                        <a href="{{ $data['button2_url'] ?? '#' }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--tz-text);border:1px solid var(--tz-border);padding:12px 28px;border-radius:var(--tz-radius);font-size:14px;font-weight:700;text-decoration:none;transition:border-color .2s;">
                            {{ $data['button2_text'] }} <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 text-center">
                @if(!empty($hero_img_url))
                    <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}" style="max-width:100%; max-height:420px; object-fit:contain;">
                @else
                    <div style="width:100%;max-width:420px;height:320px;margin:0 auto;background:var(--tz-surface);border:1px solid var(--tz-border);border-radius:var(--tz-radius-xl);display:flex;align-items:center;justify-content:center;font-size:80px;">💻</div>
                @endif
            </div>
        </div>
    </div>
</section>
