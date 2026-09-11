@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title ?? __('Digital Shop') !!} @endsection
@section('page-title') {!! $page_post->title ?? __('Digital Shop') !!} @endsection

@section('content')
{{-- Page Hero --}}
<div class="pf-page-hero">
    <div class="container">
        <h1>{!! $page_post->title ?? __('Digital Shop') !!}</h1>
        <div class="pf-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{{ $page_post->title ?? __('Digital Shop') }}</span>
        </div>
    </div>
</div>

{{-- Shop Layout --}}
<div style="background:var(--pf-bg);padding:36px 0 72px;">
<div class="container">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="pf-sidebar">

                {{-- Search --}}
                <div class="pf-sidebar-search">
                    <input type="text" id="pf-dp-search" placeholder="{{ __('Search products…') }}">
                    <button type="button" onclick="pfDpFilter()"><i class="las la-search"></i></button>
                </div>

                {{-- Categories --}}
                @if($categories->isNotEmpty())
                <div class="pf-sidebar-section">
                    <div class="pf-sidebar-title">{{ __('Categories') }}</div>
                    @foreach($categories as $cat)
                    <div class="pf-check-item">
                        <label>
                            <input type="checkbox" class="pf-dp-cat-check" data-slug="{{ $cat->slug }}">
                            {{ $cat->name }}
                        </label>
                        @if($cat->product_count ?? false)
                            <span class="pf-count-badge">{{ $cat->product_count }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Price Range --}}
                <div class="pf-sidebar-section">
                    <div class="pf-sidebar-title">{{ __('Price Range') }}</div>
                    <div class="pf-price-inputs">
                        <input type="number" id="pf-dp-min" placeholder="{{ __('Min') }}" value="0" min="0">
                        <input type="number" id="pf-dp-max" placeholder="{{ __('Max') }}" value="10000" min="0">
                    </div>
                    <button class="pf-btn pf-btn-teal pf-btn-sm w-100" onclick="pfDpFilter()">
                        {{ __('Apply') }}
                    </button>
                </div>

                {{-- Tags --}}
                @if($tags->isNotEmpty())
                <div class="pf-sidebar-section">
                    <div class="pf-sidebar-title">{{ __('Tags') }}</div>
                    <div class="pf-type-pills">
                        @foreach($tags->take(15) as $tag)
                        <button class="pf-type-pill pf-dp-tag" data-tag="{{ $tag->tag_name }}">
                            {{ $tag->tag_name }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <a class="pf-clear-link" id="pf-dp-clear">
                    <i class="las la-times-circle"></i> {{ __('Clear Filters') }}
                </a>
            </div>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">

            <div class="pf-sort-bar">
                <p class="pf-dp-showing">
                    {{ __('Showing') }} <span>{{ $pagination->count() }}</span> {{ __('of') }} <span>{{ $pagination->total() }}</span> {{ __('products') }}
                </p>
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size:13px;font-weight:500;color:var(--pf-muted);">{{ __('Sort by:') }}</span>
                    <select id="pf-dp-sort" class="pf-sort-select">
                        <option value="3">{{ __('Latest') }}</option>
                        <option value="1">{{ __('Name') }}</option>
                        <option value="4">{{ __('Price: Low–High') }}</option>
                        <option value="5">{{ __('Price: High–Low') }}</option>
                    </select>
                </div>
            </div>

            <div class="row row-cols-2 row-cols-md-3 g-3 pf-dp-grid">
                @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
            </div>

        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
(function($){
    'use strict';

    function pfDpFilter(page){
        var cats = [];
        $('.pf-dp-cat-check:checked').each(function(){ cats.push($(this).data('slug')); });
        var data = {
            category:  cats.join(',') || null,
            tag:       $('.pf-dp-tag.active').data('tag') || null,
            search:    $('#pf-dp-search').val() || null,
            min_price: $('#pf-dp-min').val() || 0,
            max_price: $('#pf-dp-max').val() || 10000,
            sort:      $('#pf-dp-sort').val(),
            page:      page || null,
        };
        Object.keys(data).forEach(function(k){ if(data[k] === null) delete data[k]; });
        $.ajax({
            type: 'GET', url: '{{ theme_digital_shop_filter_url() }}', data: data,
            beforeSend: function(){ $('.loader').show(); },
            success: function(res){
                $('.pf-dp-grid').html(res.grid);
                var p = res.pagination;
                $('.pf-dp-showing').html('{{ __("Showing") }} <span>' + (p.from ?? 0) + '</span> {{ __("of") }} <span>' + (p.total ?? 0) + '</span> {{ __("products") }}');
                $('.loader').hide();
            },
            error: function(){ $('.loader').hide(); }
        });
    }

    window.pfDpFilter = pfDpFilter;

    $(document).on('change', '.pf-dp-cat-check', function(){ pfDpFilter(); });
    $('#pf-dp-sort').on('change', function(){ pfDpFilter(); });

    $(document).on('click', '.pf-dp-tag', function(){
        var active = $(this).hasClass('active');
        $('.pf-dp-tag').removeClass('active');
        if(!active){ $(this).addClass('active'); }
        pfDpFilter();
    });

    $(document).on('keyup', '#pf-dp-search', function(){
        clearTimeout(window._pfDpTimer);
        window._pfDpTimer = setTimeout(function(){ pfDpFilter(); }, 350);
    });

    $('#pf-dp-clear').on('click', function(){
        $('.pf-dp-cat-check').prop('checked', false);
        $('.pf-dp-tag').removeClass('active');
        $('#pf-dp-search').val('');
        $('#pf-dp-min').val(0);
        $('#pf-dp-max').val(10000);
        pfDpFilter();
    });
})(jQuery);
</script>
@endsection
