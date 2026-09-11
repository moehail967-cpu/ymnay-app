<?php

namespace Plugins\PageBuilder\Addons\Tenants\Fitpeak\Product;

use App\Helpers\SanitizeInput;
use Modules\Attributes\Entities\Category;
use Plugins\PageBuilder\Fields\Number;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\PageBuilderBase;

class CategoryGrid extends PageBuilderBase
{
    public function preview_image(): string
    {
        return 'Tenant/themes/universal/product/category-grid.jpg';
    }

    public function admin_render(): string
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $v = $this->get_settings();

        $output .= Text::get(['name' => 'title',       'label' => __('Section Title'),       'value' => $v['title'] ?? null]);
        $output .= Number::get(['name' => 'item_count', 'label' => __('Categories to Show'), 'value' => $v['item_count'] ?? 6]);
        $output .= $this->padding_fields($v);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render(): string
    {
        $count      = (int) ($this->setting_item('item_count') ?? 6);
        $categories = Category::where('status', 1)->take($count)->get();

        $data = [
            'title'          => SanitizeInput::esc_html($this->setting_item('title') ?? __('Shop by Goal')),
            'categories'     => $categories,
            'padding_top'    => SanitizeInput::esc_html($this->setting_item('padding_top') ?? '80'),
            'padding_bottom' => SanitizeInput::esc_html($this->setting_item('padding_bottom') ?? '80'),
        ];

        return self::renderView('tenant.fitpeak.product.category-grid', $data);
    }

    public function addon_title(): string
    {
        return __('Fitpeak: Category Grid');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
