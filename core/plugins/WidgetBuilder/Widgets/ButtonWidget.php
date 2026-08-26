<?php

namespace Plugins\WidgetBuilder\Widgets;

use Xgenious\PageBuilder\Core\Widgets\ButtonWidget as VendorButtonWidget;

class ButtonWidget extends VendorButtonWidget
{
    public function render(array $settings = []): string
    {
        $general  = $settings['general'] ?? [];
        $content  = $general['content']  ?? [];
        $type     = $general['type']     ?? [];
        $layout   = $general['layout']   ?? [];

        $text       = htmlspecialchars($content['text'] ?? 'Click me', ENT_QUOTES, 'UTF-8');
        $urlData    = $content['url'] ?? '#';
        $url        = is_array($urlData) ? ($urlData['url'] ?? '#') : $urlData;
        $target     = is_array($urlData) ? ($urlData['target'] ?? '_self') : '_self';

        $buttonType = $type['button_type'] ?? 'primary';
        $size       = $type['size']        ?? 'normal';
        $alignment  = $layout['alignment'] ?? 'left';
        $width      = $layout['width']     ?? 'auto';

        // Inline style defaults — no Tailwind dependency
        $typeStyles = [
            'primary'   => 'background:#3B82F6;color:#ffffff;',
            'secondary' => 'background:#f3f4f6;color:#111827;border:1px solid #d1d5db;',
            'success'   => 'background:#16a34a;color:#ffffff;',
            'danger'    => 'background:#dc2626;color:#ffffff;',
        ];
        $sizeStyles = [
            'small'  => 'padding:6px 14px;font-size:13px;',
            'normal' => 'padding:10px 22px;font-size:15px;',
            'large'  => 'padding:14px 30px;font-size:17px;',
        ];

        $btnStyle  = ($typeStyles[$buttonType] ?? $typeStyles['primary'])
                   . ($sizeStyles[$size]       ?? $sizeStyles['normal'])
                   . 'display:inline-block;border-radius:6px;text-decoration:none;font-weight:500;line-height:1;cursor:pointer;'
                   . ($width === 'full' ? 'width:100%;text-align:center;' : '');

        $alignMap    = ['center' => 'text-align:center;', 'right' => 'text-align:right;'];
        $wrapStyle   = $alignMap[$alignment] ?? '';

        return "<div style=\"{$wrapStyle}\"><a href=\"" . htmlspecialchars($url, ENT_QUOTES, 'UTF-8')
             . "\" target=\"{$target}\" style=\"{$btnStyle}\">{$text}</a></div>";
    }
}
