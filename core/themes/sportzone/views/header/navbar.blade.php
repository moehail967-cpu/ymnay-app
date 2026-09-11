{{-- SportZone: Navbar --}}
@php
    $sz_announcement = get_static_option('site_announcement_text');
@endphp

{{-- ===== Announcement / Promo Bar ===== --}}
@if($sz_announcement)
<div class="sz-promo-bar">
    <div class="container">
        <div class="sz-promo-bar-inner">
            <span class="sz-promo-bar-text">{!! $sz_announcement !!}</span>
        </div>
    </div>
</div>
@endif

{{-- ===== Main Navbar ===== --}}
<nav class="sz-navbar">
    <div class="container">
        <div class="sz-navbar-inner d-flex align-items-center justify-content-between gap-3">

            {{-- Logo --}}
            <div class="sz-brand flex-shrink-0">
                {!! theme_logo_html('sz-navbar-brand-link', 'sz-navbar-logo-img', 'sz-brand') !!}
            </div>

            {{-- Center-left: Desktop Navigation --}}
            <div class="sz-nav-links d-none d-lg-flex align-items-center">
                {!! theme_nav_menu(null, 'sz-nav') !!}
            </div>

            {{-- Center-right: Inline Search --}}
            <div class="sz-search d-none d-lg-flex align-items-center">
                <input type="text"
                       id="sz_search_input"
                       class="sz-search-input"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('Search for sports gear...') }}"
                       autocomplete="off"
                       aria-label="{{ __('Search products') }}">
                <button type="button" class="sz-search-btn" onclick="doSearchSz()" aria-label="{{ __('Submit search') }}">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <ul class="sz-search-dropdown" aria-live="polite"></ul>
            </div>

            {{-- Right: Icon Group + Hamburger --}}
            <div class="sz-navbar-actions d-flex align-items-center gap-2 flex-shrink-0">

                {{-- Wishlist --}}
                {!! theme_wishlist_icon_html('sz-icon-btn', 'sz-badge') !!}

                {{-- Compare --}}
                <a href="{{ theme_compare_page_url() }}" class="sz-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="mdi mdi-compare-horizontal"></i>
                </a>

                {{-- Cart --}}
                {!! theme_cart_icon_html('sz-icon-btn', 'sz-badge') !!}

                {{-- Account --}}
                {!! theme_account_icon_html('sz-icon-btn') !!}

                {{-- Plugin hook icons --}}
                {!! renderNavbarIconsHooks() !!}

                {{-- Mobile Hamburger (hidden on lg+) --}}
                <button id="sz_menu_toggle"
                        class="sz-hamburger d-flex d-lg-none align-items-center justify-content-center"
                        type="button"
                        aria-label="{{ __('Open navigation menu') }}"
                        aria-expanded="false"
                        aria-controls="sz_mobile_drawer">
                    <i class="mdi mdi-menu"></i>
                </button>

            </div>
            {{-- end .sz-navbar-actions --}}

        </div>
        {{-- end .sz-navbar-inner --}}
    </div>
    {{-- end .container --}}
</nav>
{{-- end .sz-navbar --}}


{{-- ===== Mobile Off-Canvas Drawer ===== --}}
<div id="sz_mobile_drawer" class="sz-mobile-drawer" aria-hidden="true" role="dialog" aria-label="{{ __('Mobile navigation') }}">

    {{-- Drawer Header --}}
    <div class="sz-mobile-drawer-header d-flex align-items-center justify-content-between">

        {{-- Drawer Logo --}}
        <div class="sz-mobile-drawer-logo">
            {!! theme_logo_html('sz-mobile-brand-link', 'sz-mobile-logo-img', 'sz-brand') !!}
        </div>

        {{-- Close Button --}}
        <button id="sz_drawer_close"
                class="sz-drawer-close"
                type="button"
                aria-label="{{ __('Close navigation menu') }}">
            <i class="mdi mdi-close"></i>
        </button>

    </div>
    {{-- end .sz-mobile-drawer-header --}}

    {{-- Mobile Search (inside drawer) --}}
    <div class="sz-mobile-drawer-search">
        <form action="{{ theme_shop_url() }}" method="GET" role="search">
            <div class="sz-mobile-search-wrap d-flex align-items-center">
                <input type="text"
                       class="sz-mobile-search-input"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('Search products...') }}"
                       autocomplete="off"
                       aria-label="{{ __('Search products') }}">
                <button type="submit" class="sz-mobile-search-btn" aria-label="{{ __('Submit search') }}">
                    <i class="mdi mdi-magnify"></i>
                </button>
            </div>
        </form>
    </div>
    {{-- end .sz-mobile-drawer-search --}}

    {{-- Mobile Nav Menu --}}
    <div class="sz-mobile-drawer-nav">
        {!! theme_nav_menu(null, 'sz-mobile-nav') !!}
    </div>
    {{-- end .sz-mobile-drawer-nav --}}

</div>
{{-- end #sz_mobile_drawer --}}

{{-- Overlay (closes drawer on click) --}}
<div id="sz_drawer_overlay" class="sz-drawer-overlay" aria-hidden="true"></div>


{{-- ===== Navbar Scripts ===== --}}
<script>
(function(){
    /* ---- Mobile drawer (vanilla JS) ---- */
    var toggle  = document.getElementById('sz_menu_toggle'),
        drawer  = document.getElementById('sz_mobile_drawer'),
        overlay = document.getElementById('sz_drawer_overlay'),
        closeBtn= document.getElementById('sz_drawer_close');
    function openDrawer(){drawer.classList.add('open');drawer.setAttribute('aria-hidden','false');overlay.classList.add('show');if(toggle)toggle.setAttribute('aria-expanded','true');document.body.classList.add('sz-drawer-open');}
    function closeDrawer(){drawer.classList.remove('open');drawer.setAttribute('aria-hidden','true');overlay.classList.remove('show');if(toggle)toggle.setAttribute('aria-expanded','false');document.body.classList.remove('sz-drawer-open');}
    if(toggle)toggle.addEventListener('click',function(){drawer.classList.contains('open')?closeDrawer():openDrawer();});
    if(overlay)overlay.addEventListener('click',closeDrawer);
    if(closeBtn)closeBtn.addEventListener('click',closeDrawer);
    document.addEventListener('keydown',function(e){if(e.key==='Escape'&&drawer&&drawer.classList.contains('open')){closeDrawer();if(toggle)toggle.focus();}});

    /* ---- Search autocomplete (desktop) ---- */
    var inp=document.getElementById('sz_search_input'),
        dd=document.querySelector('.sz-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchSz();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="sz-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="sz-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchSz=function(){
        var q=(document.getElementById('sz_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>
