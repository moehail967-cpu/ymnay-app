@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')
{{-- Banner --}}
<div style="background:var(--tr-bark);padding:32px 0;">
    <div class="container">
        <h1 style="font-size:26px;font-weight:800;color:#fff;margin-bottom:8px;">{!! $page_post->title !!}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
            <a href="{{ theme_home_url() }}" style="color:rgba(255,255,255,.7);text-decoration:none;">{{ __('Home') }}</a>
            <span style="color:rgba(255,255,255,.45);"><i class="mdi mdi-chevron-right"></i></span>
            <span style="color:var(--tr-sand);">{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:36px 0 72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:18px;margin-bottom:16px;box-shadow:var(--tr-shadow);">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--tr-stone);margin-bottom:10px;">{{ __('Search') }}</div>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="tr-dp-search"
                           style="flex:1;padding:9px 12px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:13px;outline:none;"
                           placeholder="{{ __('Search…') }}"
                           onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                    <button onclick="tcDpFilter()"
                            style="padding:9px 13px;background:var(--tr-olive);color:#fff;border:none;border-radius:var(--tr-radius);cursor:pointer;">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:18px;margin-bottom:16px;box-shadow:var(--tr-shadow);">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--tr-stone);margin-bottom:10px;">{{ __('Categories') }}</div>
                <div style="display:flex;flex-direction:column;gap:2px;">
                    @foreach($categories as $cat)
                    <label style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;padding:5px 0;font-size:13px;color:var(--tr-bark);">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin:0;">
                            <input type="checkbox" class="tr-dp-cat-check" data-slug="{{ $cat->slug }}"
                                   style="accent-color:var(--tr-olive);"> {{ $cat->name }}
                        </label>
                        @if($cat->product_count ?? false)
                            <span style="background:var(--tr-cream);color:var(--tr-stone);font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;">{{ $cat->product_count }}</span>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Authors --}}
            @if($authors->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:18px;margin-bottom:16px;box-shadow:var(--tr-shadow);">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--tr-stone);margin-bottom:10px;">{{ __('Authors') }}</div>
                <div style="display:flex;flex-direction:column;gap:2px;">
                    @foreach($authors as $author)
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:5px 0;font-size:13px;color:var(--tr-bark);">
                        <input type="radio" name="tr_dp_author" class="tr-dp-author-filter" value="{{ $author->slug }}"
                               style="accent-color:var(--tr-olive);"> {{ $author->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Languages --}}
            @if($languages->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:18px;margin-bottom:16px;box-shadow:var(--tr-shadow);">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--tr-stone);margin-bottom:10px;">{{ __('Language') }}</div>
                <div style="display:flex;flex-direction:column;gap:2px;">
                    @foreach($languages as $lang)
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:5px 0;font-size:13px;color:var(--tr-bark);">
                        <input type="radio" name="tr_dp_lang" class="tr-dp-lang-filter" value="{{ $lang->slug }}"
                               style="accent-color:var(--tr-olive);"> {{ $lang->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Price Range --}}
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:18px;margin-bottom:16px;box-shadow:var(--tr-shadow);">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--tr-stone);margin-bottom:10px;">{{ __('Price Range') }}</div>
                <div style="display:flex;gap:8px;margin-bottom:10px;">
                    <input type="number" id="tr-dp-min" placeholder="{{ __('Min') }}" value="0" min="0"
                           style="flex:1;padding:8px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:13px;outline:none;"
                           onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                    <input type="number" id="tr-dp-max" placeholder="{{ __('Max') }}" value="10000" min="0"
                           style="flex:1;padding:8px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:13px;outline:none;"
                           onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                </div>
                <button onclick="tcDpFilter()"
                        style="width:100%;padding:9px;background:var(--tr-olive);color:#fff;border:none;border-radius:var(--tr-radius);font-size:13px;font-weight:700;cursor:pointer;">
                    {{ __('Apply') }}
                </button>
            </div>

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:18px;margin-bottom:16px;box-shadow:var(--tr-shadow);">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--tr-stone);margin-bottom:10px;">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($tags->take(15) as $tag)
                        <button class="tr-dp-tag-filter" data-tag="{{ $tag->tag_name }}"
                                style="padding:4px 12px;border:1.5px solid var(--tr-border);border-radius:20px;background:#fff;color:var(--tr-stone);font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;">
                            {{ $tag->tag_name }}
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <button onclick="tcDpClear()"
                    style="width:100%;padding:9px;border:1.5px solid var(--tr-olive);background:transparent;color:var(--tr-olive);border-radius:var(--tr-radius);font-size:13px;font-weight:700;cursor:pointer;">
                <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear Filters') }}
            </button>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
                <span class="tr-dp-showing" style="font-size:13px;color:var(--tr-stone);">
                    {{ __('Showing') }} <strong>{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong>{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <label style="font-size:13px;font-weight:600;color:var(--tr-stone);">{{ __('Sort:') }}</label>
                    <select id="tr-dp-sort"
                            style="padding:8px 12px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:13px;outline:none;background:#fff;cursor:pointer;">
                        <option value="3">{{ __('Latest') }}</option>
                        <option value="1">{{ __('Name') }}</option>
                        <option value="4">{{ __('Price: Low → High') }}</option>
                        <option value="5">{{ __('Price: High → Low') }}</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 tr-dp-grid">
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

    function tcDpFilter(page) {
        var cats = [];
        $('.tr-dp-cat-check:checked').each(function () { cats.push($(this).data('slug')); });

        var data = {
            category:  cats.join(',') || null,
            author:    $('input[name=tr_dp_author]:checked').val() || null,
            language:  $('input[name=tr_dp_lang]:checked').val() || null,
            tag:       $('.tr-dp-tag-filter.active').data('tag') || null,
            min_price: $('#tr-dp-min').val() || 0,
            max_price: $('#tr-dp-max').val() || 10000,
            sort:      $('#tr-dp-sort').val(),
            search:    $('#tr-dp-search').val() || null,
            page:      page || null,
        };

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function () { $('.loader').show(); },
            success: function (res) {
                $('.tr-dp-grid').html(res.grid);
                var p = res.pagination;
                var from = p.from ?? 0, total = p.total ?? 0;
                $('.tr-dp-showing').html('{{ __('Showing') }} <strong>' + from + '</strong> {{ __('of') }} <strong>' + total + '</strong> {{ __('products') }}');
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); }
        });
    }

    function tcDpClear() {
        $('.tr-dp-cat-check').prop('checked', false);
        $('input[name=tr_dp_author]').prop('checked', false);
        $('input[name=tr_dp_lang]').prop('checked', false);
        $('.tr-dp-tag-filter').removeClass('active');
        $('#tr-dp-min').val(0);
        $('#tr-dp-max').val(10000);
        $('#tr-dp-search').val('');
        tcDpFilter();
    }

    window.tcDpFilter = tcDpFilter;
    window.tcDpClear  = tcDpClear;

    $(document).on('change', '.tr-dp-cat-check, input[name=tr_dp_author], input[name=tr_dp_lang]', function () { tcDpFilter(); });
    $('#tr-dp-sort').on('change', function () { tcDpFilter(); });

    $(document).on('click', '.tr-dp-tag-filter', function () {
        var active = $(this).hasClass('active');
        $('.tr-dp-tag-filter').removeClass('active').css({'background':'#fff','color':'var(--tr-stone)','border-color':'var(--tr-border)'});
        if (!active) {
            $(this).addClass('active').css({'background':'var(--tr-olive)','color':'#fff','border-color':'var(--tr-olive)'});
        }
        tcDpFilter();
    });

    $(document).on('click', '.tr-dp-page-btn', function (e) {
        e.preventDefault();
        tcDpFilter($(this).data('page'));
    });

    $(document).on('keyup', '#tr-dp-search', function () {
        clearTimeout(window._tcDpTimer);
        window._tcDpTimer = setTimeout(tcDpFilter, 350);
    });

})(jQuery);
</script>
@endsection
