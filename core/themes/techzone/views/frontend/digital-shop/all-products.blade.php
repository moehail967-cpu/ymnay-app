@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title !!} @endsection
@section('page-title') {!! $page_post->title !!} @endsection

@section('content')
<div class="tz-page-banner">
    <div class="container tz-page-banner-content">
        <h1>{!! $page_post->title !!}</h1>
        <div class="tz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right sep"></i>
            <span class="current">{!! $page_post->title !!}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:40px 0 72px;">
    <div class="row g-4">

        {{-- Sidebar --}}
        <div class="col-lg-3">

            {{-- Search --}}
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;margin-bottom:16px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);margin-bottom:12px;">{{ __('Search') }}</div>
                <div style="display:flex;border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);overflow:hidden;background:var(--tz-mid);">
                    <input type="text" id="tz-dp-search" placeholder="{{ __('Search digital products…') }}"
                           style="flex:1;padding:8px 12px;border:0;background:transparent;color:var(--tz-text);font-size:13px;font-family:var(--tz-font);outline:none;">
                    <button onclick="tzDpFilter()" style="background:var(--tz-blue);border:0;color:#fff;padding:0 14px;cursor:pointer;font-size:16px;">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if($categories->isNotEmpty())
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;margin-bottom:16px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);margin-bottom:12px;">{{ __('Categories') }}</div>
                @foreach($categories as $cat)
                <label style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;cursor:pointer;border-bottom:1px solid var(--tz-border);">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin:0;font-size:13px;color:var(--tz-text);">
                        <input type="checkbox" class="tz-dp-cat-check" data-slug="{{ $cat->slug }}" style="accent-color:var(--tz-blue);">
                        {{ $cat->name }}
                    </label>
                    @if($cat->product_count ?? false)
                        <span style="background:var(--tz-blue-glow);color:var(--tz-blue);font-size:10px;font-weight:700;padding:2px 8px;border-radius:var(--tz-radius-sm);">{{ $cat->product_count }}</span>
                    @endif
                </label>
                @endforeach
            </div>
            @endif

            {{-- Price Range --}}
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;margin-bottom:16px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);margin-bottom:12px;">{{ __('Price Range') }}</div>
                <div style="display:flex;gap:8px;margin-bottom:12px;">
                    <input type="number" id="tz-dp-min" placeholder="{{ __('Min') }}" value="0" min="0"
                           style="width:50%;padding:7px 10px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:12px;font-family:var(--tz-font);outline:none;">
                    <input type="number" id="tz-dp-max" placeholder="{{ __('Max') }}" value="10000" min="0"
                           style="width:50%;padding:7px 10px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:12px;font-family:var(--tz-font);outline:none;">
                </div>
                <button onclick="tzDpFilter()"
                        style="width:100%;padding:9px;background:var(--tz-blue);color:#fff;border:0;border-radius:var(--tz-radius-sm);font-size:12px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:background .2s;"
                        onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                    {{ __('Apply Filter') }}
                </button>
            </div>

            {{-- Tags --}}
            @if(isset($tags) && $tags->isNotEmpty())
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;margin-bottom:16px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);margin-bottom:12px;">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($tags->take(15) as $tag)
                    <button class="tz-dp-tag-filter" data-tag="{{ $tag->tag_name }}"
                            style="padding:4px 12px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);font-size:11px;font-weight:600;color:var(--tz-muted);cursor:pointer;transition:all .2s;">
                        #{{ $tag->tag_name }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <button onclick="tzDpClear()"
                    style="width:100%;padding:9px;background:transparent;color:var(--tz-muted);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);font-size:12px;font-weight:600;cursor:pointer;font-family:var(--tz-font);transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px;"
                    onmouseover="this.style.borderColor='var(--tz-blue)';this.style.color='var(--tz-blue)'" onmouseout="this.style.borderColor='var(--tz-border)';this.style.color='var(--tz-muted)'">
                <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear Filters') }}
            </button>
        </div>

        {{-- Products --}}
        <div class="col-lg-9">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--tz-border);">
                <span class="showing-results" style="font-size:13px;color:var(--tz-muted);">
                    {{ __('Showing') }} <strong style="color:#fff;">{{ $pagination->count() }}</strong>
                    {{ __('of') }} <strong style="color:#fff;">{{ $pagination->total() }}</strong> {{ __('products') }}
                </span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <label style="font-size:12px;color:var(--tz-muted);">{{ __('Sort:') }}</label>
                    <select id="tz-dp-sort"
                            style="padding:7px 12px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:12px;font-family:var(--tz-font);outline:none;">
                        <option value="3">{{ __('Latest') }}</option>
                        <option value="1">{{ __('Name') }}</option>
                        <option value="4">{{ __('Price: Low → High') }}</option>
                        <option value="5">{{ __('Price: High → Low') }}</option>
                    </select>
                </div>
            </div>

            <div class="loader" style="display:none;text-align:center;padding:40px;">
                <div style="width:32px;height:32px;border:3px solid var(--tz-border);border-top-color:var(--tz-blue);border-radius:50%;animation:tz-spin .7s linear infinite;margin:0 auto;"></div>
            </div>

            <div class="row g-3 tz-dp-grid">
                @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<style>
@keyframes tz-spin { to { transform: rotate(360deg); } }
.tz-dp-tag-filter.active { background: var(--tz-blue) !important; color: #fff !important; border-color: var(--tz-blue) !important; }
</style>
<script>
(function($){
    function tzDpFilter(page) {
        var cats = [];
        $('.tz-dp-cat-check:checked').each(function(){ cats.push($(this).data('slug')); });
        var data = {
            category:  cats.join(',') || null,
            tag:       $('.tz-dp-tag-filter.active').data('tag') || null,
            min_price: $('#tz-dp-min').val() || 0,
            max_price: $('#tz-dp-max').val() || 10000,
            sort:      $('#tz-dp-sort').val(),
            search:    $('#tz-dp-search').val() || null,
            page:      page || null,
        };
        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function(){ $('.loader').show(); $('.tz-dp-grid').css('opacity','.4'); },
            success: function(res){
                $('.tz-dp-grid').html(res.grid).css('opacity','1');
                var p = res.pagination;
                $('.showing-results').html('{{ __('Showing') }} <strong style="color:#fff;">'+(p.from||0)+'</strong> {{ __('of') }} <strong style="color:#fff;">'+(p.total||0)+'</strong> {{ __('products') }}');
                $('.loader').hide();
            },
            error: function(){ $('.loader').hide(); $('.tz-dp-grid').css('opacity','1'); }
        });
    }
    function tzDpClear() {
        $('.tz-dp-cat-check').prop('checked',false);
        $('.tz-dp-tag-filter').removeClass('active');
        $('#tz-dp-min').val(0); $('#tz-dp-max').val(10000); $('#tz-dp-search').val('');
        tzDpFilter();
    }
    window.tzDpFilter = tzDpFilter;
    window.tzDpClear  = tzDpClear;
    $(document).on('change','.tz-dp-cat-check',function(){ tzDpFilter(); });
    $('#tz-dp-sort').on('change',function(){ tzDpFilter(); });
    $(document).on('click','.tz-dp-tag-filter',function(){
        var active=$(this).hasClass('active');
        $('.tz-dp-tag-filter').removeClass('active');
        if(!active) $(this).addClass('active');
        tzDpFilter();
    });
    $(document).on('keyup','#tz-dp-search',function(){
        clearTimeout(window._tzDpTimer);
        window._tzDpTimer=setTimeout(tzDpFilter,350);
    });
})(jQuery);
</script>
@endsection
