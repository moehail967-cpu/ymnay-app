<link rel="stylesheet" href="{{global_asset('assets/plugins/PageBuilder/css/jquery-ui.min.css')}}">
<link rel="stylesheet" href="{{global_asset('assets/plugins/PageBuilder/css/spectrum.min.css')}}">
<style>
    input::-webkit-calendar-picker-indicator { display: none; }
    input[type="date"]::-webkit-input-placeholder { visibility: hidden !important; }

    /* ============================================
       DRAGGABLE DROP ZONE
    ============================================ */
    .page-builder-area-wrapper .main-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 12px;
        padding: 0;
        background: transparent;
    }
    .page-builder-area-wrapper .sortable {
        border: 2px dashed #c7d2fe;
        border-radius: 12px;
        padding: 12px;
        min-height: 60px;
        background: #fafbff;
        list-style: none;
        margin: 0;
    }
    .ui-sortable .ui-sortable-placeholder {
        min-height: 40px;
        border: 2px dashed #a5b4fc;
        border-radius: 10px;
        margin-bottom: 8px;
        visibility: visible !important;
        background: #eef2ff;
    }

    /* ============================================
       ADDON CARD
    ============================================ */
    .page-builder-area-wrapper li.widget-handler {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        margin-bottom: 8px;
        position: relative;    /* critical: buttons are absolute to this */
        list-style: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    /* Card header bar */
    .page-builder-area-wrapper h4.top-part {
        margin: 0;
        padding: 10px 84px 10px 14px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 10px 10px 0 0;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        cursor: grab;
        user-select: none;
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        box-sizing: border-box;
    }
    /* When card is collapsed hide border-bottom */
    .page-builder-area-wrapper li.widget-handler:not(:has(.content-part.show)) h4.top-part {
        border-bottom-color: transparent;
        border-radius: 10px;
    }

    /* Expand toggle button — FIXED top so it stays in the header */
    .widget-handler .expand {
        position: absolute;
        right: 44px;
        top: 8px;           /* anchored to header, not card middle */
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e0e7ff;
        color: #4f46e5;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .15s;
        z-index: 2;
    }
    .widget-handler .expand:hover { background: #c7d2fe; }

    /* Remove button — FIXED top */
    .widget-handler .remove-widget {
        position: absolute;
        right: 10px;
        top: 8px;           /* anchored to header */
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #fee2e2;
        color: #ef4444;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .15s;
        z-index: 2;
    }
    .widget-handler .remove-widget:hover { background: #fecaca; }

    /* ============================================
       CARD CONTENT (form area)
    ============================================ */
    .page-builder-area-wrapper .content-part {
        visibility: hidden;
        opacity: 0;
        height: 0;
        overflow: hidden;
    }
    .page-builder-area-wrapper .content-part.show {
        visibility: visible;
        opacity: 1;
        height: auto;
        overflow: visible;
        padding: 18px 20px 16px;
    }

    /* nice-select height fixes inside collapsed state */
    .page-builder-area-wrapper .content-part select.form-control:not([size]):not([multiple]) { height: 0; margin-bottom: 0; }
    .page-builder-area-wrapper .content-part .nice-select.wide { margin-bottom: 0; height: 0; display: none; }
    .page-builder-area-wrapper .content-part.show .nice-select.wide { display: block; margin-bottom: 14px; height: auto; }
    .page-builder-area-wrapper .content-part.show select.form-control:not([size]):not([multiple]) { height: 40px; margin-bottom: 14px; }

    /* ============================================
       FORM FIELDS
    ============================================ */
    .pb-field-group { margin-bottom: 14px; }

    .pb-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .pb-input {
        display: block;
        width: 100%;
        padding: 7px 11px;
        font-size: 13px;
        line-height: 1.5;
        color: #1e293b;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        outline: none;
        box-sizing: border-box;
        transition: border-color .15s, box-shadow .15s;
        font-family: inherit;
    }
    .pb-input:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129,140,248,.15);
    }
    textarea.pb-input {
        resize: vertical;
        min-height: 80px;
    }
    select.pb-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 16 16'%3E%3Cpath fill='%2394a3b8' d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 30px;
        cursor: pointer;
    }
    .pb-input[type="number"] { -moz-appearance: textfield; }
    .pb-input[type="number"]::-webkit-inner-spin-button,
    .pb-input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; }

    .pb-info {
        display: block;
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
        line-height: 1.4;
    }

    /* ============================================
       SAVE BUTTON
    ============================================ */
    .pb-save-btn,
    button.widget_save_change_button {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 20px;
        font-size: 13px;
        font-weight: 600;
        color: #fff;
        background: #4f46e5;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background .15s;
        margin-top: 6px;
        font-family: inherit;
    }
    .pb-save-btn:hover,
    button.widget_save_change_button:hover { background: #4338ca; }
    .pb-save-btn:disabled,
    button.widget_save_change_button:disabled { opacity: .6; cursor: not-allowed; }

    /* ============================================
       COLOR PICKER SWATCH
    ============================================ */
    .color_picker {
        display: inline-block;
        width: 34px;
        height: 34px;
        border-radius: 7px;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        transition: border-color .15s;
    }
    .color_picker:hover { border-color: #818cf8; }

    /* ============================================
       TOGGLE / SWITCH
    ============================================ */
    label.switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
    }
    label.switch input { opacity: 0; width: 0; height: 0; }
    label.switch .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #cbd5e1;
        border-radius: 22px;
        transition: background .2s;
    }
    label.switch .slider:before {
        content: '';
        position: absolute;
        width: 16px; height: 16px;
        left: 3px; bottom: 3px;
        background: #fff;
        border-radius: 50%;
        transition: transform .2s;
        box-shadow: 0 1px 3px rgba(0,0,0,.15);
    }
    label.switch input:checked + .slider { background: #4f46e5; }
    label.switch input:checked + .slider:before { transform: translateX(18px); }

    /* ============================================
       REPEATER
    ============================================ */
    .pb-repeater-wrap,
    .iconbox-repeater-wrapper {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 14px;
    }
    .pb-repeater-row,
    .all-field-wrap {
        padding: 14px 56px 14px 14px;
        background: #f8fafc;
        position: relative;
        border-top: 1px solid #e2e8f0;
    }
    .pb-repeater-row:first-child,
    .all-field-wrap:first-child { border-top: none; }

    .pb-repeater-actions,
    .action-wrap {
        position: absolute;
        right: 0; top: 0;
        width: 48px;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-left: 1px solid #e2e8f0;
        background: #f1f5f9;
    }
    .pb-repeater-actions button.add,
    .action-wrap button.add {
        width: 26px; height: 26px;
        background: #dcfce7;
        border: none;
        border-radius: 6px;
        color: #16a34a;
        font-size: 16px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .15s;
        padding: 0;
    }
    .pb-repeater-actions button.add:hover,
    .action-wrap button.add:hover { background: #bbf7d0; }

    .pb-repeater-actions button.remove,
    .action-wrap button.remove {
        width: 26px; height: 26px;
        background: #fee2e2;
        border: none;
        border-radius: 6px;
        color: #ef4444;
        font-size: 14px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .15s;
        padding: 0;
    }
    .pb-repeater-actions button.remove:hover,
    .action-wrap button.remove:hover { background: #fecaca; }

    /* ============================================
       LANGUAGE TABS
    ============================================ */
    .pb-lang-tabs nav,
    .page-builder-area-wrapper nav {
        margin-bottom: 0;
    }
    .pb-lang-tabs .nav-tabs,
    .page-builder-area-wrapper .nav-tabs {
        display: flex;
        gap: 2px;
        border-bottom: 2px solid #e2e8f0;
        padding: 0;
        margin-bottom: 14px;
        list-style: none;
    }
    .pb-lang-tabs .nav-link,
    .page-builder-area-wrapper .nav-link {
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        border: none;
        border-radius: 5px 5px 0 0;
        background: transparent;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: color .15s, background .15s;
        margin-bottom: -2px;
    }
    .pb-lang-tabs .nav-link:hover,
    .page-builder-area-wrapper .nav-link:hover { color: #4f46e5; background: #eef2ff; }
    .pb-lang-tabs .nav-link.active,
    .page-builder-area-wrapper .nav-link.active {
        color: #4f46e5;
        background: #eef2ff;
        border-bottom: 2px solid #4f46e5;
    }
    .tab-content.pb-lang-tabs { padding-top: 10px; }

    /* ============================================
       WIDGET SIDEBAR (right panel)
    ============================================ */
    .search-wrap { margin-bottom: 10px; }
    #search_addon_field {
        width: 100%;
        padding: 8px 12px;
        font-size: 13px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        outline: none;
        box-sizing: border-box;
        transition: border-color .15s;
        font-family: inherit;
    }
    #search_addon_field:focus { border-color: #818cf8; background: #fff; }

    .all-addons-wrapper ul.all-widgets {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        list-style: none;
        padding: 10px;
        margin: 0;
        border: 2px dashed #e2e8f0;
        border-radius: 10px;
        background: #fafafa;
    }
    .all-addons-wrapper ul.all-widgets li.widget-handler {
        width: calc(50% - 4px);
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 28px 8px 10px;
        cursor: grab;
        position: relative;
        transition: border-color .15s, box-shadow .15s;
        list-style: none;
        box-sizing: border-box;
    }
    .all-addons-wrapper ul.all-widgets li.widget-handler:hover {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(129,140,248,.1);
    }
    .all-addons-wrapper ul.all-widgets li h4.top-part {
        font-size: 11.5px;
        font-weight: 500;
        color: #374151;
        margin: 0;
        padding: 0;
        background: transparent;
        cursor: grab;
        line-height: 1.4;
        border: none;
        min-height: unset;
    }
    .all-addons-wrapper ul li .preview-image {
        position: absolute;
        right: 6px; top: 50%;
        transform: translateY(-50%);
        width: 18px; height: 18px;
        background: #e0e7ff;
        border-radius: 4px;
        color: #4f46e5;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: background .15s;
    }
    .all-addons-wrapper ul li .preview-image:hover { background: #c7d2fe; }

    /* Hover preview image popup */
    .all-addons-wrapper ul.ui-sortable li.widget-handler .imageupshow {
        position: absolute;
        left: 0; bottom: calc(100% + 6px);
        max-width: 200px;
        z-index: 99;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,.1);
        visibility: hidden; opacity: 0;
        transition: opacity .15s;
        overflow: hidden;
    }
    .all-addons-wrapper ul.ui-sortable li.widget-handler:hover .imageupshow {
        visibility: visible; opacity: 1;
    }
    .all-addons-wrapper ul.ui-sortable li.widget-handler:hover .imageupshow img {
        display: block; max-width: 200px;
    }

    /* ============================================
       RANGE FIELD
    ============================================ */
    .available-form-field .range-wrap { display: flex; align-items: center; gap: 8px; }
    .available-form-field .range-wrap .range-val {
        min-width: 52px;
        padding: 3px 8px;
        height: 28px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        text-align: center;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        line-height: 22px;
    }

    /* ============================================
       MISC / LEGACY COMPAT
    ============================================ */
    .available-form-field { list-style: none; margin: 0; padding: 0; }

    /* image attachment preview */
    .attachment-preview { overflow: hidden; }
    .attachment-preview .thumbnail {
        overflow: hidden;
        width: 110px; height: 110px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        position: static;
        transition: unset;
    }
    .attachment-preview .thumbnail .centered { position: static; transform: initial; width: 110px; height: 110px; }
    .attachment-preview .thumbnail .centered img {
        transform: initial; max-width: 110px; max-height: 110px;
        width: 100%; height: 100%; object-fit: cover;
    }

    /* page-builder-info-text fallback */
    .page-builder-info-text {
        display: block; font-size: 11px; color: #94a3b8; margin-top: 4px;
    }
</style>
