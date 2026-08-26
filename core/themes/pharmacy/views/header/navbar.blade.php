{{-- Pharmacy: Navbar --}}
@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)<div class="pf-promo-bar">{!! $promo !!}</div>@endif

<nav class="pf-navbar">
    <div class="container">
        <div class="pf-nav-top">
            <div class="pf-nav-logo">
                {!! theme_logo_html('pf-brand', 'pf-brand-logo-img', 'pf-brand') !!}
            </div>
            <div class="pf-nav-search-wrap">
                <div class="pf-search">
                    <input id="pf_search_input" type="text" autocomplete="off"
                           placeholder="{{ __('Search medicines, vitamins, devices…') }}">
                    <button type="button" onclick="doSearchPf()"><i class="las la-search"></i></button>
                    <div class="pf-search-results"><ul class="product-suggestion-list"></ul></div>
                </div>
            </div>
            <div class="pf-nav-actions">
                <div class="pf-action-item">
                    {!! theme_account_icon_html('pf-action-icon', 'las la-user-circle') !!}
                    <span class="pf-action-label">{{ __('Account') }}</span>
                </div>
                <div class="pf-action-item">
                    {!! theme_wishlist_icon_html('pf-action-icon', 'pf-badge', 'las la-heart') !!}
                    <span class="pf-action-label">{{ __('Saved') }}</span>
                </div>
                <div class="pf-action-item">
                    <a href="{{ theme_compare_page_url() }}" class="pf-action-icon" aria-label="{{ __('Compare') }}">
                        <i class="las la-retweet"></i>
                    </a>
                    <span class="pf-action-label">{{ __('Compare') }}</span>
                </div>
                <div class="pf-action-item">
                    {!! theme_cart_icon_html('pf-action-icon', 'pf-badge', 'las la-shopping-cart') !!}
                    <span class="pf-action-label">{{ __('Cart') }}</span>
                </div>
                {!! renderNavbarIconsHooks() !!}
            </div>
        </div>
    </div>
    <div class="pf-nav-cats">
        <div class="container">
            <div class="pf-nav-cat-links">
                {!! theme_nav_menu(null, 'pf-cat-menu') !!}
            </div>
        </div>
    </div>
</nav>

<script>
(function(){
    var inp=document.getElementById('pf_search_input'),
        wrap=document.querySelector('.pf-search-results'),
        ul=wrap?wrap.querySelector('.product-suggestion-list'):null,t;
    if(inp&&wrap&&ul){
        inp.addEventListener('keyup',function(e){
            if(e.key==='Enter'){doSearchPf();return;}
            clearTimeout(t);var q=this.value.trim();
            if(q.length<2){wrap.classList.remove('open');return;}
            t=setTimeout(function(){
                $.ajax({url:'{{ theme_search_ajax_url() }}',type:'GET',data:{search:q},success:function(d){
                    if(d.product_object&&d.product_object.length){
                        ul.innerHTML=d.markup;
                        var footer=wrap.querySelector('.pf-search-footer');
                        if(!footer){footer=document.createElement('li');footer.className='pf-search-footer';wrap.appendChild(footer);}
                        footer.innerHTML='<a href="{{ theme_shop_url() }}?search='+encodeURIComponent(q)+'">{{ __("View all results") }} <i class="las la-arrow-right"></i></a>';
                    }else{
                        ul.innerHTML='<li class="pf-search-empty"><span>{{ __("No results found") }}</span></li>';
                        var footer=wrap.querySelector('.pf-search-footer');
                        if(footer)footer.remove();
                    }
                    wrap.classList.add('open');
                }});
            },300);
        });
        document.addEventListener('click',function(e){
            if(!inp.closest('.pf-search').contains(e.target))wrap.classList.remove('open');
        });
    }
    window.doSearchPf=function(){
        var q=(document.getElementById('pf_search_input')||{}).value?.trim();
        if(q)location.href='{{ theme_shop_url() }}?search='+encodeURIComponent(q);
    };
})();
</script>
