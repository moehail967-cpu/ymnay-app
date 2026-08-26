@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $page_post->title ?? __('Digital Shop') !!} @endsection
@section('page-title') {!! $page_post->title ?? __('Digital Shop') !!} @endsection

@section('content')

{{-- Page Banner --}}
<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:44px 0 32px;text-align:center;">
    <div class="container">
        <div style="font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;">{{ __('Digital') }}</div>
        <h1 style="font-size:clamp(22px,4vw,36px);font-weight:300;color:var(--ms-dark);margin:0 auto 12px;line-height:1.2;">{!! $page_post->title ?? __('Digital Shop') !!}</h1>
        <div style="display:flex;align-items:center;justify-content:center;gap:8px;font-size:12px;color:var(--ms-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;"
               onmouseover="this.style.color='var(--ms-olive)'" onmouseout="this.style.color='var(--ms-linen-d)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ __('Digital Shop') }}</span>
        </div>
    </div>
</div>

<section style="background:var(--ms-cream);padding:48px 0 72px;">
    <div class="container">
        <div class="row g-4">

            {{-- Sidebar --}}
            <div class="col-lg-3">

                {{-- Search --}}
                <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:20px;margin-bottom:20px;box-shadow:var(--ms-shadow);">
                    <div style="font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:12px;">{{ __('Search') }}</div>
                    <div style="display:flex;border:1px solid var(--ms-border);border-radius:var(--ms-radius);overflow:hidden;">
                        <input type="text" id="ms-dp-search"
                               style="flex:1;padding:9px 14px;border:0;font-size:13px;font-family:inherit;outline:none;color:var(--ms-dark);background:#fff;"
                               placeholder="{{ __('Search…') }}">
                        <button type="button" onclick="msDpFilter()"
                                style="background:var(--ms-dark);border:0;color:#fff;padding:0 14px;cursor:pointer;font-size:16px;transition:background .2s;"
                                onmouseover="this.style.background='var(--ms-charcoal)'"
                                onmouseout="this.style.background='var(--ms-dark)'">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </div>

                {{-- Categories --}}
                @if($categories->isNotEmpty())
                <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:20px;margin-bottom:20px;box-shadow:var(--ms-shadow);">
                    <div style="font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:12px;">{{ __('Categories') }}</div>
                    <ul style="list-style:none;margin:0;padding:0;">
                        @foreach($categories as $cat)
                        <li style="border-bottom:1px dashed var(--ms-border);padding:8px 0;">
                            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;color:var(--ms-charcoal);">
                                <input type="checkbox" class="ms-cat-check" data-slug="{{ $cat->slug }}"
                                       style="width:14px;height:14px;accent-color:var(--ms-olive);">
                                {{ $cat->name }}
                                @if($cat->product_count ?? false)
                                <span style="margin-left:auto;font-size:11px;color:var(--ms-muted);background:var(--ms-warm);padding:1px 8px;border-radius:2px;">{{ $cat->product_count }}</span>
                                @endif
                            </label>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Tags --}}
                @if($tags->isNotEmpty())
                <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:20px;margin-bottom:20px;box-shadow:var(--ms-shadow);">
                    <div style="font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:12px;">{{ __('Tags') }}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                        @foreach($tags->take(15) as $tag)
                        <button class="ms-dp-tag" data-tag="{{ $tag->tag_name }}"
                                style="display:inline-flex;align-items:center;padding:4px 12px;background:var(--ms-warm);border:1px solid var(--ms-border);border-radius:2px;font-size:11px;font-weight:600;letter-spacing:.04em;color:var(--ms-muted);cursor:pointer;transition:all .2s;"
                                onmouseover="if(!this.classList.contains('active')){this.style.background='var(--ms-linen)';this.style.color='#fff';this.style.borderColor='var(--ms-linen)';}"
                                onmouseout="if(!this.classList.contains('active')){this.style.background='var(--ms-warm)';this.style.color='var(--ms-muted)';this.style.borderColor='var(--ms-border)';}">
                            {{ $tag->tag_name }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Price Range --}}
                <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:20px;margin-bottom:20px;box-shadow:var(--ms-shadow);">
                    <div style="font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:12px;">{{ __('Price Range') }}</div>
                    <div style="display:flex;gap:8px;margin-bottom:12px;">
                        <input type="number" id="ms-dp-min" value="0"
                               style="flex:1;padding:8px 12px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:13px;color:var(--ms-dark);outline:none;background:#fff;min-width:0;"
                               placeholder="{{ __('Min') }}">
                        <input type="number" id="ms-dp-max" value="10000"
                               style="flex:1;padding:8px 12px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:13px;color:var(--ms-dark);outline:none;background:#fff;min-width:0;"
                               placeholder="{{ __('Max') }}">
                    </div>
                    <button onclick="msDpFilter()" class="ms-btn-full ms-btn-dark" style="font-size:12px;padding:9px;">
                        {{ __('Apply') }}
                    </button>
                </div>

                {{-- Clear --}}
                <button onclick="msDpClear()"
                        style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;background:transparent;border:1px dashed var(--ms-border);border-radius:var(--ms-radius);padding:10px;font-size:12px;font-weight:600;color:var(--ms-muted);cursor:pointer;transition:all .2s;"
                        onmouseover="this.style.borderColor='var(--ms-linen)';this.style.color='var(--ms-linen-d)'"
                        onmouseout="this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-muted)'">
                    <i class="mdi mdi-close-circle-outline"></i> {{ __('Clear All Filters') }}
                </button>

            </div>

            {{-- Products --}}
            <div class="col-lg-9">
                {{-- Sort Bar --}}
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;padding:12px 16px;background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);">
                    <span class="ms-dp-showing" style="font-size:13px;color:var(--ms-muted);">
                        {{ __('Showing') }} <strong style="color:var(--ms-dark);">{{ $pagination->count() }}</strong>
                        {{ __('of') }} <strong style="color:var(--ms-dark);">{{ $pagination->total() }}</strong>
                        {{ __('products') }}
                    </span>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <label style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);">{{ __('Sort by') }}:</label>
                        <select id="ms-dp-sort"
                                style="padding:7px 12px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:12px;color:var(--ms-dark);background:#fff;outline:none;">
                            <option value="3">{{ __('Latest') }}</option>
                            <option value="1">{{ __('Name') }}</option>
                            <option value="4">{{ __('Price: Low to High') }}</option>
                            <option value="5">{{ __('Price: High to Low') }}</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 ms-dp-grid">
                    @include(include_theme_path('digital-shop.partials.product-partials.grid-products'))
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
(function($){
    'use strict';

    function msDpFilter(page){
        var cats = [];
        $('.ms-cat-check:checked').each(function(){ cats.push($(this).data('slug')); });
        var data = {
            category:  cats.join(',') || null,
            tag:       $('.ms-dp-tag.active').data('tag') || null,
            search:    $('#ms-dp-search').val() || null,
            min_price: $('#ms-dp-min').val() || 0,
            max_price: $('#ms-dp-max').val() || 10000,
            sort:      $('#ms-dp-sort').val(),
            page:      page || null,
        };
        // remove null keys
        Object.keys(data).forEach(function(k){ if(data[k] === null) delete data[k]; });
        $.ajax({
            type: 'GET',
            url: '{{ theme_digital_shop_filter_url() }}',
            data: data,
            beforeSend: function(){ $('.loader').show(); },
            success: function(res){
                $('.ms-dp-grid').html(res.grid);
                var p = res.pagination;
                var from = p.from ?? 0, total = p.total ?? 0;
                $('.ms-dp-showing').html(
                    '{{ __('Showing') }} <strong style="color:var(--ms-dark);">' + from + '</strong> {{ __('of') }} <strong style="color:var(--ms-dark);">' + total + '</strong> {{ __('products') }}'
                );
                $('.loader').hide();
            },
            error: function(){ $('.loader').hide(); }
        });
    }

    function msDpClear(){
        $('.ms-cat-check').prop('checked', false);
        $('.ms-dp-tag').removeClass('active');
        $('#ms-dp-search').val('');
        $('#ms-dp-min').val(0);
        $('#ms-dp-max').val(10000);
        msDpFilter();
    }

    window.msDpFilter = msDpFilter;
    window.msDpClear  = msDpClear;

    $(document).on('change', '.ms-cat-check', function(){ msDpFilter(); });
    $('#ms-dp-sort').on('change', function(){ msDpFilter(); });

    $(document).on('click', '.ms-dp-tag', function(){
        var active = $(this).hasClass('active');
        $('.ms-dp-tag').removeClass('active').css({ background:'var(--ms-warm)', color:'var(--ms-muted)', borderColor:'var(--ms-border)' });
        if(!active){
            $(this).addClass('active').css({ background:'var(--ms-linen)', color:'#fff', borderColor:'var(--ms-linen)' });
        }
        msDpFilter();
    });

    $(document).on('keyup', '#ms-dp-search', function(){
        clearTimeout(window._msDpTimer);
        window._msDpTimer = setTimeout(function(){ msDpFilter(); }, 350);
    });

})(jQuery);
</script>
@endsection
