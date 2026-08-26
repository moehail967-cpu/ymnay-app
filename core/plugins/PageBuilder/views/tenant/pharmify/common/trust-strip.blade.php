@php
    $pt = !empty($data['padding_top'])    ? (int)$data['padding_top']    : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
@endphp

<style>
    .pf-trust-strip {
        background: #fff;
        border-top: 1px solid var(--pf-border, #DDE6EA);
        border-bottom: 1px solid var(--pf-border, #DDE6EA);
    }
    .pf-trust-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 16px;
    }
    .pf-trust-icon {
        font-size: 28px;
        color: var(--pf-teal, #00897B);
        flex-shrink: 0;
    }
    .pf-trust-label {
        font-size: 14px;
        font-weight: 700;
        color: var(--pf-dark, #1A2332);
        margin-bottom: 2px;
    }
    .pf-trust-sub {
        font-size: 12px;
        color: var(--pf-muted, #607080);
    }
    @media (max-width: 575px) {
        .pf-trust-item { padding: 16px 12px; }
    }
</style>

<div class="pf-trust-strip" style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="container">
        <div class="row g-0">
            @foreach($data['items'] as $item)
                <div class="col-6 col-md-3">
                    <div class="pf-trust-item">
                        <span class="mdi {{ $item['icon'] }} pf-trust-icon"></span>
                        <div>
                            <div class="pf-trust-label">{{ $item['title'] }}</div>
                            <div class="pf-trust-sub">{{ $item['sub'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
