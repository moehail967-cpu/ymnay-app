<div class="analytics-list">
    <div class="list-head">
        <div class="icon"><i class="mdi mdi-earth"></i></div>
        <h4>{{__('Geo Locations')}}</h4>
    </div>
    <div class="list-cols">
        <span>{{__('Country')}}</span>
        <span>{{__('Users')}}</span>
    </div>
    <div class="list-body">
        @foreach ($users as $i => $user)
            <div class="list-row">
                <div class="flex items-center min-w-0 mr-3">
                    <span class="rank">{{ $i + 1 }}</span>
                    <span class="name">{{$user->country}}</span>
                </div>
                <span class="count">{{ $user->users }}</span>
            </div>
        @endforeach
    </div>
</div>
