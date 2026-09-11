<?php

namespace Plugins\WidgetBuilder\Widgets;

use Xgenious\PageBuilder\Core\Widgets\ParagraphWidget as VendorParagraphWidget;

class ParagraphWidget extends VendorParagraphWidget
{
    protected function getWidgetDefaultCSS(string $widgetId): string
    {
        // Vendor's generateCSS() has a registerStyleFields() no-op bug — generates no CSS.
        // Theme CSS then bleeds through (e.g. blue text colour). Inject neutral defaults here
        // so the paragraph always has sensible styling even before the user configures it.
        return '.paragraph-element{color:#333333;font-size:1rem;line-height:1.6;margin-bottom:1rem}';
    }
}
