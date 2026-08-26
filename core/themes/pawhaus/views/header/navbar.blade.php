{{-- PawHaus: Navbar --}}
@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)
<div class="ph-promo-bar">{!! $promo !!}</div>
@endif

<nav class="ph-navbar">
    <div class="container">
        <div class="ph-navbar-inner">

            {{-- Logo --}}
            <div class="ph-navbar-logo">
                {!! theme_logo_html('ph-brand-link', 'ph-logo-img', 'ph-brand') !!}
            </div>

            {{-- Nav links (desktop) --}}
            <div class="d-none d-lg-block">
                {!! theme_nav_menu(null, 'ph-nav-links', 'ph-nav-link') !!}
            </div>

            {{-- Search bar --}}
            <div class="ph-search-wrap d-none d-lg-flex">
                <input type="text"
                       id="ph_search_input"
                       class="ph-search-input"
                       placeholder="{{ __('Search pet products...') }}"
                       autocomplete="off">
                <button class="ph-search-btn" type="button" onclick="doSearchPh()">
                    <i class="las la-search"></i>
                </button>
                <ul class="ph-search-dropdown"></ul>
            </div>

            {{-- Icons --}}
            <div class="d-flex align-items-center gap-2">
                {!! theme_wishlist_icon_html('ph-icon-btn', 'ph-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="ph-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('ph-icon-btn', 'ph-badge') !!}
                {!! theme_account_icon_html('ph-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>

        </div>
    </div>
</nav>

<script>
(function(){
    var inp=document.getElementById('ph_search_input'),
        dd=document.querySelector('.ph-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchPh();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="ph-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="ph-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchPh=function(){
        var q=(document.getElementById('ph_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>
