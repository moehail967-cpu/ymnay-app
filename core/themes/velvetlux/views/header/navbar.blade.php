{{-- VelvetLux: Navbar --}}
@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)<div class="vl-promo-bar">{!! $promo !!}</div>@endif
<nav class="vl-navbar">
    <div class="container">
        <div class="d-flex align-items-center gap-4">
            <div class="flex-shrink-0 vl-logo-img">{!! theme_logo_html('vl-navbar-logo', 'vl-navbar-logo-img', 'vl-brand') !!}</div>
            <div class="d-none d-lg-flex flex-grow-1 justify-content-center">
                {!! theme_nav_menu(null, 'vl-nav-links') !!}
            </div>
            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <div class="vl-search d-none d-lg-flex">
                    <input autocomplete="off" id="vl_search_input" type="text" placeholder="{{ __('Search...') }}">
                    <button type="button" onclick="doSearchVl()"><i class="mdi mdi-magnify"></i></button>
                    <ul class="vl-search-dropdown"></ul>
                </div>
                {!! theme_wishlist_icon_html('vl-icon-btn', 'vl-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="vl-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="mdi mdi-compare-horizontal"></i>
                </a>
                {!! theme_cart_icon_html('vl-icon-btn', 'vl-badge') !!}
                {!! theme_account_icon_html('vl-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>
        </div>
    </div>
</nav>

<script>
(function(){
    var inp=document.getElementById('vl_search_input'),
        dd=document.querySelector('.vl-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchVl();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="vl-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="vl-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchVl=function(){
        var q=(document.getElementById('vl_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>
