@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title ?? __('Digital Shop') !!} @endsection
@section('page-title') {!! $page_post->title ?? __('Digital Shop') !!} @endsection

@section('content')
{{-- Page Banner --}}
<div class="sz-page-banner">
    <div class="container">
        <h1>{!! $page_post->title ?? __('Digital Shop') !!}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{!! $page_post->title ?? __('Digital Shop') !!}</span>
        </div>
    </div>
</div>

{{-- Main Layout --}}
<div style="background:var(--sz-bg);padding:36px 0 72px;">
<div class="container">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">
            <div class="sz-sidebar">
                <div class="sz-sidebar-head"><i class="mdi mdi-filter-outline"></i> {{ __('Filter') }}</div>

                {{-- Search --}}
                <div class="sz-sidebar-block">
                    <div class="sz-sidebar-title">{{ __('Search') }}</div>
                    <div style="display:flex;border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);overflow:hidden;background:#fff;">
                        <input type="text" id="sz-dp-search"
                               style="flex:1;padding:9px 14px;border:0;font-size:13px;font-family:var(--sz-font-body);outline:none;color:var(--sz-dark);background:transparent;"
                               placeholder="{{ __('Search digital products…') }}">
                        <button type="button" onclick="szDpFilter()"
                                style="background:var(--sz-red);border:0;color:#fff;padding:0 14px;cursor:pointer;font-size:18px;transition:background .2s;"
                                onmouseover="this.style.background='var(--sz-navy)'"
                                onmouseout="this.style.background='var(--sz-red)'">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </div>

                {{-- Categories --}}
                @if($categories->isNotEmpty())
                <div class="sz-sidebar-block">
                    <div class="sz-sidebar-title">{{ __('Categories') }}</div>
                    @foreach($categories as $cat)
                    <label class="sz-filter-check">
                        <input type="checkbox" class="sz-dp-cat-check" data-slug="{{ $cat->slug }}">
                        {{ $cat->name }}
                        @if($cat->product_count ?? false)
                            <span class="sz-filter-check-count">{{ $cat->product_count }}</span>
                        @endif
                    </label>
                    @endforeach
                </div>
                @endif

                {{-- Price Range --}}
                <div class="sz-sidebar-block">
                    <div class="sz-sidebar-title">{{ __('Price Range') }}</div>
                    <div class="sz-price-inputs" style="margin-bottom:12px;">
                        <input type="number" id="sz-dp-min" placeholder="{{ __('Min') }}" value="0" min="0">
                        <span style="color:var(--sz-muted);">–</span>
                        <input type="number" id="sz-dp-max" placeholder="{{ __('Max') }}" value="10000" min="0">
                    </div>
                    <button class="sz-btn sz-btn-red sz-btn-sm w-100" onclick="szDpFilter()">
                        <i class="mdi mdi-check"></i> {{ __('Apply') }}
                    </button>
                </div>

                {{-- Tags --}}
                @if($tags->isNotEmpty())
                <div class="sz-sidebar-block">
                    <div class="sz-sidebar-title">{{ __('Tags') }}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                        @foreach($tags->take(15) as $tag)
                        <button class="sz-tag sz-dp-tag" data-tag="{{ $tag->tag_name }}"
                                style="cursor:pointer;border:none;font-family:var(--sz-font-head);font-size:11px;letter-spacing:1px;text-transform:uppercase;transition:all .2s;">
                            {{ $tag->tag_name }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Clear Filters --}}
                <div class="sz-sidebar-block" style="border-bottom:none;">
                    <button id="sz-dp-clear"
                            style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;background:transparent;border:2px dashed var(--sz-border);border-radius:var(--sz-radius-xl);padding:10px;font-family:var(--sz-font-head);font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:var(--sz-muted);cursor:pointer;transition:all .2s;"
                            onmouseover="this.style.borderColor='var(--sz-red)';this.style.color='var(--sz-red)';"
                            onmouseout="this.style.borderColor='var(--sz-border)';this.style.color='var(--sz-muted)';">
                        <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear All Filters') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">

            {{-- Sort Bar --}}
            <div class="sz-sort-bar" style="margin-bottom:20px;">
                <span class="sz-sort-count sz-dp-showing">
                    {{ __('Showing') }} <strong style="color:var(--sz-dark);">{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong style="color:var(--sz-dark);">{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);">{{ __('Sort:') }}</span>
                    <select id="sz-dp-sort" class="sz-sort-select">
                        <option value="3">{{ __('Latest') }}</option>
                        <option value="1">{{ __('Name') }}</option>
                        <option value="4">{{ __('Price: Low to High') }}</option>
                        <option value="5">{{ __('Price: High to Low') }}</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 sz-dp-grid">
                @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
            </div>

        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    function szDpFilter(page) {
        var cats = [];
        $('.sz-dp-cat-check:checked').each(function () { cats.push($(this).data('slug')); });
        var data = {
            category:  cats.join(',') || null,
            tag:       $('.sz-dp-tag.active').data('tag') || null,
            search:    $('#sz-dp-search').val() || null,
            min_price: $('#sz-dp-min').val() || 0,
            max_price: $('#sz-dp-max').val() || 10000,
            sort:      $('#sz-dp-sort').val(),
            page:      page || null,
        };
        // Strip null keys so server doesn't get empty strings
        Object.keys(data).forEach(function (k) { if (data[k] === null) delete data[k]; });

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function () { $('.loader').show(); },
            success: function (res) {
                $('.sz-dp-grid').html(res.grid);
                var p = res.pagination;
                var from  = p.from  ?? 0;
                var total = p.total ?? 0;
                $('.sz-dp-showing').html(
                    '{{ __("Showing") }} <strong style="color:var(--sz-dark);">' + from + '</strong>'
                    + ' {{ __("of") }} <strong style="color:var(--sz-dark);">' + total + '</strong>'
                    + ' {{ __("products") }}'
                );
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.szDpFilter = szDpFilter;

    // Category checkboxes
    $(document).on('change', '.sz-dp-cat-check', function () { szDpFilter(); });

    // Sort select
    $('#sz-dp-sort').on('change', function () { szDpFilter(); });

    // Tag pills — toggle active
    $(document).on('click', '.sz-dp-tag', function () {
        var isActive = $(this).hasClass('active');
        $('.sz-dp-tag').removeClass('active')
            .css({ background: '', color: '', borderColor: '' });
        if (!isActive) {
            $(this).addClass('active')
                .css({ background: 'var(--sz-red)', color: '#fff', borderColor: 'var(--sz-red)' });
        }
        szDpFilter();
    });

    // Search — debounce 350ms
    $(document).on('keyup', '#sz-dp-search', function () {
        clearTimeout(window._szDpTimer);
        window._szDpTimer = setTimeout(function () { szDpFilter(); }, 350);
    });

    // Clear all filters
    $('#sz-dp-clear').on('click', function () {
        $('.sz-dp-cat-check').prop('checked', false);
        $('.sz-dp-tag').removeClass('active').css({ background: '', color: '', borderColor: '' });
        $('#sz-dp-search').val('');
        $('#sz-dp-min').val(0);
        $('#sz-dp-max').val(10000);
        szDpFilter();
    });

})(jQuery);
</script>
@endsection
