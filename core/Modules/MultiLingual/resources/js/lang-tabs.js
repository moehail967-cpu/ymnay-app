/**
 * Multi-Lingual Plugin — Admin Language Tab UI
 *
 * Injects locale tab strips above translatable fields in admin create/edit forms.
 * The "default" pane keeps the original field name so server validation works unchanged.
 * Non-default panes use name="field[locale]" so TranslationService can save per-locale values.
 */
(function ($) {
    'use strict';

    var cfg = window.mlConfig || {};
    var LOCALES       = cfg.locales       || [];   // [{slug, name}, ...]
    var FALLBACK      = cfg.fallback      || 'en';
    var FIELDS        = cfg.fields        || [];
    var TRANSLATE_URL = cfg.translateUrl  || '';
    var FETCH_URL     = cfg.fetchUrl      || '';
    var EDIT_PATTERNS = cfg.editPatterns  || [];

    // DEBUG — remove after confirming tabs appear
    console.log('[ML] mlConfig:', window.mlConfig);
    console.log('[ML] LOCALES:', LOCALES, 'FALLBACK:', FALLBACK);

    if (LOCALES.length < 2) {
        console.warn('[ML] Not enough locales (' + LOCALES.length + '), tab injection skipped.');
        return;
    }

    // The "default" locale keeps the original name="field" attribute.
    // Find the one matching FALLBACK; if none, treat the first locale as default.
    var DEFAULT_SLUG = (function () {
        for (var i = 0; i < LOCALES.length; i++) {
            if (LOCALES[i].slug === FALLBACK) return LOCALES[i].slug;
        }
        return LOCALES[0].slug;
    }());

    // ── Edit-page context detection ───────────────────────────────────────────

    var mlEditContext = (function () {
        if (!FETCH_URL) return null;
        var path = window.location.pathname;
        for (var i = 0; i < EDIT_PATTERNS.length; i++) {
            var p   = EDIT_PATTERNS[i];
            var idx = path.indexOf(p.segment);
            if (idx !== -1) {
                var after = path.substring(idx + p.segment.length).replace(/^\//, '');
                var id    = parseInt(after, 10);
                if (id > 0) return { type: p.type, id: id };
            }
        }
        return null;
    }());

    var mlExisting = {};  // {locale: {field: value}} fetched from server

    function loadAndPopulate() {
        if (!mlEditContext) return;
        $.getJSON(FETCH_URL, { model_type: mlEditContext.type, model_id: mlEditContext.id })
            .done(function (data) {
                mlExisting = data || {};
                populateAllFields();
            });
    }

    function populateAllFields() {
        LOCALES.forEach(function (locale) {
            if (locale.slug === DEFAULT_SLUG) return;
            var localeData = mlExisting[locale.slug] || {};
            FIELDS.forEach(function (field) {
                var val = localeData[field];
                if (!val) return;
                var $input = $('[name="' + field + '__' + locale.slug + '"]');
                $input.each(function () {
                    var $i = $(this);
                    if ($i.val()) return; // don't overwrite user edits
                    if ($i.hasClass('summernote')) {
                        try { $i.summernote('code', val); } catch (e) { $i.val(val); }
                    } else {
                        $i.val(val);
                    }
                });
            });
        });
    }

    // ── Injection ─────────────────────────────────────────────────────────────

    $(document).ready(function () {
        // Run early (before summernote) and again for wizard steps
        injectTabs();
        setTimeout(function () { injectTabs(); loadAndPopulate(); }, 200);
        setTimeout(function () { injectTabs(); populateAllFields(); }, 800);
        setTimeout(function () { injectTabs(); populateAllFields(); }, 2000);
    });

    $(document).on('click', '[data-toggle="tab"], .nav-link, .wizard-nav, .step-item, .product-wizard-step', function () {
        setTimeout(function () { injectTabs(); populateAllFields(); }, 400);
    });

    function injectTabs() {
        FIELDS.forEach(function (field) {
            // Standard inputs & textareas
            $('[name="' + field + '"]:not(.ml-injected)').each(function () {
                var $el = $(this);
                if ($el.attr('type') === 'hidden') return;
                if ($el.closest('.ml-field-wrap').length) return;
                console.log('[ML] injecting tabs for field:', field, $el[0]);
                injectTabsForElement($el, field);
            });
        });
    }

    function injectTabsForElement($el, field) {
        if ($el.closest('.ml-field-wrap').length) return;
        $el.addClass('ml-injected');

        // Tear down any existing Summernote instance to prevent orphaned editors
        var sn_options = null;
        var sn_content = '';
        var isSummernoteEl = $el.hasClass('summernote') || $el.hasClass('note-codable');
        if (isSummernoteEl) {
            try {
                var snCtx = $el.data('summernote');
                if (snCtx && snCtx.options) {
                    sn_options = $.extend(true, {}, snCtx.options);
                    sn_content = $el.summernote('code') || '';
                    $el.summernote('destroy');
                    if (sn_content) { $el.val(sn_content); }
                    $el.show();
                }
            } catch (e) {}
        }

        var $tabs = $('<div class="ml-lang-tabs"></div>');
        var $wrap = $('<div class="ml-field-wrap"></div>');

        LOCALES.forEach(function (locale, idx) {
            var isDefault = locale.slug === DEFAULT_SLUG;
            var isFirst   = idx === 0;

            // ── Tab button ─────────────────────────────────────────────────
            var $tab = $('<button type="button">')
                .addClass('ml-lang-tab' + (isFirst ? ' active' : ''))
                .attr('data-ml-locale', locale.slug)
                .text(locale.name);
            if (isDefault) {
                $tab.append(' <span class="ml-default-badge">default</span>');
            }
            $tabs.append($tab);

            // ── Pane ───────────────────────────────────────────────────────
            var $pane = $('<div>')
                .addClass('ml-field-pane' + (isFirst ? ' active' : ''))
                .attr('data-ml-pane', locale.slug);

            if (isDefault) {
                // Clone the original element (keeps classes, value, etc.)
                $pane.append($el.clone(true).removeClass('ml-injected'));
            } else {
                var $copy = buildSiblingInput($el, field, locale.slug);
                $pane.append($copy);

                if (TRANSLATE_URL) {
                    var $btn = $('<button type="button" class="ml-translate-btn">')
                        .attr('data-ml-field', field)
                        .attr('data-ml-locale', locale.slug)
                        .html('<i class="mdi mdi-robot-outline"></i> Auto Translate');
                    $pane.append($btn);
                }
            }

            $wrap.append($pane);
        });

        $wrap.prepend($tabs);

        // Tab click handler
        $tabs.on('click', '.ml-lang-tab', function () {
            var slug = $(this).data('ml-locale');
            $tabs.find('.ml-lang-tab').removeClass('active');
            $(this).addClass('active');
            $wrap.find('.ml-field-pane').removeClass('active');
            $wrap.find('[data-ml-pane="' + slug + '"]').addClass('active');
        });

        // ── DOM insertion (always happens, regardless of which locale is default) ──
        $el.replaceWith($wrap);

        // Re-init summernote on the cloned textarea in the default pane
        if (isSummernoteEl) {
            var reInitOpts   = sn_options || {};
            var reInitContent = sn_content;

            var $defaultPane = $wrap.find('[data-ml-pane="' + DEFAULT_SLUG + '"]');
            var $newTa = $defaultPane.find('textarea');
            if ($newTa.length) {
                setTimeout(function () {
                    try {
                        $newTa.summernote(reInitOpts);
                        if (reInitContent) {
                            try { $newTa.summernote('code', reInitContent); } catch (e2) {}
                        }
                    } catch (e) {}
                }, 300);
            }
            // Also init summernote on non-default panes
            $wrap.find('.ml-field-pane:not([data-ml-pane="' + DEFAULT_SLUG + '"]) textarea.summernote').each(function () {
                var $ta = $(this);
                setTimeout(function () {
                    try { $ta.summernote(reInitOpts); } catch (e) {}
                }, 500);
            });
        }
    }

    function buildSiblingInput($original, field, locale) {
        var inputName = field + '__' + locale;

        if ($original.is('textarea')) {
            var $ta = $('<textarea>')
                .attr('name', inputName)
                .attr('rows', $original.attr('rows') || 4)
                .attr('placeholder', $original.attr('placeholder') || '')
                .addClass($original.attr('class') || '')
                .css('width', '100%');

            if ($original.hasClass('summernote')) {
                $ta.addClass('summernote');
            }
            return $ta;
        }

        if ($original.is('input')) {
            return $('<input>')
                .attr('type', $original.attr('type') || 'text')
                .attr('name', inputName)
                .attr('placeholder', $original.attr('placeholder') || '')
                .addClass($original.attr('class') || '');
        }

        return $('<input type="text">').attr('name', inputName);
    }

    // ── Auto-translate ────────────────────────────────────────────────────────

    $(document).on('click', '.ml-translate-btn', function () {
        var $btn         = $(this);
        var field        = $btn.data('ml-field');
        var targetLocale = $btn.data('ml-locale');

        // Source text from the default pane
        var $sourcePane = $btn.closest('.ml-field-wrap').find('[data-ml-pane="' + DEFAULT_SLUG + '"]');
        var $sourceEl   = $sourcePane.find('input, textarea').first();
        var sourceText  = $sourceEl.val() || '';

        // Try summernote editor
        if (!sourceText && $sourceEl.hasClass('summernote')) {
            try { sourceText = $sourceEl.summernote('code') || ''; } catch (e) {}
        }
        // Try .note-editable in case summernote is active
        if (!sourceText) {
            var $editable = $sourcePane.find('.note-editable');
            if ($editable.length) sourceText = $editable.html() || '';
        }

        if (!sourceText.trim()) {
            alert('Please enter the default language content first.');
            return;
        }

        var isHtml = $sourceEl.hasClass('summernote') || $sourcePane.find('.note-editable').length > 0;

        var $targetPane = $btn.closest('[data-ml-pane]');
        var $targetEl   = $targetPane.find('[name="' + field + '__' + targetLocale + '"]').first();

        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Translating…');

        $.ajax({
            url:  TRANSLATE_URL,
            type: 'POST',
            data: {
                _token:        $('meta[name="csrf-token"]').attr('content') || '',
                text:          sourceText,
                source_locale: DEFAULT_SLUG,
                target_locale: targetLocale,
                is_html:       isHtml ? 1 : 0,
            },
            success: function (res) {
                if (res.translated) {
                    if ($targetEl.hasClass('summernote')) {
                        try { $targetEl.summernote('code', res.translated); } catch (e) { $targetEl.val(res.translated); }
                    } else {
                        $targetEl.val(res.translated);
                    }
                    $btn.html('<i class="mdi mdi-check"></i> Done');
                    setTimeout(function () {
                        $btn.prop('disabled', false).html('<i class="mdi mdi-robot-outline"></i> Auto Translate');
                    }, 2000);
                }
            },
            error: function (xhr) {
                var msg = 'Translation failed.';
                try { msg = xhr.responseJSON.error || msg; } catch (e) {}
                alert(msg);
                $btn.prop('disabled', false).html('<i class="mdi mdi-robot-outline"></i> Auto Translate');
            },
        });
    });

})(jQuery);
