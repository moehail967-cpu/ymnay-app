<!-- new product modal - start -->
<div class="modal product-quick-view-bg-color" id="product_quick_view" tabindex="-1" role="dialog"
     aria-labelledby="productModal"
     aria-hidden="true">
</div>

<!-- footer area start -->
@include('tenant.frontend.partials.widget-area')
<!-- footer area end -->

<!-- For Mobile nav start -->
@include('tenant.frontend.partials.mobile-footer-menu')
<!-- For Mobile nav end -->

<!-- back to top area start -->
@include('tenant.frontend.partials.backtop')
<!-- back to top area end -->

@php
    $theme_footer_css_files = \App\Facades\ThemeDataFacade::getFooterHookCssFiles();
@endphp
@foreach($theme_footer_css_files ?? [] as $cssFile)
    <link rel="stylesheet" href="{{ loadCss($cssFile) }}" type="text/css"/>
@endforeach

@php
    $loadCoreScript = loadCoreScript();
@endphp

@if(in_array('jquery-3.6.1.min', $loadCoreScript))
    <!-- jquery -->
    <script src="{{global_asset('assets/tenant/frontend/js/jquery-3.6.1.min.js')}}"></script>
@endif

@if(in_array('jquery-migrate-3.4.0.min', $loadCoreScript))
    <!-- jquery Migrate -->
    <script src="{{global_asset('assets/tenant/frontend/js/jquery-migrate-3.4.0.min.js')}}"></script>
@endif

@if(in_array('bootstrap.bundle.min', $loadCoreScript))
    <!-- bootstrap -->
    <script src="{{global_asset('assets/tenant/frontend/js/bootstrap.bundle.min.js')}}"></script>
@endif

@if(in_array('jquery.lazy.min', $loadCoreScript))
    <!-- Lazyload Js -->
    <script src="{{global_asset('assets/tenant/frontend/js/jquery.lazy.min.js')}}"></script>
@endif

@if(in_array('slick', $loadCoreScript))
    <!-- Slick Slider -->
    <script src="{{global_asset('assets/tenant/frontend/js/slick.js')}}"></script>
@endif

@if(in_array('odometer', $loadCoreScript))
    <!-- Odometer js -->
    <script src="{{global_asset('assets/tenant/frontend/js/odometer.js')}}"></script>
@endif

@if(in_array('viewport.jquery', $loadCoreScript))
    <!-- Viewport js -->
    <script src="{{global_asset('assets/tenant/frontend/js/viewport.jquery.js')}}"></script>
@endif

@if(in_array('wow', $loadCoreScript))
    <!-- All Plugins js -->
    <script src="{{global_asset('assets/tenant/frontend/js/wow.js')}}"></script>
@endif

@if(in_array('jquery.nice-select', $loadCoreScript))
    <!-- Nice Select Js -->
    <script src="{{global_asset('assets/tenant/frontend/js/jquery.nice-select.js')}}"></script>
@endif

@if(in_array('jquery.syotimer.min', $loadCoreScript))
    <!-- COuntdown Js -->
    <script src="{{global_asset('assets/tenant/frontend/js/jquery.syotimer.min.js')}}"></script>
@endif

@if(in_array('sweetalert2', $loadCoreScript))
    <!-- Sweet Alert -->
    <script src="{{global_asset('assets/landlord/common/js/sweetalert2.js')}}"></script>
@endif

@if(in_array('toastr.min', $loadCoreScript))
    <!-- Toastr -->
    <script src="{{global_asset('assets/common/js/toastr.min.js')}}"></script>
@endif

@if(in_array('jquery.nicescroll.min', $loadCoreScript))
    <!-- Nice Scroll -->
    <script src="{{global_asset('assets/tenant/frontend/js/jquery.nicescroll.min.js')}}"></script>
@endif

@if(in_array('nouislider-8.5.1.min', $loadCoreScript))
    <!-- Range Slider -->
    <script src="{{global_asset('assets/tenant/frontend/js/nouislider-8.5.1.min.js')}}"></script>
@endif

@if(in_array('custom-alert-message', $loadCoreScript))
    <script src="{{global_asset('assets/tenant/frontend/js/custom-alert-message.js')}}"></script>
@endif

@if(in_array('main', $loadCoreScript))
    <!-- main js -->
    <script src="{{global_asset('assets/tenant/frontend/js/main.js')}}"></script>
@endif

@if(in_array('star-rating.min', $loadCoreScript))
    <script src="{{global_asset('assets/common/js/star-rating.min.js')}}"></script>
@endif

@if(in_array('md5', $loadCoreScript))
    <script src="{{global_asset('assets/common/js/md5.js')}}"></script>
@endif

<script src="{{global_asset('assets/common/js/custom-sweetalert-two.js')}}"></script>

@include('landlord.frontend.partials.gdpr-cookie')

@php
    $theme_footer_js_files = \App\Facades\ThemeDataFacade::getFooterHookJsFiles();
