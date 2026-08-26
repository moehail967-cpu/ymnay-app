<div class="analytics-list">
    <div class="list-head">
        <div class="icon"><i class="mdi mdi-link-variant"></i></div>
        <h4>{{__('Traffic Sources')}}</h4>
    </div>
    <div class="list-cols">
        <span>{{__('Source')}}</span>
        <span>{{__('Users')}}</span>
    </div>
    <div class="list-body">
        @foreach ($sources as $i => $source)
            <div class="list-row">
                <div class="flex items-center min-w-0 mr-3">
                    <span class="rank">{{ $i + 1 }}</span>
                    <img class="analytics-fav mr-2" src="https://www.google.com/s2/favicons?domain={{ urlencode($source->page) }}" alt="" />
                    <a href="http://{{$source->page}}" target="_blank" class="name">{{$source->page}}</a>
                </div>
                <span class="count">{{ $source->users }}</span>
            </div>
        @endforeach
    </div>
</div>
