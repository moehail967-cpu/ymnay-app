{{-- TechZone: Navbar --}}
@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)
    <div class="tz-promo-bar text-center py-2" style="background:var(--tz-blue);color:#fff;font-size:13px;">{!! $promo !!}</div>
@endif

<nav class="tz-navbar">
    <div class="container px-3 px-lg-4">
        <div class="tz-navbar-inner">

            {{-- Logo --}}
            @php $logoHtml = theme_logo_html('tz-brand-link', 'tz-logo-img', 'tz-brand-wrap'); @endphp
            @if($logoHtml)
                {!! $logoHtml !!}
            @else
                <a href="{{ theme_home_url() }}" class="tz-brand-link tz-brand-text">
                    <span class="tz-brand-dot">&#8226;</span>Tech<span>Zone</span>
                </a>
            @endif

            {{-- Desktop nav --}}
            <div class="tz-nav-menu d-none d-xl-flex">
                {!! theme_nav_menu(null, 'tz-nav-links') !!}
            </div>

            {{-- Search bar --}}
            <div class="tz-searchbar">
                <div class="tz-searchbar-inner">
                    <input type="text"
                           id="tz_search_input"
                           class="tz-searchbar-input"
                           placeholder="{{ __('Search electronics...') }}"
                           autocomplete="off">
                    <button class="tz-searchbar-btn" type="button" onclick="doSearchTz()">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                    <ul class="tz-search-dropdown"></ul>
                </div>
            </div>

            {{-- Icon group --}}
            <div class="tz-nav-icons">
                {!! theme_wishlist_icon_html('tz-icon-btn', 'tz-badge', 'mdi mdi-heart-outline') !!}
                <a href="{{ theme_compare_page_url() }}" class="tz-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="mdi mdi-compare-horizontal"></i>
                </a>
                {!! theme_cart_icon_html('tz-icon-btn', 'tz-badge', 'mdi mdi-cart-outline') !!}
                {!! theme_account_icon_html('tz-icon-btn', 'mdi mdi-account-outline') !!}
                {!! renderNavbarIconsHooks() !!}
                <button class="tz-hamburger d-xl-none" id="tz_hamburger" aria-label="Menu">
                    <i class="mdi mdi-menu"></i>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile drawer --}}
    <div class="tz-drawer-overlay" id="tz_drawer_overlay"></div>
    <div class="tz-drawer" id="tz_drawer">
        <div class="tz-drawer-head">
            <span class="tz-brand-link" style="font-size:20px;font-weight:700;color:#fff;">
                <span style="color:var(--tz-blue);">•</span>Tech<span style="color:var(--tz-blue);">Zone</span>
            </span>
            <button class="tz-drawer-close" id="tz_drawer_close"><i class="mdi mdi-close"></i></button>
        </div>
        <div class="tz-drawer-search">
            <input type="text" placeholder="{{ __('Search...') }}" autocomplete="off">
        </div>
        <div class="tz-drawer-body">
            {!! theme_nav_menu(null, 'tz-drawer-links') !!}
        </div>
    </div>
</nav>

<script>
(function () {
    const hamburger = document.getElementById('tz_hamburger');
    const drawer    = document.getElementById('tz_drawer');
    const overlay   = document.getElementById('tz_drawer_overlay');
    const closeBtn  = document.getElementById('tz_drawer_close');
    function open()  { drawer.classList.add('open'); overlay.classList.add('open'); document.body.style.overflow = 'hidden'; }
    function close() { drawer.classList.remove('open'); overlay.classList.remove('open'); document.body.style.overflow = ''; }
    if (hamburger) hamburger.addEventListener('click', open);
    if (closeBtn)  closeBtn.addEventListener('click', close);
    if (overlay)   overlay.addEventListener('click', close);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
})();
(function(){
    var inp=document.getElementById('tz_search_input'),
        dd=document.querySelector('.tz-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchTz();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="tz-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="tz-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchTz=function(){
        var q=(document.getElementById('tz_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>
