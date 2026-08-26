@props(['label' => '', 'name' => '', 'value' => '', 'setValue' => null, 'class' => ''])
<div class="form-group">
    @if(!empty($label))<label>{{$label}}</label>@endif
    <label class="switch {{$class ?? ''}}">
        <input type="checkbox" name="{{$name}}" @if(isset($setValue)) value="{{$setValue}}" @endif @if(!empty($value)) checked @endif>
        <span class="slider onff"></span>
    </label>
    @if(isset($info))
        <small class="info-text d-block mt-2 {{$infoClass ?? ''}}">{{$info}}</small>
    @endif
</div>
