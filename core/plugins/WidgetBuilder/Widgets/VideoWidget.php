<?php

namespace Plugins\WidgetBuilder\Widgets;

use Xgenious\PageBuilder\Widgets\Media\VideoWidget as VendorVideoWidget;

class VideoWidget extends VendorVideoWidget
{
    public function render(array $settings = []): string
    {
        // Vendor's render() reads all fields flat from $general/$style,
        // but ControlManager stores them nested under group IDs.
        // Merge each known group up into a flat array before delegating.
        $gen = $settings['general'] ?? [];
        $settings['general'] = array_merge(
            $gen['source']   ?? [],
            $gen['settings'] ?? [],
            $gen['playback'] ?? [],
            $gen['overlay']  ?? []
        );

        $sty = $settings['style'] ?? [];
        $settings['style'] = array_merge(
            $sty['container'] ?? [],
            $sty['border']    ?? [],
            $sty['overlay']   ?? [],
            $sty['spacing']   ?? []
        );

        // IMAGE fields return {img_url, url, ...} arrays — extract string URL
        foreach (['hosted_url', 'poster_image'] as $field) {
            if (isset($settings['general'][$field]) && is_array($settings['general'][$field])) {
                $arr = $settings['general'][$field];
                $settings['general'][$field] = $arr['img_url'] ?? $arr['url'] ?? '';
            }
        }

        // URL fields return {url: '...', target: '...', ...} arrays — extract string URL
        foreach (['youtube_url', 'vimeo_url'] as $field) {
            if (isset($settings['general'][$field]) && is_array($settings['general'][$field])) {
                $settings['general'][$field] = $settings['general'][$field]['url'] ?? '';
            }
        }

        return parent::render($settings);
    }
}
