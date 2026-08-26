@php
    $hero_image   = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top'])    ? (int)$data['padding_top']    : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url = route('tenant.shop');
@endphp

<div style="
    background: linear-gradient(135deg, var(--tc-olive-light) 0%, var(--tc-terra-light) 100%);
    padding-top:{{$pt}}px;
    padding-bottom:{{$pb}}px;
    position:relative;
    overflow:hidden;
">
    {{-- Topographic texture dots --}}
    <div style="position:absolute;inset:0;background-image:radial-gradient(var(--tc-border) 1px, transparent 1px);background-size:24px 24px;opacity:.5;pointer-events:none;"></div>

    <div class="container" style="position:relative;z-index:1;">
        <div style="display:flex;align-items:center;gap:48px;flex-wrap:wrap;">

            {{-- Left: text --}}
            <div style="flex:1;min-width:280px;">

                @if(!empty($data['badge_text']))
                    <div style="display:inline-flex;align-items:center;gap:8px;background:var(--tc-olive);color:#fff;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:6px 16px;border-radius:50px;margin-bottom:20px;">
                        <i class="mdi mdi-pine-tree"></i>
                        {{ $data['badge_text'] }}
                    </div>
                @endif

                @if(!empty($data['title']))
                    <h1 style="font-size:clamp(30px,5vw,58px);font-weight:900;line-height:1.1;color:var(--tc-dark);margin-bottom:18px;letter-spacing:-.02em;">
                        {!! nl2br(e($data['title'])) !!}
                    </h1>
                @endif

                @if(!empty($data['subtitle']))
                    <p style="font-size:16px;color:var(--tc-muted);max-width:480px;line-height:1.7;margin-bottom:32px;">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['button_text']))
                        <a href="{{ $data['button_url'] ?? $shop_url }}"
                           style="display:inline-flex;align-items:center;gap:8px;background:var(--tc-olive);color:#fff;font-weight:700;font-size:15px;padding:14px 28px;border-radius:6px;text-decoration:none;transition:background .2s;"
                           onmouseover="this.style.background='var(--tc-olive-deep)'"
                           onmouseout="this.style.background='var(--tc-olive)'">
                            <i class="mdi mdi-shopping"></i>
                            {{ $data['button_text'] }}
                        </a>
                    @endif
                    @if(!empty($data['button2_text']))
                        <a href="{{ $data['button2_url'] ?? '#' }}"
                           style="display:inline-flex;align-items:center;gap:8px;background:transparent;color:var(--tc-olive);font-weight:700;font-size:15px;padding:14px 28px;border-radius:6px;border:2px solid var(--tc-olive);text-decoration:none;transition:background .2s,color .2s;"
                           onmouseover="this.style.background='var(--tc-olive)';this.style.color='#fff'"
                           onmouseout="this.style.background='transparent';this.style.color='var(--tc-olive)'">
                            {{ $data['button2_text'] }}
                            <i class="mdi mdi-map-marker-path"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Right: hero image --}}
            @if(!empty($hero_img_url))
                <div style="flex:0 0 auto;max-width:420px;width:100%;">
                    <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}"
                         style="width:100%;height:auto;object-fit:contain;filter:drop-shadow(0 20px 40px rgba(30,32,25,.18));"
                         loading="lazy">
                </div>
            @endif

        </div>
    </div>
</div>
