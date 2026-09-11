@php
    $uid = 'xgpb_pbc_' . uniqid();
    $col_class = match((int)$columns) {
        2 => 'col-6',
        3 => 'col-6 col-md-4',
        5 => 'col-6 col-md col-lg',
        default => 'col-6 col-md-4 col-lg-3',
    };
@endphp

<section class="xgpb-products-by-cat" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        @if(!empty($title))
            <div class="xgpb-section-header d-flex align-items-center justify-content-between mb-4">
                <h2 class="xgpb-section-title mb-0">{{ $title }}</h2>
                @if(!empty($view_all_text))
                    <a href="{{ $view_all_url }}" class="btn btn-outline-primary btn-sm">{{ $view_all_text }}</a>
                @endif
            </div>
        @endif

        {{-- Category Tabs --}}
        @if($categories->isNotEmpty())
            <ul class="xgpb-cat-tabs nav gap-2 mb-4" id="{{ $uid }}_tabs">
                @if($show_all_tab)
                    <li class="nav-item">
                        <button class="xgpb-cat-tab active" data-target="{{ $uid }}_all">{{ __('All') }}</button>
                    </li>
                @endif
                @foreach($categories as $cat)
                    <li class="nav-item">
                        <button class="xgpb-cat-tab" data-target="{{ $uid }}_cat_{{ $cat->id }}">{{ $cat->name }}</button>
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- All Products Tab --}}
        @if($show_all_tab)
            <div class="xgpb-tab-panel" id="{{ $uid }}_all">
                @if($all_products->isNotEmpty())
                    <div class="row g-4">
                        @foreach($all_products as $product)
                            @include('widgetbuilder::tenant.partials.product_card', ['product' => $product, 'col_class' => $col_class])
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted py-4">{{ __('No products found.') }}</p>
                @endif
            </div>
        @endif

        {{-- Per-Category Tab Panels --}}
        @foreach($categories as $cat)
            <div class="xgpb-tab-panel d-none" id="{{ $uid }}_cat_{{ $cat->id }}">
                @if(!empty($cat_products[$cat->id]) && $cat_products[$cat->id]->isNotEmpty())
                    <div class="row g-4">
                        @foreach($cat_products[$cat->id] as $product)
                            @include('widgetbuilder::tenant.partials.product_card', ['product' => $product, 'col_class' => $col_class])
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted py-4">{{ __('No products in this category.') }}</p>
                @endif
            </div>
        @endforeach
    </div>
</section>

<script>
(function() {
    var tabs = document.querySelectorAll('#{{ $uid }}_tabs .xgpb-cat-tab');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            document.querySelectorAll('.xgpb-tab-panel').forEach(function(p) { p.classList.add('d-none'); });
            var target = document.getElementById(this.dataset.target);
            if (target) target.classList.remove('d-none');
        });
    });
})();
</script>

<style>
.xgpb-cat-tabs { flex-wrap: wrap; list-style: none; padding: 0; margin: 0; }
.xgpb-cat-tab { background: #f3f4f6; border: none; border-radius: 50px; padding: .45em 1.2em; font-size: .875rem; font-weight: 600; color: #4b5563; cursor: pointer; transition: all .2s; }
.xgpb-cat-tab.active, .xgpb-cat-tab:hover { background: var(--main-color, #0d6efd); color: #fff; }
</style>
