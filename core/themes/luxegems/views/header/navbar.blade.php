{{-- LuxeGems: Navbar --}}
@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)<div class="lg-promo-bar">{!! $promo !!}</div>@endif
<nav class="lg-navbar">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ theme_home_url() }}" class="lg-brand me-4">
                @php $logo = theme_logo_url(); @endphp
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ get_static_option('site_title') }}" style="max-height:36px;vertical-align:middle;">
                @else
                    {!! preg_replace('/^(LUXE|LUXEGEMS?)/i', '<span style="color:var(--lg-gold)">$0</span>', strtoupper(get_static_option('site_title') ?: 'LUXEGEMS')) !!}
                @endif
            </a>
            <div class="d-none d-xl-flex me-auto">
                {!! theme_nav_menu(null, 'lg-nav-links', 'lg-nav-links') !!}
            </div>
            <div class="lg-search d-none d-md-flex me-3">
                <input type="text" id="lg_search_input" name="search" placeholder="{{ __('Search...') }}"
                       value="{{ request('search') }}" autocomplete="off">
                <button type="button" onclick="doSearchLg()" aria-label="{{ __('Search') }}">
                    <i class="las la-search"></i>
                </button>
                <ul class="lg-search-dropdown"></ul>
            </div>

<script>
(function(){
    var inp=document.getElementById('lg_search_input'),
        dd=document.querySelector('.lg-search-dropdown'),t;
    if(inp&&dd){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchLg();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){dd.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        dd.innerHTML=d.markup+'<li class="lg-search-footer"><a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="mdi mdi-arrow-right"></i></a></li>';
                    }else{
                        dd.innerHTML='<li class="lg-search-empty"><span>{{ __("No results found") }}</span></li>';
                    }
                    dd.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.contains(e.target)&&!dd.contains(e.target))dd.classList.remove('open');
        });
    }
    window.doSearchLg=function(){
        var q=(document.getElementById('lg_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>
            <div class="d-flex gap-2">
                {!! theme_wishlist_icon_html('lg-icon-btn', 'lg-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="lg-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="las la-retweet"></i>
                </a>
                {!! theme_cart_icon_html('lg-icon-btn', 'lg-badge') !!}
                {!! theme_account_icon_html('lg-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>
        </div>
    </div>
</nav>
