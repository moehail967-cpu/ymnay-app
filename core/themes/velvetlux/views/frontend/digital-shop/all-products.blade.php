@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')
<div class="vl-page-header">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Digital') }}</div>
        <h1 style="font-size:32px;font-weight:300;color:var(--vl-ivory);font-family:'Cormorant Garamond',serif;letter-spacing:1px;margin-bottom:12px;">{!! $page_post->title !!}</h1>
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
                           style="flex:1;padding:10px 14px;border:0;background:var(--vl-surface);color:var(--vl-ivory);font-size:13px;font-family:inherit;outline:none;">
                    <button onclick="vlDpFilter()" style="background:var(--vl-champagne);border:0;color:var(--vl-dark);padding:0 14px;cursor:pointer;font-size:15px;">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Categories') }}</div>
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
            @endif

            {{-- Authors --}}
            @if($authors->isNotEmpty())
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Authors') }}</div>
                @foreach($authors as $author)
                <div class="vl-check-item">
                    <label>
                        <input type="checkbox" class="vl-dp-author-check" data-id="{{ $author->id }}"
                               style="accent-color:var(--vl-champagne);">
                        {{ $author->name }}
                    </label>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Languages --}}
            @if($languages->isNotEmpty())
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Languages') }}</div>
                @foreach($languages as $lang)
                <div class="vl-check-item">
                    <label>
                        <input type="checkbox" class="vl-dp-lang-check" data-id="{{ $lang->id }}"
                               style="accent-color:var(--vl-champagne);">
                        {{ $lang->name }}
                    </label>
                </div>
                @endforeach
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
                <button class="vl-btn vl-btn-outline" style="width:100%;justify-content:center;padding:10px;"
                        onclick="vlDpFilter()">{{ __('Apply') }}</button>
            </div>

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div class="vl-sidebar-card">
                <div class="vl-sidebar-title">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($tags as $tag)
                    <span class="vl-dp-tag" data-tag="{{ $tag->tag_name }}"
                          style="padding:4px 12px;border:1px solid var(--vl-border);background:var(--vl-surface);color:var(--vl-muted);font-size:10px;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all .2s;font-family:'Inter',sans-serif;">
                        {{ $tag->tag_name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div class="vl-sort-bar">
                <span class="vl-sort-count vl-dp-showing">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <select class="vl-sort-select" id="vl-dp-sort">
                    <option value="">{{ __('Default') }}</option>
                    <option value="price_low">{{ __('Price: Low to High') }}</option>
                    <option value="price_high">{{ __('Price: High to Low') }}</option>
                    <option value="newest">{{ __('Newest First') }}</option>
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
    function vlDpFilter(page) {
        var cats    = [];
        var authors = [];
        var langs   = [];
        var tags    = [];

        $('.vl-dp-cat-check:checked').each(function () { cats.push($(this).data('slug')); });
        $('.vl-dp-author-check:checked').each(function () { authors.push($(this).data('id')); });
        $('.vl-dp-lang-check:checked').each(function () { langs.push($(this).data('id')); });
        $('.vl-dp-tag.active').each(function () { tags.push($(this).data('tag')); });

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: {
                category: cats.join(',') || null,
                author:   authors.join(',') || null,
                language: langs.join(',') || null,
                tag:      tags.join(',') || null,
                min_price: $('#vl-dp-min').val() || 0,
                max_price: $('#vl-dp-max').val() || 10000,
                sort:     $('#vl-dp-sort').val() || null,
                search:   $('#vl-dp-search').val() || null,
                page:     page || 1,
            },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.vl-dp-grid').html(data.grid);
                var p = data.pagination;
                $('.vl-dp-showing').html('{{ __('Showing') }} <strong>' + (p.to ?? 0) + '</strong> {{ __('of') }} <strong>' + (p.total ?? 0) + '</strong> {{ __('products') }}');
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

    window.vlDpFilter = vlDpFilter;

    $(document).on('change', '.vl-dp-cat-check, .vl-dp-author-check, .vl-dp-lang-check', function () { vlDpFilter(); });
    $('#vl-dp-sort').on('change', function () { vlDpFilter(); });
    $(document).on('click', '.vl-dp-tag', function () {
        $(this).toggleClass('active');
        if ($(this).hasClass('active')) {
            $(this).css({'background':'var(--vl-champagne)','color':'var(--vl-dark)','border-color':'var(--vl-champagne)'});
        } else {
            $(this).css({'background':'var(--vl-surface)','color':'var(--vl-muted)','border-color':'var(--vl-border)'});
        }
        vlDpFilter();
    });
})(jQuery);
</script>
@endsection
