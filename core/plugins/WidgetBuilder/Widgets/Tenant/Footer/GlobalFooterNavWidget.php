<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant\Footer;

use App\Helpers\SanitizeInput;
use App\Models\Menu;
use Plugins\PageBuilder\Fields\Text;
use Plugins\WidgetBuilder\WidgetBase;

class GlobalFooterNavWidget extends WidgetBase
{
    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $widget_saved_values = $this->get_settings();

        $output .= Text::get([
            'name'  => 'title',
            'label' => __('Column Title'),
            'value' => $widget_saved_values['title'] ?? '',
        ]);

        $selected_menu_id = $widget_saved_values['menu_id'] ?? '';
        $all_menus        = Menu::all();

        $output .= '<div class="form-group"><label>' . __('Select Menu') . '</label>';
        $output .= '<select class="form-control" name="menu_id">';
        $output .= '<option value="">' . __('— None —') . '</option>';
        foreach ($all_menus as $menu) {
            $sel     = ((string) $selected_menu_id === (string) $menu->id) ? 'selected' : '';
            $output .= '<option value="' . $menu->id . '" ' . $sel . '>'
                . SanitizeInput::esc_html($menu->title)
                . '</option>';
        }
        $output .= '</select></div>';

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render()
    {
        $widget_saved_values = $this->get_settings();
        $title   = SanitizeInput::esc_html($widget_saved_values['title'] ?? '');
        $menu_id = $widget_saved_values['menu_id'] ?? null;

        if (!$menu_id) {
            return '';
        }

        $links = theme_footer_menu_links((int) $menu_id);

        if (empty($links)) {
            return '';
        }

        $linksHtml = '';
        foreach ($links as $link) {
            $url   = SanitizeInput::esc_url($link['url'] ?? '#');
            $label = SanitizeInput::esc_html($link['label'] ?? '');
            $linksHtml .= '<a href="' . $url . '" class="d-block mb-2"'
                . ' style="font-size:.875rem;text-decoration:none;opacity:.8;">'
                . $label
                . '</a>';
        }

        $titleHtml = $title
            ? '<h6 class="fw-semibold mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.06em;">'
                . $title . '</h6>'
            : '';

        return '<div class="col-lg-2 col-md-3 col-6">'
            . $titleHtml
            . $linksHtml
            . '</div>';
    }

    public function widget_title()
    {
        return __('Footer: Nav Links Column');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
