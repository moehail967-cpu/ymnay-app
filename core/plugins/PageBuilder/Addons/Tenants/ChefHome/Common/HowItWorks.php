<?php

namespace Plugins\PageBuilder\Addons\Tenants\ChefHome\Common;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Repeater;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\Helpers\RepeaterField;
use Plugins\PageBuilder\PageBuilderBase;

class HowItWorks extends PageBuilderBase
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

        $output .= Text::get(['name' => 'title',    'label' => __('Section Title'), 'value' => $v['title'] ?? null]);
        $output .= Text::get(['name' => 'subtitle',  'label' => __('Section Subtitle'), 'value' => $v['subtitle'] ?? null]);

        $output .= Repeater::get([
            'multi_lang' => false,
            'settings'   => $v,
            'id'         => 'steps_repeater',
            'fields'     => [
                ['type' => RepeaterField::ICON,  'label' => 'Step Icon',        'name' => 'icon'],
                ['type' => RepeaterField::TEXT,  'label' => 'Step Title',       'name' => 'title'],
                ['type' => RepeaterField::TEXT,  'label' => 'Step Description', 'name' => 'description'],
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
            'title'          => SanitizeInput::esc_html($this->setting_item('title') ?? __('How It Works')),
            'subtitle'       => SanitizeInput::esc_html($this->setting_item('subtitle') ?? ''),
            'repeater_data'  => $this->setting_item('steps_repeater'),
            'padding_top'    => SanitizeInput::esc_html($this->setting_item('padding_top') ?? '80'),
            'padding_bottom' => SanitizeInput::esc_html($this->setting_item('padding_bottom') ?? '80'),
        ];

        return self::renderView('tenant.chefhome.common.how-it-works', $data);
    }

    public function addon_title(): string
    {
        return __('ChefHome: How It Works');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
