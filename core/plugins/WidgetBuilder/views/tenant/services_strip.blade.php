<section class="xgpb-services-strip" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px; background:{{ $bg_color ?? '#ffffff' }}; border-top:1px solid #f0f0f0; border-bottom:1px solid #f0f0f0;">
    <div class="container">
        @if(!empty($services))
            <div class="row g-4 justify-content-center">
                @foreach($services as $service)
                    <div class="col-6 col-md-3 col-lg">
                        <div class="xgpb-service-item text-center p-3">
                            @if(!empty($service['icon']))
                                <div class="xgpb-service-icon mx-auto mb-3">
                                    <i class="{{ $service['icon'] }}"></i>
                                </div>
                            @endif
                            @if(!empty($service['title']))
                                <h5 class="xgpb-service-title">{{ $service['title'] }}</h5>
                            @endif
                            @if(!empty($service['description']))
                                <p class="xgpb-service-desc mb-0">{{ $service['description'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<style>
.xgpb-service-item { border-radius: 12px; transition: background .2s; }
.xgpb-service-item:hover { background: rgba(0,0,0,.03); }
.xgpb-service-icon { width: 60px; height: 60px; border-radius: 50%; background: rgba(0,0,0,.05); background: color-mix(in srgb, var(--main-color-one, #0d6efd) 10%, transparent); display: flex; align-items: center; justify-content: center; }
.xgpb-service-icon i { font-size: 1.5rem; color: var(--main-color-one, #0d6efd); }
.xgpb-service-title { font-weight: 600; font-size: .95rem; color: #1a1a2e; margin-bottom: .25rem; }
.xgpb-service-desc { font-size: .8rem; color: #6c757d; }
</style>
