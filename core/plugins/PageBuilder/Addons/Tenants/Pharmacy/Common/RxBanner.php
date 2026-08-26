<?php

namespace Plugins\PageBuilder\Addons\Tenants\Pharmacy\Common;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\Fields\Textarea;
use Plugins\PageBuilder\PageBuilderBase;

class RxBanner extends PageBuilderBase
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

        $output .= Text::get(['name' => 'badge_text',   'label' => __('Badge Text'),            'value' => $v['badge_text']   ?? null]);
        $output .= Text::get(['name' => 'title',        'label' => __('Title'),                 'value' => $v['title']        ?? null]);
        $output .= Textarea::get(['name' => 'text',     'label' => __('Body Text'),             'value' => $v['text']         ?? null]);
        $output .= Text::get(['name' => 'btn1_text',    'label' => __('Primary Button Text'),   'value' => $v['btn1_text']    ?? null]);
        $output .= Text::get(['name' => 'btn1_url',     'label' => __('Primary Button URL'),    'value' => $v['btn1_url']     ?? null]);
        $output .= Text::get(['name' => 'btn2_text',    'label' => __('Secondary Button Text'), 'value' => $v['btn2_text']    ?? null]);
        $output .= Text::get(['name' => 'btn2_url',     'label' => __('Secondary Button URL'),  'value' => $v['btn2_url']     ?? null]);
        $output .= Text::get(['name' => 'step1_label',  'label' => __('Step 1 Label'),          'value' => $v['step1_label']  ?? null]);
        $output .= Text::get(['name' => 'step2_label',  'label' => __('Step 2 Label'),          'value' => $v['step2_label']  ?? null]);
        $output .= Text::get(['name' => 'step3_label',  'label' => __('Step 3 Label'),          'value' => $v['step3_label']  ?? null]);
        $output .= $this->padding_fields($v);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render(): string
    {
        $data = [
            'badge_text'     => SanitizeInput::esc_html($this->setting_item('badge_text')  ?? ''),
            'title'          => SanitizeInput::esc_html($this->setting_item('title')       ?? ''),
            'text'           => SanitizeInput::esc_html($this->setting_item('text')        ?? ''),
            'btn1_text'      => SanitizeInput::esc_html($this->setting_item('btn1_text')   ?? ''),
            'btn1_url'       => SanitizeInput::esc_url($this->setting_item('btn1_url')     ?? '#'),
            'btn2_text'      => SanitizeInput::esc_html($this->setting_item('btn2_text')   ?? ''),
            'btn2_url'       => SanitizeInput::esc_url($this->setting_item('btn2_url')     ?? '#'),
            'step1_label'    => SanitizeInput::esc_html($this->setting_item('step1_label') ?? __('Upload')),
            'step2_label'    => SanitizeInput::esc_html($this->setting_item('step2_label') ?? __('Verified')),
            'step3_label'    => SanitizeInput::esc_html($this->setting_item('step3_label') ?? __('Delivered')),
            'padding_top'    => SanitizeInput::esc_html($this->setting_item('padding_top')    ?? '60'),
            'padding_bottom' => SanitizeInput::esc_html($this->setting_item('padding_bottom') ?? '60'),
        ];

        return self::renderView('tenant.pharmacy.common.rx-banner', $data);
    }

    public function addon_title(): string
    {
        return __('Pharmacy: Prescription Banner');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
