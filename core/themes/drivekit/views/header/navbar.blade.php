{{-- DriveKit: Navbar --}}
<nav class="dk-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between gap-4">

            {{-- Logo --}}
            <div class="dk-navbar-logo">
                {!! theme_logo_html('dk-navbar-brand-link', 'dk-navbar-logo-img', 'dk-navbar-brand') !!}
            </div>

            {{-- Nav links (desktop) --}}
            <div class="dk-nav-links d-none d-lg-flex">
                {!! theme_nav_menu(null, 'dk-nav-menu') !!}
            </div>

            {{-- Icons --}}
            <div class="d-flex align-items-center gap-3">
                <button class="dk-icon-btn dk-search-trigger" aria-label="{{ __('Search') }}" type="button">
                    <i class="mdi mdi-magnify"></i>
                </button>
                {!! theme_wishlist_icon_html('dk-icon-btn', 'dk-icon-badge', 'mdi mdi-heart-outline') !!}
                <a href="{{ theme_compare_page_url() }}" class="dk-icon-btn" aria-label="{{ __('Compare') }}">
                    <i class="mdi mdi-compare-horizontal"></i>
                </a>
                {!! theme_cart_icon_html('dk-icon-btn', 'dk-icon-badge', 'mdi mdi-cart-outline') !!}
                {!! theme_account_icon_html('dk-icon-btn', 'mdi mdi-account-outline') !!}
                {!! renderNavbarIconsHooks() !!}
            </div>

            {{-- Mobile toggle --}}
            <button class="dk-nav-toggle d-lg-none" aria-label="{{ __('Menu') }}">
                <i class="mdi mdi-menu"></i>
            </button>

        </div>
    </div>
</nav>

{{-- Search Overlay --}}
<div class="dk-search-overlay" id="dk_search_overlay">
    <div class="dk-search-box">
        <button class="dk-search-box-close" id="dk_search_close" type="button"><i class="mdi mdi-close"></i></button>
        <div style="font-family:var(--dk-font-head);font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:2px;color:var(--dk-silver);margin-bottom:14px;">
            <i class="mdi mdi-magnify" style="color:var(--dk-red);"></i> {{ __('Search Products') }}
        </div>
        <div class="dk-search-field">
            <input autocomplete="off" id="search_form_input" type="text" placeholder="{{ __('Enter product name, SKU or keyword…') }}">
            <button type="button"><i class="mdi mdi-magnify"></i></button>
        </div>
        <div class="dk-search-results">
            <ul class="product-suggestion-list"></ul>
        </div>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('dk_search_overlay');
    var input   = document.getElementById('search_form_input');
    document.querySelector('.dk-search-trigger').addEventListener('click', function () {
        overlay.classList.add('active');
        setTimeout(function () { input.focus(); }, 100);
    });
    document.getElementById('dk_search_close').addEventListener('click', function () {
        overlay.classList.remove('active');
    });
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) overlay.classList.remove('active');
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') overlay.classList.remove('active');
    });
})();
</script>