@endphp
@foreach($theme_footer_js_files ?? [] as $jsFile)
    <script src="{{loadJs($jsFile)}}"></script>
@endforeach

<script src="{{global_asset('assets/tenant/frontend/js/digital-shop-common.js')}}"></script>

@php
    $tenant_id = !empty(tenant()) ? tenant()->id : '';
    $file = file_exists('assets/tenant/frontend/js/'.$tenant_id.'/dynamic-script.js');
@endphp
@if($file)
    <script src="{{global_asset('assets/tenant/frontend/js/'.$tenant_id.'/dynamic-script.js')}}"></script>
@endif

{!! \App\Facades\ThemeDataFacade::renderFooterHookBladeFile() !!}

<script>
    let site_currency_symbol = '{{ site_currency_symbol() }}';

    // ── Wishlist: guest localStorage + heart sync ─────────────────────────────
    var nazmart_is_logged_in = {{ auth('web')->check() ? 'true' : 'false' }};
    var nazmart_wl_key = 'nzwl_{{ tenant() ? tenant()->id : 0 }}';
    function wl_get() { try { return JSON.parse(localStorage.getItem(nazmart_wl_key) || '[]').map(Number); } catch(e) { return []; } }
    function wl_has(pid) { return wl_get().indexOf(parseInt(pid)) > -1; }
    function wl_add(pid) { var ids = wl_get(); pid = parseInt(pid); if (ids.indexOf(pid) === -1) { ids.push(pid); localStorage.setItem(nazmart_wl_key, JSON.stringify(ids)); } }
    function wl_del(pid) { localStorage.setItem(nazmart_wl_key, JSON.stringify(wl_get().filter(function(id){ return id !== parseInt(pid); }))); }
    // ─────────────────────────────────────────────────────────────────────────

    @if(in_array(tenant()->theme_slug, ["hexfashion","furnito","aromatic","electro"]))
    $('.theme-one-footer .col-lg-3').removeClass('col-lg-3').addClass('col-lg-4');
    $('.theme-two-footer .col-lg-3').removeClass('col-lg-3').addClass('col-lg-4');
    $('.theme-aromatic-footer .col-lg-3').removeClass('col-lg-3').addClass('col-lg-4');
    $('.theme-electro-footer .footer-top-contents .col-lg-3').removeClass('col-lg-3').addClass('col-lg-4');
    @endif
</script>

<x-custom-js.newsletter-store/>
<x-custom-js.contact-form-store/>
<x-custom-js.lang-change/>
<x-tenant-addon-custom-js/>
<x-custom-js.lazy-load-image/>

