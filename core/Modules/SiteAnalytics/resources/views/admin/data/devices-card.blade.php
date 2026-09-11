<div class="analytics-list">
    <div class="list-head">
        <div class="icon"><i class="mdi mdi-cellphone-link"></i></div>
        <h4>{{__('Devices')}}</h4>
    </div>
    <div class="list-cols">
        <span>{{__('Type')}}</span>
        <span>{{__('Users')}}</span>
    </div>
    <div class="list-body">
        @foreach ($devices as $i => $device)
            <div class="list-row">
                <div class="flex items-center gap-2 min-w-0 mr-3">
                    <span class="rank">{{ $i + 1 }}</span>
                    @if(strtolower($device->type) === 'desktop')
                        <i class="mdi mdi-monitor text-muted"></i>
                    @elseif(strtolower($device->type) === 'mobile')
                        <i class="mdi mdi-cellphone text-muted"></i>
                    @elseif(strtolower($device->type) === 'tablet')
                        <i class="mdi mdi-tablet text-muted"></i>
                    @else
                        <i class="mdi mdi-devices text-muted"></i>
                    @endif
                    <span class="name">{{$device->type}}</span>
                </div>
                <span class="count">{{ $device->users }}</span>
            </div>
        @endforeach
    </div>
</div>
