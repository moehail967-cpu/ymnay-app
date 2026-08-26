<?php

namespace Plugins\PageBuilder\Addons\Tenants\Universal\Common;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Repeater;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\Helpers\RepeaterField;
use Plugins\PageBuilder\PageBuilderBase;

class ServicesStrip extends PageBuilderBase
{
    public function preview_image(): string
    {
        return 'Tenant/themes/universal/common/services-strip.jpg';
    }

    public function admin_render(): string
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $v = $this->get_settings();

        $output .= Text::get(['name' => 'title', 'label' => __('Section Title'), 'value' => $v['title'] ?? null]);

        $output .= Repeater::get([
            'multi_lang' => false,
            'settings'   => $v,
            'id'         => 'services_repeater',
            'fields'     => [
                ['type' => RepeaterField::ICON_PICKER, 'label' => 'Icon', 'name' => 'icon'],
                ['type' => RepeaterField::TEXT, 'label' => 'Title', 'name' => 'title'],
                ['type' => RepeaterField::TEXT, 'label' => 'Description', 'name' => 'description'],
            ],
        ]);

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
            'repeater_data'  => $this->setting_item('services_repeater'),
            'padding_top'    => SanitizeInput::esc_html($this->setting_item('padding_top') ?? '60'),
            'padding_bottom' => SanitizeInput::esc_html($this->setting_item('padding_bottom') ?? '60'),
        ];

        return self::renderView('tenant.universal.common.services-strip', $data);
    }

    public function addon_title(): string
    {
        return __('Services Strip');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
