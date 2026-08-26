<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Footer;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Repeater;
use Plugins\PageBuilder\Fields\Textarea;
use Plugins\PageBuilder\Helpers\RepeaterField;
use Plugins\WidgetBuilder\WidgetBase;

class FooterSocialWidget extends WidgetBase
{
    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $widget_saved_values = $this->get_settings();

        $output .= Textarea::get([
            'name' => 'description',
            'label' => __('Description'),
            'value' => $widget_saved_values['description'] ?? null
        ]);

        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'footer_social_links',
            'fields' => [
                [
                    'type' => RepeaterField::ICON_PICKER,
                    'name' => 'social_icon',
                    'label' => __('Social Icon')
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'social_url',
                    'label' => __('Social URL')
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
        $description = SanitizeInput::esc_html($widget_saved_values['description'] ?? '');

        $repeater_data = $widget_saved_values['footer_social_links'] ?? [];
        $social_markup = '';

        if (!empty($repeater_data) && array_key_exists('social_icon_', $repeater_data)) {
            foreach ($repeater_data['social_icon_'] as $key => $icon) {
                $social_icon = $icon ?? '';
                $social_url  = SanitizeInput::esc_url($repeater_data['social_url_'][$key] ?? '#');
                $social_markup .= '<a href="' . $social_url . '" target="_blank" class="w-10 h-10 rounded-lg flex items-center justify-center transition" style="border: 1px solid var(--extra-light-color, #CFCFCF); color: var(--body-color, #4b5563)">'
                    . '<i class="' . $social_icon . '"></i>'
                    . '</a>';
            }
        }

        return <<<HTML
<div class="col-span-2 md:col-span-3 lg:col-span-6">
    <div class="flex flex-wrap gap-3 mb-5">
        {$social_markup}
    </div>
    <p class="text-sm leading-relaxed max-w-sm" style="color: var(--body-color, #4b5563)">{$description}</p>
</div>
HTML;
    }

    public function widget_title()
    {
        return __('New Footer: Social Links');
    }

    public function enable(): bool
    {
        return is_null(tenant());
    }
}
