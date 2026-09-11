{{-- Glowlab: Dynamic theme color --}}
@php
    $gl_primary       = get_static_option('main_color_one_glowlab') ?: '#1C1410';
    $gl_primary_hover = get_static_option('main_color_two_glowlab') ?: '#2e2e2e';
@endphp
<style>:root{--gl-primary:{{ $gl_primary }};--gl-primary-hover:{{ $gl_primary_hover }};}</style>

{{-- Glowlab: Navbar --}}
@if(get_static_option('site_announcement_text'))
<div class="gl-promo-bar">
    <div class="container">
        <span>{!! get_static_option('site_announcement_text') !!}</span>
    </div>
</div>
@endif

<nav class="gl-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-4">

            {{-- Logo --}}
            <div class="gl-navbar-logo">
                {!! theme_logo_html('gl-navbar-brand-link', 'gl-navbar-logo-img', 'gl-navbar-brand') !!}
            </div>

            {{-- Nav links (desktop) --}}
            <div class="gl-nav-links d-none d-lg-flex">
                {!! theme_nav_menu(null, 'gl-nav-links', 'gl-nav-link') !!}
            </div>

            {{-- Search --}}
            <div class="gl-search d-none d-lg-flex">
                <input type="text" id="gl_search_input" name="search" placeholder="{{ __('Search products...') }}" value="{{ request('search') }}" autocomplete="off">
                <button type="button" onclick="doSearchGl()"><i class="las la-search"></i></button>
                <ul class="gl-search-dropdown"></ul>
            </div>

<script>
(function(){
    var inp=document.getElementById('gl_search_input'),
        dd=document.querySelector('.gl-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchGl();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="gl-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="gl-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchGl=function(){
        var q=(document.getElementById('gl_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>

            {{-- Icons --}}
            <div class="d-flex align-items-center gap-3">
                {!! theme_wishlist_icon_html('gl-icon-btn', 'gl-icon-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="gl-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('gl-icon-btn', 'gl-icon-badge') !!}
                {!! theme_account_icon_html('gl-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>

        </div>
    </div>
</nav>
