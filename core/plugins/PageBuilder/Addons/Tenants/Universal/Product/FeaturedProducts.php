<?php

namespace Plugins\PageBuilder\Addons\Tenants\Universal\Product;

use App\Helpers\SanitizeInput;
use Modules\Product\Entities\Product;
use Plugins\PageBuilder\Fields\Number;
use Plugins\PageBuilder\Fields\Select;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\PageBuilderBase;

class FeaturedProducts extends PageBuilderBase
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

        $output .= Text::get(['name' => 'title', 'label' => __('Section Title'), 'value' => $v['title'] ?? null]);
        $output .= Number::get(['name' => 'item_count', 'label' => __('Products to Show'), 'value' => $v['item_count'] ?? 8]);
        $output .= Select::get([
            'name'    => 'sort_by',
            'label'   => __('Sort By'),
            'options' => ['latest' => __('Latest'), 'price_asc' => __('Price: Low to High'), 'price_desc' => __('Price: High to Low')],
            'value'   => $v['sort_by'] ?? 'latest',
        ]);
        $output .= $this->padding_fields($v);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render(): string
    {
        $title     = SanitizeInput::esc_html($this->setting_item('title') ?? __('Featured Products'));
        $count     = (int) ($this->setting_item('item_count') ?? 8);
        $sortBy    = $this->setting_item('sort_by') ?? 'latest';

        $query = Product::with('badge', 'campaign_product', 'inventory', 'inventoryDetail')
            ->where('status_id', 1)
            ->withSum('taxOptions', 'rate');

        match ($sortBy) {
            'price_asc'  => $query->orderBy('sale_price', 'asc'),
            'price_desc' => $query->orderBy('sale_price', 'desc'),
            default      => $query->latest(),
        };

        $products = $query->take($count)->get();

        $data = [
            'title'          => $title,
            'products'       => $products,
            'padding_top'    => SanitizeInput::esc_html($this->setting_item('padding_top') ?? '80'),
            'padding_bottom' => SanitizeInput::esc_html($this->setting_item('padding_bottom') ?? '80'),
        ];

        return self::renderView('tenant.universal.product.featured-products', $data);
    }

    public function addon_title(): string
    {
        return __('Featured Products');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
