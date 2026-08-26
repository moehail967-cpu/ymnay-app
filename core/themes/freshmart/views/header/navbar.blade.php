{{-- FreshMart: Navbar --}}
@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)
<div class="fm-promo-bar">{!! $promo !!}</div>
@endif

<nav class="fm-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-4">

            {{-- Logo --}}
            <div>
                {!! theme_logo_html('fm-navbar-brand-link', 'fm-navbar-logo-img', 'fm-brand') !!}
            </div>

            {{-- Nav links (desktop) --}}
            <div class="d-none d-lg-flex fm-nav-links">
                {!! theme_nav_menu() !!}
            </div>

            {{-- Search --}}
            <div class="fm-search d-none d-lg-flex">
                <input type="text" id="fm_search_input" name="search" placeholder="{{ __('Search products...') }}" value="{{ request('search') }}" autocomplete="off">
                <button type="button" onclick="doSearchFm()"><i class="las la-search"></i></button>
                <ul class="fm-search-dropdown"></ul>
            </div>

<script>
(function(){
    var inp=document.getElementById('fm_search_input'),
        dd=document.querySelector('.fm-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchFm();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="fm-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="fm-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchFm=function(){
        var q=(document.getElementById('fm_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>

            {{-- Icons --}}
            <div class="d-flex align-items-center gap-2">
                {!! theme_wishlist_icon_html('fm-icon-btn', 'fm-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="fm-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('fm-icon-btn', 'fm-badge') !!}
                {!! theme_account_icon_html('fm-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>

        </div>
    </div>
</nav>
