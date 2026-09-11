<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant\Footer;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Repeater;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\Helpers\RepeaterField;
use Plugins\WidgetBuilder\WidgetBase;

class GlobalFooterBadgesWidget extends WidgetBase
{
    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $widget_saved_values = $this->get_settings();

        $output .= Text::get([
            'name'  => 'title',
            'label' => __('Section Title'),
            'value' => $widget_saved_values['title'] ?? '',
        ]);

        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id'       => 'footer_badges',
            'fields'   => [
                ['type' => RepeaterField::TEXT, 'name' => 'badge_icon',  'label' => __('Icon / Emoji')],
                ['type' => RepeaterField::TEXT, 'name' => 'badge_title', 'label' => __('Title')],
                ['type' => RepeaterField::TEXT, 'name' => 'badge_sub',   'label' => __('Subtitle')],
            ],
        ]);

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render()
    {
        $widget_saved_values = $this->get_settings();
        $title    = SanitizeInput::esc_html($widget_saved_values['title'] ?? '');
        $repeater = $widget_saved_values['footer_badges'] ?? [];

        $badgesHtml = '';
        foreach ($repeater['badge_title_'] ?? [] as $i => $badgeTitle) {
            $icon   = $repeater['badge_icon_'][$i]  ?? '';
            $sub    = SanitizeInput::esc_html($repeater['badge_sub_'][$i]  ?? '');
            $btitle = SanitizeInput::esc_html($badgeTitle ?? '');
            $badgesHtml .= '<div class="d-flex align-items-start gap-2 mb-3">'
                . '<span style="font-size:1.1rem;line-height:1.4;flex-shrink:0;">' . $icon . '</span>'
                . '<div>'
                . '<div style="font-size:.8rem;font-weight:600;">' . $btitle . '</div>'
                . ($sub ? '<div style="font-size:.75rem;opacity:.65;">' . $sub . '</div>' : '')
                . '</div>'
                . '</div>';
        }

        if (!$badgesHtml) {
            return '';
        }

        $titleHtml = $title
            ? '<h6 class="fw-semibold mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.06em;">'
                . $title . '</h6>'
            : '';

        return '<div class="col-lg-4 col-md-6">'
            . $titleHtml
            . $badgesHtml
            . '</div>';
    }

    public function widget_title()
    {
        return __('Footer: Trust & Badges');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
