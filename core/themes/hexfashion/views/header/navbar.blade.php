@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)
<div class="hf-promo-bar">{!! $promo !!}</div>
@endif

<nav class="hf-navbar">
    <div class="container">
        <div class="hf-navbar-inner">

            {{-- Logo --}}
            <div class="hf-navbar-logo">
                {!! theme_logo_html('hf-logo-link', 'hf-logo-img', 'hf-brand') !!}
            </div>

            {{-- Nav Links (desktop) --}}
            <div class="hf-nav-desktop d-none d-lg-block">
                {!! theme_nav_menu(null, 'hf-nav-links') !!}
            </div>

            {{-- Right side --}}
            <div class="hf-navbar-right">

                {{-- Search (desktop) — id="search_form_input" kept for AJAX compat --}}
                <div class="hf-search d-none d-md-flex">
                    <input type="text" id="search_form_input" autocomplete="off"
                           placeholder="{{ __('Search products…') }}" value="{{ request('search') }}">
                    <button type="button" onclick="elDoSearch()"><i class="las la-search"></i></button>
                    <ul class="hf-search-dropdown"></ul>
                </div>

                {!! theme_wishlist_icon_html('hf-icon-btn', 'hf-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="hf-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('hf-icon-btn', 'hf-badge') !!}
                {!! theme_account_icon_html('hf-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}

                {{-- Mobile toggle --}}
                <button class="hf-hamburger d-lg-none" id="elMenuToggle" type="button" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div class="hf-mobile-nav" id="elMobileNav">
            {!! theme_nav_menu(null, 'hf-mobile-links') !!}
            <div class="hf-mobile-search">
                <input type="text" id="hf_search_mob" autocomplete="off" placeholder="{{ __('Search products…') }}">
                <button type="button" onclick="elDoSearchMob()"><i class="las la-search"></i></button>
            </div>
        </div>
    </div>
</nav>

<script>
(function(){
    var inp=document.getElementById('search_form_input'),dd=document.querySelector('.hf-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){elDoSearch();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="hf-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    } else {
                        dd.innerHTML='<li class="hf-search-empty"><i class="mdi mdi-magnify-close hf-search-empty-icon"></i><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');});
    }
    window.elDoSearch=function(){var q=(document.getElementById('search_form_input')||{}).value?.trim();if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);};
    window.elDoSearchMob=function(){var q=(document.getElementById('hf_search_mob')||{}).value?.trim();if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);};
    var tog=document.getElementById('elMenuToggle'),nav=document.getElementById('elMobileNav');
    if(tog&&nav)tog.addEventListener('click',function(){nav.classList.toggle('open');this.classList.toggle('open');});
})();
</script>
