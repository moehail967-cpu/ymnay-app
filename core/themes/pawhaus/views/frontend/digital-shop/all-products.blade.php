@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title ?? __('Digital Shop') !!} @endsection
@section('page-title') {!! $page_post->title ?? __('Digital Shop') !!} @endsection

@section('content')
<div class="ph-page-banner">
    <div class="container">
        <h1>{!! $page_post->title ?? __('Digital Shop') !!}</h1>
        <div class="ph-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Digital Shop') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:36px 0 72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Search') }}</div>
                <div style="display:flex;border:2px solid var(--ph-border);border-radius:var(--ph-radius-xl);overflow:hidden;">
                    <input type="text" id="ph-dp-search" class="ph-price-input" style="border:0;border-radius:0;background:#fff;" placeholder="{{ __('Search…') }}">
                    <button onclick="phDpFilter()" class="ph-btn ph-btn-terra ph-btn-sm" style="border-radius:0;white-space:nowrap;border:0;">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Categories') }}</div>
                @foreach($categories as $cat)
                <label class="ph-filter-item">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                        <input type="checkbox" class="ph-dp-cat-check" data-slug="{{ $cat->slug }}" style="accent-color:var(--ph-terra);width:15px;height:15px;">
                        {{ $cat->name }}
                    </label>
                    @if($cat->product_count ?? false)
                        <span class="ph-filter-count">{{ $cat->product_count }}</span>
                    @endif
                </label>
                @endforeach
            </div>
            @endif

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags->take(15) as $tag)
                    <button class="ph-tag ph-dp-tag" data-tag="{{ $tag->tag_name }}">{{ $tag->tag_name }}</button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Price Range') }}</div>
                <div class="ph-price-inputs">
                    <input type="number" id="ph-dp-min" class="ph-price-input" placeholder="{{ __('Min') }}" value="0" min="0">
                    <input type="number" id="ph-dp-max" class="ph-price-input" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button onclick="phDpFilter()" class="ph-btn ph-btn-terra ph-btn-sm mt-3 w-100 justify-content-center">
                    {{ __('Apply') }}
                </button>
            </div>

            <button onclick="phDpClear()" class="ph-btn ph-btn-ghost w-100 justify-content-center">
                <i class="las la-times-circle"></i> {{ __('Clear Filters') }}
            </button>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div class="ph-sort-bar">
                <span class="ph-dp-showing" style="font-size:13px;color:var(--ph-muted);">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong> {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <div class="d-flex align-items-center gap-3">
                    <select class="ph-select" id="ph-dp-sort">
                        <option value="3">{{ __('Latest') }}</option>
                        <option value="1">{{ __('Name') }}</option>
                        <option value="4">{{ __('Price: Low to High') }}</option>
                        <option value="5">{{ __('Price: High to Low') }}</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 ph-dp-grid">
                @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
(function($){
    'use strict';

    function phDpFilter(page){
        var cats = [];
        $('.ph-dp-cat-check:checked').each(function(){ cats.push($(this).data('slug')); });
        var data = {
            category:  cats.join(',') || null,
            tag:       $('.ph-dp-tag.active').data('tag') || null,
            search:    $('#ph-dp-search').val() || null,
            min_price: $('#ph-dp-min').val() || 0,
            max_price: $('#ph-dp-max').val() || 10000,
            sort:      $('#ph-dp-sort').val(),
            page:      page || null,
        };
        Object.keys(data).forEach(function(k){ if(data[k] === null) delete data[k]; });
        $.ajax({
            type: 'GET', url: '{{ theme_digital_shop_filter_url() }}', data: data,
            beforeSend: function(){ $('.loader').show(); },
            success: function(res){
                $('.ph-dp-grid').html(res.grid);
                var p = res.pagination;
                $('.ph-dp-showing').html('{{ __("Showing") }} <strong>' + (p.from ?? 0) + '</strong> {{ __("of") }} <strong>' + (p.total ?? 0) + '</strong> {{ __("products") }}');
                $('.loader').hide();
            },
            error: function(){ $('.loader').hide(); }
        });
    }

    function phDpClear(){
        $('.ph-dp-cat-check').prop('checked', false);
        $('.ph-dp-tag').removeClass('active').css({ background: 'var(--ph-terra-light)', color: 'var(--ph-terra)' });
        $('#ph-dp-search').val('');
        $('#ph-dp-min').val(0);
        $('#ph-dp-max').val(10000);
        phDpFilter();
    }

    window.phDpFilter = phDpFilter;
    window.phDpClear  = phDpClear;

    $(document).on('change', '.ph-dp-cat-check', function(){ phDpFilter(); });
    $('#ph-dp-sort').on('change', function(){ phDpFilter(); });

    $(document).on('click', '.ph-dp-tag', function(){
        var active = $(this).hasClass('active');
        $('.ph-dp-tag').removeClass('active').css({ background: 'var(--ph-terra-light)', color: 'var(--ph-terra)' });
        if(!active){
            $(this).addClass('active').css({ background: 'var(--ph-terra)', color: '#fff' });
        }
        phDpFilter();
    });

    $(document).on('keyup', '#ph-dp-search', function(){
        clearTimeout(window._phDpTimer);
        window._phDpTimer = setTimeout(function(){ phDpFilter(); }, 350);
    });
})(jQuery);
</script>
@endsection
