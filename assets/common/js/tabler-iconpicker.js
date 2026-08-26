/**
 * Tabler Icon Picker - jQuery Plugin
 * Replaces fontawesome-iconpicker with Tabler Icons support
 * Parses loaded stylesheets for .tabler-* classes and provides a searchable modal picker
 */
(function ($) {
    "use strict";

    var tablerIcons = null;
    var cachedGridHtml = null;
    var BATCH_SIZE = 150;

    function extractTablerIcons() {
        if (tablerIcons !== null) return tablerIcons;

        tablerIcons = [];
        var sheets = document.styleSheets;

        for (var i = 0; i < sheets.length; i++) {
            var rules;
            try {
                rules = sheets[i].cssRules || sheets[i].rules;
            } catch (e) {
                continue;
            }
            if (!rules) continue;

            for (var j = 0; j < rules.length; j++) {
                var selector = rules[j].selectorText;
                if (!selector) continue;

                var match = selector.match(/^\.(tabler-[a-z0-9-]+)$/);
                if (match) {
                    tablerIcons.push(match[1]);
                }
            }
        }

        tablerIcons.sort();
        return tablerIcons;
    }

    function buildIconHtml(iconName) {
        return '<div class="tabler-iconpicker-item" data-icon="' + iconName + '" title="' + iconName.replace('tabler-', '') + '">' +
            '<i class="ti ' + iconName + '"></i>' +
            '</div>';
    }

    function renderBatch(icons, $grid, startIndex) {
        var end = Math.min(startIndex + BATCH_SIZE, icons.length);
        var html = '';
        for (var i = startIndex; i < end; i++) {
            html += buildIconHtml(icons[i]);
        }
        $grid.append(html);
        return end;
    }

    function createOverlay($trigger) {
        var icons = extractTablerIcons();

        if (!icons.length) {
            console.warn('Tabler IconPicker: No icons found. Make sure tablar-icon.css is loaded.');
            return;
        }

        var $overlay = $('<div class="tabler-iconpicker-overlay">' +
            '<div class="tabler-iconpicker-modal">' +
            '<div class="tabler-iconpicker-header">' +
            '<input type="text" class="tabler-iconpicker-search" placeholder="Search icons... (' + icons.length + ' icons)">' +
            '<button type="button" class="tabler-iconpicker-close">&times;</button>' +
            '</div>' +
            '<div class="tabler-iconpicker-grid"></div>' +
            '</div>' +
            '</div>');

        var $grid = $overlay.find('.tabler-iconpicker-grid');
        var $search = $overlay.find('.tabler-iconpicker-search');
        var currentIndex = 0;
        var currentIcons = icons;
        var isSearching = false;

        // render first batch
        currentIndex = renderBatch(icons, $grid, 0);

        // infinite scroll - load more on scroll
        $grid.on('scroll', function () {
            if (isSearching) return;
            var el = this;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 100) {
                if (currentIndex < currentIcons.length) {
                    currentIndex = renderBatch(currentIcons, $grid, currentIndex);
                }
            }
        });

        // close on overlay click
        $overlay.on('click', function (e) {
            if ($(e.target).hasClass('tabler-iconpicker-overlay')) {
                $overlay.remove();
            }
        });

        // close button
        $overlay.find('.tabler-iconpicker-close').on('click', function () {
            $overlay.remove();
        });

        // close on Escape
        $(document).on('keydown.tablerIconpicker', function (e) {
            if (e.key === 'Escape') {
                $overlay.remove();
                $(document).off('keydown.tablerIconpicker');
            }
        });

        // search with debounce
        var searchTimer = null;
        $search.on('input', function () {
            var term = $(this).val().toLowerCase().trim();
            clearTimeout(searchTimer);

            searchTimer = setTimeout(function () {
                $grid.empty();
                currentIndex = 0;

                if (!term) {
                    isSearching = false;
                    currentIcons = icons;
                    currentIndex = renderBatch(icons, $grid, 0);
                } else {
                    isSearching = true;
                    var filtered = [];
                    for (var i = 0; i < icons.length; i++) {
                        if (icons[i].indexOf(term) !== -1) {
                            filtered.push(icons[i]);
                        }
                    }
                    currentIcons = filtered;
                    // render all filtered results (search narrows it down)
                    var html = '';
                    for (var j = 0; j < filtered.length; j++) {
                        html += buildIconHtml(filtered[j]);
                    }
                    $grid.html(html);
                    currentIndex = filtered.length;
                }
            }, 200);
        });

        // icon selection
        $grid.on('click', '.tabler-iconpicker-item', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var iconClass = $(this).attr('data-icon');
            var fullValue = 'ti ' + iconClass;

            // update preview button
            var $previewBtn = $trigger.siblings('.iconpicker-component');
            if ($previewBtn.length) {
                $previewBtn.find('i').attr('class', 'ti ' + iconClass);
            }

            // update hidden input
            var $input = $trigger.parent().parent().children('input[type="hidden"]');
            if (!$input.length) {
                $input = $trigger.parent().siblings('input[type="hidden"]');
            }
            if ($input.length) {
                $input.val(fullValue);
            }

            // fire event
            var event = $.Event('iconpickerSelected');
            event.iconpickerValue = fullValue;
            $trigger.trigger(event);

            // cleanup
            $overlay.remove();
            $(document).off('keydown.tablerIconpicker');
        });

        $('body').append($overlay);

        // focus search after append
        setTimeout(function () {
            $search.focus();
        }, 50);
    }

    function addStyles() {
        if ($('#tabler-iconpicker-styles').length) return;

        var css =
            '.tabler-iconpicker-overlay {' +
            '  position: fixed; top: 0; left: 0; width: 100%; height: 100%;' +
            '  background: rgba(0,0,0,0.5); z-index: 99999;' +
            '  display: flex; align-items: center; justify-content: center;' +
            '}' +
            '.tabler-iconpicker-modal {' +
            '  background: #fff; border-radius: 8px; width: 640px; max-width: 90vw;' +
            '  max-height: 80vh; display: flex; flex-direction: column;' +
            '  box-shadow: 0 10px 40px rgba(0,0,0,0.2);' +
            '}' +
            '.tabler-iconpicker-header {' +
            '  display: flex; padding: 12px 16px; border-bottom: 1px solid #e5e5e5;' +
            '  gap: 8px; align-items: center; flex-shrink: 0;' +
            '}' +
            '.tabler-iconpicker-search {' +
            '  flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px;' +
            '  font-size: 14px; outline: none;' +
            '}' +
            '.tabler-iconpicker-search:focus { border-color: #4a90d9; box-shadow: 0 0 0 2px rgba(74,144,217,0.2); }' +
            '.tabler-iconpicker-close {' +
            '  background: none; border: none; font-size: 24px; cursor: pointer;' +
            '  color: #666; padding: 0 8px; line-height: 1;' +
            '}' +
            '.tabler-iconpicker-close:hover { color: #333; }' +
            '.tabler-iconpicker-grid {' +
            '  overflow-y: auto; padding: 12px;' +
            '  display: flex; flex-wrap: wrap; gap: 2px;' +
            '  align-content: flex-start;' +
            '}' +
            '.tabler-iconpicker-item {' +
            '  display: flex; align-items: center; justify-content: center;' +
            '  width: 46px; height: 46px; cursor: pointer; border-radius: 6px;' +
            '  transition: background 0.15s, transform 0.1s;' +
            '}' +
            '.tabler-iconpicker-item:hover { background: #e8f0fe; transform: scale(1.1); }' +
            '.tabler-iconpicker-item i { font-size: 22px; color: #333; display: block; pointer-events: none; }';

        $('head').append('<style id="tabler-iconpicker-styles">' + css + '</style>');
    }

    $.fn.iconpicker = function (action) {
        if (action === 'destroy') {
            return this.each(function () {
                $(this).off('click.tablerIconpicker');
                $(this).removeData('tablerIconpicker');
            });
        }

        addStyles();

        return this.each(function () {
            var $el = $(this);

            if ($el.data('tablerIconpicker')) {
                return;
            }

            $el.data('tablerIconpicker', true);

            // remove bootstrap dropdown attributes that conflict
            $el.removeAttr('data-bs-toggle');
            $el.removeClass('dropdown-toggle');

            $el.on('click.tablerIconpicker', function (e) {
                e.preventDefault();
                e.stopPropagation();
                createOverlay($el);
            });
        });
    };

})(jQuery);
