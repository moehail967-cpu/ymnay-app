<section class="xgpb-box-section" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        <div class="xgpb-box text-{{$alignment}}">
            @if(!empty($heading))
                <h3 class="xgpb-box__heading">{{ $heading }}</h3>
            @endif
            @if(!empty($body))
                <div class="xgpb-box__body">{!! $body !!}</div>
            @endif
        </div>
    </div>
</section>

<style>
.xgpb-box { background: #fff; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
.xgpb-box__heading { font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: #1a1a2e; }
.xgpb-box__body { color: #555; line-height: 1.7; margin-bottom: 0; }
</style>
