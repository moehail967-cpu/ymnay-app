<?php

namespace Plugins\WidgetBuilder\Widgets;

use Xgenious\PageBuilder\Core\Widgets\HeadingWidget as VendorHeadingWidget;

class HeadingWidget extends VendorHeadingWidget
{
    public function render(array $settings = []): string
    {
        // Vendor's renderManually() checks `in_array($content['heading_level'] ?? 'h2', [...])` then accesses
        // `$content['heading_level']` again without null coalescing — throws ErrorException when key is absent.
        // Pre-fill defaults so the key always exists.
        $settings['general']['content']['heading_level'] ??= 'h2';
        $settings['general']['link']['enhanced_link']['target'] ??= '_self';

        $html = parent::render($settings);

        // Vendor style fields use '{{WRAPPER}} .heading-element' selectors but the rendered element
        // never has .heading-element class — CSS never matches, live style updates don't reflect.
        // Inject the class onto the h1-h6 tag so vendor-generated CSS selectors work.
        $html = preg_replace('/(<h[1-6]\s+class=")/i', '$1heading-element ', $html, 1);

        // Vendor's generateStyleAttribute() bakes current color/typography settings as an inline
        // style="..." attribute directly on the h1-h6 element. Inline styles have the highest
        // specificity and cannot be overridden by the CSS selector system — so live style-panel
        // changes never reflect in the editor canvas. Strip the inline style so all styling is
        // driven by the generated CSS selectors ({{WRAPPER}} .heading-element { color: ... }).
        $html = preg_replace('/\s*style="[^"]*"/', '', $html);

        return $html;
    }

    protected function getWidgetDefaultCSS(string $widgetId): string
    {
        // The page-builder.css has h1-h6 { font-size: inherit } (Tailwind preflight),
        // which strips browser heading defaults inside the editor canvas.
        // These classes are already added to the element by buildCssClasses() so they override
        // the preflight reset without needing a scoped wrapper selector.
        return '
.heading-h1{font-size:2rem;font-weight:700;line-height:1.2;margin-bottom:.5rem}
.heading-h2{font-size:1.75rem;font-weight:600;line-height:1.25;margin-bottom:.5rem}
.heading-h3{font-size:1.5rem;font-weight:600;line-height:1.3;margin-bottom:.5rem}
.heading-h4{font-size:1.25rem;font-weight:600;line-height:1.35;margin-bottom:.5rem}
.heading-h5{font-size:1.125rem;font-weight:600;line-height:1.4;margin-bottom:.5rem}
.heading-h6{font-size:1rem;font-weight:600;line-height:1.4;margin-bottom:.5rem}';
    }
}
