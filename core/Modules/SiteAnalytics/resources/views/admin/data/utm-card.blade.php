<div class="col-span-1">
    <div class="analytics-list">
        <div class="list-head">
            <div class="icon"><i class="mdi mdi-tag-text-outline"></i></div>
            <h4>{{ $data['key'] }}</h4>
        </div>
        <div class="list-cols">
            <span>{{__('Term')}}</span>
            <span>{{__('Count')}}</span>
        </div>
        <div class="list-body">
            @foreach ($data['items'] as $i => $item)
                <div class="list-row">
                    <div class="flex items-center min-w-0 mr-3">
                        <span class="rank">{{ $i + 1 }}</span>
                        <span class="name">{{ $item['value'] }}</span>
                    </div>
                    <span class="count">{{ $item['count'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
