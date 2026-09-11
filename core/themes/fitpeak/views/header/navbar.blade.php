{{-- FitPeak: Navbar --}}
@php
    $announcement = get_static_option('site_announcement_text');
@endphp

@if($announcement)
<div class="fp-promo-bar">
    <span>{{ $announcement }}</span>
</div>
@endif

<nav class="fp-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-4">

            {{-- Logo --}}
            <div class="fp-navbar-logo">
                {!! theme_logo_html('fp-navbar-brand-link', 'fp-navbar-logo-img', 'fp-navbar-brand') !!}
            </div>

            {{-- Nav links (desktop) --}}
            <div class="fp-nav-links d-none d-lg-flex">
                {!! theme_nav_menu() !!}
            </div>

            {{-- Search --}}
            <div class="fp-navbar-search fp-search-wrap d-none d-lg-flex">
                <input type="text" id="fp_search_input" name="search" class="fp-search-input" placeholder="{{ __('Search supplements...') }}" value="{{ request('search') }}" autocomplete="off">
                <button type="button" class="fp-search-btn" onclick="doSearchFp()"><i class="mdi mdi-magnify"></i></button>
                <ul class="fp-search-dropdown"></ul>
            </div>

<script>
(function(){
    var inp=document.getElementById('fp_search_input'),
        dd=document.querySelector('.fp-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchFp();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="fp-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="fp-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchFp=function(){
        var q=(document.getElementById('fp_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>

            {{-- Icons --}}
            <div class="d-flex align-items-center gap-3">
                {!! theme_wishlist_icon_html('fp-icon-btn', 'fp-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="fp-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('fp-icon-btn', 'fp-badge') !!}
                {!! theme_account_icon_html('fp-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>

        </div>
    </div>
</nav>
