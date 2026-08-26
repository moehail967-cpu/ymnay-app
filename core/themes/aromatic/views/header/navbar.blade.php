@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)
<div class="ar-promo-bar">{!! $promo !!}</div>
@endif

<nav class="ar-navbar">
    <div class="container">
        <div class="ar-navbar-inner">

            {{-- Logo --}}
            <div class="ar-navbar-logo">
                {!! theme_logo_html('ar-logo-link', 'ar-logo-img', 'ar-brand') !!}
            </div>

            {{-- Nav Links (desktop) --}}
            <div class="d-none d-lg-flex ar-nav-desktop">
                {!! theme_nav_menu(null, 'ar-nav-links', 'ar-nav-link') !!}
            </div>

            {{-- Right side --}}
            <div class="ar-navbar-right">

                {{-- Search (desktop) --}}
                <div class="ar-search d-none d-md-flex">
                    <input type="text" id="ar_search_input" autocomplete="off"
                           placeholder="{{ __('Search products…') }}" value="{{ request('search') }}">
                    <button type="button" onclick="arDoSearch()"><i class="las la-search"></i></button>
                    <ul class="ar-search-dropdown"></ul>
                </div>

                {!! theme_wishlist_icon_html('ar-icon-btn', 'ar-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="ar-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('ar-icon-btn', 'ar-badge') !!}
                {!! theme_account_icon_html('ar-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}

                {{-- Mobile toggle --}}
                <button class="ar-hamburger d-lg-none" id="arMenuToggle" type="button" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div class="ar-mobile-nav" id="arMobileNav">
            {!! theme_nav_menu(null, 'ar-mobile-links') !!}
            <div class="ar-mobile-search">
                <input type="text" id="ar_search_mob" autocomplete="off" placeholder="{{ __('Search products…') }}">
                <button type="button" onclick="arDoSearchMob()"><i class="las la-search"></i></button>
            </div>
        </div>
    </div>
</nav>


<script>
(function(){
    var inp=document.getElementById('ar_search_input'),dd=document.querySelector('.ar-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){arDoSearch();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="ar-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    } else {
                        dd.innerHTML='<li class="ar-search-empty"><i class="mdi mdi-magnify-close ar-search-empty-icon"></i><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');});
    }
    window.arDoSearch=function(){var q=(document.getElementById('ar_search_input')||{}).value?.trim();if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);};
    window.arDoSearchMob=function(){var q=(document.getElementById('ar_search_mob')||{}).value?.trim();if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);};
    var tog=document.getElementById('arMenuToggle'),nav=document.getElementById('arMobileNav');
    if(tog&&nav)tog.addEventListener('click',function(){nav.classList.toggle('open');this.classList.toggle('open');});
})();
</script>
