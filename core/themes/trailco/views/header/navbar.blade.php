{{-- TrailCo: Navbar --}}
<nav class="tc-navbar">
    <div class="container">
        <div class="d-flex align-items-center gap-3">

            {{-- Logo --}}
            {!! theme_logo_html('tc-brand-link', 'tc-brand-logo-img', 'tc-brand me-4') !!}

            {{-- Nav links: theme_nav_menu renders <ul class="tc-nav-links"> inside this div --}}
            <div class="tc-nav-links-wrap me-auto d-none d-lg-flex">
                {!! theme_nav_menu(null, 'tc-nav-links') !!}
            </div>

            {{-- Search --}}
            <div class="tc-search d-none d-md-flex me-3">
                <input autocomplete="off" id="tc_search_input" type="text"
                       placeholder="{{ __('Search outdoor gear...') }}">
                <button type="button" onclick="doSearchTc()" aria-label="{{ __('Search') }}">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <ul class="tc-search-dropdown"></ul>
            </div>

            {{-- Icons --}}
            <div class="d-flex gap-2">
                {!! theme_wishlist_icon_html('tc-icon-btn', 'tc-badge', 'mdi mdi-heart-outline') !!}
                <a href="{{ theme_compare_page_url() }}" class="tc-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="mdi mdi-compare-horizontal"></i>
                </a>
                {!! theme_cart_icon_html('tc-icon-btn', 'tc-badge', 'mdi mdi-cart-outline') !!}
                {!! theme_account_icon_html('tc-icon-btn', 'mdi mdi-account-outline') !!}
                {!! renderNavbarIconsHooks() !!}
                <button class="tc-icon-btn d-lg-none" id="tr_mobile_toggle" type="button" aria-label="{{ __('Menu') }}">
                    <i class="mdi mdi-menu"></i>
                </button>
            </div>

        </div>

        {{-- Mobile nav --}}
        <div class="tr-mobile-nav" id="tr_mobile_nav">
            {!! theme_nav_menu(null, 'tr-mobile-menu') !!}
        </div>
    </div>
</nav>

<script>
(function(){
    var inp=document.getElementById('tc_search_input'),
        dd=document.querySelector('.tc-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchTc();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="tc-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="tc-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchTc=function(){
        var q=(document.getElementById('tc_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
    var tog=document.getElementById('tr_mobile_toggle'),nav=document.getElementById('tr_mobile_nav');
    if(tog&&nav)tog.addEventListener('click',function(){nav.classList.toggle('open');this.classList.toggle('open');});
})();
</script>
