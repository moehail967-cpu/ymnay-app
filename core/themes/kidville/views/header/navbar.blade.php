{{-- KidVille: Navbar --}}
@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)
<div class="kv-promo-bar">{!! $promo !!}</div>
@endif
<nav class="kv-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="kv-navbar-logo flex-shrink-0">
                {!! theme_logo_html('kv-brand-link', 'kv-logo-img', 'kv-brand') !!}
            </div>
            <div class="kv-nav-links d-none d-xl-flex flex-shrink-0">
                {!! theme_nav_menu() !!}
            </div>
            <div class="kv-search-bar kv-search-wrap flex-grow-1 d-none d-lg-flex">
                <input type="text" id="kv_search_input" name="search" class="kv-search-input" placeholder="{{ __('Search toys & games...') }}" value="{{ request('search') }}" autocomplete="off">
                <button type="button" class="kv-search-btn" onclick="doSearchKv()" aria-label="{{ __('Search') }}">
                    <i class="las la-search"></i>
                </button>
                <ul class="kv-search-dropdown"></ul>
            </div>

<script>
(function(){
    var inp=document.getElementById('kv_search_input'),
        dd=document.querySelector('.kv-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchKv();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="kv-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="kv-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchKv=function(){
        var q=(document.getElementById('kv_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                {!! theme_wishlist_icon_html('kv-icon-btn', 'kv-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="kv-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('kv-icon-btn', 'kv-badge') !!}
                {!! theme_account_icon_html('kv-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>
        </div>
    </div>
</nav>
