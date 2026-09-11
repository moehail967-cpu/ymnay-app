{{-- BakerCo: Navbar --}}
<style>
.bk-search-dropdown { display:none; position:absolute; top:calc(100% + 8px); left:0; right:0; background:#fff; border:1.5px solid var(--bk-border); border-radius:var(--bk-radius); box-shadow:var(--bk-shadow); z-index:9999; list-style:none; margin:0; padding:6px 0; max-height:360px; overflow-x:hidden; overflow-y:auto; }
.bk-search-dropdown.open { display:block; }
.bk-search-dropdown .product-suggestion-list-item { width:100%!important; }
.bk-search-dropdown .product-suggestion-list-item a { display:flex; align-items:center; gap:12px; padding:10px 14px; text-decoration:none; color:var(--bk-dark); transition:background .15s; }
.bk-search-dropdown .product-suggestion-list-item a:hover { background:var(--bk-cream); }
.bk-search-dropdown .product-suggestion-list-item .product-image { width:44px; height:44px; border-radius:var(--bk-radius-sm); overflow:hidden; flex-shrink:0; background:var(--bk-rose-light); }
.bk-search-dropdown .product-suggestion-list-item .product-image img { width:100%; height:100%; object-fit:cover; }
.bk-search-dropdown .product-suggestion-list-item .product-name { font-size:13px; font-weight:600; line-height:1.3; }
.bk-search-dropdown .product-suggestion-list-item .flash-price { font-size:12px; color:var(--bk-rose); font-weight:700; }
.bk-search-dropdown .product-suggestion-list-item .flash-old-prices { font-size:11px; color:var(--bk-muted); text-decoration:line-through; margin-left:4px; }
</style>
@php $promo = get_static_option('site_announcement_text'); @endphp
@if($promo)
<div class="bk-promo-bar">
    {!! $promo !!}
</div>
@endif

<nav class="bk-navbar">
    <div class="container">
        <div class="d-flex align-items-center gap-3">

            {{-- Logo / Brand --}}
            <div class="me-4">
                {!! theme_logo_html('bk-navbar-brand-link', 'bk-navbar-logo-img', 'bk-brand') !!}
            </div>

            {{-- Nav links (desktop) --}}
            <div class="d-none d-lg-flex me-auto" style="gap:0;">
                {!! theme_nav_menu(null, 'bk-nav-links', 'bk-nav-link') !!}
            </div>

            {{-- Search (desktop) --}}
            <div class="bk-search d-none d-md-flex me-2" style="position:relative;">
                <input type="text" id="search_form_input" autocomplete="off"
                       placeholder="{{ __('Search baked goods…') }}" value="{{ request('search') }}">
                <button type="button" onclick="bkDoSearch()"><i class="mdi mdi-magnify"></i></button>
                <ul class="product-suggestion-list bk-search-dropdown"></ul>
            </div>

            {{-- Icons --}}
            <div class="d-flex gap-2">
                {!! theme_wishlist_icon_html('bk-icon-btn', 'bk-badge') !!}
                <a href="{{ theme_compare_page_url() }}" class="bk-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="mdi mdi-compare-horizontal"></i>
                </a>
                {!! theme_cart_icon_html('bk-icon-btn', 'bk-badge') !!}
                {!! theme_account_icon_html('bk-icon-btn') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>

        </div>
    </div>
</nav>

<script>
(function () {
    var input    = document.getElementById('search_form_input');
    var dropdown = document.querySelector('.bk-search-dropdown');
    if (!input || !dropdown) return;

    var timer;
    input.addEventListener('keyup', function (e) {
        if (e.key === 'Enter') { bkDoSearch(); return; }
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
                        dropdown.innerHTML = '<li style="padding:12px 14px;font-size:13px;color:var(--bk-muted);">{{ __("No results found") }}</li>';
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

    window.bkDoSearch = function () {
        var q = input.value.trim();
        if (q) {
            window.location.href = '{{ theme_shop_url() }}' + '?search=' + encodeURIComponent(q);
        }
    };
})();
</script>
