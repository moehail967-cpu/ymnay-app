@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)
<div class="cs-promo-bar">{!! $promo !!}</div>
@endif

<nav class="cs-navbar">
    <div class="container">
        <div class="cs-navbar-inner">

            {{-- Logo --}}
            <div class="cs-navbar-logo">
                {!! theme_logo_html('cs-logo-link', 'cs-logo-img', 'cs-brand') !!}
            </div>

            {{-- Nav Links (desktop) --}}
            <div class="cs-nav-desktop d-none d-lg-block">
                {!! theme_nav_menu(null, 'cs-nav-links') !!}
            </div>

            {{-- Right side --}}
            <div class="cs-navbar-right">

                {{-- Search (desktop) --}}
                <div class="cs-search d-none d-md-flex" style="position:relative;">
                    <input type="text" id="cs_search_input" autocomplete="off"
                           placeholder="{{ __('Search products…') }}" value="{{ request('search') }}">
                    <button type="button" onclick="csDoSearch()"><i class="las la-search"></i></button>
                    <ul class="product-suggestion-list cs-search-dropdown"></ul>
                </div>

                {!! theme_wishlist_icon_html('cs-icon-btn', 'cs-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="cs-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('cs-icon-btn', 'cs-badge') !!}
                {!! theme_account_icon_html('cs-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}

                {{-- Mobile toggle --}}
                <button class="cs-hamburger d-lg-none" id="csMenuToggle" type="button" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div class="cs-mobile-nav" id="csMobileNav">
            {!! theme_nav_menu(null, 'cs-mobile-links') !!}
            <div class="cs-mobile-search">
                <input type="text" id="cs_search_mob" autocomplete="off" placeholder="{{ __('Search products…') }}">
                <button type="button" onclick="csDoSearchMob()"><i class="las la-search"></i></button>
            </div>
        </div>
    </div>
</nav>

<style>
.cs-search-dropdown{display:none;position:absolute;top:calc(100% + 8px);left:0;right:0;background:#fff;border:1.5px solid #fce8e6;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.10);z-index:9999;list-style:none;margin:0;padding:6px 0;max-height:360px;overflow-x:hidden;overflow-y:auto;}
.cs-search-dropdown.open{display:block;}
.cs-search-dropdown .product-suggestion-list-item{width:100%!important;}
.cs-search-dropdown li a{display:flex;align-items:center;gap:12px;padding:10px 14px;text-decoration:none;color:#1a1a1a;transition:background .15s;}
.cs-search-dropdown li a:hover{background:#fff5f3;}
.cs-search-dropdown li .product-image{width:44px;height:44px;border-radius:6px;overflow:hidden;flex-shrink:0;}
.cs-search-dropdown li .product-image img{width:100%;height:100%;object-fit:cover;}
.cs-search-dropdown li .product-name{font-size:13px;font-weight:600;line-height:1.3;}
.cs-search-dropdown li .flash-price{font-size:12px;color:#F83A26;font-weight:700;}
.cs-search-dropdown li .flash-old-prices{font-size:11px;color:#aaa;text-decoration:line-through;margin-left:4px;}
</style>

<script>
(function(){
    var inp=document.getElementById('cs_search_input'),dd=document.querySelector('.cs-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){csDoSearch();return;}
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
    window.csDoSearch=function(){var q=(document.getElementById('cs_search_input')||{}).value?.trim();if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);};
    window.csDoSearchMob=function(){var q=(document.getElementById('cs_search_mob')||{}).value?.trim();if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);};
    var tog=document.getElementById('csMenuToggle'),nav=document.getElementById('csMobileNav');
    if(tog&&nav)tog.addEventListener('click',function(){nav.classList.toggle('open');this.classList.toggle('open');});
})();
</script>
