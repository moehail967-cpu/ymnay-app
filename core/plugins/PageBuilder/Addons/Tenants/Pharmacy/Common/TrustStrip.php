<?php

namespace Plugins\PageBuilder\Addons\Tenants\Pharmacy\Common;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\PageBuilderBase;

class TrustStrip extends PageBuilderBase
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

        foreach (range(1, 4) as $i) {
            $output .= Text::get(['name' => "item_{$i}_icon",  'label' => __("Item {$i}: MDI Icon Class"), 'value' => $v["item_{$i}_icon"]  ?? null, 'info' => __('e.g. mdi-certificate-outline')]);
            $output .= Text::get(['name' => "item_{$i}_title", 'label' => __("Item {$i}: Title"),          'value' => $v["item_{$i}_title"] ?? null]);
            $output .= Text::get(['name' => "item_{$i}_sub",   'label' => __("Item {$i}: Sub-text"),       'value' => $v["item_{$i}_sub"]   ?? null]);
        }

        $output .= $this->padding_fields($v);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render(): string
    {
        $items = [];
        $defaults = [
            1 => ['icon' => 'mdi-certificate-outline', 'title' => __('GPhC Registered'),       'sub' => __('Reg. No. 9011482')],
            2 => ['icon' => 'mdi-account-tie-outline',  'title' => __('Qualified Pharmacists'), 'sub' => __('Available 7 days/week')],
            3 => ['icon' => 'mdi-snowflake',             'title' => __('Cold Chain Storage'),   'sub' => __('Temperature controlled')],
            4 => ['icon' => 'mdi-lock-outline',          'title' => __('Secure Checkout'),      'sub' => __('256-bit SSL encryption')],
        ];

        foreach (range(1, 4) as $i) {
            $items[] = [
                'icon'  => SanitizeInput::esc_html($this->setting_item("item_{$i}_icon")  ?? $defaults[$i]['icon']),
                'title' => SanitizeInput::esc_html($this->setting_item("item_{$i}_title") ?? $defaults[$i]['title']),
                'sub'   => SanitizeInput::esc_html($this->setting_item("item_{$i}_sub")   ?? $defaults[$i]['sub']),
            ];
        }

        $data = [
            'items'          => $items,
            'padding_top'    => SanitizeInput::esc_html($this->setting_item('padding_top')    ?? '32'),
            'padding_bottom' => SanitizeInput::esc_html($this->setting_item('padding_bottom') ?? '32'),
        ];

        return self::renderView('tenant.pharmacy.common.trust-strip', $data);
    }

    public function addon_title(): string
    {
        return __('Pharmacy: Trust Strip');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
