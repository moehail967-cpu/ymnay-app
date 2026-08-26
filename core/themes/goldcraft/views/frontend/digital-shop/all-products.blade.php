@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection

@section('content')

<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:13px;font-weight:400;letter-spacing:4px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{!! $page_post->title !!}</h2>
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <span>{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:12px;">{{ __('Search') }}</div>
                <div style="display:flex;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;">
                    <input type="text" id="gc-dp-search" placeholder="{{ __('Search products…') }}"
                           style="flex:1;padding:8px 12px;border:0;font-size:13px;font-family:Georgia,serif;outline:none;font-style:italic;color:var(--gc-dark);">
                    <button onclick="gcDpFilter()" style="background:var(--gc-rose);border:0;color:#fff;padding:0 14px;cursor:pointer;font-size:16px;transition:background .2s;"
                            onmouseover="this.style.background='var(--gc-dark)'" onmouseout="this.style.background='var(--gc-rose)'">
                        <i class="las la-search"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:12px;">{{ __('Categories') }}</div>
                <div style="display:flex;flex-direction:column;gap:4px;">
                    @foreach($categories as $cat)
                    <label style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;cursor:pointer;font-size:13px;color:var(--gc-dark);border-bottom:1px dashed var(--gc-border);">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin:0;font-weight:400;font-style:italic;font-family:Georgia,serif;">
                            <input type="checkbox" class="gc-dp-cat-check" data-slug="{{ $cat->slug }}"
                                   style="accent-color:var(--gc-rose);"> {{ $cat->name }}
                        </label>
                        @if($cat->product_count ?? false)
                            <span style="background:var(--gc-warm);color:var(--gc-rose);font-size:10px;padding:2px 8px;border-radius:50px;">{{ $cat->product_count }}</span>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Authors --}}
            @if($authors->isNotEmpty())
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:12px;">{{ __('Authors') }}</div>
                <div style="display:flex;flex-direction:column;gap:4px;">
                    @foreach($authors as $author)
                    <label style="display:flex;align-items:center;gap:8px;padding:6px 0;cursor:pointer;font-size:13px;color:var(--gc-dark);border-bottom:1px dashed var(--gc-border);font-style:italic;font-family:Georgia,serif;">
                        <input type="radio" name="gc_dp_author" value="{{ $author->slug }}"
                               style="accent-color:var(--gc-rose);"> {{ $author->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Languages --}}
            @if($languages->isNotEmpty())
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:12px;">{{ __('Language') }}</div>
                <div style="display:flex;flex-direction:column;gap:4px;">
                    @foreach($languages as $lang)
                    <label style="display:flex;align-items:center;gap:8px;padding:6px 0;cursor:pointer;font-size:13px;color:var(--gc-dark);border-bottom:1px dashed var(--gc-border);font-style:italic;font-family:Georgia,serif;">
                        <input type="radio" name="gc_dp_lang" value="{{ $lang->slug }}"
                               style="accent-color:var(--gc-rose);"> {{ $lang->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Price Range --}}
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:12px;">{{ __('Price Range') }}</div>
                <div style="display:flex;gap:8px;margin-bottom:12px;">
                    <input type="number" id="gc-dp-min" placeholder="{{ __('Min') }}" value="0" min="0"
                           style="width:50%;padding:7px 10px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:12px;font-family:Georgia,serif;outline:none;color:var(--gc-dark);"
                           onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                    <input type="number" id="gc-dp-max" placeholder="{{ __('Max') }}" value="10000" min="0"
                           style="width:50%;padding:7px 10px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:12px;font-family:Georgia,serif;outline:none;color:var(--gc-dark);"
                           onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                </div>
                <button onclick="gcDpFilter()" class="gc-btn gc-btn-primary" style="width:100%;justify-content:center;font-size:11px;">
                    {{ __('Apply') }}
                </button>
            </div>

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:12px;">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($tags->take(15) as $tag)
                    <button class="gc-dp-tag-filter"
                            data-tag="{{ $tag->tag_name }}"
                            style="padding:4px 12px;background:var(--gc-warm);border:1.5px solid var(--gc-border);border-radius:50px;font-size:11px;color:var(--gc-muted);cursor:pointer;transition:all .2s;font-style:italic;"
                            onmouseover="if(!this.classList.contains('active')){this.style.background='var(--gc-rose)';this.style.color='#fff';this.style.borderColor='var(--gc-rose)';}"
                            onmouseout="if(!this.classList.contains('active')){this.style.background='var(--gc-warm)';this.style.color='var(--gc-muted)';this.style.borderColor='var(--gc-border)';}">
                        #{{ $tag->tag_name }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <button onclick="gcDpClear()" class="gc-btn gc-btn-ghost" style="width:100%;justify-content:center;font-size:11px;">
                <i class="las la-times-circle"></i> {{ __('Clear Filters') }}
            </button>

        </div>

        {{-- Products area --}}
        <div class="col-lg-9">

            {{-- Sort bar --}}
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid var(--gc-border);">
                <span class="showing-results" style="font-size:13px;color:var(--gc-muted);font-style:italic;">
                    {{ __('Showing') }} <strong style="color:var(--gc-dark);">{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong style="color:var(--gc-dark);">{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <label style="font-size:12px;color:var(--gc-muted);font-style:italic;">{{ __('Sort:') }}</label>
                    <select id="gc-dp-sort"
                            style="padding:7px 12px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:12px;font-family:Georgia,serif;color:var(--gc-dark);outline:none;background:#fff;cursor:pointer;"
                            onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                        <option value="3">{{ __('Latest') }}</option>
                        <option value="1">{{ __('Name') }}</option>
                        <option value="4">{{ __('Price: Low → High') }}</option>
                        <option value="5">{{ __('Price: High → Low') }}</option>
                    </select>
                </div>
            </div>

            {{-- Loader --}}
            <div class="loader" style="display:none;text-align:center;padding:40px;">
                <div style="width:36px;height:36px;border:3px solid var(--gc-warm);border-top-color:var(--gc-rose);border-radius:50%;animation:gc-spin .7s linear infinite;margin:0 auto;"></div>
            </div>

            {{-- Grid --}}
            <div class="row g-3 gc-dp-grid">
                @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
            </div>

        </div>
    </div>
</div>

<style>
@keyframes gc-spin { to { transform: rotate(360deg); } }
.gc-dp-tag-filter.active { background: var(--gc-rose) !important; color: #fff !important; border-color: var(--gc-rose) !important; }
</style>

@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    function gcDpFilter(page) {
        var cats = [];
        $('.gc-dp-cat-check:checked').each(function () { cats.push($(this).data('slug')); });

        var data = {
            category:  cats.join(',') || null,
            author:    $('input[name=gc_dp_author]:checked').val() || null,
            language:  $('input[name=gc_dp_lang]:checked').val() || null,
            tag:       $('.gc-dp-tag-filter.active').data('tag') || null,
            min_price: $('#gc-dp-min').val() || 0,
            max_price: $('#gc-dp-max').val() || 10000,
            sort:      $('#gc-dp-sort').val(),
            search:    $('#gc-dp-search').val() || null,
            page:      page || null,
        };

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function () { $('.loader').show(); $('.gc-dp-grid').css('opacity', '.4'); },
            success: function (res) {
                $('.gc-dp-grid').html(res.grid).css('opacity', '1');
                var p = res.pagination;
                var from = p.from ?? 0, total = p.total ?? 0;
                $('.showing-results').html('{{ __('Showing') }} <strong style="color:var(--gc-dark);">' + from + '</strong> {{ __('of') }} <strong style="color:var(--gc-dark);">' + total + '</strong> {{ __('products') }}');
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); $('.gc-dp-grid').css('opacity', '1'); }
        });
    }

    function gcDpClear() {
        $('.gc-dp-cat-check').prop('checked', false);
        $('input[name=gc_dp_author]').prop('checked', false);
        $('input[name=gc_dp_lang]').prop('checked', false);
        $('.gc-dp-tag-filter').removeClass('active').each(function () {
            $(this).css({ background: 'var(--gc-warm)', color: 'var(--gc-muted)', borderColor: 'var(--gc-border)' });
        });
        $('#gc-dp-min').val(0);
        $('#gc-dp-max').val(10000);
        $('#gc-dp-search').val('');
        gcDpFilter();
    }

    window.gcDpFilter = gcDpFilter;
    window.gcDpClear  = gcDpClear;

    $(document).on('change', '.gc-dp-cat-check, input[name=gc_dp_author], input[name=gc_dp_lang]', function () { gcDpFilter(); });
    $('#gc-dp-sort').on('change', function () { gcDpFilter(); });

    $(document).on('click', '.gc-dp-tag-filter', function () {
        var active = $(this).hasClass('active');
        $('.gc-dp-tag-filter').removeClass('active').each(function () {
            $(this).css({ background: 'var(--gc-warm)', color: 'var(--gc-muted)', borderColor: 'var(--gc-border)' });
        });
        if (!active) {
            $(this).addClass('active').css({ background: 'var(--gc-rose)', color: '#fff', borderColor: 'var(--gc-rose)' });
        }
        gcDpFilter();
    });

    $(document).on('click', '.gc-dp-page-btn', function (e) {
        e.preventDefault();
        gcDpFilter($(this).data('page'));
    });

    $(document).on('keyup', '#gc-dp-search', function () {
        clearTimeout(window._gcDpTimer);
        window._gcDpTimer = setTimeout(gcDpFilter, 350);
    });

})(jQuery);
</script>
@endsection
