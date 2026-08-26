<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Footer;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Repeater;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\Helpers\RepeaterField;
use Plugins\WidgetBuilder\WidgetBase;

class FooterNavLinksWidget extends WidgetBase
{
    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $widget_saved_values = $this->get_settings();

        $output .= Text::get([
            'name' => 'title',
            'label' => __('Title'),
            'value' => $widget_saved_values['title'] ?? null
        ]);

        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'footer_nav_links',
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
        $title = SanitizeInput::esc_html($widget_saved_values['title'] ?? '');

        $repeater_data = $widget_saved_values['footer_nav_links'] ?? [];
        $links_markup = '';

        if (!empty($repeater_data) && array_key_exists('link_text_', $repeater_data)) {
            foreach ($repeater_data['link_text_'] as $key => $text) {
                $link_text = SanitizeInput::esc_html($text ?? '');
                $link_url  = SanitizeInput::esc_url($repeater_data['link_url_'][$key] ?? '#');
                $links_markup .= '<li><a href="' . $link_url . '" class="text-sm transition" style="color: var(--body-color, #4b5563)">' . $link_text . '</a></li>';
            }
        }

        return <<<HTML
<div class="col-span-1 lg:col-span-2">
    <h3 class="font-semibold text-sm mb-4" style="color: var(--heading-color, #111827)">{$title}</h3>
    <ul class="space-y-3">
        {$links_markup}
    </ul>
</div>
HTML;
    }

    public function widget_title()
    {
        return __('New Footer: Nav Links');
    }

    public function enable(): bool
    {
        return is_null(tenant());
    }
}
