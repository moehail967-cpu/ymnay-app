@php
    $justify = match($alignment) {
        'right'  => 'end',
        'left'   => 'start',
        default  => 'center',
    };
@endphp

<section class="xgpb-icon-box-section" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        @if(!empty($link))
            <a href="{{ e($link) }}" class="xgpb-icon-box__link text-decoration-none d-block">
        @endif
        <div class="xgpb-icon-box text-{{$alignment}}">
            <div class="d-flex justify-content-{{$justify}} mb-3">
                <span class="xgpb-icon-box__icon-wrap">
                    <i class="{{ $icon }} xgpb-icon-box__icon"></i>
                </span>
            </div>
            @if(!empty($title))
                <h3 class="xgpb-icon-box__title">{{ $title }}</h3>
            @endif
            @if(!empty($description))
                <p class="xgpb-icon-box__desc">{{ $description }}</p>
            @endif
        </div>
        @if(!empty($link))
            </a>
        @endif
    </div>
</section>

<style>
.xgpb-icon-box { padding: 1.5rem; }
.xgpb-icon-box__link { color: inherit; }
.xgpb-icon-box__icon-wrap { display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background: color-mix(in srgb, var(--main-color-one, #6366f1) 10%, transparent); }
.xgpb-icon-box__icon { font-size: 3rem; color: var(--main-color-one, #6366f1); line-height: 1; }
.xgpb-icon-box__title { font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: .5rem; margin-top: .25rem; }
.xgpb-icon-box__desc { color: #6b7280; line-height: 1.6; margin-bottom: 0; }
</style>
