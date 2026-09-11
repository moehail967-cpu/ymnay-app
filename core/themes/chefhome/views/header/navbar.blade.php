{{-- ChefHome: Navbar --}}
<nav class="ch-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-4">

            {{-- Logo --}}
            <div class="ch-navbar-logo">
                {!! theme_logo_html('ch-navbar-brand-link', 'ch-navbar-logo-img', 'ch-navbar-brand') !!}
            </div>

            {{-- Nav links (desktop) --}}
            <div class="ch-nav-links d-none d-lg-flex">
                {!! theme_nav_menu() !!}
            </div>

            {{-- Search + Icons --}}
            <div class="d-flex align-items-center gap-3">

                {{-- Search (desktop) --}}
                <div class="ch-search d-none d-md-flex">
                    <input type="text" id="ch_search_input" autocomplete="off"
                           placeholder="{{ __('Search dishes…') }}" value="{{ request('search') }}">
                    <button type="button" onclick="doSearchCh()"><i class="las la-search"></i></button>
                    <ul class="ch-search-dropdown"></ul>
                </div>

                {!! theme_wishlist_icon_html('ch-icon-btn', 'ch-icon-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="ch-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('ch-icon-btn', 'ch-icon-badge') !!}
                {!! theme_account_icon_html('ch-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>

        </div>
    </div>
</nav>

<script>
(function(){
    var inp=document.getElementById('ch_search_input'),
        dd=document.querySelector('.ch-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchCh();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="ch-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="ch-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchCh=function(){
        var q=(document.getElementById('ch_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>
