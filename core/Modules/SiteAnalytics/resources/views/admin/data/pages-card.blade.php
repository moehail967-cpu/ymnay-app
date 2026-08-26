<div class="analytics-list">
    <div class="list-head">
        <div class="icon"><i class="mdi mdi-file-document-outline"></i></div>
        <h4>{{__('Top Pages')}}</h4>
    </div>
    <div class="list-cols">
        <span>{{__('Page')}}</span>
        <span>{{__('Users')}}</span>
    </div>
    <div class="list-body">
        @foreach ($pages as $i => $page)
            @php
                $newString = preg_replace_callback('/^\/|^\//', function ($matches) {
                    return $matches[0] === '/' ? '' : 'home';
                }, $page->page);
                if ($newString === '') $newString = 'home';
            @endphp
            <div class="list-row">
                <div class="flex items-center min-w-0 mr-3">
                    <span class="rank">{{ $i + 1 }}</span>
                    <a href="{{$page->page}}" target="_blank" class="name">{{$newString}}</a>
                </div>
                <span class="count">{{ $page->users }}</span>
            </div>
        @endforeach
    </div>
</div>
