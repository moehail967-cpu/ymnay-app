@php
    $hero_image  = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top'])    ? (int)$data['padding_top']    : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url = route('tenant.shop');
@endphp

<div style="
    background: linear-gradient(135deg, var(--sz-navy) 0%, var(--sz-navy-mid) 100%);
    padding-top:{{$pt}}px;
    padding-bottom:{{$pb}}px;
    position:relative;
    overflow:hidden;
">
    {{-- Diagonal accent stripe --}}
    <div style="position:absolute;top:0;right:0;width:45%;height:100%;background:var(--sz-red);clip-path:polygon(18% 0,100% 0,100% 100%,0% 100%);opacity:.08;pointer-events:none;"></div>

    <div class="container" style="position:relative;z-index:1;">
        <div style="display:flex;align-items:center;gap:48px;flex-wrap:wrap;">

            {{-- Left: text --}}
            <div style="flex:1;min-width:280px;">

                @if(!empty($data['badge_text']))
                    <div style="display:inline-flex;align-items:center;gap:8px;background:var(--sz-red);color:#fff;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:6px 16px;border-radius:50px;margin-bottom:20px;">
                        <i class="mdi mdi-football"></i>
                        {{ $data['badge_text'] }}
                    </div>
                @endif

                @if(!empty($data['title']))
                    <h1 style="font-size:clamp(32px,5vw,60px);font-weight:900;line-height:1.08;color:#ffffff;margin-bottom:18px;letter-spacing:-.02em;">
                        {!! nl2br(e($data['title'])) !!}
                    </h1>
                @endif

                @if(!empty($data['subtitle']))
                    <p style="font-size:16px;color:rgba(255,255,255,.75);max-width:480px;line-height:1.7;margin-bottom:32px;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['button_text']))
                        <a href="{{ $data['button_url'] ?? $shop_url }}"
                           style="display:inline-flex;align-items:center;gap:8px;background:var(--sz-red);color:#fff;font-weight:700;font-size:15px;padding:14px 28px;border-radius:6px;text-decoration:none;transition:background .2s;"
                           onmouseover="this.style.background='var(--sz-red-deep)'"
                           onmouseout="this.style.background='var(--sz-red)'">
                            <i class="mdi mdi-shopping"></i>
                            {{ $data['button_text'] }}
                        </a>
                    @endif
                    @if(!empty($data['button2_text']))
                        <a href="{{ $data['button2_url'] ?? '#' }}"
                           style="display:inline-flex;align-items:center;gap:8px;background:transparent;color:#fff;font-weight:600;font-size:15px;padding:14px 28px;border-radius:6px;border:2px solid rgba(255,255,255,.35);text-decoration:none;transition:border-color .2s;"
                           onmouseover="this.style.borderColor='rgba(255,255,255,.7)'"
                           onmouseout="this.style.borderColor='rgba(255,255,255,.35)'">
                            {{ $data['button2_text'] }}
                            <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Right: hero image --}}
            @if(!empty($hero_img_url))
                <div style="flex:0 0 auto;max-width:420px;width:100%;">
                    <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}"
                         style="width:100%;height:auto;object-fit:contain;filter:drop-shadow(0 20px 40px rgba(0,0,0,.4));"
                         loading="lazy">
                </div>
            @endif

        </div>
    </div>
</div>
