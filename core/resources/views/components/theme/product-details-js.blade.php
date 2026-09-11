{{-- Use theme_product_js() helper instead of <x-theme.product-details-js/> --}}
@props(['product_inventory_set' => [], 'additional_info_store' => [], 'campaign_product' => null, 'campaign_active' => 0, 'product' => null])
<script>
    var attribute_store      = JSON.parse('{!! json_encode($product_inventory_set) !!}');
    var additional_info_store = JSON.parse('{!! json_encode($additional_info_store) !!}');
    var selected_variant     = '';

    /* ── Variant selection helpers ───────────────────────────────── */

    function get_selected_options() {
        var sel = {};
        $('.size-lists').each(function () {
            var li = $(this).find('li.active'), type = $(this).data('type'), val = li.data('displayValue');
            if (type && val !== undefined && String(val) !== 'undefined') sel[type] = String(val);
        });
        var ord = {}, keys = Object.keys(sel).sort();
        keys.forEach(function (k) { ord[k] = sel[k]; });
        return ord;
    }

    function getSelectionHash(s) { return MD5(JSON.stringify(s)); }

    function getAttributesForCart() {
        var s = get_selected_options(), h = getSelectionHash(s);
        if (additional_info_store[h]) return additional_info_store[h]['pid_id'];
        if (Object.keys(s).length) toastr.error('{{ __("Attribute not available") }}');
        return '';
    }

    function validateSelectedAttributes() {
        var s = get_selected_options(), h = getSelectionHash(s);
        if (attribute_store.length && Object.keys(attribute_store[0] || {}).length > 0) {
            if (!Object.keys(s).length) return false;
            if (!additional_info_store[h]) return false;
            return !!additional_info_store[h]['pid_id'];
        }
        return true;
    }

    function syncImage(s) {
        var h = getSelectionHash(s), el = $('#shop_details_gallery_slider .long-img img');
        var orig = el.parent().data('src');
        if (additional_info_store[h] && additional_info_store[h].image) {
            el.attr('src', additional_info_store[h].image);
            el.parent().attr('data-src', additional_info_store[h].image);
        } else {
            el.attr('src', orig);
            el.parent().attr('data-src', orig);
        }
    }

    function syncPrice(s) {
        var h = getSelectionHash(s), el = $('#price');
        var base = Number(String(el.data('mainPrice'))).toFixed(2), sym = el.data('currencySymbol');
        if (additional_info_store[h] && additional_info_store[h]['additional_price']) {
            el.text(sym + (Number(base) + Number(additional_info_store[h]['additional_price'])).toFixed(2));
        } else { el.text(sym + base); }
    }

    function syncStock(s) {
        var h = getSelectionHash(s), stockEl = $('#stock'), leftEl = $('#item_left');
        var qtyBtn = $('.quantity-btn'), qtyDiv = $('.product-quantity');
        qtyBtn.show(); qtyDiv.removeAttr('hidden');
        if (additional_info_store[h]) {
            var cnt = additional_info_store[h]['stock_count'];
            if (Number(cnt) > 0) {
                stockEl.html('<span class="text-success">{{ __("In Stock") }}</span>');
                leftEl.text('{{ __("Only!") }} ' + cnt + ' {{ __("Item Left!") }}').addClass('text-success').removeClass('text-danger');
            } else {
                qtyBtn.hide(); qtyDiv.attr('hidden', true);
                stockEl.html('<span class="text-danger">{{ __("Out of Stock") }}</span>');
                leftEl.text('{{ __("No Item Left!") }}').addClass('text-danger').removeClass('text-success');
            }
        } else {
            stockEl.html(stockEl.data('stock-text'));
            leftEl.html(leftEl.data('stock-text'));
        }
    }

    function selectedAttributeSearch() {
        var sel = {};
        $('.size-lists').each(function () {
            var li = $(this).find('li.active'), type = $(this).data('type'), val = li.data('displayValue');
            if (type && val !== undefined && String(val) !== 'undefined') sel[type] = String(val);
        });

        syncImage(get_selected_options()); syncPrice(get_selected_options()); syncStock(get_selected_options());

        // Disable all options first
        $('.size-lists li').addClass('disabled');

        // For each axis, enable values compatible with all OTHER axes' current selections.
        // Using only the OTHER axes as filters prevents over-constraining (the root cause of the bug
        // where selecting one combo disables all other valid options on that same axis).
        var allTypes = [];
        $('.size-lists').each(function () { var t = $(this).data('type'); if (t) allTypes.push(t); });

        allTypes.forEach(function (type) {
            var otherSel = {};
            Object.keys(sel).forEach(function (t) { if (t !== type) otherSel[t] = sel[t]; });

            var available = [];
            attribute_store.forEach(function (arr) {
                var matched = true;
                // Use String() on arr values: JSON column may store integers (e.g. 3 not "3")
                // while JS always reads data-attributes as strings, causing strict !== to fail.
                Object.keys(otherSel).forEach(function (t) { if (String(arr[t]) !== otherSel[t]) matched = false; });
                if (matched && arr[type] !== undefined && available.indexOf(String(arr[type])) === -1) {
                    available.push(String(arr[type]));
                }
            });

            available.forEach(function (val) {
                $('.size-lists[data-type="' + type + '"] li[data-display-value="' + val + '"]').removeClass('disabled');
            });
        });

        if (allTypes.length === 0) { $('.size-lists li').removeClass('disabled'); }

        selected_variant = sel;
    }

    function autoSelectUnpickedGroups(skipGroup) {
        var changed = false;
        $('.size-lists').not(skipGroup).each(function () {
            var active = $(this).find('li.active');
            if (!active.length || active.hasClass('disabled')) {
                var first = $(this).find('li:not(.disabled):first');
                if (first.length) {
                    first.addClass('active').siblings().removeClass('active');
                    first.parent().parent().find('input[type=text]').val(first.data('displayValue'));
                    first.parent().parent().find('input[type=hidden]').val(first.data('value'));
                    changed = true;
                }
            }
        });
        if (changed) { syncImage(get_selected_options()); syncPrice(get_selected_options()); syncStock(get_selected_options()); }
    }

    /* ── Event wiring ────────────────────────────────────────────── */

    (function ($) {
        $(document).ready(function () {

            $(document).on('click', '#login_submit_btn', function (e) {
                e.preventDefault();
                var el = $(this);
                var username = $('#login_form_order_page input[name=email]').val();
                var password = $('#login_form_order_page input[name=password]').val();
                var remember = $('#login_form_order_page input[name=remember]').val();
                el.text('{{ __("Please Wait") }}');
                $.ajax({
                    type: 'POST', url: '{{ theme_ajax_login_url() }}',
                    data: { _token: '{{ theme_csrf() }}', username: username, password: password, remember: remember },
                    success: function (data) {
                        if (data.status === 'invalid') { el.text('{{ __("Login") }}'); toastr.warning(data.msg); }
                        else { el.text('{{ __("Redirecting…") }}'); setTimeout(function () { location.reload(); }, 300); }
                    },
                    error: function (xhr) {
                        el.text('{{ __("Login") }}');
                        try { $.each(xhr.responseJSON.errors, function (k, v) { toastr.error(v); }); }
                        catch(e) { toastr.error('{{ __("An error occurred") }}'); }
                    }
                });
            });

            $(document).on('click', '.size-lists li', function () {
                if ($(this).hasClass('disabled')) return;
                var el = $(this), val = el.data('displayValue'), pw = el.parent().parent();
                el.addClass('active').siblings().removeClass('active');
                pw.find('input[type=text]').val(val);
                pw.find('input[type=hidden]').val(el.data('value'));
                selectedAttributeSearch();
                autoSelectUnpickedGroups(el.closest('.size-lists'));
            });


            // Auto-select first variant combination on page load
            if (attribute_store.length) {
                $('.size-lists').each(function () {
                    var first = $(this).find('li:first');
                    if (first.length) {
                        first.addClass('active').siblings().removeClass('active');
                        first.parent().parent().find('input[type=text]').val(first.data('displayValue'));
                        first.parent().parent().find('input[type=hidden]').val(first.data('value'));
                    }
                });
                selectedAttributeSearch();
            }
        });
    })(jQuery);

    /* ── Cart AJAX shared handler ────────────────────────────────── */

    function doCartAjax(url, extra) {
        var pid_id = getAttributesForCart();
        var data = Object.assign({
            product_id:      '{{ $product->id ?? "" }}',
            quantity:        Number($('#quantity').val().trim() || 1),
            pid_id:          pid_id,
            product_variant: pid_id,
            selected_size:   $('#selected_size').val() || '',
            selected_color:  $('#selected_color').val() || '',
            _token:          '{{ theme_csrf() }}'
        }, extra || {});
        if (!validateSelectedAttributes()) {
            toastr.error('{{ __("Select all attribute to proceed") }}'); return;
        }
        $.ajax({
            url: url, type: 'POST', data: data,
            success: function (d) {
                if (d.quantity_msg) { toastr.warning(d.quantity_msg); }
                else if (d.error_msg) { toastr.error(d.error_msg); }
                else if (d.type === 'success' && d.redirect) {
                    toastr.success(d.msg);
                    setTimeout(function () { location.href = d.redirect; }, 2000);
                } else {
                    toastr.success(d.msg);
                    var track = $('.track-icon-list');
                    track.hide(); track.load(location.href + ' .track-icon-list'); track.fadeIn();
                }
            },
            error: function (xhr) {
                var msg = '{{ __("An error occurred") }}';
                try { msg = xhr.responseJSON.message || msg; } catch (e) {}
                toastr.error(msg);
            }
        });
    }

    $(document).on('click', '.add_to_cart_single_page', function (e) {
        e.preventDefault();
        var hc = '{{ empty($campaign_product) ? 0 : 1 }}', ce = '{{ isset($campaign_active) ? $campaign_active : 0 }}';
        if (hc == 1 && ce == 0) { toastr.error('{{ __("This campaign has ended. You cannot add this product to your cart.") }}'); return; }
        doCartAjax('{{ theme_add_to_cart_url() }}');
    });

    $(document).on('click', '.add_to_wishlist_single_page', function (e) {
        e.preventDefault();
        var hc = '{{ empty($campaign_product) ? 0 : 1 }}', ce = '{{ isset($campaign_active) ? $campaign_active : 0 }}';
        if (hc == 1 && ce == 0) { toastr.error('{{ __("This campaign has ended. You cannot add this product to your cart.") }}'); return; }
        doCartAjax('{{ theme_add_to_wishlist_url() }}');
    });

    $(document).on('click', '.but_now_single_page', function (e) {
        e.preventDefault();
        var hc = '{{ empty($campaign_product) ? 0 : 1 }}', ce = '{{ isset($campaign_active) ? $campaign_active : 0 }}';
        if (hc == 1 && ce == 0) { toastr.error('{{ __("This campaign has ended. You cannot add this product to your cart.") }}'); return; }
        doCartAjax('{{ theme_buy_now_url() }}');
    });

    $(document).on('click', '.compare-btn', function (e) {
        e.preventDefault();
        doCartAjax('{{ theme_add_to_compare_url() }}');
    });
</script>
