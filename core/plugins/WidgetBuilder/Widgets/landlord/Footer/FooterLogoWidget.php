<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Footer;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Image;
use Plugins\WidgetBuilder\WidgetBase;

class FooterLogoWidget extends WidgetBase
{
    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $widget_saved_values = $this->get_settings();

        $output .= Image::get([
            'name' => 'footer_logo',
            'label' => __('Footer Logo'),
            'value' => $widget_saved_values['footer_logo'] ?? null
        ]);

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render()
    {
        $widget_saved_values = $this->get_settings();
        $logo_id = $widget_saved_values['footer_logo'] ?? '';
        $logo_image = render_image_markup_by_attachment_id($logo_id, 'h-10');
        $root_url = url('/');

        return <<<HTML
<div class="col-span-2 md:col-span-3 lg:col-span-6">
    <a href="{$root_url}">
        {$logo_image}
    </a>
</div>
HTML;
    }

    public function widget_title()
    {
        return __('New Footer: Logo');
    }

    public function enable(): bool
    {
        return is_null(tenant());
    }
}
