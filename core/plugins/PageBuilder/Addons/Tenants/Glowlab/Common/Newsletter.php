<?php

namespace Plugins\PageBuilder\Addons\Tenants\Glowlab\Common;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\PageBuilderBase;

class Newsletter extends PageBuilderBase
{
    public function preview_image(): string
    {
        return 'Tenant/themes/universal/header/hero.jpg';
    }

    public function admin_render(): string
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $v = $this->get_settings();

        $output .= Text::get(['name' => 'title',       'label' => __('Title'),       'value' => $v['title'] ?? null]);
        $output .= Text::get(['name' => 'subtitle',    'label' => __('Subtitle'),    'value' => $v['subtitle'] ?? null]);
        $output .= Text::get(['name' => 'button_text', 'label' => __('Button Text'), 'value' => $v['button_text'] ?? null]);
        $output .= $this->padding_fields($v);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render(): string
    {
        $data = [
            'title'          => SanitizeInput::esc_html($this->setting_item('title') ?? ''),
            'subtitle'       => SanitizeInput::esc_html($this->setting_item('subtitle') ?? ''),
            'button_text'    => SanitizeInput::esc_html($this->setting_item('button_text') ?? __('Subscribe')),
            'padding_top'    => SanitizeInput::esc_html($this->setting_item('padding_top') ?? '80'),
            'padding_bottom' => SanitizeInput::esc_html($this->setting_item('padding_bottom') ?? '80'),
        ];

        return self::renderView('tenant.glowlab.common.newsletter', $data);
    }

    public function addon_title(): string
    {
        return __('Glowlab: Newsletter');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
