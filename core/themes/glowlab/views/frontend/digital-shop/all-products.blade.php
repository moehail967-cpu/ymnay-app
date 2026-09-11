@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')

{{-- Page banner --}}
<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:300;color:var(--gl-dark);margin-bottom:8px;letter-spacing:-.3px;">{!! $page_post->title !!}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span>{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:80px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:12px;">{{ __('Search') }}</div>
                <div style="display:flex;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;">
                    <input type="text" id="gl-dp-search" placeholder="{{ __('Search products…') }}"
                           style="flex:1;padding:8px 12px;border:0;font-size:13px;font-family:inherit;outline:none;">
                    <button onclick="glDpFilter()" style="background:var(--gl-dark);border:0;color:#fff;padding:0 14px;cursor:pointer;font-size:16px;transition:background .2s;"
                            onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:12px;">{{ __('Categories') }}</div>
                <div style="display:flex;flex-direction:column;gap:4px;">
                    @foreach($categories as $cat)
                    <label style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;cursor:pointer;font-size:13px;color:var(--gl-dark);border-bottom:1px dashed var(--gl-border);">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin:0;font-weight:400;">
                            <input type="checkbox" class="gl-dp-cat-check" data-slug="{{ $cat->slug }}"
                                   style="accent-color:var(--gl-dark);"> {{ $cat->name }}
                        </label>
                        @if($cat->product_count ?? false)
                            <span style="background:var(--gl-gold-pale);color:var(--gl-gold);font-size:10px;font-weight:700;padding:2px 8px;border-radius:50px;">{{ $cat->product_count }}</span>
                        @endif
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Authors --}}
            @if($authors->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:12px;">{{ __('Authors') }}</div>
                <div style="display:flex;flex-direction:column;gap:4px;">
                    @foreach($authors as $author)
                    <label style="display:flex;align-items:center;gap:8px;padding:6px 0;cursor:pointer;font-size:13px;color:var(--gl-dark);border-bottom:1px dashed var(--gl-border);">
                        <input type="radio" name="gl_dp_author" value="{{ $author->slug }}"
                               style="accent-color:var(--gl-dark);"> {{ $author->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Languages --}}
            @if($languages->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:12px;">{{ __('Language') }}</div>
                <div style="display:flex;flex-direction:column;gap:4px;">
                    @foreach($languages as $lang)
                    <label style="display:flex;align-items:center;gap:8px;padding:6px 0;cursor:pointer;font-size:13px;color:var(--gl-dark);border-bottom:1px dashed var(--gl-border);">
                        <input type="radio" name="gl_dp_lang" value="{{ $lang->slug }}"
                               style="accent-color:var(--gl-dark);"> {{ $lang->name }}
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Price Range --}}
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:12px;">{{ __('Price Range') }}</div>
                <div style="display:flex;gap:8px;margin-bottom:12px;">
                    <input type="number" id="gl-dp-min" placeholder="{{ __('Min') }}" value="0" min="0"
                           style="width:50%;padding:7px 10px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:12px;font-family:inherit;outline:none;">
                    <input type="number" id="gl-dp-max" placeholder="{{ __('Max') }}" value="10000" min="0"
                           style="width:50%;padding:7px 10px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:12px;font-family:inherit;outline:none;">
                </div>
                <button onclick="glDpFilter()"
                        style="width:100%;padding:8px;background:var(--gl-dark);color:#fff;border:0;border-radius:var(--gl-radius);font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                    {{ __('Apply') }}
                </button>
            </div>

            {{-- Tags --}}
            @if($tags->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;margin-bottom:16px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:12px;">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($tags->take(15) as $tag)
                    <button class="gl-dp-tag-filter"
                            data-tag="{{ $tag->tag_name }}"
                            style="padding:4px 12px;background:var(--gl-gold-pale);border:1.5px solid var(--gl-border);border-radius:50px;font-size:11px;font-weight:600;color:var(--gl-muted);cursor:pointer;transition:all .2s;"
                            onmouseover="if(!this.classList.contains('active')){this.style.background='var(--gl-gold)';this.style.color='#fff';this.style.borderColor='var(--gl-gold)';}"
                            onmouseout="if(!this.classList.contains('active')){this.style.background='var(--gl-gold-pale)';this.style.color='var(--gl-muted)';this.style.borderColor='var(--gl-border)';}">
                        #{{ $tag->tag_name }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <button onclick="glDpClear()"
                    style="width:100%;padding:9px;background:#fff;color:var(--gl-muted);border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px;"
                    onmouseover="this.style.borderColor='var(--gl-dark)';this.style.color='var(--gl-dark)'"
                    onmouseout="this.style.borderColor='var(--gl-border)';this.style.color='var(--gl-muted)'">
                <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear Filters') }}
            </button>

        </div>

        {{-- Products area --}}
        <div class="col-lg-9">

            {{-- Sort bar --}}
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid var(--gl-border);">
                <span class="showing-results" style="font-size:13px;color:var(--gl-muted);">
                    {{ __('Showing') }} <strong style="color:var(--gl-dark);">{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong style="color:var(--gl-dark);">{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <label style="font-size:12px;font-weight:600;color:var(--gl-muted);">{{ __('Sort:') }}</label>
                    <select id="gl-dp-sort"
                            style="padding:7px 12px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:12px;font-family:inherit;color:var(--gl-dark);outline:none;background:#fff;">
                        <option value="3">{{ __('Latest') }}</option>
                        <option value="1">{{ __('Name') }}</option>
                        <option value="4">{{ __('Price: Low → High') }}</option>
                        <option value="5">{{ __('Price: High → Low') }}</option>
                    </select>
                </div>
            </div>

            {{-- Loader --}}
            <div class="loader" style="display:none;text-align:center;padding:40px;">
                <div style="width:36px;height:36px;border:3px solid var(--gl-gold-pale);border-top-color:var(--gl-gold);border-radius:50%;animation:gl-spin .7s linear infinite;margin:0 auto;"></div>
            </div>

            {{-- Grid --}}
            <div class="row g-3 gl-dp-grid">
                @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
            </div>

        </div>
    </div>
</div>

<style>
@keyframes gl-spin { to { transform: rotate(360deg); } }
.gl-dp-tag-filter.active { background: var(--gl-dark) !important; color: #fff !important; border-color: var(--gl-dark) !important; }
</style>

@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    function glDpFilter(page) {
        var cats = [];
        $('.gl-dp-cat-check:checked').each(function () { cats.push($(this).data('slug')); });

        var data = {
            category:  cats.join(',') || null,
            author:    $('input[name=gl_dp_author]:checked').val() || null,
            language:  $('input[name=gl_dp_lang]:checked').val() || null,
            tag:       $('.gl-dp-tag-filter.active').data('tag') || null,
            min_price: $('#gl-dp-min').val() || 0,
            max_price: $('#gl-dp-max').val() || 10000,
            sort:      $('#gl-dp-sort').val(),
            search:    $('#gl-dp-search').val() || null,
            page:      page || null,
        };

        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function () { $('.loader').show(); $('.gl-dp-grid').css('opacity', '.4'); },
            success: function (res) {
                $('.gl-dp-grid').html(res.grid).css('opacity', '1');
                var p = res.pagination;
                var from = p.from ?? 0, total = p.total ?? 0;
                $('.showing-results').html('{{ __('Showing') }} <strong style="color:var(--gl-dark);">' + from + '</strong> {{ __('of') }} <strong style="color:var(--gl-dark);">' + total + '</strong> {{ __('products') }}');
                $('.loader').hide();
            },
            error: function () { $('.loader').hide(); $('.gl-dp-grid').css('opacity', '1'); }
        });
    }

    function glDpClear() {
        $('.gl-dp-cat-check').prop('checked', false);
        $('input[name=gl_dp_author]').prop('checked', false);
        $('input[name=gl_dp_lang]').prop('checked', false);
        $('.gl-dp-tag-filter').removeClass('active').each(function () {
            $(this).css({ background: 'var(--gl-gold-pale)', color: 'var(--gl-muted)', borderColor: 'var(--gl-border)' });
        });
        $('#gl-dp-min').val(0);
        $('#gl-dp-max').val(10000);
        $('#gl-dp-search').val('');
        glDpFilter();
    }

    window.glDpFilter = glDpFilter;
    window.glDpClear  = glDpClear;

    $(document).on('change', '.gl-dp-cat-check, input[name=gl_dp_author], input[name=gl_dp_lang]', function () { glDpFilter(); });
    $('#gl-dp-sort').on('change', function () { glDpFilter(); });

    $(document).on('click', '.gl-dp-tag-filter', function () {
        var active = $(this).hasClass('active');
        $('.gl-dp-tag-filter').removeClass('active').each(function () {
            $(this).css({ background: 'var(--gl-gold-pale)', color: 'var(--gl-muted)', borderColor: 'var(--gl-border)' });
        });
        if (!active) {
            $(this).addClass('active').css({ background: 'var(--gl-dark)', color: '#fff', borderColor: 'var(--gl-dark)' });
        }
        glDpFilter();
    });

    $(document).on('click', '.gl-dp-page-btn', function (e) {
        e.preventDefault();
        glDpFilter($(this).data('page'));
    });

    $(document).on('keyup', '#gl-dp-search', function () {
        clearTimeout(window._glDpTimer);
        window._glDpTimer = setTimeout(glDpFilter, 350);
    });

})(jQuery);
</script>
@endsection
