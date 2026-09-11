<?php

namespace Plugins\WidgetBuilder\Widgets;

use Xgenious\PageBuilder\Widgets\Media\ImageWidget as VendorImageWidget;

class ImageWidget extends VendorImageWidget
{
    public function render(array $settings = []): string
    {
        // IMAGE field stores array {img_url, url, id, ...} from the media uploader.
        // The vendor passes it directly to htmlspecialchars() expecting a string — fix here.
        if (isset($settings['general']['content']['image_source'])
            && is_array($settings['general']['content']['image_source'])) {
            $src = $settings['general']['content']['image_source'];
            $settings['general']['content']['image_source'] = $src['img_url'] ?? $src['url'] ?? '';
        }

        // URL field stores {url: '...', target: '...', ...} — extract string URL
        if (isset($settings['general']['link']['link_url'])
            && is_array($settings['general']['link']['link_url'])) {
            $settings['general']['link']['link_url'] = $settings['general']['link']['link_url']['url'] ?? '#';
        }

        $imageSrc = $settings['general']['content']['image_source'] ?? '';

        if (empty($imageSrc)) {
            return '<div class="xgpb-image-placeholder">'
                . '<i class="las la-camera"></i>'
                . '<span>Select or upload an image</span>'
                . '</div>'
                . '<style>'
                . '.xgpb-image-placeholder{'
                . 'display:flex;flex-direction:column;align-items:center;justify-content:center;'
                . 'min-height:160px;border:2px dashed #d1d5db;border-radius:8px;'
                . 'background:#f9fafb;color:#9ca3af;gap:8px;'
                . '}'
                . '.xgpb-image-placeholder i{font-size:40px;}'
                . '.xgpb-image-placeholder span{font-size:13px;}'
                . '</style>';
        }

        return parent::render($settings);
    }
}
