@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $category->name !!} @endsection
@section('page-title') {!! $category->name !!} @endsection

@section('content')

{{-- Breadcrumb --}}
<div class="dk-breadcrumb-bar">
    <div class="container">
        <ul class="dk-bc-list">
            <li><a href="{{ theme_home_url() }}"><i class="mdi mdi-home-outline"></i> {{ __('Home') }}</a></li>
            <li class="sep"><i class="mdi mdi-chevron-right"></i></li>
            <li><a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a></li>
            <li class="sep"><i class="mdi mdi-chevron-right"></i></li>
            <li class="active">{!! $category->name !!}</li>
        </ul>
    </div>
</div>



{{-- Main Layout --}}
<section class="py-4">
    <div class="container">
        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-lg-3">

                {{-- Search --}}
                <div class="dk-sidebar-card">
                    <div class="dk-sidebar-title"><i class="mdi mdi-magnify"></i> {{ __('Search') }}</div>
                    <div class="dk-sidebar-search">
                        <input type="text" id="dk-part-search" placeholder="{{ __('Search products...') }}">
                        <button type="button" onclick="dkFilterRequest()"><i class="mdi mdi-magnify"></i></button>
                    </div>
                </div>

                {{-- Categories --}}
                @if(isset($categories) && $categories->isNotEmpty())
                <div class="dk-sidebar-card">
                    <div class="dk-sidebar-title">{{ __('Categories') }}</div>
                    <div class="d-flex flex-column gap-1">
                        @foreach($categories as $cat)
                        <label class="dk-check-label">
                            <span>
                                <input type="checkbox" class="dk-cat-check" data-slug="{{ $cat->slug }}"
                                    {{ ($cat->slug === ($category->slug ?? '')) ? 'checked' : '' }}>
                                {{ $cat->name }}
                            </span>
                            @if($cat->product_count ?? false)
                                <span class="dk-check-count">{{ $cat->product_count }}</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Price Range --}}
                <div class="dk-sidebar-card">
                    <div class="dk-sidebar-title">{{ __('Price Range') }}</div>
                    <div class="dk-price-row">
                        <input type="number" id="dk-min-price" class="dk-price-input" placeholder="{{ __('Min') }}" value="0" min="0">
                        <input type="number" id="dk-max-price" class="dk-price-input" placeholder="{{ __('Max') }}" value="10000" min="0">
                    </div>
                    <button class="dk-btn dk-btn-red dk-btn-sm dk-btn-block" onclick="dkFilterRequest()">
                        {{ __('APPLY') }}
                    </button>
                </div>

                {{-- Rating --}}
                <div class="dk-sidebar-card">
                    <div class="dk-sidebar-title">{{ __('Customer Rating') }}</div>
                    <div class="d-flex flex-column gap-1">
                        @foreach([5,4,3] as $r)
                        <label class="dk-check-label">
                            <span>
                                <input type="radio" name="dk_rating" class="dk-rating-filter" value="{{ $r }}">
                                <span class="dk-star-gold">{{ str_repeat('★',$r) }}</span><span class="dk-star-dim">{{ str_repeat('★',5-$r) }}</span>
                            </span>
                            <span class="dk-check-count">{{ $r }}{{ $r < 5 ? '+' : '' }}</span>
                        </label>
                        @endforeach
                    </div>
                    <a class="dk-clear-link" onclick="$('.dk-rating-filter').prop('checked',false);$('.dk-cat-check').prop('checked',false);dkFilterRequest();">
                        <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear All Filters') }}
                    </a>
                </div>

            </div>

            {{-- Products --}}
            <div class="col-lg-9">
                <div class="dk-sort-bar">
                    <span class="dk-sort-count showing-results">
                        {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                        {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('products') }}
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <label class="dk-sort-label">{{ __('SORT BY:') }}</label>
                        <select id="dk-sort-select" class="dk-sort-select">
                            <option value="3">{{ __('LATEST') }}</option>
                            <option value="1">{{ __('NAME') }}</option>
                            <option value="2">{{ __('POPULAR') }}</option>
                            <option value="4">{{ __('PRICE: LOW TO HIGH') }}</option>
                            <option value="5">{{ __('PRICE: HIGH TO LOW') }}</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 grid-product-list">
                    @include(include_theme_path('shop.partials.product-partials.grid-products'))
                </div>
            </div>

        </div>
    </div>
</section>

<div id="product-modal"></div>
@endsection

@section('scripts')
<script>
(function($){
    'use strict';
    function dkFilterRequest(page) {
        page = page || null;
        var cats = [];
        $('.dk-cat-check:checked').each(function(){ cats.push($(this).data('slug')); });
        var category_slug = cats.join(',') || null;
        var rating    = $('input[name=dk_rating]:checked').val() || null;
        var min_price = $('#dk-min-price').val() || 0;
        var max_price = $('#dk-max-price').val() || 10000;
        var sort      = $('#dk-sort-select').val();
        var search    = $('#dk-part-search').val() || null;

        $.ajax({
            type:'GET', url:'{{ theme_shop_filter_url() }}',
            data:{category:category_slug, rating:rating, min_price:min_price, max_price:max_price, sort:sort, page:page, search:search},
            beforeSend:function(){ $('.loader').show(); },
            success:function(data){
                $('.grid-product-list').html(data.grid);
                var p=data.pagination;
                $('.showing-results').html('{{ __("Showing") }} <strong>'+(p.to ? p.from+'-'+p.to : 0)+'</strong> {{ __("of") }} <strong>'+(p.total||0)+'</strong> {{ __("products") }}');
                $('.loader').hide();
            },
            error:function(){ $('.loader').hide(); }
        });
    }

    window.dkFilterRequest = dkFilterRequest;

    $(document).on('click','.dk-cat-nav-btn',function(){
        var slug=$(this).data('slug');
        $('.dk-cat-nav-btn').removeClass('dk-btn-red').addClass('dk-btn-ghost');
        $(this).removeClass('dk-btn-ghost').addClass('dk-btn-red');
        $('.dk-cat-check').prop('checked',false);
        if(slug){ $('.dk-cat-check[data-slug="'+slug+'"]').prop('checked',true); }
        dkFilterRequest();
    });

    $(document).on('change','.dk-cat-check',function(){ dkFilterRequest(); });
    $('#dk-sort-select').on('change',function(){ dkFilterRequest(); });
    $(document).on('change','.dk-rating-filter',function(){ dkFilterRequest(); });

    $(document).on('keydown','#dk-part-search',function(e){
        if(e.key==='Enter'){ dkFilterRequest(); }
    });

        // .add-to-cart-btn handled globally in footer.blade.php
})(jQuery);
</script>
@endsection
