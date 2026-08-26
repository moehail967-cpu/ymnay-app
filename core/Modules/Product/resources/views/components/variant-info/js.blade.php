<script>
(function ($) {
    'use strict';

    // ── Translations for JS strings ──────────────────────────────────────────
    var _t = {
        color:  '{{ __("Color") }}',
        size:   '{{ __("Size") }}',
        price:  '{{ __("Price") }}',
        cost:   '{{ __("Cost") }}',
        stock:  '{{ __("Stock") }}',
        sku:    '{{ __("SKU") }}',
        image:  '{{ __("Image") }}',
        pick:   '{{ __("Pick") }}',
        change: '{{ __("Change") }}',
        none:   '—',
        values: '{{ __("Values") }}',
    };

    // ── SKU auto-generator ────────────────────────────────────────────────────
    function autoSku(allAxes, combo) {
        var parts = [];
        allAxes.forEach(function (ax, i) {
            parts.push(abbrevSku(combo[i].name));
        });
        return parts.join('-').toUpperCase();
    }
    function abbrevSku(str) {
        str = String(str || '').trim();
        if (!str) return '';
        if (str.length <= 4) return str.toUpperCase();
        // Keep first char, strip interior vowels, cap at 4 chars
        var first = str[0].toUpperCase();
        var rest  = str.slice(1).replace(/[aeiouAEIOU]/g, '').toUpperCase();
        return (first + rest).slice(0, 4);
    }

    // ── Pre-seed map from existing variants (edit page) ───────────────────────
    // Keyed by buildKey() — preserved across regenerateCombos() calls.
    var pendingExisting = {};

    function buildKey(colorId, sizeId, customAttrs) {
        var sorted = {};
        Object.keys(customAttrs || {}).sort().forEach(function (k) { sorted[k] = customAttrs[k]; });
        return (colorId || '') + '|' + (sizeId || '') + '|' + JSON.stringify(sorted);
    }

    function seedPendingFromExistingVariants() {
        var data = window.existingVariantsData || [];
        data.forEach(function (v) {
            var attrs = v.attributes || {};
            var key = buildKey(v.color, v.size, attrs);
            pendingExisting[key] = {
                price:  v.price,
                cost:   v.cost,
                stock:  v.stock,
                sku:    v.sku,
                image:  v.image,
                attrs:  JSON.stringify(attrs),
            };
        });
    }

    // ── Cartesian product for N axes ──────────────────────────────────────────
    function cartesianProduct(arrays) {
        if (!arrays.length) return [[]];
        return arrays.reduce(function (acc, arr) {
            var result = [];
            acc.forEach(function (a) {
                arr.forEach(function (b) { result.push(a.concat([b])); });
            });
            return result;
        }, [[]]);
    }

    // ── Read checked color chips ──────────────────────────────────────────────
    function getChecked(selector) {
        var items = [];
        $(selector + ':checked').each(function () {
            items.push({ id: $(this).val(), name: $(this).data('name') });
        });
        return items;
    }

    // ── Read selected custom axes and their checked values ────────────────────
    function getCustomAxes() {
        var axes = [];
        $('.custom-axis-toggle:checked').each(function () {
            var axisId   = $(this).val();
            var axisName = $(this).data('name');
            var values   = [];
            $('#custom_axis_vals_' + axisId + ' .custom-val-chip.selected').each(function () {
                values.push($(this).data('value'));
            });
            if (values.length) {
                axes.push({ name: axisName, values: values });
            }
        });
        return axes;
    }

    // ── Rebuild table <thead> based on active axes ────────────────────────────
    function rebuildTableHeader(allAxes) {
        var th = function (label) {
            return '<th class="px-3 py-2 text-left font-semibold text-muted uppercase tracking-widest text-xs">' + label + '</th>';
        };
        var html = '<tr class="bg-surface border-b border-main">';
        allAxes.forEach(function (ax) {
            var label = ax.type === 'color' ? _t.color
                      : ax.type === 'size'  ? _t.size
                      : ax.name;
            html += th(label);
        });
        html += th(_t.price) + th(_t.cost) + th(_t.stock + ' *') + th(_t.sku) + th(_t.image);
        html += '</tr>';
        $('#variant_combo_head').html(html);
    }

    // ── Generate image pick/change cell HTML ──────────────────────────────────
    function variantImageCell(savedImage) {
        var uid = 'vimg_' + Date.now() + '_' + Math.floor(Math.random() * 99999);
        return '<div id="' + uid + '">' +
            '<input type="hidden" name="v_image[]" value="' + (savedImage || '') + '" class="tw-media-id-input variant-image-id">' +
            '<button type="button" class="tw-media-open-btn px-2 py-1 border border-main rounded text-muted hover:border-primary text-xs" data-target="' + uid + '">' +
                (savedImage ? _t.change : _t.pick) +
            '</button>' +
        '</div>';
    }

    // ── Main combination generator ────────────────────────────────────────────
    function regenerateCombos() {
        var colors      = getChecked('.color-axis-check');
        var sizes       = getChecked('.size-axis-check');
        var customAxes  = getCustomAxes();

        // Build ordered list of active axes with their item arrays
        var allAxes = [];
        if (colors.length) allAxes.push({ type: 'color', name: _t.color, items: colors });
        if (sizes.length)  allAxes.push({ type: 'size',  name: _t.size,  items: sizes });
        customAxes.forEach(function (ax) {
            allAxes.push({
                type:  'custom',
                name:  ax.name,
                items: ax.values.map(function (v) { return { id: v, name: v }; }),
            });
        });

        var $body  = $('#variant_combo_body');
        var $empty = $('#variant_empty_msg');

        // Preserve values already in the DOM (user-typed data on an already-visible grid)
        var domExisting = {};
        $body.find('tr.variant-row').each(function () {
            var key = $(this).data('combo-key') || '';
            if (key) {
                domExisting[key] = {
                    price: $(this).find('[name="v_price[]"]').val(),
                    cost:  $(this).find('[name="v_cost[]"]').val(),
                    stock: $(this).find('[name="v_stock[]"]').val(),
                    sku:   $(this).find('[name="v_sku[]"]').val(),
                    image: $(this).find('[name="v_image[]"]').val(),
                    attrs: $(this).find('[name="v_attributes[]"]').val(),
                };
            }
        });

        rebuildTableHeader(allAxes);
        $body.empty();

        if (!allAxes.length) {
            $empty.removeClass('hidden');
            return;
        }
        $empty.addClass('hidden');

        // Compute cartesian product across all axes
        var itemArrays = allAxes.map(function (ax) { return ax.items; });
        var combos     = cartesianProduct(itemArrays);

        combos.forEach(function (combo) {
            var colorId    = '';
            var colorName  = '';
            var sizeId     = '';
            var sizeName   = '';
            var customAttrs = {};

            allAxes.forEach(function (ax, i) {
                var item = combo[i];
                if (ax.type === 'color') { colorId = item.id; colorName = item.name; }
                else if (ax.type === 'size')  { sizeId  = item.id; sizeName  = item.name; }
                else { customAttrs[ax.name] = item.id; }
            });

            var key   = buildKey(colorId, sizeId, customAttrs);
            var saved = domExisting[key] || pendingExisting[key] || {};
            var attrsJson = JSON.stringify(customAttrs);
            // Auto-generate SKU for new rows that have no saved SKU
            if (!saved.sku) { saved.sku = autoSku(allAxes, combo); }

            // Build row cells for each axis
            var axisCells = '';
            allAxes.forEach(function (ax, i) {
                var item      = combo[i];
                var display   = item.name || _t.none;
                var hiddenInput = '';
                if (ax.type === 'color') {
                    hiddenInput = '<input type="hidden" name="v_color[]" value="' + colorId + '">';
                } else if (ax.type === 'size') {
                    hiddenInput = '<input type="hidden" name="v_size[]" value="' + sizeId + '">';
                }
                axisCells += '<td class="px-3 py-2 font-medium text-dark">' + display + hiddenInput + '</td>';
            });

            // If no color axis, still emit empty v_color[] so backend array lengths match
            if (!allAxes.some(function (a) { return a.type === 'color'; })) {
                axisCells += '<td style="display:none"><input type="hidden" name="v_color[]" value=""></td>';
            }
            if (!allAxes.some(function (a) { return a.type === 'size'; })) {
                axisCells += '<td style="display:none"><input type="hidden" name="v_size[]" value=""></td>';
            }

            var row = '<tr class="border-b border-main variant-row" data-combo-key="' + key + '">' +
                axisCells +
                '<td class="px-3 py-1"><input type="number" name="v_price[]" value="' + (saved.price ?? 0) + '" step="0.01" min="0" class="variant-input w-24"></td>' +
                '<td class="px-3 py-1"><input type="number" name="v_cost[]"  value="' + (saved.cost  ?? 0) + '" step="0.01" min="0" class="variant-input w-24"></td>' +
                '<td class="px-3 py-1"><input type="number" name="v_stock[]" value="' + (saved.stock ?? 0) + '" min="0" required class="variant-input w-20"></td>' +
                '<td class="px-3 py-1"><input type="text"   name="v_sku[]"   value="' + (saved.sku   || '') + '" class="variant-input w-28"></td>' +
                '<td class="px-3 py-1">' + variantImageCell(saved.image) +
                    '<input type="hidden" name="v_attributes[]" value="' + attrsJson.replace(/"/g, '&quot;') + '">' +
                '</td>' +
            '</tr>';

            $body.append(row);
        });
    }

    // ── Build value-picker section for a custom axis ──────────────────────────
    function buildAxisValuePicker(axisId, axisName, terms) {
        var chips = terms.map(function (val) {
            return '<label class="custom-val-chip" data-value="' + val + '">' +
                '<input type="checkbox" class="sr-only custom-val-check" data-axis-id="' + axisId + '" value="' + val + '">' +
                val +
            '</label>';
        }).join('');

        return '<div id="custom_axis_vals_' + axisId + '" class="flex flex-wrap items-center gap-2 p-2 bg-surface border border-main rounded-lg">' +
            '<span class="text-[10px] font-bold text-muted uppercase tracking-wider mr-1">' + axisName + ':</span>' +
            chips +
        '</div>';
    }

    // ── Inline axis builder (no pre-existing ProductAttribute required) ───────
    var inlineAxisCounter = 0;
    window.addInlineAxis = function () {
        var name  = $('#inline_axis_name').val().trim();
        var raw   = $('#inline_axis_terms').val().trim();
        if (!name || !raw) return;

        var terms = raw.split(',').map(function (v) { return v.trim(); }).filter(Boolean);
        if (!terms.length) return;

        var axisId = 'inline_' + (++inlineAxisCounter);

        // Add a pill to the picker (so it looks consistent with pre-built axes)
        var pill = $('<label class="custom-axis-pill selected inline-flex items-center gap-1.5 cursor-pointer px-3 py-1.5 rounded-lg border border-main text-xs font-medium select-none">' +
            '<input type="checkbox" class="custom-axis-toggle sr-only" checked value="' + axisId + '" data-name="' + name + '" data-terms=\'' + JSON.stringify(terms) + '\'>' +
            '<i class="mdi mdi-check text-primary text-sm"></i>' +
            name +
        '</label>');
        $('#custom_axes_picker').append(pill);

        // Build the value picker
        $('#custom_axis_values_wrap').append(buildAxisValuePicker(axisId, name, terms));

        // Auto-check all values
        $('#custom_axis_vals_' + axisId + ' .custom-val-chip').each(function () {
            $(this).addClass('selected');
            $(this).find('input').prop('checked', true);
        });

        // Clear inputs
        $('#inline_axis_name, #inline_axis_terms').val('');

        regenerateCombos();
    };

    // ── Apply-to-all ──────────────────────────────────────────────────────────
    window.applyToAll = function (fieldName, sourceId) {
        var val = $('#' + sourceId).val();
        if (val === '') return;
        $('[name="' + fieldName + '[]"]').val(val);
    };

    // ── Event: Handle-Variants Toggle ─────────────────────────────────────────
    $(document).on('change', '#handle_variants_toggle', function () {
        var on = this.checked;
        $('#variants_section').toggleClass('hidden', !on);
        $('#simple_quantity_wrap').toggleClass('hidden', on);
        if (on) regenerateCombos();
    });

    // ── Event: Color / Size chip toggle ──────────────────────────────────────
    $(document).on('change', '.color-axis-check, .size-axis-check', function () {
        $(this).closest('label').toggleClass('selected', this.checked);
        regenerateCombos();
    });

    // ── Event: Custom axis toggle ─────────────────────────────────────────────
    $(document).on('change', '.custom-axis-toggle', function () {
        var pill     = $(this).closest('.custom-axis-pill');
        var on       = this.checked;
        var axisId   = $(this).val();
        var axisName = $(this).data('name');
        // jQuery auto-parses data attributes that look like JSON arrays, handle both
        var terms    = [];
        try {
            var rawTerms = $(this).data('terms');
            terms = Array.isArray(rawTerms) ? rawTerms : JSON.parse(rawTerms || '[]');
        } catch (e) { terms = []; }

        pill.toggleClass('selected', on);

        if (on) {
            if (!$('#custom_axis_vals_' + axisId).length) {
                $('#custom_axis_values_wrap').append(buildAxisValuePicker(axisId, axisName, terms));
            }
            // Auto-select all value chips so the grid generates immediately
            $('#custom_axis_vals_' + axisId + ' .custom-val-chip').each(function () {
                $(this).addClass('selected');
                $(this).find('input').prop('checked', true);
            });
            regenerateCombos();
        } else {
            $('#custom_axis_vals_' + axisId).remove();
            regenerateCombos();
        }
    });

    // ── Event: Custom axis value chip toggle ──────────────────────────────────
    $(document).on('click', '.custom-val-chip', function () {
        var $chip    = $(this);
        var $cb      = $chip.find('input[type="checkbox"]');
        var selected = !$chip.hasClass('selected');
        $chip.toggleClass('selected', selected);
        $cb.prop('checked', selected);
        regenerateCombos();
    });

    // ── Page ready: restore state for edit page ───────────────────────────────
    $(document).ready(function () {
        var data = window.existingVariantsData || [];
        if (!data.length) return;

        // Delay so the page wizard/Alpine init finishes before we restore chips
        setTimeout(function () { restoreExistingVariants(data); }, 150);
    });

    function restoreExistingVariants(data) {

        // Pre-seed preserved values
        seedPendingFromExistingVariants();

        // Determine which colors/sizes are used
        var usedColors = {}, usedSizes = {};
        var usedCustomAxes = {}; // axisName → Set of values

        data.forEach(function (v) {
            if (v.color) usedColors[v.color] = true;
            if (v.size)  usedSizes[v.size]   = true;
            var attrs = v.attributes || {};
            Object.keys(attrs).forEach(function (axisName) {
                if (!usedCustomAxes[axisName]) usedCustomAxes[axisName] = {};
                usedCustomAxes[axisName][attrs[axisName]] = true;
            });
        });

        // Re-check color chips
        $('.color-axis-check').each(function () {
            if (usedColors[$(this).val()]) {
                this.checked = true;
                $(this).closest('label').addClass('selected');
            }
        });

        // Re-check size chips
        $('.size-axis-check').each(function () {
            if (usedSizes[$(this).val()]) {
                this.checked = true;
                $(this).closest('label').addClass('selected');
            }
        });

        // Re-check custom axis pills and their value chips.
        // Axes that match a pre-built Attribute pill are restored via the DOM.
        // Axes that were added inline (not in pre-built list) get a new pill created.
        if (Object.keys(usedCustomAxes).length) {
            var restoredAxes = {}; // track which axis names were found in DOM

            $('.custom-axis-toggle').each(function () {
                var axisName = $(this).data('name');
                if (!usedCustomAxes[axisName]) return;

                restoredAxes[axisName] = true;

                // Check the pill
                this.checked = true;
                $(this).closest('.custom-axis-pill').addClass('selected');

                // Build value picker
                var axisId = $(this).val();
                var terms  = [];
                try {
                    var rawT = $(this).data('terms');
                    terms = Array.isArray(rawT) ? rawT : JSON.parse(rawT || '[]');
                } catch (e) { terms = []; }

                if (!$('#custom_axis_vals_' + axisId).length) {
                    $('#custom_axis_values_wrap').append(buildAxisValuePicker(axisId, axisName, terms));
                }

                // Check the used values
                var usedVals = usedCustomAxes[axisName];
                $('#custom_axis_vals_' + axisId + ' .custom-val-chip').each(function () {
                    var val = $(this).data('value');
                    if (usedVals[val]) {
                        $(this).addClass('selected');
                        $(this).find('input').prop('checked', true);
                    }
                });
            });

            // For inline axes (not in pre-built list), recreate the pill dynamically
            $.each(usedCustomAxes, function (axisName, usedVals) {
                if (restoredAxes[axisName]) return; // already handled above

                var terms  = Object.keys(usedVals);
                var axisId = 'inline_' + (++inlineAxisCounter);

                var pill = $('<label class="custom-axis-pill selected inline-flex items-center gap-1.5 cursor-pointer px-3 py-1.5 rounded-lg border border-main text-xs font-medium select-none">' +
                    '<input type="checkbox" class="custom-axis-toggle sr-only" checked value="' + axisId + '" data-name="' + axisName + '" data-terms=\'' + JSON.stringify(terms) + '\'>' +
                    '<i class="mdi mdi-check text-primary text-sm"></i>' +
                    axisName +
                '</label>');

                // Append inside the flex container so pills sit side-by-side
                var $target = $('#custom_axes_picker');
                if ($target.length) { $target.append(pill); }
                else { $('#custom_axis_values_wrap').prepend(pill); }

                $('#custom_axis_values_wrap').append(buildAxisValuePicker(axisId, axisName, terms));

                // Select the values that were actually used
                $('#custom_axis_vals_' + axisId + ' .custom-val-chip').each(function () {
                    var val = $(this).data('value');
                    if (usedVals[val]) {
                        $(this).addClass('selected');
                        $(this).find('input').prop('checked', true);
                    }
                });
            });
        }

        // Now render the combination grid with restored values
        regenerateCombos();
    } // end restoreExistingVariants

})(jQuery);
</script>
