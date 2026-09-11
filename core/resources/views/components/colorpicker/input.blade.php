<div class="color-picker-card group">
    <div class="flex items-center gap-3">
        <div class="spectrum_color_picker" title="{{__('Click to change color')}}" style="background-color: {{$value ?? ''}}"></div>
        <input type="hidden" name="{{$name}}" class="form-control" value="{{$value ?? ''}}">
        <div class="flex-1 min-w-0">
            <span class="block text-xs font-semibold text-dark leading-tight truncate">{{$label}}</span>
            <span class="color-hex-display text-[11px] font-mono text-muted mt-0.5 block">{{$value ?? '—'}}</span>
        </div>
    </div>
    @if(isset($info))
        <p class="text-[11px] text-muted mt-2 leading-relaxed">{!! $info !!}</p>
    @endif
</div>
