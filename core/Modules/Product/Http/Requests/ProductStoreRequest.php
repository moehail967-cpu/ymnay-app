<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function prepareForValidation()
    {
        $description = $this->description;
        // Strip tags and trim to check if description is effectively empty
        $cleanDescription = trim(strip_tags($description));
        $this->merge([
            'brand' => $this->brand === '' ? null : $this->brand, // From previous fix
            'description' => $cleanDescription === '' ? null : $description, // Treat empty HTML as null
            "is_inventory_warn_able" => $this->is_inventory_warning,
        ]);
    }
    public function rules() : array
    {
        return [
            "name" => "required",
            "slug" => "required|unique:products,id," . $this->id ?? 0,
            "summery" => "nullable|string|max:500",
            "description" => "required|string|min:10",
            "brand" => "nullable",
            "cost" => "nullable|numeric",
            "price" => "nullable|numeric",
            "sale_price" => "required|numeric",
            "sku" => ["required", ($this->id ?? null) ? Rule::unique("product_inventories")->ignore($this->id,"product_id") :  Rule::unique("product_inventories")],
            "quantity" => "nullable",
            "unit_id" => "required",
            "uom" => "required",
            "image_id" => "required",
            "product_gallery" => "nullable",
            "tags" => "nullable",
            "badge_id" => "nullable",
            "item_size" => "nullable",
            "item_color" => "nullable",
            "item_image" => "nullable",
            "item_additional_price" => "nullable",
            "item_extra_price" => "nullable",
            "item_stock_count" => "nullable",
            "item_extra_cost" => "nullable",
            "item_attribute_id" => "nullable",
            "item_attribute_name" => "nullable",
            "item_attribute_value" => "nullable",
            "category_id" => "required",
            "sub_category" => "nullable",
            "child_category" => "nullable",
            "delivery_option" => "nullable",
            "general_title" => "nullable",
            "general_description" => "nullable",
            "general_image" => "nullable",
            "facebook_title" => "nullable",
            "facebook_description" => "nullable",
            "facebook_image" => "nullable",
            "twitter_title" => "nullable",
            "twitter_description" => "nullable",
            "twitter_image" => "nullable",
            "min_purchase" => "nullable",
            "max_purchase" => "nullable",
            "is_refundable" => "nullable",
            "is_taxable" => "nullable",
            "tax_class" => "required_if:is_taxable,==,yes",
            "is_inventory_warn_able" => "nullable",
            "policy_description" => "nullable",
            "custom_spec_title" => "nullable|array",
            "custom_spec_title.*" => "nullable|string|max:255",
            "custom_spec_value" => "nullable|array",
            "custom_spec_value.*" => "nullable|string|max:255",
            "custom_spec_title.*" => "required_with:custom_spec_value.*",
            "custom_spec_value.*" => "required_with:custom_spec_title.*",
            // Variant fields
            "handle_variants"  => "nullable",
            "v_color"          => "nullable|array",
            "v_color.*"        => "nullable",
            "v_size"           => "nullable|array",
            "v_size.*"         => "nullable",
            "v_price"          => "nullable|array",
            "v_price.*"        => "nullable|numeric",
            "v_cost"           => "nullable|array",
            "v_cost.*"         => "nullable|numeric",
            "v_stock"          => "nullable|array",
            "v_stock.*"        => "nullable|integer",
            "v_sku"            => "nullable|array",
            "v_sku.*"          => "nullable|string|max:255",
            "v_image"          => "nullable|array",
            "v_image.*"        => "nullable",
            "v_attributes"     => "nullable|array",
            "v_attributes.*"   => "nullable|string",
         ];
    }

    public function messages(): array
    {
        return [
            "name.required" => __('Product name field is required'),
            "sale_price.required" => __("Sale price is required."),
            "sku.required" => __("SKU Stock Kipping Unit is required"),
            "uni.required" => __("Please Select a unit type"),
            "uom.required" => __("UOM Unit of measurement field is required."),
            "unit_id.required" => __("Unit of product is required."),
            "tax_class.required_if" => __("Tax class is required.")
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}
