<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant\Footer;

use App\Models\TopbarInfo;
use Plugins\WidgetBuilder\WidgetBase;

class GlobalFooterBrandWidget extends WidgetBase
{
    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();
        $output .= '<p class="text-muted" style="font-size:.8rem;">'
            . __('This widget automatically displays your site logo, description, and social icons from your site settings.')
            . '</p>';
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render()
    {
        $logo    = theme_white_logo_url() ?? theme_logo_url();
        $homeUrl = theme_home_url();
        $title   = get_static_option('site_title') ?? '';
        $desc    = get_static_option('site_description') ?? '';
        $socials = TopbarInfo::all();

        if ($logo) {
            $logoHtml = '<a href="' . $homeUrl . '" class="d-inline-block mb-3">'
                . '<img src="' . $logo . '" alt="' . e($title) . '" style="max-height:44px;">'
                . '</a>';
        } else {
            $logoHtml = '<a href="' . $homeUrl . '" class="d-inline-block mb-3 fw-bold fs-5 text-decoration-none">'
                . e($title)
                . '</a>';
        }

        $descHtml = $desc
            ? '<p class="mb-4" style="font-size:.875rem;line-height:1.65;opacity:.75;">' . e($desc) . '</p>'
            : '';

        $socialHtml = '';
        if ($socials->isNotEmpty()) {
            $socialHtml = '<div class="d-flex gap-2 flex-wrap">';
            foreach ($socials as $s) {
                $socialHtml .= '<a href="' . e($s->url) . '" target="_blank" rel="noopener"'
                    . ' class="d-inline-flex align-items-center justify-content-center rounded"'
                    . ' style="width:36px;height:36px;border:1px solid currentColor;opacity:.7;">'
                    . '<i class="' . e($s->icon) . '"></i>'
                    . '</a>';
            }
            $socialHtml .= '</div>';
        }

        return '<div class="col-lg-4 col-md-6">'
            . $logoHtml
            . $descHtml
            . $socialHtml
            . '</div>';
    }

    public function widget_title()
    {
        return __('Footer: Brand & Social');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
