@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Digital Collection') }} @endsection

@section('content')
<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="active">{{ __('Digital Collection') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:40px 0 80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="lg-sidebar" style="margin-bottom:20px;">
                <div class="lg-sidebar-title">{{ __('Search') }}</div>
                <input type="text" id="lx-dp-search" class="lg-form-control"
                       placeholder="{{ __('Search products…') }}">
            </div>

            {{-- Categories --}}
            @if(!empty($categories))
            <div class="lg-sidebar" style="margin-bottom:20px;">
                <div class="lg-sidebar-title">{{ __('Categories') }}</div>
                @foreach($categories as $cat)
                <div class="lg-check-row">
                    <label>
                        <input type="checkbox" class="lx-dp-cat-check"
                               data-slug="{{ $cat->slug }}"> {{ $cat->name }}
                    </label>
                    <span class="lg-check-count">{{ $cat->digital_product_count ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Price Range --}}
            <div class="lg-sidebar" style="margin-bottom:20px;">
                <div class="lg-sidebar-title">{{ __('Price Range') }}</div>
                <div class="lg-price-inputs">
                    <input type="number" id="lx-dp-min" placeholder="{{ __('Min') }}" value="0" min="0">
                    <span>—</span>
                    <input type="number" id="lx-dp-max" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="lx-btn lx-btn-outline" style="width:100%;justify-content:center;padding:10px;" onclick="lxDpFilter()">
                    {{ __('Apply') }}
                </button>
            </div>

            {{-- Tags --}}
            @if(!empty($tags))
            <div class="lg-sidebar">
                <div class="lg-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                    <span class="lg-tag lx-dp-tag" data-slug="{{ $tag->slug }}" style="cursor:pointer;">
                        {{ $tag->tag_name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div class="lg-shop-topbar">
                <span class="lg-shop-count lx-dp-showing">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('results') }}
                </span>
                <select class="lg-sort-select" id="lx-dp-sort">
                    <option value="3">{{ __('Sort: Latest') }}</option>
                    <option value="1">{{ __('Sort: Name') }}</option>
                    <option value="4">{{ __('Price: Low to High') }}</option>
                    <option value="5">{{ __('Price: High to Low') }}</option>
                </select>
            </div>

            <div class="row g-3 lx-dp-grid">
                @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
(function($){
    var _searchTimer;

    function lxDpFilter(page) {
        page = page || null;
        var cats = [];
        $('.lx-dp-cat-check:checked').each(function(){ cats.push($(this).data('slug')); });
        var tags = [];
        $('.lx-dp-tag.active').each(function(){ tags.push($(this).data('slug')); });

        var data = {
            sort: $('#lx-dp-sort').val(),
            min_price: $('#lx-dp-min').val() || 0,
            max_price: $('#lx-dp-max').val() || 10000,
            page: page
        };
        if(cats.length) data.category = cats.join(',');
        if(tags.length) data.tag = tags.join(',');
        var s = $('#lx-dp-search').val().trim();
        if(s.length) data.search = s;

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function(){ $('.loader').show(); },
            success: function(data){
                $('.lx-dp-grid').html(data.grid);
                var p = data.pagination;
                $('.lx-dp-showing').html('{{ __('Showing') }} <strong>' + (p.to ? p.from+'-'+p.to : 0) + '</strong> {{ __('of') }} <strong>' + (p.total||0) + '</strong> {{ __('results') }}');
                setTimeout(function(){ $('.loader').hide(); }, 400);
            },
            error: function(){ $('.loader').hide(); }
        });
    }

    window.lxDpFilter = lxDpFilter;

    $(document).on('change', '.lx-dp-cat-check', function(){ lxDpFilter(); });
    $('#lx-dp-sort').on('change', function(){ lxDpFilter(); });

    $(document).on('click', '.lx-dp-tag', function(){
        $(this).toggleClass('active');
        lxDpFilter();
    });

    $('#lx-dp-search').on('keyup', function(){
        clearTimeout(_searchTimer);
        _searchTimer = setTimeout(function(){ lxDpFilter(); }, 350);
    });

})(jQuery);
</script>
@endsection
