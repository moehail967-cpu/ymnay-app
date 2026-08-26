<?php

namespace Plugins\PageBuilder\Addons\Tenants\ChefHome\Product;

use App\Helpers\SanitizeInput;
use Modules\Attributes\Entities\Category;
use Modules\Product\Entities\Product;
use Plugins\PageBuilder\Fields\Number;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\PageBuilderBase;

class ProductsByCategory extends PageBuilderBase
{
    public function preview_image(): string
    {
        return 'Tenant/themes/universal/product/featured-products.jpg';
    }

    public function admin_render(): string
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $v = $this->get_settings();

        $output .= Text::get(['name' => 'title',          'label' => __('Section Title'),             'value' => $v['title'] ?? null]);
        $output .= Number::get(['name' => 'category_count', 'label' => __('Number of Category Tabs'), 'value' => $v['category_count'] ?? 4]);
        $output .= Number::get(['name' => 'products_per_tab', 'label' => __('Products Per Tab'),      'value' => $v['products_per_tab'] ?? 4]);
        $output .= Text::get(['name' => 'view_all_url',   'label' => __('View All URL'),              'value' => $v['view_all_url'] ?? null]);
        $output .= $this->padding_fields($v);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render(): string
    {
        $catCount  = (int) ($this->setting_item('category_count') ?? 4);
        $perTab    = (int) ($this->setting_item('products_per_tab') ?? 4);

        $categories = Category::where('status', 1)->take($catCount)->get();

        // Eager-load products for each category
        $tabData = $categories->map(function (Category $cat) use ($perTab) {
            $products = Product::with('badge', 'campaign_product', 'inventory', 'inventoryDetail')
                ->where('status_id', 1)
                ->where('category_id', $cat->id)
                ->withSum('taxOptions', 'rate')
                ->latest()
                ->take($perTab)
                ->get();

            return ['category' => $cat, 'products' => $products];
        });

        $data = [
            'title'          => SanitizeInput::esc_html($this->setting_item('title') ?? __('Order by Category')),
            'tabs'           => $tabData,
            'view_all_url'   => SanitizeInput::esc_url($this->setting_item('view_all_url') ?? route('tenant.shop')),
            'padding_top'    => SanitizeInput::esc_html($this->setting_item('padding_top') ?? '80'),
            'padding_bottom' => SanitizeInput::esc_html($this->setting_item('padding_bottom') ?? '80'),
        ];

        return self::renderView('tenant.chefhome.product.products-by-category', $data);
    }

    public function addon_title(): string
    {
        return __('ChefHome: Products by Category');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
