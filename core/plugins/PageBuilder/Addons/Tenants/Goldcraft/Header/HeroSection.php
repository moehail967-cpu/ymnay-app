<?php

namespace Plugins\PageBuilder\Addons\Tenants\Goldcraft\Header;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Image;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\Fields\Textarea;
use Plugins\PageBuilder\PageBuilderBase;

class HeroSection extends PageBuilderBase
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

        $output .= Text::get(['name' => 'badge_text',   'label' => __('Badge Text (pill above headline)'),    'value' => $v['badge_text'] ?? null]);
        $output .= Text::get(['name' => 'title',         'label' => __('Headline'),                            'value' => $v['title'] ?? null]);
        $output .= Textarea::get(['name' => 'subtitle',  'label' => __('Sub-headline'),                        'value' => $v['subtitle'] ?? null]);
        $output .= Text::get(['name' => 'button_text',   'label' => __('Primary Button Text'),                 'value' => $v['button_text'] ?? null]);
        $output .= Text::get(['name' => 'button_url',    'label' => __('Primary Button URL'),                  'value' => $v['button_url'] ?? null]);
        $output .= Text::get(['name' => 'button2_text',  'label' => __('Secondary Button Text'),               'value' => $v['button2_text'] ?? null]);
        $output .= Text::get(['name' => 'button2_url',   'label' => __('Secondary Button URL'),                'value' => $v['button2_url'] ?? null]);
        $output .= Image::get(['name' => 'hero_image',   'label' => __('Hero Image (right side)'),             'value' => $v['hero_image'] ?? null, 'dimensions' => '500x500 px (PNG transparent preferred)']);
        $output .= $this->padding_fields($v);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render(): string
    {
        $data = [
            'badge_text'     => SanitizeInput::esc_html($this->setting_item('badge_text') ?? ''),
            'title'          => SanitizeInput::esc_html($this->setting_item('title') ?? ''),
            'subtitle'       => SanitizeInput::esc_html($this->setting_item('subtitle') ?? ''),
            'button_text'    => SanitizeInput::esc_html($this->setting_item('button_text') ?? ''),
            'button_url'     => SanitizeInput::esc_url($this->setting_item('button_url') ?? '#'),
            'button2_text'   => SanitizeInput::esc_html($this->setting_item('button2_text') ?? ''),
            'button2_url'    => SanitizeInput::esc_url($this->setting_item('button2_url') ?? '#'),
            'hero_image'     => $this->setting_item('hero_image'),
            'padding_top'    => SanitizeInput::esc_html($this->setting_item('padding_top') ?? '32'),
            'padding_bottom' => SanitizeInput::esc_html($this->setting_item('padding_bottom') ?? '32'),
        ];

        return self::renderView('tenant.goldcraft.header.hero-section', $data);
    }

    public function addon_title(): string
    {
        return __('Goldcraft: Hero Section');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
