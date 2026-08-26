{{-- GoldCraft: Navbar --}}
@if(get_static_option('site_announcement_text'))
<div class="gc-promo-bar">
    <div class="container">
        <span>{!! get_static_option('site_announcement_text') !!}</span>
    </div>
</div>
@endif

<nav class="gc-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-4">

            {{-- Logo --}}
            <div class="gc-navbar-logo">
                {!! theme_logo_html('gc-navbar-brand-link', 'gc-navbar-logo-img', 'gc-navbar-brand') !!}
            </div>

            {{-- Nav links (desktop) --}}
            <div class="gc-nav-links d-none d-lg-flex">
                {!! theme_nav_menu(null, 'gc-nav-links', 'gc-nav-link') !!}
            </div>

            {{-- Search --}}
            <div class="gc-search d-none d-lg-flex">
                <input type="text" id="gc_search_input" name="search" placeholder="{{ __('Search jewellery...') }}" value="{{ request('search') }}" autocomplete="off">
                <button type="button" onclick="doSearchGc()"><i class="las la-search"></i></button>
                <ul class="gc-search-dropdown"></ul>
            </div>

<script>
(function(){
    var inp=document.getElementById('gc_search_input'),
        dd=document.querySelector('.gc-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchGc();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="gc-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="gc-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchGc=function(){
        var q=(document.getElementById('gc_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>

            {{-- Icons --}}
            <div class="d-flex align-items-center gap-3">
                {!! theme_wishlist_icon_html('gc-icon-btn', 'gc-icon-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="gc-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('gc-icon-btn', 'gc-icon-badge') !!}
                {!! theme_account_icon_html('gc-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>

        </div>
    </div>
</nav>
