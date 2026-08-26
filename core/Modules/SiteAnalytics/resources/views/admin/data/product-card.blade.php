@php
    $plans = \Modules\Product\Entities\Product::published()->select(['id','slug', 'name']);
    $slug_name = $plans->pluck('name', 'slug');
    $id_slug = $plans->pluck('id','slug');

    $pages_array = $products_views->toArray();
    $plan_pages = array_map(function ($item) {
        $item['page'] = str_replace(['/shop/product/'],'',$item['page']);
        return $item;
    }, $pages_array);

    $product_with_names = [];
    foreach ($plan_pages ?? [] as $key => $item)
    {
        if (!empty(current($id_slug)[$item['page']]))
        {
            $product_with_names[$key]['id'] = current($id_slug)[$item['page']] ?? '';
            $product_with_names[$key]['users'] = $item['users'] ?? '';
            $product_with_names[$key]['name'] = current($slug_name)[$item['page']] ?? '';
        }
    }
@endphp

<div class="analytics-list">
    <div class="list-head">
        <div class="icon"><i class="mdi mdi-shopping-outline"></i></div>
        <h4>{{__('Top Products')}}</h4>
    </div>
    <div class="list-cols">
        <span>{{__('Product')}}</span>
        <span>{{__('Views')}}</span>
    </div>
    <div class="list-body">
        @foreach ($product_with_names ?? [] as $i => $item)
            <div class="list-row">
                <div class="flex items-center min-w-0 mr-3">
                    <span class="rank">{{ $i + 1 }}</span>
                    <a href="{{$item['id'] ? route('tenant.admin.product.edit', $item['id']) : '#0'}}" class="name">
                        {{$item['name'] ?? ''}}
                    </a>
                </div>
                <span class="count">{{ $item['users'] }}</span>
            </div>
        @endforeach
    </div>
</div>
