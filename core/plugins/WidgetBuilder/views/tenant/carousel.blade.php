<section class="xgpb-carousel-section" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        <div class="xgpb-carousel" id="{{ $id }}">
            @foreach($slides as $i => $slide)
                <input type="radio"
                       name="xgpbcr-{{ $id }}"
                       id="xgpbcr-s{{ $i }}-{{ $id }}"
                       class="xgpb-cr-input"
                       @if($i === 0) checked @endif>
            @endforeach

            <div class="xgpb-cr-slides" style="border-radius:{{ $radius }}px; height:{{ $height }}px;">
                @foreach($slides as $i => $slide)
                    <div class="xgpb-cr-slide">
                        @if(!empty($slide['link']))
                            <a href="{{ e($slide['link']) }}" class="d-block w-100 h-100">
                        @endif
                        <img src="{{ $slide['img_url'] }}"
                             alt="{{ $slide['caption'] }}"
                             class="xgpb-cr-img w-100 h-100"
                             loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                        @if(!empty($slide['link']))</a>@endif

                        @if($show_caption && !empty($slide['caption']))
                            <div class="xgpb-cr-caption">{{ $slide['caption'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(count($slides) > 1)
                <div class="xgpb-cr-dots d-flex justify-content-center gap-2 mt-3">
                    @foreach($slides as $i => $slide)
                        <label for="xgpbcr-s{{ $i }}-{{ $id }}" class="xgpb-cr-dot" title="{{ __('Slide') }} {{ $i + 1 }}"></label>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<style>
.xgpb-cr-input { display: none; }
.xgpb-cr-slides { position: relative; overflow: hidden; background: #e9ecef; }
.xgpb-cr-slide { display: none; position: absolute; inset: 0; }
.xgpb-cr-img { object-fit: cover; }
.xgpb-cr-caption { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,.5); color: #fff; padding: .5rem 1rem; font-size: .875rem; }
.xgpb-cr-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #ccc; cursor: pointer; transition: background .2s; }
@foreach($slides as $i => $slide)
#xgpbcr-s{{ $i }}-{{ $id }}:checked ~ .xgpb-cr-slides .xgpb-cr-slide:nth-child({{ $i + 1 }}) { display: block; }
#xgpbcr-s{{ $i }}-{{ $id }}:checked ~ .xgpb-cr-dots .xgpb-cr-dot:nth-child({{ $i + 1 }}) { background: var(--main-color, #0d6efd); }
@endforeach
</style>
