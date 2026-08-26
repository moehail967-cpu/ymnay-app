<?php

namespace Plugins\PageBuilder\Addons\Tenants\Pharmacy\Product;

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
        return 'Tenant/themes/universal/header/hero.jpg';
    }

    public function admin_render(): string
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $v = $this->get_settings();

        $output .= Text::get(['name' => 'title',      'label' => __('Section Title'),      'value' => $v['title'] ?? null]);
        $output .= Number::get(['name' => 'item_count', 'label' => __('Products to Show'), 'value' => $v['item_count'] ?? 8]);
        $output .= Select::get([
            'name'    => 'sort_by',
            'label'   => __('Sort By'),
            'options' => [
                'latest'     => __('Latest'),
                'price_asc'  => __('Price: Low to High'),
                'price_desc' => __('Price: High to Low'),
                'popular'    => __('Most Popular'),
            ],
            'value' => $v['sort_by'] ?? 'latest',
        ]);
        $output .= $this->padding_fields($v);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render(): string
    {
        $count  = (int) ($this->setting_item('item_count') ?? 8);
        $sortBy = $this->setting_item('sort_by') ?? 'latest';

        $query = Product::with('badge', 'campaign_product', 'inventory', 'inventoryDetail')
            ->where('status_id', 1)
            ->withSum('taxOptions', 'rate');

        match ($sortBy) {
            'price_asc'  => $query->orderBy('sale_price', 'asc'),
            'price_desc' => $query->orderBy('sale_price', 'desc'),
            'popular'    => $query->orderBy('view_count', 'desc'),
            default      => $query->latest(),
        };

        $products = $query->take($count)->get();

        $data = [
            'title'          => SanitizeInput::esc_html($this->setting_item('title') ?? __('Popular Products')),
            'products'       => $products,
            'view_all_url'   => SanitizeInput::esc_url($this->setting_item('view_all_url') ?? route('tenant.shop')),
            'padding_top'    => SanitizeInput::esc_html($this->setting_item('padding_top') ?? '80'),
            'padding_bottom' => SanitizeInput::esc_html($this->setting_item('padding_bottom') ?? '80'),
        ];

        return self::renderView('tenant.pharmacy.product.featured-products', $data);
    }

    public function addon_title(): string
    {
        return __('Pharmacy: Featured Products');
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }
}