<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "2000",
        "extendedTimeOut": "2000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "slideDown",
        "hideMethod": "slideUp"
    };

    $(function () {
        $(document).on('keyup', '#search_form_input', function (e) {
            e.preventDefault();

            let search_text = $(this).val().trim();

            if (search_text.length < 1) {
                $('.product-suggestion-list').html('');
                $('#search_suggestions_wrap').removeClass('show');
                $('.search-suggestion-overlay').removeClass('show');
                return;
            }

            $.ajax({
                url: '{{ route("tenant.search.ajax") }}',
                type: 'GET',
                data: {
                    search: search_text
                },
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.product_object && data.product_object.length > 0) {
                        $('.product-suggestion-list').html(data.markup);
                        $('#search_suggestions_wrap').addClass('show');
                        $('.tc-search-results').addClass('show');
                        $('.vl-search-results').removeClass('d-none');
                        $('.search-suggestion-overlay').addClass('show');
                    } else {
                        $('.product-suggestion-list').html('');
                        $('#search_suggestions_wrap').removeClass('show');
                        $('.tc-search-results').removeClass('show');
                        $('.vl-search-results').addClass('d-none');
                        $('.search-suggestion-overlay').removeClass('show');
                    }
                }
            });
        });

        $(document).on('click', '.search-suggestion-overlay, .suggetions-icon-close', function () {
            $('.product-suggestion-list').html('');
            $('#search_suggestions_wrap').removeClass('show');
            $('.tc-search-results').removeClass('show');
            $('.vl-search-results').addClass('d-none');
            $('.search-suggestion-overlay').removeClass('show');
        });
        // Compare Product
        $(document).on('click', '.quick-view-compare-btn', function (e) {
            e.preventDefault();

            let quick_view_has_campaign = '{{empty($campaign_product) ? 0 : 1}}';
            let quick_view_campaign_expired = '{{isset($campaign_active) ? $campaign_active : 0}}';

            if (quick_view_has_campaign === 1) {
                if (quick_view_campaign_expired === 0) {
                    toastr.error('{{ __('This campaign has ended. You cannot add this product to your cart.') }}');
                    return false;
                }
            }

            let selected_size = $('#selected_size').val();
            let selected_color = $('#selected_color').val();

            let pid_id = getQuickViewAttributesForCart();

            let product_id = quick_view_product_id;
            let quantity = Number($('#quick-view-quantity').val().trim());
            let price = $('#price').text().split(site_currency_symbol)[1];
            let attributes = {};
            let product_variant = pid_id;
            let productAttribute = quick_view_selected_variant;

            attributes['price'] = price;

            // if selected attribute is a valid product item
            if (quickViewValidateSelectedAttributes()) {
                $.ajax({
                    url: '{{ route("tenant.shop.product.add.to.compare.ajax") }}',
                    type: 'POST',
                    data: {
                        product_id: product_id,
                        quantity: quantity,
                        pid_id: pid_id,
                        product_variant: product_variant,
                        selected_size: selected_size,
                        selected_color: selected_color,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function () {

                    },
                    success: function (data) {
                        if (data.quantity_msg) {
                            toastr.warning(data.quantity_msg);
                        } else if (data.error_msg) {
                            toastr.error(data.error_msg);
                        } else {
                            toastr.success(data.msg, '{{__('Go to Cart')}}', '#', 60000);
                            $('.track-icon-list').load(location.href + " .track-icon-list");
                        }
                    },
                    error: function (err) {
                        toastr.error('{{ __("An error occurred") }}')
                    }
                });
            } else {
                toastr.error('{{ __("Select all attribute to proceed") }}')
            }
        });
        $(document).on('click', '.compare-btn', function (e) {
            e.preventDefault();

            let pid_id = null;
            let product_id = $(this).attr("data-product_id") || $(this).data("product_id") || $(this).data("id");
            let quantity = 1;
            let product_variant = null;

            $.ajax({
                url: '{{ route("tenant.shop.product.add.to.compare.ajax") }}',
                type: 'POST',
                data: {
                    product_id: product_id,
                    quantity: quantity,
                    pid_id: pid_id,
                    product_variant: product_variant,
                    selected_size: null,
                    selected_color: null,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.quantity_msg) {
                        toastr.warning(data.quantity_msg);
                    } else if (data.error_msg) {
                        toastr.error(data.error_msg);
                    } else {
                        toastr.success(data.msg, '{{__('Go to Cart')}}', '#', 60000);
                        $('.track-icon-list').load(location.href + " .track-icon-list");
                    }
                },
                error: function (err) {
                    toastr.error('{{ __("An error occurred") }}')
                }
            });

        });
        // for add cart
        $(document).on('click', '.add-to-cart-btn', function (e) {
            e.preventDefault();

            let pid_id = null;
            let product_id = $(this).attr("data-product_id") || $(this).data("product_id") || $(this).data("id");
            let quantity = 1;
            let product_variant = null;

            $.ajax({
                url: '{{ route("tenant.shop.product.add.to.cart.ajax") }}',
                type: 'POST',
                data: {
                    product_id: product_id,
                    quantity: quantity,
                    pid_id: pid_id,
                    product_variant: product_variant,
                    selected_size: null,
                    selected_color: null,
                    _token: '{{ csrf_token() }}'
                },
                success: function (data) {
                    if (data.quantity_msg) {
                        toastr.warning(data.quantity_msg);
                    } else if (data.error_msg) {
                        toastr.error(data.error_msg);
                    } else {
                        toastr.success(data.msg, '{{__('Go to Cart')}}', '#', 60000);
                        if (data.count !== undefined) { cartUpdateBadge(data.count); }
                        $('.track-icon-list').load(location.href + " .track-icon-list");
                    }
                },
                error: function (err) {
                    toastr.error('{{ __("An error occurred") }}')
                }
            });

        });
        // ── Cart navbar badge updater ─────────────────────────────────────────
        function cartUpdateBadge(count) {
            var badge = $('.theme-cart-count');
            if (!badge.length) return;
            if (count > 0) {
                badge.text(count > 99 ? '99+' : count).show();
            } else {
                badge.hide();
            }
        }

        // ── Wishlist navbar badge updater ─────────────────────────────────────
        function wlUpdateBadge(count) {
            var badge = $('.theme-wishlist-count');
            if (!badge.length) return;
            if (count > 0) {
                badge.text(count > 99 ? '99+' : count).show();
            } else {
                badge.text('0').hide();
            }
        }

        // ── Wishlist icon helper — works for all themes ───────────────────────
        // Handles: lar/las (Line Awesome), mdi-heart-outline/mdi-heart (MDI)
        // Uses inline style for color so theme CSS cannot override it
        function wlMarkBtn(btn, active) {
            btn.toggleClass('wl-added', active);
            var icon = btn.find('i').first();
            if (active) {
                // inline style wins over any theme CSS including !important cascade
                btn.css('color', '#e11d48');
                if (icon.length) icon.css('color', '#e11d48');
            } else {
                btn.css('color', '');
                if (icon.length) icon.css('color', '');
            }
            if (!icon.length) return;
            if (icon.hasClass('mdi')) {
                icon.toggleClass('mdi-heart-outline', !active).toggleClass('mdi-heart', active);
            } else {
                // Line Awesome — lar = outline, las = solid
                icon.toggleClass('lar', !active).toggleClass('las', active);
            }
        }

        // for add/remove wishlist (toggle)
        $(document).on('click', '.add-to-wishlist-btn', function (e) {
            e.preventDefault();

            let btn = $(this);
            let product_id = btn.attr('data-product_id') || btn.data('product_id') || btn.data("id");
            if (!product_id) { toastr.error("{{ __('Product ID not found!') }}"); return; }
            product_id = parseInt(product_id);
            let isAdded = btn.hasClass('wl-added');

            // Guest path — toggle localStorage
            if (!nazmart_is_logged_in) {
                if (isAdded) {
                    wl_del(product_id);
                    wlMarkBtn(btn, false);
                    toastr.info('{{ __("Removed from wishlist") }}');
                } else {
                    wl_add(product_id);
                    wlMarkBtn(btn, true);
                    toastr.success('{{ __("Added to wishlist. Login to save permanently.") }}');
                }
                return;
            }

            // Logged-in: toggle via server
            if (isAdded) {
                // Remove from wishlist
                $.ajax({
                    url: '{{ route("tenant.shop.wishlist.remove.by.product") }}',
                    type: 'POST',
                    dataType: 'json',
                    data: { product_id: product_id, _token: '{{ csrf_token() }}' },
                    success: function (data) {
                        wlMarkBtn(btn, false);
                        wl_del(product_id);
                        wlUpdateBadge(data.count !== undefined ? data.count : Math.max(0, parseInt($('.theme-wishlist-count').text() || '0') - 1));
                        toastr.info(data.msg || '{{ __("Removed from wishlist") }}');
                        $('.track-icon-list').load(location.href + " .track-icon-list");
                    },
                    error: function () { toastr.error('{{ __("An error occurred") }}'); }
                });
            } else {
                // Add to wishlist
                $.ajax({
                    url: '{{ route("tenant.shop.product.add.to.wishlist.ajax") }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        product_id: product_id, quantity: 1,
                        pid_id: null, product_variant: null,
                        selected_size: null, selected_color: null,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        if (data.quantity_msg) {
                            toastr.warning(data.quantity_msg);
                        } else if (data.error_msg) {
                            toastr.error(data.error_msg);
                        } else {
                            wlMarkBtn(btn, true);
                            wl_add(product_id);
                            wlUpdateBadge(data.count !== undefined ? data.count : parseInt($('.theme-wishlist-count').text() || '0') + 1);
                            toastr.success(data.msg || '{{ __("Added to wishlist") }}');
                            $('.track-icon-list').load(location.href + " .track-icon-list");
                        }
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON?.message || '{{ __("An error occurred") }}');
                    }
                });
            }
        });

        // Heart sync on page load — fill hearts for already-wishlisted products
        (function syncWishlistHearts() {
            function applyHearts(pids) {
                pids.forEach(function(pid) {
                    $('.add-to-wishlist-btn[data-product_id="'+pid+'"], .add-to-wishlist-btn[data-id="'+pid+'"]')
                        .each(function() { wlMarkBtn($(this), true); });
                });
            }
            @if(auth('web')->check())
            $.getJSON('{{ route("tenant.shop.wishlist.ids") }}', function(res) {
                if (!res.ids || !res.ids.length) return;
                res.ids.forEach(function(pid) { wl_add(pid); }); // keep localStorage in sync
                applyHearts(res.ids);
            });
            @else
            applyHearts(wl_get());
            @endif
        })();

        // Merge guest wishlist into server wishlist after login
        @if(auth('web')->check())
        (function mergeGuestWishlist() {
            var ids = wl_get();
            if (!ids.length) return;
            $.ajax({
                url: '{{ route("tenant.shop.wishlist.merge") }}',
                type: 'POST',
                data: { ids: ids, _token: '{{ csrf_token() }}' },
                success: function() {
                    localStorage.removeItem(nazmart_wl_key);
                }
            });
        })();
        @endif

        // Save for later (cart → wishlist) — handler for JS-injected buttons
        $(document).on('click', '.save-for-later-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            var row = btn.closest('tr');
            var row_id = btn.data('row_id');
            $.ajax({
                url: '{{ route("tenant.shop.cart.save.for.later") }}',
                type: 'POST',
                data: { row_id: row_id, _token: '{{ csrf_token() }}' },
                success: function(data) {
                    if (data.type === 'success') {
                        toastr.success(data.msg);
                        row.slideUp(200, function() { $(this).remove(); });
                        $('.track-icon-list').load(location.href + " .track-icon-list");
                    } else {
                        toastr.error(data.msg);
                    }
                },
                error: function() { toastr.error('{{ __("An error occurred") }}'); }
            });
        });

        // ── Wishlist page enhancements (stock badge + label fix) ─────────────
        // ── Cart page enhancement (Save for Later button) ─────────────────────
        (function wishlistCartEnhancements() {
            var isWishlist = $('body').find('.close-table-wishlist').length > 0;
            var isCart     = $('body').find('.close-table-cart').length > 0;

            // ── CART: inject "Save for Later" heart button beside each remove btn
            if (isCart) {
                @if(auth('web')->check())
                $('.close-table-cart').each(function() {
                    var td = $(this).closest('td');
                    var row_id = td.data('product_hash_id');
                    if (!row_id || td.find('.save-for-later-btn').length) return;
                    var btn = $('<button class="save-for-later-btn btn-save-later" title="{{ __("Save for Later") }}"><i class="lar la-heart"></i></button>');
                    btn.data('row_id', row_id);
                    var inner = td.find('.mc-cart-action-inner');
                    if (inner.length) { inner.prepend(btn); } else { td.prepend(btn); }
                });
                @endif
            }

            // ── WISHLIST: inject stock badges + change move-to-wishlist label
            if (isWishlist) {
                // Collect product IDs from wishlist rows
                var pids = [];
                $('tr.table-cart-row').each(function() {
                    var pid = $(this).data('product-id');
                    if (pid) pids.push(parseInt(pid));
                });
                if (!pids.length) return;

                $.getJSON('{{ route("tenant.shop.product.stock.status") }}', { ids: pids.join(',') }, function(res) {
                    var stock = res.stock || {};
                    $('tr.table-cart-row').each(function() {
                        var row = $(this);
                        var pid = parseInt(row.data('product-id'));
                        var inStock = stock[pid] !== undefined ? stock[pid] : true;

                        // Inject badge after product name
                        var nameCell = row.find('.carts-contents .name-title, .carts-contents a').first();
                        if (nameCell.length && !nameCell.next('.wl-stock-badge').length) {
                            var cls = inStock ? 'wl-in-stock' : 'wl-out-stock';
                            var label = inStock ? '{{ __("In Stock") }}' : '{{ __("Out of Stock") }}';
                            nameCell.after('<span class="wl-stock-badge ' + cls + '">' + label + '</span>');
                        }

                        // Disable move-to-wishlist btn if out of stock
                        var moveBtn = row.find('.move-to-wishlist');
                        if (!inStock) {
                            moveBtn.addClass('wl-atc-disabled').attr('title', '{{ __("Out of Stock") }}');
                            moveBtn.off('click.wlstock').on('click.wlstock', function(e) { e.stopImmediatePropagation(); toastr.warning('{{ __("This product is out of stock") }}'); });
                        } else {
                            moveBtn.attr('title', '{{ __("Add to Cart") }}');
                        }
                    });
                });
            }
        })();
        //for remmove wishlist
        $(document).on('click', '.single-addto-carts .close-cart', function (e) {
            e.preventDefault();

            let button = $(this);
            let rowId = button.data('id');
            let instance = button.data('instance');

            $.ajax({
                url: "{{ route('tenant.ajax.remove.cart.item') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    row_id: rowId,
                    instance: instance
                },
                success: function (response) {
                    if (response.success) {

                        // remove the item visually
                        button.closest('.single-addto-carts').slideUp(200, function () {
                            $(this).remove();
                        });

                        // update the visible count in navbar for that instance
                        if (instance === 'wishlist') {
                            $('.nav-right-content .cart-shopping .icon-notification')
                                .filter(function () {
                                    return $(this).prev('.icon').find('i').hasClass('lar') && $(this).prev('.icon').find('i').hasClass('la-heart');
                                })
                                .text(response.count);
                        } else if (instance === 'default') {
                            $('.nav-right-content .cart-shopping .icon-notification')
                                .filter(function () {
                                    return $(this).prev('.icon').find('i').hasClass('las') && $(this).prev('.icon').find('i').hasClass('la-shopping-cart');
                                })
                                .text(response.count);
                        }

                        // update dropdown area message if empty
                        if (response.count === 0) {
                            button.closest('.single-addto-cart-wrappers')
                                .html('<p class="text-center">{{ __("No Item in Wishlist") }}</p>');
                        }
                    }
                },
                error: function () {
                    location.reload(); // fallback if AJAX fails
                }
            });
        });

        $(document).on('click', '.digital-add-to-cart-btn', function (e) {
            e.preventDefault();

            let product_id = $(this).attr("data-product_id") || $(this).data("product_id") || $(this).data("id");

            $.ajax({
                url: '{{ route("tenant.digital.shop.product.add.to.cart.ajax") }}',
                type: 'POST',
                data: {
                    product_id: product_id,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.quantity_msg) {
                        toastr.warning(data.quantity_msg);
                    } else if (data.error_msg) {
                        toastr.error(data.error_msg);
                    } else {
                        toastr.success(data.msg, '{{__('Go to Cart')}}', '#', 60000);
                        let track_icon_list = $('.track-icon-list');
                        track_icon_list.hide();
                        track_icon_list.load(location.href + " .track-icon-list");
                        track_icon_list.fadeIn();
                    }
                },
                error: function (err) {
                    toastr.error('{{ __("An error occurred") }}')
                }
            });
        });

        function storeIntoSession(product_id) {
            let arrItem = [];

            if (sessionStorage.length === 0) {
                sessionStorage.setItem('products', product_id);
            } else {
                arrItem.push(sessionStorage.getItem('products'));
                arrItem.push(product_id);
                sessionStorage.setItem('products', arrItem);
            }

            return sessionStorage.getItem('products');
        }
    });

    $(document).on('click', '.social_share_parent', function (e) {
        $('.social_share_wrapper_item').toggleClass('show');
    });

    $('body').on('click', '.quick-view-size-lists li', function (event) {
        let el = $(this);
        let value = el.data('displayValue');
        let parentWrap = el.parent().parent();
        el.addClass('active');
        el.siblings().removeClass('active');
        parentWrap.find('input[type=text]').val(value);
        parentWrap.find('input[type=hidden]').val(el.data('value'));

        // selected attributes
        selectedAttributeSearch(this);
    });

    function selectedAttributeSearch(selected_item) {
        /*
        * search based on all selected attributes
        *
        * 1. get all selected attributes in {key:value} format
        * 2. search in attribute_store for all available matches
        * 3. display available matches (keep available matches selectable, and rest as disabled)
        * */

        let available_variant_types = [];
        let selected_options = {};

        $('.quick-view-size-lists li').addClass('disabled');

        // get all selected attributes in {key:value} format
        quick_view_available_options.map(function (k, option) {
            let selected_option = $(option).find('li.active');
            let type = selected_option.closest('.quick-view-size-lists').data('type');
            let value = selected_option.data('displayValue');

            if (type) {
                available_variant_types.push(type);
            }

            if (type && value) {
                selected_options[type] = value;
            }
        });

        quickViewSyncImage(get_quick_view_selected_options());
        quickViewSyncPrice(get_quick_view_selected_options());
        quickViewSyncStock(get_quick_view_selected_options());

        // search in attribute_store for all available matches
        let available_variants_selection = [];
        let selected_attributes_by_type = {};
        quick_view_attribute_store.map(function (arr) {
            let matched = true;

            Object.keys(selected_options).map(function (type) {

                if (arr[type] !== selected_options[type]) {
                    matched = false;
                }
            })

            if (matched) {
                available_variants_selection.push(arr);

                // insert as {key: [value, value...]}
                Object.keys(arr).map(function (type) {
                    // not array available for the given key
                    if (!selected_attributes_by_type[type]) {
                        selected_attributes_by_type[type] = []
                    }

                    // insert value if not inserted yet
                    if (selected_attributes_by_type[type].indexOf(arr[type]) <= -1) {
                        selected_attributes_by_type[type].push(arr[type]);
                    }
                })
            }

            window.quick_view_selected_variant = selected_attributes_by_type;
        });

        // selected item not contain product then de-select all selected option hare
        if (Object.keys(selected_attributes_by_type).length == 0) {
            $('.quick-view-size-lists li.active').each(function () {
                let sizeItem = $(this).parent().parent();

                sizeItem.find('input[type=hidden]').val('');
                sizeItem.find('input[type=text]').val('');
            });

            $('.quick-view-size-lists li.active').removeClass("active");
            $('.quick-view-size-lists li.disabled').removeClass("disabled");

            let el = $(selected_item);
            let value = el.data('displayValue');
            let parentWrap = el.parent().parent();

            el.addClass("active");
            el.siblings().removeClass('active');

            selectedAttributeSearch();

            parentWrap.find('input[type=text]').val(value);
            parentWrap.find('input[type=hidden]').val(el.data('value'));
        }

        // keep only available matches selectable
        Object.keys(selected_attributes_by_type).map(function (type) {
            // initially, disable all buttons
            $('.quick-view-size-lists[data-type="' + type + '"] li').addClass('disabled');

            // make buttons selectable for the available options
            selected_attributes_by_type[type].map(function (value) {
                let available_buttons = $('.quick-view-size-lists[data-type="' + type + '"] li[data-display-value="' + value + '"]');
                available_buttons.map(function (key, el) {
                    $(el).removeClass('disabled');
                })
            })
        });
        // todo check is empty object
        // selected_attributes_by_type
    }

    function quickViewSyncImage(selected_options) {
        //todo fire when attribute changed
        let hashed_key = getQuickViewSelectionHash(selected_options);

        let product_image_el = $('.quick-view-long-img img');

        let img_original_src = product_image_el.parent().data('src');

        // if selection has any image to it
        if (quick_view_additional_info_store[hashed_key]) {
            let attribute_image = quick_view_additional_info_store[hashed_key].image;
            if (attribute_image) {
                product_image_el.attr('src', attribute_image);
            }
        } else {
            product_image_el.attr('src', img_original_src);
        }
    }

    function quickViewSyncPrice(selected_options) {
        let hashed_key = getQuickViewSelectionHash(selected_options);

        let product_price_el = $('#quick-view-price');
        let product_main_price = Number(String(product_price_el.data('mainPrice'))).toFixed(2);
        let site_currency_symbol = product_price_el.data('currencySymbol');

        // if selection has any additional price to it
        if (quick_view_additional_info_store[hashed_key]) {
            let attribute_price = quick_view_additional_info_store[hashed_key]['additional_price'];
            if (attribute_price) {
                let price = Number(product_main_price) + Number(attribute_price);
                product_price_el.text(site_currency_symbol + Number(price).toFixed(2));
            } else {
                product_price_el.text(site_currency_symbol + product_main_price);
            }
        } else {
            product_price_el.text(site_currency_symbol + product_main_price);
        }
    }

    function quickViewSyncStock(selected_options) {
        let hashed_key = getQuickViewSelectionHash(selected_options);
        let product_stock_el = $('#quick_view_stock');
        let product_item_left_el = $('#quick_view_item_left');

        // if selection has any size and color to it

        if (quick_view_additional_info_store[hashed_key]) {
            let stock_count = quick_view_additional_info_store[hashed_key]['stock_count'];

            let stock_message = '';
            if (Number(stock_count) > 0) {
                stock_message = `<span class="text-success">{{__('In Stock')}}</span>`;
                product_item_left_el.text(`Only! ${stock_count} Item Left!`);
                product_item_left_el.addClass('text-success');
                product_item_left_el.removeClass('text-danger');
            } else {
                stock_message = `<span class="text-danger">{{__('Our fo Stock')}}</span>`;
                product_item_left_el.text(`No Item Left!`);
                product_item_left_el.addClass('text-danger');
                product_item_left_el.removeClass('text-success');
            }

            product_stock_el.html(stock_message);
        } else {
            product_stock_el.html(product_stock_el.data("stock-text"))
            product_item_left_el.html(product_item_left_el.data("stock-text"))
        }
    }

    function attributeSelected() {
        let total_options_count = $('.quick-view-size-lists').length;
        let selected_options_count = $('.quick-view-size-lists li.active').length;
        return total_options_count === selected_options_count;
    }

    function addslashes(str) {
        return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
    }

    function getQuickViewSelectionHash(selected_options) {
        return MD5(JSON.stringify(selected_options));
    }

    function get_quick_view_selected_options() {
        let selected_options = {};
        var quick_view_available_options = $('.quick-view-value-input-area');
        // get all selected attributes in {key:value} format
        quick_view_available_options.map(function (k, option) {
            let selected_option = $(option).find('li.active');
            let type = selected_option.closest('.quick-view-size-lists').data('type');
            let value = selected_option.data('displayValue');

            if (type && value) {
                selected_options[type] = value;
            }
        });

        let ordered_data = {};
        let selected_options_keys = Object.keys(selected_options).sort();
        selected_options_keys.map(function (e) {
            ordered_data[e] = selected_options[e];
        });

        return ordered_data;
    }

    function getQuickViewAttributesForCart() {
        let selected_options = get_quick_view_selected_options();
        let cart_selected_options = selected_options;
        let hashed_key = getQuickViewSelectionHash(selected_options);

        // if selected attribute set is available
        if (quick_view_additional_info_store[hashed_key]) {
            return quick_view_additional_info_store[hashed_key]['pid_id'];
        }

        // if selected attribute set is not available
        if (Object.keys(selected_options).length) {
            toastr.error('{{ __("Attribute not available") }}')
        }

        return '';
    }

    function quickViewValidateSelectedAttributes() {
        let selected_options = get_quick_view_selected_options();
        let hashed_key = getQuickViewSelectionHash(selected_options);

        // validate if product has any attribute
        if (quick_view_attribute_store.length) {
            if (!Object.keys(selected_options).length) {
                return false;
            }

            if (!quick_view_additional_info_store[hashed_key]) {
                return false;
            }

            return !!quick_view_additional_info_store[hashed_key]['pid_id'];
        }

        return true;
    }

    $(document).on('click', '.quick_view_add_to_cart', function (e) {
        // alert('ghgh');
        e.preventDefault();

        let selected_size = $('#selected_size').val();
        let selected_color = $('#selected_color').val();

        let pid_id = getQuickViewAttributesForCart();

        let product_id = quick_view_product_id;
        // console.log(product_id);
        let quantity = Number($('#quick-view-quantity').val().trim());
        let price = $('#price').text().split(site_currency_symbol)[1];
        let attributes = {};
        let product_variant = pid_id;
        let productAttribute = quick_view_selected_variant;

        attributes['price'] = price;

        // if selected attribute is a valid product item
        if (quickViewValidateSelectedAttributes()) {
            $.ajax({
                url: '{{ route("tenant.shop.product.add.to.cart.ajax") }}',
                type: 'POST',
                data: {
                    product_id: product_id,
                    quantity: quantity,
                    pid_id: pid_id,
                    product_variant: product_variant,
                    selected_size: selected_size,
                    selected_color: selected_color,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.quantity_msg) {
                        toastr.warning(data.quantity_msg);
                    } else if (data.error_msg) {
                        toastr.error(data.error_msg);
                    } else {
                        toastr.success(data.msg, '{{__('Go to Cart')}}', '#', 60000);
                        let track_icon_list = $('.track-icon-list');
                        track_icon_list.hide();
                        track_icon_list.load(location.href + " .track-icon-list");
                        track_icon_list.fadeIn();
                    }
                },
                error: function (err) {
                    toastr.error('{{ __("An error occurred") }}')
                }
            });
        } else {
            toastr.error('{{ __("Select all attribute to proceed") }}')
        }
    });

    $(document).on('click', '.quick_view_add_to_wishlist', function (e) {
        e.preventDefault();
        // alert('dfdf');

        let quick_view_has_campaign = '{{empty($campaign_product) ? 0 : 1}}';
        let quick_view_campaign_expired = '{{isset($campaign_active) ? $campaign_active : 0}}';

        if (quick_view_has_campaign === 1) {
            if (quick_view_campaign_expired === 0) {
                toastr.error('This campaign has ended. You cannot add this product to your cart.');
                return false;
            }
        }

        let selected_size = $('#selected_size').val();
        let selected_color = $('#selected_color').val();

        let pid_id = getQuickViewAttributesForCart();

        let product_id = quick_view_product_id;
        let quantity = Number($('#quick-view-quantity').val().trim());
        let price = $('#price').text().split(site_currency_symbol)[1];
        let attributes = {};
        let product_variant = pid_id;
        let productAttribute = quick_view_selected_variant;

        attributes['price'] = price;

        // if selected attribute is a valid product item
        if (quickViewValidateSelectedAttributes()) {
            $.ajax({
                url: '{{ route("tenant.shop.product.add.to.wishlist.ajax") }}',
                type: 'POST',
                data: {
                    product_id: product_id,
                    quantity: quantity,
                    pid_id: pid_id,
                    product_variant: product_variant,
                    selected_size: selected_size,
                    selected_color: selected_color,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.quantity_msg) {
                        toastr.warning(data.quantity_msg);
                    } else if (data.error_msg) {
                        toastr.error(data.error_msg);
                    } else {
                        toastr.success(data.msg, '{{__('Go to Cart')}}', '#', 60000);
                        $('.track-icon-list').load(location.href + " .track-icon-list");
                    }
                },
                error: function (err) {
                    toastr.error('{{ __("An error occurred") }}')
                }
            });
        } else {
            toastr.error('{{ __("Select all attribute to proceed") }}')
        }
    });

    $(document).on('click', '.quick_view_but_now', function (e) {
        e.preventDefault();

        let selected_size = $('#selected_size').val();
        let selected_color = $('#selected_color').val();

        let pid_id = getQuickViewAttributesForCart();

        let product_id = quick_view_product_id;
        let quantity = Number($('#quick-view-quantity').val().trim());
        let price = $('#price').text().split(site_currency_symbol)[1];
        let attributes = {};
        let product_variant = pid_id;
        let productAttribute = quick_view_selected_variant;

        attributes['price'] = price;

        // if selected attribute is a valid product item
        if (quickViewValidateSelectedAttributes()) {
            $.ajax({
                url: '{{ route("tenant.shop.product.add.to.cart.ajax") }}',
                type: 'POST',
                data: {
                    product_id: product_id,
                    quantity: quantity,
                    pid_id: pid_id,
                    product_variant: product_variant,
                    selected_size: selected_size,
                    selected_color: selected_color,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.quantity_msg) {
                        toastr.warning(data.quantity_msg);
                    } else if (data.error_msg) {
                        toastr.error(data.error_msg);
                    } else {
                        toastr.success(data.msg, '{{__('Go to Cart')}}', '#', 60000);
                        $('.track-icon-list').load(location.href + " .track-icon-list");
                    }

                    setTimeout(() => {
                        location.href = "{{ route('tenant.shop.checkout') }}";
                    }, 2000)
                },
                error: function (err) {
                    toastr.error('{{ __("An error occurred") }}')
                }
            });
        } else {
            toastr.error('{{ __("Select all attribute to proceed") }}')
        }
    });

    /* ========================================
                Product Quantity JS
    ========================================*/

    $(document).on('click', '.quick-view-plus', function () {
        var selectedInput = $(this).prev('.quick-view-quantity-input');
        if (selectedInput.val()) {
            selectedInput[0].stepUp(1);
        }
    });

    $(document).on('click', '.quick-view-substract', function () {
        var selectedInput = $(this).next('.quick-view-quantity-input');
        if (selectedInput.val() > 1) {
            selectedInput[0].stepDown(1);
        }
    });
</script>

@include("components.tenant.product.quick-view-js")
@yield('scripts')
@pluginFrontendScripts

{!! get_static_option('site_third_party_tracking_code') !!}

{!! renderBodyEndHooks() !!}
</body>
</html>
