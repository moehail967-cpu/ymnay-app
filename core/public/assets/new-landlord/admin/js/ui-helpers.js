/**
 * Admin Panel UI Helpers
 */

(function ($) {
    "use strict";

    // ====== Sidebar Toggle Functions ======
    window.toggleSidebar = function() {
        document.body.classList.toggle('sidebar-mobile-open');
    };

    window.closeSidebar = function() {
        document.body.classList.remove('sidebar-mobile-open');
    };

    window.toggleSidebarCollapse = function() {
        document.body.classList.toggle('sidebar-collapsed');
        var content = document.querySelector('.admin-content');
        if (document.body.classList.contains('sidebar-collapsed')) {
            content.style.marginLeft = '4.5rem';
        } else {
            content.style.marginLeft = '';
        }
    };

    // ====== Submenu Toggle ======
    window.toggleSubmenu = function(btn) {
        var submenu = btn.nextElementSibling;
        if (!submenu || !submenu.classList.contains('submenu-collapse')) return;
        var arrow = btn.querySelector('.submenu-arrow');
        submenu.classList.toggle('hidden');
        if (arrow) arrow.classList.toggle('rotate-180');
    };

    // ====== DataTable translation helper ======
    window.translatedDataTable = function(translations) {
        translations = translations || {};
        return {
            "decimal": "",
            "emptyTable": translations.emptyTable || "No data available in table",
            "info": translations.info || "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": translations.infoEmpty || "Showing 0 to 0 of 0 entries",
            "infoFiltered": translations.infoFiltered || "(filtered from _MAX_ total entries)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": translations.lengthMenu || "Show _MENU_ entries",
            "loadingRecords": translations.loadingRecords || "Loading...",
            "processing": "",
            "search": translations.search || "Search:",
            "zeroRecords": translations.zeroRecords || "No matching records found",
            "paginate": {
                "first": translations.first || "First",
                "last": translations.last || "Last",
                "next": translations.next || "Next",
                "previous": translations.previous || "Previous"
            },
            "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            }
        };
    };

    $(document).ready(function ($) {
        // Bootstrap tooltips
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            $("body").tooltip({ selector: '[data-bs-toggle=tooltip]' });
        }

        if ($.fn.select2) {
            $('select.select2').select2();
        }

        // SweetAlert delete confirmation
        $(document).on('click', '.swal_delete_button', function (e) {
            e.preventDefault();
            const translations = window.SwalTranslations || {};
            Swal.fire({
                title: translations.deleteTitle || 'Are you sure?',
                text: translations.deleteText || 'You would not be able to revert this item!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2d6a4f',
                cancelButtonColor: '#D2042D',
                confirmButtonText: translations.confirmBtn || "Yes, Accept it!",
                cancelButtonText: translations.cancelBtn || "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    let el = $(this);
                    el.removeClass('swal_delete_button btn-danger');
                    el.addClass('btn-secondary');
                    $(this).next().find('.swal_form_submit_btn').trigger('click');
                }
            });
        });

        // Sidebar Search logic
        const $search = $('#menuSearch');
        const $noResults = $('#noResults');
        const selectors = {
            item: '.menu-item',
            title: '.menu-title',
            submenu: '.submenu-collapse'
        };

        if ($search.length) {
            $search.on('input', function () {
                const term = $(this).val().toLowerCase().trim();
                let hasAnyMatch = false;

                if (!term) {
                    $(selectors.item).show();
                    $(selectors.submenu).each(function () {
                        var $sub = $(this);
                        if (!$sub.find('.font-semibold').length) {
                            $sub.addClass('hidden');
                        }
                    });
                    $noResults.addClass('hidden');
                    return;
                }

                $('.sidebar-scroll .nav > ' + selectors.item).each(function () {
                    const $item = $(this);
                    if ($item.find('#menuSearch').length) return;

                    const $title = $item.find('> a ' + selectors.title + ', > button ' + selectors.title).first();
                    if (!$title.length) return;

                    const text = $title.text().toLowerCase();
                    const isMatch = text.includes(term);

                    const $submenuContainer = $item.find('> ' + selectors.submenu);
                    const $submenu = $submenuContainer.find('ul').first();
                    const hasChildren = $submenu.length && $submenu.find(selectors.item).length > 0;

                    if (hasChildren) {
                        let childHasMatch = false;
                        $submenu.find(selectors.item).each(function () {
                            const $child = $(this);
                            const childText = $child.find(selectors.title).first().text().toLowerCase();
                            if (childText.includes(term)) {
                                $child.show();
                                childHasMatch = true;
                                hasAnyMatch = true;
                            } else {
                                $child.hide();
                            }
                        });

                        if (isMatch || childHasMatch) {
                            $item.show();
                            $submenuContainer.removeClass('hidden');
                            hasAnyMatch = true;
                        } else {
                            $item.hide();
                        }
                    } else {
                        if (isMatch) {
                            $item.show();
                            hasAnyMatch = true;
                        } else {
                            $item.hide();
                        }
                    }
                });

                if (!hasAnyMatch) {
                    $noResults.removeClass('hidden');
                } else {
                    $noResults.addClass('hidden');
                }
            });

            $search.on('keydown', function (e) {
                if (e.key === 'Escape') {
                    $(this).val('').trigger('input');
                }
            });
        }
    });

})(jQuery);
