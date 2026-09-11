{{-- Maison: Navbar --}}
@if(get_static_option('site_announcement_text'))
<div class="ms-promo-bar">
    <div class="container">
        <span>{!! get_static_option('site_announcement_text') !!}</span>
    </div>
</div>
@endif

<nav class="ms-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-4">

            {{-- Logo --}}
            <div class="ms-navbar-logo">
                {!! theme_logo_html('ms-navbar-brand-link', 'ms-navbar-logo-img', 'ms-navbar-brand') !!}
            </div>

            {{-- Nav links (desktop) --}}
            <div class="ms-nav-links d-none d-lg-flex">
                {!! theme_nav_menu(null, 'ms-nav-links', 'ms-nav-link') !!}
            </div>

            {{-- Search bar --}}
            <div class="ms-search-wrap d-none d-lg-block">
                <input type="text"
                       id="ms_search_input"
                       class="ms-search-input"
                       placeholder="{{ __('Search home décor...') }}"
                       autocomplete="off">
                <button class="ms-search-btn" type="button" onclick="doSearchMs()">
                    <i class="las la-search"></i>
                </button>
                <ul class="ms-search-dropdown"></ul>
            </div>

            {{-- Icons --}}
            <div class="d-flex align-items-center gap-3">
                {!! theme_wishlist_icon_html('ms-icon-btn', 'ms-icon-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="ms-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('ms-icon-btn', 'ms-icon-badge') !!}
                {!! theme_account_icon_html('ms-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>

        </div>
    </div>
</nav>

<script>
(function(){
    var inp=document.getElementById('ms_search_input'),
        dd=document.querySelector('.ms-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchMs();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="ms-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="ms-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchMs=function(){
        var q=(document.getElementById('ms_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>
