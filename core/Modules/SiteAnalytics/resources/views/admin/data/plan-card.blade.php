@php
    $plans = \App\Models\PricePlan::where('status', 1)->select(['id', 'title'])->pluck('title', 'id');

    $pages_array = $pages->toArray();
    $plan_pages = array_map(function ($item) {
        $item['page'] = str_replace(['/plan-order/', '/view-plan/','/trial'],'',$item['page']);
        return $item;
    }, $pages_array);

    $plan_with_names = [];
    foreach ($plan_pages ?? [] as $key => $item)
    {
        $plan_with_names[$key]['id'] = $item['page'] ?? '';
        $plan_with_names[$key]['users'] = $item['users'];
        $plan_with_names[$key]['name'] = current($plans)[$item['page']] ?? '';
    }
@endphp

<div class="analytics-list">
    <div class="list-head">
        <div class="icon"><i class="mdi mdi-package-variant"></i></div>
        <h4>{{__('Plan Views')}}</h4>
    </div>
    <div class="list-cols">
        <span>{{__('Plan')}}</span>
        <span>{{__('Views')}}</span>
    </div>
    <div class="list-body">
        @foreach ($plan_with_names ?? [] as $i => $item)
            <div class="list-row">
                <div class="flex items-center min-w-0 mr-3">
                    <span class="rank">{{ $i + 1 }}</span>
                    <a href="{{$item['id'] ? route('landlord.admin.price.plan.edit', $item['id']) : '#0'}}" class="name">
                        {{$item['name'] ?? ''}}
                    </a>
                </div>
                <span class="count">{{ $item['users'] }}</span>
            </div>
        @endforeach
    </div>
</div>
