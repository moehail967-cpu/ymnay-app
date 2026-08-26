<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Footer;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Text;
use Plugins\WidgetBuilder\WidgetBase;

class FooterNewsletterWidget extends WidgetBase
{
    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $widget_saved_values = $this->get_settings();

        $output .= Text::get([
            'name' => 'heading',
            'label' => __('Heading'),
            'value' => $widget_saved_values['heading'] ?? null
        ]);

        $output .= Text::get([
            'name' => 'button_text',
            'label' => __('Button Text'),
            'value' => $widget_saved_values['button_text'] ?? null
        ]);

        $output .= Text::get([
            'name' => 'disclaimer',
            'label' => __('Disclaimer Text'),
            'value' => $widget_saved_values['disclaimer'] ?? null
        ]);

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render()
    {
        $widget_saved_values = $this->get_settings();
        $heading      = SanitizeInput::esc_html($widget_saved_values['heading']      ?? 'Join our newsletter to stay updated on new features and releases');
        $button_text  = SanitizeInput::esc_html($widget_saved_values['button_text']  ?? 'Start Free Trial');
        $disclaimer   = SanitizeInput::esc_html($widget_saved_values['disclaimer']   ?? 'Subscribe to agree to our Privacy Policy and get updates');

        $action_url = route('landlord.frontend.newsletter.store.ajax');
        $csrf_token = csrf_token();

        return <<<HTML
<div class="col-span-2 md:col-span-3 lg:col-span-6">
    <p class="font-semibold text-base leading-snug mb-4 max-w-[333px]" style="color: var(--heading-color, #111827)">{$heading}</p>
    <form action="{$action_url}" method="POST" class="newsletter-form-widget max-w-md">
        <input type="hidden" name="_token" value="{$csrf_token}">
        <div class="flex items-center gap-2 mb-3">
            <input type="email" name="email" placeholder="Email"
                class="flex-1 px-4 py-2.5 rounded-lg text-sm focus:outline-none min-w-0"
                style="border: 1px solid var(--extra-light-color, #d1d5db); background-color: var(--section-bg-1, #fff); color: var(--heading-color, #111827)">
            <button type="submit"
                class="px-5 py-2.5 bg-primary text-white text-sm font-medium rounded-lg hover:opacity-90 whitespace-nowrap transition">{$button_text}</button>
        </div>
    </form>
    <p class="text-xs max-w-md" style="color: var(--body-color, #6b7280)">{$disclaimer}</p>
</div>
HTML;
    }

    public function widget_title()
    {
        return __('New Footer: Newsletter');
    }

    public function enable(): bool
    {
        return is_null(tenant());
    }
}
