@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)
<div class="bp-promo-bar">{!! $promo !!}</div>
@endif

<nav class="bp-navbar">
    <div class="container">
        <div class="bp-navbar-inner">

            {{-- Logo --}}
            <div class="bp-navbar-logo">
                {!! theme_logo_html('bp-logo-link', 'bp-logo-img', 'bp-brand') !!}
            </div>

            {{-- Nav Links (desktop) --}}
            <div class="bp-nav-desktop d-none d-lg-block">
                {!! theme_nav_menu(null, 'bp-nav-links') !!}
            </div>

            {{-- Right side --}}
            <div class="bp-navbar-right">

                {{-- Search (desktop) --}}
                <div class="bp-search d-none d-md-flex" style="position:relative;">
                    <input type="text" id="bp_search_input" autocomplete="off"
                           placeholder="{{ __('Search books…') }}" value="{{ request('search') }}">
                    <button type="button" onclick="bpDoSearch()"><i class="las la-search"></i></button>
                    <ul class="product-suggestion-list bp-search-dropdown"></ul>
                </div>

                {!! theme_wishlist_icon_html('bp-icon-btn', 'bp-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="bp-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('bp-icon-btn', 'bp-badge') !!}
                {!! theme_account_icon_html('bp-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}

                {{-- Mobile toggle --}}
                <button class="bp-hamburger d-lg-none" id="bpMenuToggle" type="button" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div class="bp-mobile-nav" id="bpMobileNav">
            {!! theme_nav_menu(null, 'bp-mobile-links') !!}
            <div class="bp-mobile-search">
                <input type="text" id="bp_search_mob" autocomplete="off" placeholder="{{ __('Search books…') }}">
                <button type="button" onclick="bpDoSearchMob()"><i class="las la-search"></i></button>
            </div>
        </div>
    </div>
</nav>

<style>
.bp-search-dropdown{display:none;position:absolute;top:calc(100% + 8px);left:0;right:0;background:#fff;border:1.5px solid #d8ede7;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.10);z-index:9999;list-style:none;margin:0;padding:6px 0;max-height:360px;overflow-x:hidden;overflow-y:auto;}
.bp-search-dropdown.open{display:block;}
.bp-search-dropdown .product-suggestion-list-item{width:100%!important;}
.bp-search-dropdown li a{display:flex;align-items:center;gap:12px;padding:10px 14px;text-decoration:none;color:#1a1a1a;transition:background .15s;}
.bp-search-dropdown li a:hover{background:#f0faf6;}
.bp-search-dropdown li .product-image{width:44px;height:44px;border-radius:6px;overflow:hidden;flex-shrink:0;}
.bp-search-dropdown li .product-image img{width:100%;height:100%;object-fit:cover;}
.bp-search-dropdown li .product-name{font-size:13px;font-weight:600;line-height:1.3;}
.bp-search-dropdown li .flash-price{font-size:12px;color:#118668;font-weight:700;}
.bp-search-dropdown li .flash-old-prices{font-size:11px;color:#aaa;text-decoration:line-through;margin-left:4px;}
</style>

<script>
(function(){
    var inp=document.getElementById('bp_search_input'),dd=document.querySelector('.bp-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){bpDoSearch();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    dd.innerHTML=d.product_object&&d.product_object.length?d.markup:'<li style="padding:12px 14px;font-size:13px;color:#aaa;">{{ __("No results found") }}</li>';
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');});
    }
    window.bpDoSearch=function(){var q=(document.getElementById('bp_search_input')||{}).value?.trim();if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);};
    window.bpDoSearchMob=function(){var q=(document.getElementById('bp_search_mob')||{}).value?.trim();if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);};
    var tog=document.getElementById('bpMenuToggle'),nav=document.getElementById('bpMobileNav');
    if(tog&&nav)tog.addEventListener('click',function(){nav.classList.toggle('open');this.classList.toggle('open');});
})();
</script>
