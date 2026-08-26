@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 60;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 60;
    $shop_url = route('tenant.shop');
@endphp

<section style="background: var(--dk-carbon); padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; position:relative; overflow:hidden; border-bottom:2px solid var(--dk-red);">
    <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:repeating-linear-gradient(45deg,rgba(255,255,255,.01) 0px,rgba(255,255,255,.01) 1px,transparent 1px,transparent 30px);pointer-events:none;"></div>
    <div class="container" style="position:relative;z-index:1;">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                @if(!empty($data['badge_text']))
                    <div style="display:inline-block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:2px; color:var(--dk-red); background:var(--dk-red-glow); border:1px solid rgba(204,34,0,.3); padding:5px 14px; border-radius:var(--dk-radius); margin-bottom:14px;">
                        {{ $data['badge_text'] }}
                    </div>
                @endif
                @if(!empty($data['title']))
                    <h1 style="font-size:clamp(28px,4vw,50px); font-weight:900; color:var(--dk-text); line-height:1.1; margin-bottom:16px; text-transform:uppercase; letter-spacing:-.5px;">{!! $data['title'] !!}</h1>
                @endif
                @if(!empty($data['subtitle']))
                    <p style="font-size:15px; color:var(--dk-muted); margin-bottom:28px; max-width:480px; line-height:1.7;">{{ $data['subtitle'] }}</p>
                @endif
                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['button_text']))
                        <a href="{{ $data['button_url'] ?? $shop_url }}" style="display:inline-flex;align-items:center;gap:6px;background:var(--dk-red);color:#fff;padding:14px 32px;border-radius:var(--dk-radius);font-size:14px;font-weight:800;text-decoration:none;text-transform:uppercase;letter-spacing:.5px;">
                            {{ $data['button_text'] }}
                        </a>
                    @endif
                    @if(!empty($data['button2_text']))
                        <a href="{{ $data['button2_url'] ?? '#' }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--dk-silver);border:1px solid var(--dk-border-mid);padding:12px 28px;border-radius:var(--dk-radius);font-size:14px;font-weight:700;text-decoration:none;text-transform:uppercase;letter-spacing:.5px;">
                            {{ $data['button2_text'] }} <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 text-center">
                @if(!empty($hero_img_url))
                    <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}" style="max-width:100%; max-height:420px; object-fit:contain; filter:drop-shadow(0 20px 40px rgba(204,34,0,.25));">
                @else
                    <div style="width:100%;max-width:420px;height:320px;margin:0 auto;background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);display:flex;align-items:center;justify-content:center;font-size:80px;">🚗</div>
                @endif
            </div>
        </div>
    </div>
</section>
