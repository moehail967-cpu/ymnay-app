@php
    $pt = !empty($data['padding_top'])    ? (int)$data['padding_top']    : 60;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 60;
    $steps = [
        ['icon' => '📋', 'label' => $data['step1_label']],
        ['icon' => '👨‍⚕️', 'label' => $data['step2_label']],
        ['icon' => '📦', 'label' => $data['step3_label']],
    ];
@endphp

<style>
    .pf-rx-banner {
        background: linear-gradient(135deg, #003D35 0%, #00574A 60%, #006358 100%);
        position: relative;
        overflow: hidden;
    }
    .pf-rx-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 80% 50%, rgba(0,137,123,.25) 0%, transparent 65%);
        pointer-events: none;
    }
    .pf-rx-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,.12);
        color: rgba(255,255,255,.9);
        border: 1px solid rgba(255,255,255,.2);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        padding: 5px 14px;
        margin-bottom: 16px;
    }
    .pf-rx-title {
        font-size: clamp(22px, 3vw, 36px);
        font-weight: 800;
        color: #fff;
        line-height: 1.2;
        margin-bottom: 14px;
    }
    .pf-rx-text {
        font-size: 15px;
        color: rgba(255,255,255,.75);
        line-height: 1.7;
        max-width: 520px;
        margin-bottom: 28px;
    }
    .pf-btn-ghost {
        background: transparent;
        color: rgba(255,255,255,.85);
        border: 2px solid rgba(255,255,255,.35);
    }
    .pf-btn-ghost:hover {
        background: rgba(255,255,255,.1);
        border-color: rgba(255,255,255,.6);
        color: #fff;
    }
    .pf-rx-steps {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .pf-rx-step {
        text-align: center;
    }
    .pf-rx-step-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255,255,255,.12);
        border: 2px solid rgba(255,255,255,.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 8px;
    }
    .pf-rx-step-text {
        font-size: 12px;
        font-weight: 700;
        color: rgba(255,255,255,.85);
        text-transform: uppercase;
        letter-spacing: .06em;
    }
    .pf-rx-arrow {
        font-size: 20px;
        color: rgba(255,255,255,.4);
        flex-shrink: 0;
    }
</style>

<section class="pf-rx-banner" style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="container" style="position:relative; z-index:1;">
        <div class="row align-items-center">
            <div class="col-lg-7">
                @if(!empty($data['badge_text']))
                    <div class="pf-rx-badge">
                        <i class="mdi mdi-file-upload-outline"></i>
                        {{ $data['badge_text'] }}
                    </div>
                @endif

                @if(!empty($data['title']))
                    <h2 class="pf-rx-title">{{ $data['title'] }}</h2>
                @endif

                @if(!empty($data['text']))
                    <p class="pf-rx-text">{{ $data['text'] }}</p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['btn1_text']))
                        <a href="{{ $data['btn1_url'] }}" class="pf-btn pf-btn-primary">
                            {{ $data['btn1_text'] }}
                            <i class="mdi mdi-upload"></i>
                        </a>
                    @endif
                    @if(!empty($data['btn2_text']))
                        <a href="{{ $data['btn2_url'] }}" class="pf-btn pf-btn-ghost">
                            {{ $data['btn2_text'] }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-flex align-items-center justify-content-center mt-4 mt-lg-0">
                <div class="pf-rx-steps">
                    @foreach($steps as $idx => $step)
                        <div class="pf-rx-step">
                            <div class="pf-rx-step-icon">{{ $step['icon'] }}</div>
                            <div class="pf-rx-step-text">{{ $step['label'] }}</div>
                        </div>
                        @if($idx < count($steps) - 1)
                            <i class="mdi mdi-arrow-right pf-rx-arrow"></i>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
