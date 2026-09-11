{{--
    Injected into the page builder editor <head> for tenant context only.
    Outputs <link> tags for @import URLs stripped by scopeCSSToSelector()
    (Google Fonts, icon CDNs, etc.) so fonts and icons load in the canvas.
    Also restores page builder control button styles overridden by scoped theme CSS resets.
    Also disables theme-level interactivity (links, buttons, forms) inside the canvas.
--}}
@foreach(config('xgpagebuilder.editor_head_imports', []) as $url)
    <link rel="stylesheet" href="{{ $url }}">
@endforeach
@if(config('xgpagebuilder.editor_root_vars_css'))
<style>
{{ config('xgpagebuilder.editor_root_vars_css') }}
</style>
@endif
<style>
/*
 * Theme CSS is scoped to .page-builder-canvas by scopeCSSToSelector().
 * Themes commonly have `* { margin: 0; padding: 0; }` resets that override
 * Tailwind utilities (p-1.5, space-x-1, etc.) on page builder control buttons.
 * These !important rules restore the native page builder control UI.
 */
.page-builder-canvas .absolute.z-20 {
    display: flex !important;
    gap: 0.25rem !important;
    margin: 0 !important;
    padding: 0 !important;
}
.page-builder-canvas .absolute.z-20 > button {
    box-sizing: border-box !important;
    padding: 0.375rem !important;
    margin: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border: none !important;
    border-radius: 0.25rem !important;
    cursor: pointer !important;
    font-size: 0.75rem !important;
    line-height: 1rem !important;
    color: #ffffff !important;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
    transition: background-color 0.15s ease !important;
}
.page-builder-canvas .absolute.z-20 > button svg {
    width: 1rem !important;
    height: 1rem !important;
    display: block !important;
    flex-shrink: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
}
</style>
<script>
(function () {
    // Disable theme-level interactivity (links, buttons, forms) inside the canvas.
    // Only preventDefault — no stopPropagation — so React's own selection/drag
    // handlers (bubble phase on root container) still fire for page builder UX.
    // Page builder control buttons (.z-20 container) are fully exempt.

    function inCanvas(target) {
        var canvas = document.querySelector('.page-builder-canvas');
        return canvas && canvas.contains(target);
    }

    function isBuilderControl(el) {
        return !!(el && el.closest && el.closest('.z-20'));
    }

    // Prevent link navigation and button-triggered submits inside the canvas.
    document.addEventListener('click', function (e) {
        if (!inCanvas(e.target) || isBuilderControl(e.target)) return;

        // Block anchor navigation
        if (e.target.closest('a[href]')) {
            e.preventDefault();
            return;
        }

        // Block submit buttons (leaves React drag/select handlers intact via bubble)
        if (e.target.closest('button[type="submit"], input[type="submit"]')) {
            e.preventDefault();
        }
    }, true);

    // Prevent all form submissions inside the canvas.
    document.addEventListener('submit', function (e) {
        if (inCanvas(e.target)) e.preventDefault();
    }, true);
}());
</script>
