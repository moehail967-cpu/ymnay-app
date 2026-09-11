<?php

namespace Plugins\PageBuilder\Addons\Tenants\Pharmacy\Blog;

use App\Helpers\SanitizeInput;
use Modules\Blog\Entities\Blog;
use Plugins\PageBuilder\Fields\Number;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\PageBuilderBase;

class BlogSection extends PageBuilderBase
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

        $output .= Text::get(['name' => 'title',         'label' => __('Section Title'),    'value' => $v['title']         ?? null]);
        $output .= Text::get(['name' => 'subtitle',      'label' => __('Section Subtitle'), 'value' => $v['subtitle']      ?? null]);
        $output .= Number::get(['name' => 'item_count',  'label' => __('Posts to Show'),    'value' => $v['item_count']    ?? 3]);
        $output .= Text::get(['name' => 'view_all_text', 'label' => __('View All Link Text'),'value' => $v['view_all_text'] ?? null]);
        $output .= Text::get(['name' => 'view_all_url',  'label' => __('View All URL'),      'value' => $v['view_all_url']  ?? null]);
        $output .= $this->padding_fields($v);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render(): string
    {
        $count = (int) ($this->setting_item('item_count') ?? 3);
        $posts = Blog::where('status', 1)->latest()->take($count)->get();

        $data = [
            'title'          => SanitizeInput::esc_html($this->setting_item('title')         ?? __('Health Advice')),
            'subtitle'       => SanitizeInput::esc_html($this->setting_item('subtitle')      ?? __('Expert tips from our pharmacists')),
            'view_all_text'  => SanitizeInput::esc_html($this->setting_item('view_all_text') ?? __('All Articles')),
            'view_all_url'   => SanitizeInput::esc_url($this->setting_item('view_all_url')   ?? route('tenant.frontend.blog.index')),
            'posts'          => $posts,
            'padding_top'    => SanitizeInput::esc_html($this->setting_item('padding_top')    ?? '80'),
            'padding_bottom' => SanitizeInput::esc_html($this->setting_item('padding_bottom') ?? '80'),
        ];

        return self::renderView('tenant.pharmacy.blog.blog-section', $data);
    }

    public function addon_title(): string
    {
        return __('Pharmacy: Health Tips / Blog');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
