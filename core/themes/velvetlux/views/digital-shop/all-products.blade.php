@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')
<div style="background:var(--vl-surface);border-bottom:1px solid var(--vl-border);padding:40px 0 28px;">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Digital') }}</div>
        <h2 style="font-size:28px;font-weight:400;color:var(--vl-ivory);margin-bottom:12px;font-family:'Cormorant Garamond',serif;letter-spacing:2px;">{!! $page_post->title !!}</h2>
        <div class="vl-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span>{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:40px 0 80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Search') }}</div>
                <div style="display:flex;border:1px solid var(--vl-border);">
                    <input type="text" id="vl-dp-search" placeholder="{{ __('Search…') }}"
                           style="flex:1;padding:10px 12px;border:0;background:var(--vl-surface);color:var(--vl-ivory);font-size:13px;font-family:inherit;outline:none;">
                    <button onclick="vlDpFilter()" style="background:var(--vl-champagne);border:0;color:var(--vl-dark);padding:0 14px;cursor:pointer;font-size:15px;">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Categories') }}</div>
                <div style="display:flex;flex-direction:column;gap:2px;">
                    @foreach($categories as $cat)
                    <div class="vl-check-item">
                        <label>
                            <input type="checkbox" class="vl-dp-cat-check" data-slug="{{ $cat->slug }}"
                                   style="accent-color:var(--vl-champagne);">
                            {{ $cat->name }}
                        </label>
                        @if($cat->product_count ?? false)
                            <span class="vl-check-count">{{ $cat->product_count }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Authors --}}
            @if($authors->isNotEmpty())
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Authors') }}</div>
                <div style="display:flex;flex-direction:column;gap:4px;">
                    @foreach($authors as $author)
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--vl-muted);cursor:pointer;padding:6px 0;border-bottom:1px solid rgba(58,36,68,.4);">
                        <input type="radio" name="vl_dp_author" class="vl-dp-author-filter" value="{{ $author->slug }}"
                               style="accent-color:var(--vl-champagne);">
                        {{ $author->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Languages --}}
            @if($languages->isNotEmpty())
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Language') }}</div>
                <div style="display:flex;flex-direction:column;gap:4px;">
                    @foreach($languages as $lang)
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--vl-muted);cursor:pointer;padding:6px 0;border-bottom:1px solid rgba(58,36,68,.4);">
                        <input type="radio" name="vl_dp_lang" class="vl-dp-lang-filter" value="{{ $lang->slug }}"
                               style="accent-color:var(--vl-champagne);">
                        {{ $lang->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Price Range --}}
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Price Range') }}</div>
                <div class="vl-price-inputs">
                    <input type="number" id="vl-dp-min" placeholder="{{ __('Min') }}" value="0" min="0">
                    <span>—</span>
                    <input type="number" id="vl-dp-max" placeholder="{{ __('Max') }}" value="10000" min="0">
                </div>
                <button class="vl-btn vl-btn-outline" style="width:100%;justify-content:center;padding:10px;" onclick="vlDpFilter()">
                    {{ __('Apply') }}
                </button>
            </div>

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach($tags->take(15) as $tag)
                    <button class="vl-dp-tag-filter" data-tag="{{ $tag->tag_name }}"
                            style="padding:5px 12px;background:var(--vl-surface);border:1px solid var(--vl-border);font-size:10px;letter-spacing:1px;text-transform:uppercase;color:var(--vl-muted);cursor:pointer;font-family:inherit;transition:all .2s;"
                            onmouseover="this.style.borderColor='var(--vl-champagne)';this.style.color='var(--vl-champagne)'" onmouseout="if(!$(this).hasClass('active')){this.style.borderColor='var(--vl-border)';this.style.color='var(--vl-muted)'}">
                        {{ $tag->tag_name }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <button class="vl-btn vl-btn-outline" style="width:100%;justify-content:center;padding:10px;" onclick="vlDpClear()">
                <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear Filters') }}
            </button>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div class="vl-sort-bar">
                <span class="vl-sort-count showing-results">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <select id="vl-dp-sort" class="vl-sort-select">
                    <option value="3">{{ __('Latest') }}</option>
                    <option value="1">{{ __('Name') }}</option>
                    <option value="4">{{ __('Price: Low to High') }}</option>
                    <option value="5">{{ __('Price: High to Low') }}</option>
                </select>
            </div>

            <div class="row g-3 vl-dp-grid">
                @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    function vlDpFilter(page) {
        var cats = [];
        $('.vl-dp-cat-check:checked').each(function () { cats.push($(this).data('slug')); });

        var data = {
            category:  cats.join(',') || null,
            author:    $('input[name=vl_dp_author]:checked').val() || null,
            language:  $('input[name=vl_dp_lang]:checked').val() || null,
            tag:       $('.vl-dp-tag-filter.active').data('tag') || null,
            min_price: $('#vl-dp-min').val() || 0,
            max_price: $('#vl-dp-max').val() || 10000,
            sort:      $('#vl-dp-sort').val(),
            search:    $('#vl-dp-search').val() || null,
            page:      page || null,
        };

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function () { $('.loader').show(); },
            success: function (res) {
                $('.vl-dp-grid').html(res.grid);
                var p = res.pagination;
                var from = p.from ?? 0, total = p.total ?? 0;
                $('.showing-results').html('{{ __('Showing') }} <strong>' + from + '</strong> {{ __('of') }} <strong>' + total + '</strong> {{ __('products') }}');
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

    function vlDpClear() {
        $('.vl-dp-cat-check').prop('checked', false);
        $('input[name=vl_dp_author]').prop('checked', false);
        $('input[name=vl_dp_lang]').prop('checked', false);
        $('.vl-dp-tag-filter').removeClass('active').css({'borderColor':'var(--vl-border)','color':'var(--vl-muted)'});
        $('#vl-dp-min').val(0);
        $('#vl-dp-max').val(10000);
        $('#vl-dp-search').val('');
        vlDpFilter();
    }

    window.vlDpFilter = vlDpFilter;
    window.vlDpClear  = vlDpClear;

    $(document).on('change', '.vl-dp-cat-check, input[name=vl_dp_author], input[name=vl_dp_lang]', function () { vlDpFilter(); });
    $('#vl-dp-sort').on('change', function () { vlDpFilter(); });

    $(document).on('click', '.vl-dp-tag-filter', function () {
        var active = $(this).hasClass('active');
        $('.vl-dp-tag-filter').removeClass('active').css({'borderColor':'var(--vl-border)','color':'var(--vl-muted)'});
        if (!active) { $(this).addClass('active').css({'borderColor':'var(--vl-champagne)','color':'var(--vl-champagne)'}); }
        vlDpFilter();
    });

    $(document).on('click', '.vl-dp-page-btn', function (e) {
        e.preventDefault();
        vlDpFilter($(this).data('page'));
    });

    $(document).on('keyup', '#vl-dp-search', function () {
        clearTimeout(window._vlDpTimer);
        window._vlDpTimer = setTimeout(vlDpFilter, 350);
    });

})(jQuery);
</script>
@endsection
