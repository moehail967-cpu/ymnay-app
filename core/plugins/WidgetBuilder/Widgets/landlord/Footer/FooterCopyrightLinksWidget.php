<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Footer;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Repeater;
use Plugins\PageBuilder\Helpers\RepeaterField;
use Plugins\WidgetBuilder\WidgetBase;

class FooterCopyrightLinksWidget extends WidgetBase
{
    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $widget_saved_values = $this->get_settings();

        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'footer_copyright_links',
            'fields' => [
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'link_text',
                    'label' => __('Link Text')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'link_url',
                    'label' => __('Link URL')
                ],
            ]
        ]);

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render()
    {
        $widget_saved_values = $this->get_settings();
        $repeater_data = $widget_saved_values['footer_copyright_links'] ?? [];
        $links = [];

        if (!empty($repeater_data) && array_key_exists('link_text_', $repeater_data)) {
            foreach ($repeater_data['link_text_'] as $key => $text) {
                $link_text = SanitizeInput::esc_html($text ?? '');
                $link_url  = SanitizeInput::esc_url($repeater_data['link_url_'][$key] ?? '#');
                if (!empty($link_text)) {
                    $links[] = '<a href="' . $link_url . '" class="text-sm transition" style="color: var(--body-color, #4b5563)">' . $link_text . '</a>';
                }
            }
        }

        if (empty($links)) {
            return '';
        }

        $output = implode('<span class="mx-1" style="color: var(--extra-light-color, #d1d5db)">|</span>', $links);

        return '<div class="flex items-center flex-wrap gap-1">' . $output . '</div>';
    }

    public function widget_title()
    {
        return __('New Footer: Copyright Links');
    }

    public function enable(): bool
    {
        return is_null(tenant());
    }
}
