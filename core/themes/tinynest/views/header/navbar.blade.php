{{-- TinyNest: Navbar --}}
<style>
.tn-search-dropdown { display:none; position:absolute; top:calc(100% + 8px); left:0; right:0; background:#fff; border:2px solid var(--tn-border); border-radius:16px; box-shadow:var(--tn-shadow); z-index:9999; list-style:none; margin:0; padding:6px 0; max-height:360px; overflow-x:hidden; overflow-y:auto; min-width:280px; }
.tn-search-dropdown.open { display:block; }
.tn-search-dropdown .product-suggestion-list-item { width:100%!important; }
.tn-search-dropdown .product-suggestion-list-item a { display:flex; align-items:center; gap:12px; padding:10px 14px; text-decoration:none; color:var(--tn-dark); transition:background .15s; }
.tn-search-dropdown .product-suggestion-list-item a:hover { background:var(--tn-border); }
.tn-search-dropdown .product-suggestion-list-item .product-image { width:44px; height:44px; border-radius:10px; overflow:hidden; flex-shrink:0; background:var(--tn-border); }
.tn-search-dropdown .product-suggestion-list-item .product-image img { width:100%; height:100%; object-fit:cover; }
.tn-search-dropdown .product-suggestion-list-item .product-name { font-size:13px; font-weight:600; line-height:1.3; }
.tn-search-dropdown .product-suggestion-list-item .flash-price { font-size:12px; color:var(--tn-pink); font-weight:700; }
.tn-search-dropdown .product-suggestion-list-item .flash-old-prices { font-size:11px; color:var(--tn-muted); text-decoration:line-through; margin-left:4px; }
</style>
@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)
<div class="tn-promo-bar">{!! $promo !!}</div>
@endif
<nav class="tn-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="tn-navbar-logo flex-shrink-0">
                {!! theme_logo_html('tn-brand-link', 'tn-logo-img', 'tn-brand') !!}
            </div>
            <div class="tn-nav-links d-none d-xl-flex">
                {!! theme_nav_menu() !!}
            </div>
            {{-- Search bar --}}
            <div class="tn-nav-search d-none d-lg-flex flex-shrink-0" style="position:relative;">
                <input type="text" id="tn_search_input" autocomplete="off"
                       class="tn-search-input"
                       placeholder="{{ __('Search baby essentials…') }}"
                       value="{{ request('search') }}">
                <button type="button" class="tn-search-btn" onclick="tnDoSearch()">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <ul class="product-suggestion-list tn-search-dropdown"></ul>
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                {!! theme_wishlist_icon_html('tn-icon-btn', 'tn-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="tn-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="mdi mdi-compare-horizontal"></i>
                </a>
                {!! theme_cart_icon_html('tn-icon-btn', 'tn-badge') !!}
                {!! theme_account_icon_html('tn-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>
        </div>
    </div>
</nav>
<script>
(function () {
    var input    = document.getElementById('tn_search_input');
    var dropdown = document.querySelector('.tn-search-dropdown');
    if (!input || !dropdown) return;
    var timer;
    input.addEventListener('keyup', function (e) {
        if (e.key === 'Enter') { tnDoSearch(); return; }
        clearTimeout(timer);
        var q = this.value.trim();
        if (q.length < 2) { dropdown.classList.remove('open'); return; }
        timer = setTimeout(function () {
            $.ajax({
                url: '{{ theme_search_ajax_url() }}',
                type: 'GET',
                data: { search: q },
                success: function (data) {
                    if (data.product_object && data.product_object.length > 0) {
                        dropdown.innerHTML = data.markup;
                        dropdown.classList.add('open');
                    } else {
                        dropdown.innerHTML = '<li style="padding:12px 14px;font-size:13px;color:var(--tn-muted);">{{ __("No results found") }}</li>';
                        dropdown.classList.add('open');
                    }
                }
            });
        }, 300);
    });
    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });
    window.tnDoSearch = function () {
        var q = input.value.trim();
        if (q) window.location.href = '{{ theme_shop_url() }}' + '?search=' + encodeURIComponent(q);
    };
})();
</script>
