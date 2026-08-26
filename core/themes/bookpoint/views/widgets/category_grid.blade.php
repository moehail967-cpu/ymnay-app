@php
    $categories  = theme_categories()->take($limit);
    $products    = theme_featured_products($product_limit);
    $uid         = 'bpcg' . substr(md5(uniqid()), 0, 8);
@endphp
<section class="bp-catgrid-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="bp-catgrid-head">
            <h2 class="bp-section-title">{{ $title }}</h2>
            @if($show_view_all)
                <a href="{{ theme_shop_url() }}" class="bp-view-all">{{ __('View All') }}</a>
            @endif
        </div>

        {{-- Tab filter row --}}
        <div class="bp-tab-row">
            <button class="bp-tab active" data-filter="all">{{ __('All') }}</button>
            @foreach($categories as $cat)
                <button class="bp-tab" data-filter="{{ $cat->slug }}">{{ $cat->name }}</button>
            @endforeach
        </div>

        {{-- Product grid --}}
        <div class="bp-catgrid-grid" id="{{ $uid }}">
            @forelse($products as $product)
            @php
                $data     = theme_product_price($product);
                $img_url  = theme_product_image($product->image_id ?? null, 'grid');
                $url      = theme_product_url($product->slug);
                $cat_slug = $product->category?->slug ?? '';
            @endphp
            <div class="bp-catgrid-item" data-category="{{ $cat_slug }}">
                <a href="{{ $url }}" class="bp-catgrid-card">
                    <div class="bp-catgrid-img">
                        @if($img_url)
                            <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <div class="bp-catgrid-placeholder"><i class="las la-book"></i></div>
                        @endif
                    </div>
                    <div class="bp-catgrid-info">
                        <div class="bp-catgrid-name">{{ \Illuminate\Support\Str::words($product->name, 5) }}</div>
                        <div class="bp-catgrid-cat">{{ $product->category?->name ?? '' }}</div>
                        <div class="bp-catgrid-price">{{ amount_with_currency_symbol($data['sale_price']) }}</div>
                    </div>
                </a>
            </div>
            @empty
            <p class="text-muted col-span-3">{{ __('No products found.') }}</p>
            @endforelse
        </div>
    </div>
</section>
<script>
(function(){
    var tabs  = document.querySelectorAll('.bp-tab-row .bp-tab');
    var items = document.querySelectorAll('#{{ $uid }} .bp-catgrid-item');
    tabs.forEach(function(tab){
        tab.addEventListener('click', function(){
            tabs.forEach(function(t){ t.classList.remove('active'); });
            tab.classList.add('active');
            var filter = tab.dataset.filter;
            items.forEach(function(item){
                item.style.display = (filter === 'all' || item.dataset.category === filter) ? '' : 'none';
            });
        });
    });
})();
</script>
