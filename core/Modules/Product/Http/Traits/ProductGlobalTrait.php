<?php

namespace Modules\Product\Http\Traits;

use App\Enums\ProductTypeEnum;
use App\Helpers\SanitizeInput;
use App\Http\Services\CustomPaginationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductCategory;
use Modules\Product\Entities\ProductChildCategory;
use Modules\Product\Entities\ProductCreatedBy;
use Modules\Product\Entities\ProductCustomSpecification;
use Modules\Product\Entities\ProductDeliveryOption;
use Modules\Product\Entities\ProductGallery;
use Modules\Product\Entities\ProductInventory;
use Modules\Product\Entities\ProductInventoryDetail;
use Modules\Product\Entities\ProductShippingReturnPolicy;
use Modules\Product\Entities\ProductSubCategory;
use Modules\Product\Entities\ProductTag;
use Modules\Product\Entities\ProductUom;

trait ProductGlobalTrait {
    private function search($request, $route = 'tenant.admin', $queryType = "admin"): array
    {
        $type = $request->type ?? 'default';
        $multiple_date = $this->is_date_range_multiple();

        // create product model instance
        // $all_products = Product::query()->with("brand", "category", "childCategory", "subCategory", "inventory");
        // first I need to check who is currently want to take data
        // run a condition that will check if vendor is currently login then only vendor product will return

        // create product model instance
        if($queryType == 'admin'){
            $all_products = Product::query()->with("brand", "category", "childCategory", "subCategory", "inventory");
        }else if ($queryType == 'frontend'){
            $all_products = Product::query()->with('campaign_sold_product', 'subCategory','campaign_product', 'inventory','badge','uom')
                ->withAvg("reviews", "rating")
                ->withCount("reviews");
        }else if ($queryType == 'api'){
            $all_products = Product::query()->with('campaign_sold_product','category','subCategory','childCategory','campaign_product', 'inventory','badge','uom')
                ->withAvg("reviews", "rating")
                ->withCount("reviews")
                ->withSum('taxOptions','rate')
                ->where('status_id', 1);
        }

        // search product name
        $all_products->when(!empty($request->name) && $request->has("name"), function ($query) use ($request) {
            $query->where("name", "LIKE", "%" . $request->name . "%");
        })->when(!empty($request->tag) && $request->has("tag"), function ($query) use ($request) {// search by using tag
            $query->whereHas("tag", function ($i_query) use ($request) {
                $i_query->where("tag_name", "like", "%" . $request->tag . "%");
            });
        })->when(!empty($request->category) && $request->has("category"), function ($query) use ($request) { // category
            $query->whereHas("category", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->category . "%");
            });
        })->when(!empty($request->brand) && $request->has("brand"), function ($query) use ($request) { // Brand
            $query->whereHas("brand", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->brand . "%");
            });
        })->when(!empty($request->sub_category) && $request->has("sub_category"), function ($query) use ($request) { // sub category
            $query->whereHas("subCategory", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->sub_category . "%");
            });
        })->when(!empty($request->child_category) && $request->has("child_category"), function ($query) use ($request) { // child category
            $query->whereHas("childCategory", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->child_category . "%");
            });
        })->when(!empty($request->category_id) && $request->has("category_id"), function ($query) use ($request) { // category
            $query->whereHas("category", function ($i_query) use ($request) {
                $i_query->where("categories.id",trim(strip_tags($request->category_id)));
            });
        })->when(!empty($request->sub_category_id) && $request->has("sub_category_id"), function ($query) use ($request) { // sub category
            $query->whereHas("subCategory", function ($i_query) use ($request) {
                $i_query->where("sub_categories.id",trim(strip_tags($request->sub_category_id)));
            });
        })->when(!empty($request->child_category_id) && $request->has("child_category_id"), function ($query) use ($request) { // child category
            $query->whereHas("childCategory", function ($i_query) use ($request) {
                $i_query->where("child_categories.id",trim(strip_tags($request->child_category_id)));
            });
        })->when(!empty($request->color) && $request->has("color"), function ($query) use ($request) { // color
            $query->whereHas("color", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->color . "%");
            });
        })->when(!empty($request->size) && $request->has("size"), function ($query) use ($request) { // size
            $query->whereHas("size", function ($i_query) use ($request) {
                $i_query->where("name", "like", "%" . $request->size . "%");
            });
        })->when(!empty($request->sku) && $request->has("sku"), function ($query) use ($request) { // sku
            $query->whereHas("inventory", function ($i_query) use ($request) {
                $i_query->where("sku", "like", "%" . $request->sku . "%");
            });
        })->when(!empty($request->delivery_option) && $request->has("delivery_option"), function ($query) use ($request) { // delivery option
            $query->whereHas("productDeliveryOption", function ($i_query) use ($request) {
                $i_query->where("title", "like", "%" . $request->delivery_option . "%");
            });
        })->when(!empty($request->refundable) && $request->has("refundable"), function ($query) use ($request) { // refundable
            $query->where("is_refundable", 1);
        })->when(!empty($request->inventory_warning) && $request->has("inventory_warning"), function ($query) use ($request) { // inventory warning
            $query->where("is_inventory_warn_able", 1);
        })->when(!empty($request->from_price) && $request->has("from_price") && !empty($request->to_price) && $request->has("to_price"), function ($query) use ($request) { // price
            $query->whereBetween("sale_price", [$request->from_price, $request->to_price]);
        })->when($multiple_date[0] && $request->has("date_range"), function ($query) use ($request, $multiple_date) { // Order By
            // make separate to date in a array
            $arr = $multiple_date[1];
            $query->whereBetween("created_at", $arr);
        })->when(!empty($multiple_date[0]) && $request->has("date_range"), function ($query) use ($request, $multiple_date) { // Order By
            // make separate to date in a array
            $date = $multiple_date[1];
            $query->whereDate("created_at", $date);
        })->when(!empty($request->order_by) && $request->has("order_by"), function ($query) use ($request) { // Order By
            $query->orderBy("id", $request->order_by);
        })->when(!$request->has('order'), function ($query) {
            $query->orderBy("id", "DESC");
        });

        $display_item_count = request()->count ?? 10;

        $current_query = request()->all();
        $create_query = http_build_query($current_query);

        return CustomPaginationService::pagination_type($all_products, $display_item_count, "custom", route($route . ".product.search") . '?' . $create_query);
    }

    private function is_date_range_multiple(): array
    {
        $date = explode(" to ", request()->date_range);

        if(count($date) > 1 && !empty(request()->date_range)){
            return [true , $date];
        }

        return [false, request()->date_range];
    }

    private function product_type(): int
    {
        return ProductTypeEnum::PHYSICAL;
    }

    public function ProductData($data): array
    {
//        dd($data);
        return [
            "name"  => strip_tags($data["name"]),
            "slug" => Str::slug($data["slug"] ?? $data["name"], '-', null),
            "summary" => strip_tags($data["summery"]),
            "description" => purify_html($data['description']), // Use purify_html instead
            "image_id" => $data["image_id"],
            "price" => $data["price"],
            "sale_price" => $data["sale_price"],
            "cost" => $data["cost"],
            "is_taxable" => $data["is_taxable"] == 'yes', // yes = 1, no = 0
            "tax_class_id" => $data["tax_class"],
            "badge_id" => $data["badge_id"],
            "brand_id" => $data["brand"],
            "status_id" => 1,
            "product_type" => $this->product_type() ?? 2,
            "min_purchase" => $data["min_purchase"],
            "max_purchase" => $data["max_purchase"],
            "is_inventory_warn_able" => $data["is_inventory_warn_able"] ? 1 : 2,
            "is_refundable" => !empty($data["is_refundable"]),
            "quantity" => $data["quantity"],
            "handle_variants" => !empty($data["handle_variants"]) && $data["handle_variants"] == '1' ? 1 : 0,
        ];
    }

    public function CloneData($data): array
    {
        return [
            "name"  => $data->name,
            "slug" => create_slug(Str::slug($data->slug ?? $data->name, '-', null), 'Slug'),
            "summary" => $data->summary,
            "description" => $data->description,
            "image_id" => $data->image_id,
            "price" => $data->price,
            "sale_price" => $data->sale_price,
            "cost" => $data->cost,
            "badge_id" => $data->badge_id,
            "brand_id" => $data->brand_id,
            "status_id" => 2,
            "product_type" => $this->product_type() ?? 2,
            "min_purchase" => $data->min_purchase,
            "max_purchase" => $data->max_purchase,
            "is_inventory_warn_able" => $data->is_inventory_warn_able ? 1 : 2,
            "is_refundable" => !empty($data->is_refundable)
        ];
    }
    /**
     * Updated method to only insert valid inventory details
     */
    public function prepareProductInventoryDetailsAndInsert($data, $product_id, $inventory_id, $type = "create"): bool
    {
        $handle_variants = !empty($data['handle_variants']) && $data['handle_variants'] == '1';

        if (!$handle_variants) {
            // No variants — remove any leftover rows from a previous variant setup
            if ($type === 'update') {
                ProductInventoryDetail::where([
                    'product_id'           => $product_id,
                    'product_inventory_id' => $inventory_id,
                ])->delete();
            }
            return true;
        }

        // Build new variant rows from v_* fields submitted by the combination grid
        $rows = $this->buildVariantRowsFromData($data, $product_id, $inventory_id);

        if (empty($rows)) {
            return true;
        }

        if ($type === 'update') {
            // Upsert by hash: keep existing rows that match, delete removed, insert new
            $existingByHash = ProductInventoryDetail::where([
                'product_id'           => $product_id,
                'product_inventory_id' => $inventory_id,
            ])->get()->keyBy('hash');

            $submittedHashes = array_column($rows, 'hash');

            // Delete rows whose hash is no longer in the submission,
            // AND delete legacy rows with null hash (created before the hash column
            // was added — they are fully replaced by the new submission).
            foreach ($existingByHash as $hash => $existing) {
                if (!$hash || !in_array($hash, $submittedHashes, true)) {
                    $existing->delete();
                }
            }

            foreach ($rows as $row) {
                if ($row['hash'] && isset($existingByHash[$row['hash']])) {
                    // Update — preserve sold_count
                    $existingByHash[$row['hash']]->update($row);
                } else {
                    ProductInventoryDetail::create($row);
                }
            }
        } else {
            foreach ($rows as $row) {
                ProductInventoryDetail::create($row);
            }
        }

        return true;
    }

    private function buildVariantRowsFromData(array $data, int $product_id, int $inventory_id): array
    {
        $stocks = $data['v_stock'] ?? [];
        $len    = count($stocks);

        if ($len === 0) {
            return [];
        }

        $rows = [];
        for ($i = 0; $i < $len; $i++) {
            $color      = $data['v_color'][$i] ?? null;
            $size       = $data['v_size'][$i] ?? null;
            $sku        = $data['v_sku'][$i] ?? null;
            $price      = (float) ($data['v_price'][$i] ?? 0);
            $cost       = (float) ($data['v_cost'][$i] ?? 0);
            $stock      = (int)   ($data['v_stock'][$i] ?? 0);
            $image      = $data['v_image'][$i] ?? null;
            $customJson = $data['v_attributes'][$i] ?? null;

            $customAttrs = null;
            if ($customJson) {
                $decoded = json_decode($customJson, true);
                $customAttrs = is_array($decoded) ? $decoded : null;
            }

            // Stable hash for upsert matching
            $hashInput = implode('|', [
                (string) ($color ?? ''),
                (string) ($size ?? ''),
                $customJson ?? '',
            ]);
            $hash = md5($hashInput);

            $rows[] = [
                'product_id'           => $product_id,
                'product_inventory_id' => $inventory_id,
                'sku'                  => $sku ? SanitizeInput::esc_html($sku) : null,
                'color'                => $color ?: null,
                'size'                 => $size ?: null,
                'hash'                 => $hash,
                'attributes'           => $customAttrs,
                'additional_price'     => $price,
                'add_cost'             => $cost,
                'image'                => $image ?: null,
                'stock_count'          => $stock,
                'sold_count'           => 0,
            ];
        }

        return $rows;
    }
    private function productCategoryData($data,$id,$arrKey = "category_id",$column = "category_id"): array
    {
        return [
            $arrKey => $data[$column],
            "product_id" => $id
        ];
    }

    private function childCategoryData($data, $id): array
    {
        $cl_category = [];
        foreach ($data["child_category"] ?? [] as $item) {
            $cl_category[] = ["child_category_id" => $item, "product_id" => $id];
        }

        return $cl_category;
    }

    private function prepareDeliveryOptionData($data, $id): array
    {
        // explode string to array
        $arr = [];
        $exp_delivery_option = $this->separateStringToArray($data["delivery_option"], " , ") ?? [];

        foreach($exp_delivery_option as $option){
            $arr[] = [
                "product_id" => $id,
                "delivery_option_id" => $option
            ];
        }

        return $arr;
    }

    private function prepareProductGalleryData($data, $id): array
    {
        // explode string to array
        $arr = [];
        $galleries = $this->separateStringToArray($data["product_gallery"], "|");

        foreach($galleries as $image){
            $arr[] = [
                "product_id" => $id,
                "image_id" => $image
            ];
        }

        return $arr;
    }

    private function prepareProductTagData($data, $id): array
    {
        // explode string to array
        $arr = [];
        $galleries = $this->separateStringToArray($data["tags"], ",");

        foreach($galleries as $tag){
            $arr[] = [
                "product_id" => $id,
                "tag_name" => SanitizeInput::esc_html($tag)
            ];
        }

        return $arr;
    }
    private function prepareInventoryData($data, $id = null): array
    {
        $handle_variants = !empty($data['handle_variants']) && $data['handle_variants'] == '1';

        if ($handle_variants && !empty($data['v_stock']) && is_array($data['v_stock'])) {
            $stock_count = array_sum(array_map('intval', $data['v_stock']));
        } else {
            $stock_count = (int) ($data['quantity'] ?? 0);
        }

        $arr = [
            "sku"         => SanitizeInput::esc_html($data["sku"] ?? ''),
            "stock_count" => $stock_count,
        ];

        return $id ? $arr + ["product_id" => $id] : $arr;
    }
    private function separateStringToArray(string | null $string,string $separator = " , "): array|bool
    {
        if(empty($string)) return [];
        return explode($separator, $string);
    }

    public function prepareMetaData($data): array
    {
        return [
            'title' => SanitizeInput::esc_html($data["general_title"]) ?? '',
            'description' => SanitizeInput::esc_html($data["general_description"]) ?? '',
            'fb_title' => SanitizeInput::esc_html($data["facebook_title"]) ?? '',
            'fb_description' => SanitizeInput::esc_html($data["facebook_description"]) ?? '',
            'fb_image' => $data["facebook_image"] ?? '',
            'tw_title' => SanitizeInput::esc_html($data["twitter_title"]) ?? '',
            'tw_description' => SanitizeInput::esc_html($data["twitter_description"]) ?? '',
            'tw_image' => $data["twitter_image"] ?? ''
        ];
    }

    private function userId(){
        return \Auth::guard("admin")->check() ? \Auth::guard("admin")->user()->id : '';
    }

    private function getGuardName(): string
    {
        return \Auth::guard("admin")->check() ? "admin" : "vendor";
    }


    private function createArrayCreatedBy($product_id, $type){
        $arr = [];

        if($type == 'create'){
            $arr = [
                "product_id" => $product_id,
                "created_by_id" => $this->userId(),
                "guard_name" => $this->getGuardName(),
            ];
        }elseif($type == 'update'){
            $arr = [
                "product_id" => $product_id,
                "updated_by" => $this->userId(),
                "updated_by_guard" => $this->getGuardName(),
            ];
        }elseif($type == 'delete'){
            $arr = [
                "product_id" => $product_id,
                "deleted_by" => $this->userId(),
                "deleted_by_guard" => $this->getGuardName(),
            ];
        }

        return $arr;
    }

    public function createdByUpdatedBy($product_id, $type = "create"): ProductCreatedBy
    {
        if (!ProductCreatedBy::where('product_id' ,$product_id)->exists())
        {
            ProductCreatedBy::create([
                "product_id" => $product_id,
                "created_by_id" => $this->userId(),
                "guard_name" => $this->getGuardName(),
            ]);
        }

        return ProductCreatedBy::updateOrCreate(
            [
                "product_id" => $product_id
            ],
            $this->createArrayCreatedBy($product_id, $type)
        );
    }

    private function prepareUomData($data, $product_id): array
    {
        return [
            "product_id" => $product_id,
            "unit_id" => $data["unit_id"],
            "quantity" => $data["uom"]
        ];
    }

    public function updateStatus($productId, $statusId): JsonResponse
    {
        $product = Product::find($productId)->update(["status_id" => $statusId]);
        $this->createdByUpdatedBy($productId, "update");

        $response_status = $product ? ["success" => true, "msg" => __("Successfully updated status")] : ["success" => false,"msg" => __("Failed to update status")];
        return response()->json($response_status)->setStatusCode(200);
    }

    /**
     * @param array $data
     * @param $id
     * @param $product
     * @return bool
     */
    public function insert_product_data(array $data, $id, $product): bool
    {
        $inventory = ProductInventory::create($this->prepareInventoryData($data, $id));
        // Always attempt inventory detail save — the method handles handle_variants check internally
        $this->prepareProductInventoryDetailsAndInsert($data, $id, $inventory->id);

        $category = ProductCategory::create($this->productCategoryData($product, $id));
        $subcategory = ProductSubCategory::create($this->productCategoryData($product, $id, "sub_category_id", "sub_category"));
        $childCategory = ProductChildCategory::insert($this->childCategoryData($product, $id));
        $deliveryOptions = ProductDeliveryOption::insert($this->prepareDeliveryOptionData($product, $id));
        $productGallery = ProductGallery::insert($this->prepareProductGalleryData($product, $id));

        ProductTag::insert($this->prepareProductTagData($product, $id));

        $productUom = ProductUom::create($this->prepareUomData($data, $id));

        $productPolicy = ProductShippingReturnPolicy::create([
            'product_id' => $id,
            'shipping_return_description' => purify_html(str_replace(["<script>","</script>", 'script'],"",$data['policy_description']))
        ]);

        return true;
    }

    protected function productInstance($type): Builder
    {
        $product = Product::query();
        if($type == "edit"){
            $product->with(["product_category","tag","uom","product_sub_category","product_child_category","inventory","delivery_option"]);
        }elseif ($type == "single"){
            $product->with(["category","gallery_images","tag","uom","subCategory","childCategory","image","inventory","delivery_option"]);
        }elseif ($type == "list"){
            $product->with(["category","uom" , "subCategory", "childCategory", "brand", "badge" , "image" , "inventory"]);
        }elseif ($type == "search"){
            $product->with(["category","uom" , "subCategory", "childCategory", "brand", "badge" , "image" , "inventory"]);
        }else{
            $product = Product::query()->with(["category" , "subCategory", "childCategory", "brand", "badge" , "image" , "inventory"]);
        }

        return $product;
    }

    private function get_product($type = "single", $id): Model|Builder|null
    {
        // get product instance
        $product = $this->productInstance($type);
        return $product->with("brand")->where("id",$id)->first();
    }

    public function productStore($data){

        $product_data = self::ProductData($data);
//        dd($product_data);
        $slug = $data['slug'] ?? $data['name'];
        $product_data['slug'] = create_slug(Str::slug($slug, '-', null), 'Slug');
        $product = Product::create($product_data);
//        dd($product);

        $product->slug()->create(['slug' => $product->slug]);

        $id = $product->id;
        $product->metaData()->updateOrCreate(["metainfoable_id" => $id],$this->prepareMetaData($data));

        // Store custom specifications
        if (!empty($data['custom_spec_title']) && !empty($data['custom_spec_value'])) {
            $titles = $data['custom_spec_title'];
            $values = $data['custom_spec_value'];

            // Ensure arrays have the same length
            $count = min(count($titles), count($values));
            for ($i = 0; $i < $count; $i++) {
                if (!empty($titles[$i]) && !empty($values[$i])) {
                    ProductCustomSpecification::create([
                        'product_id' => $id,
                        'title' => $titles[$i],
                        'value' => $values[$i],
                        'sort_order' => $i + 1, // Optional: assign sort order based on array index
                    ]);
                }
            }
        }
        // store created by info in product created by table
        $this->createdByUpdatedBy($id);
        $result = $this->insert_product_data($data, $id, $data);
        do_action('nazmart:after_product_save', $product, 'create');
        return $result;
    }

    public function productUpdate($data, $id){
        $product_data = self::ProductData($data);
        $product = Product::find($id);

        if ($product->slug != $data['slug'])
        {
            $slug = $data['slug'] ?? $data['name'];
            $product_data['slug'] = create_slug(Str::slug($slug, '-', null), 'Slug');
        }

        $product->update($product_data);
        $product->slug()->update(['slug' => $product->slug]);

        $product->metaData()->updateOrCreate(["metainfoable_id" => $id],$this->prepareMetaData($data));

        // Ensure inventory always exists and capture its ID reliably
        $inventory = ProductInventory::updateOrCreate(
            ['product_id' => $id],
            $this->prepareInventoryData($data, $id)
        );

        $this->createdByUpdatedBy($id, "update");

        $this->prepareProductInventoryDetailsAndInsert($data, $id, $inventory->id, "update");
        $category = $product?->product_category?->updateOrCreate(["product_id" => $id],$this->productCategoryData($data,$id));
        $subcategory = $product?->product_sub_category?->updateOrCreate(["product_id" => $id],$this->productCategoryData($data,$id,"sub_category_id","sub_category"));

        // delete product child category
        ProductChildCategory::where("product_id", $id)->delete();
        ProductDeliveryOption::where("product_id", $id)->delete();
        ProductGallery::where("product_id", $id)->delete();
        ProductTag::where("product_id", $id)->delete();

        $product?->uom?->update($this->prepareUomData($data,$id));
        $childCategory = ProductChildCategory::insert($this->childCategoryData($data, $id));
        $deliveryOptions = ProductDeliveryOption::insert($this->prepareDeliveryOptionData($data,$id));
        $productGallery = ProductGallery::insert($this->prepareProductGalleryData($data,$id));
        $productTag = ProductTag::insert($this->prepareProductTagData($data, $id));

        $productPolicy = ProductShippingReturnPolicy::updateOrCreate(
            ['product_id' => $id],
            [
                'product_id' => $id,
                'shipping_return_description' => str_replace('script','', $data['policy_description'])
            ]);

        /**
         * 🔹 Update Product Custom Specifications
         */
        if (Schema::hasTable('product_custom_specifications')) {
            if (!empty($data['custom_spec_title']) && !empty($data['custom_spec_value'])) {
                // Delete old specifications
                ProductCustomSpecification::where('product_id', $id)->delete();

                $titles = $data['custom_spec_title'];
                $values = $data['custom_spec_value'];

                $count = min(count($titles), count($values));
                for ($i = 0; $i < $count; $i++) {
                    if (!empty($titles[$i]) && !empty($values[$i])) {
                        ProductCustomSpecification::create([
                            'product_id' => $id,
                            'title' => $titles[$i],
                            'value' => $values[$i],
                            'sort_order' => $i + 1,
                        ]);
                    }
                }
            } else {
                // If no spec provided, remove all
                ProductCustomSpecification::where('product_id', $id)->delete();
            }
        } else {
            Log::warning('Skipped ProductCustomSpecification operation: table does not exist.');
            return back()->with('error', 'Failed to save custom specifications.');
        }

        do_action('nazmart:after_product_save', $product, 'update');
        return true;
    }

    public function productClone($id): bool
    {
        $data = array();
        $product = Product::findOrFail($id);
        $product_data = self::CloneData($product);
        $newProduct = $product->create($product_data);
        $id = $newProduct->id;

        // now store slug information into slugs table
        $newProduct->slug()->create(['slug' => $product_data['slug']]);

        $metaData = [];
        if ($product?->metaData != null)
        {
            $metaData = [
                'general_title' => $product?->metaData?->title,
                'general_description' => $product?->metaData?->description,
                'facebook_title' => $product?->metaData?->fb_title,
                'facebook_description' => $product?->metaData?->fb_description,
                'facebook_image' => $product?->metaData?->fb_image,
                'twitter_title' => $product?->metaData?->tw_title,
                'twitter_description' => $product?->metaData?->tw_description,
                'twitter_image' => $product?->metaData?->tw_image,
            ];

            $newProduct->metaData()->create(["metainfoable_id" => $id],$this->prepareMetaData($metaData));
        }

        $this->createdByUpdatedBy($id);

        $data["sku"] = create_slug(optional($product->inventory)->sku, 'ProductInventory', true, 'Product', 'sku');
        $inventoryQuantity = $product?->inventory?->stock_count;

        $product->category_id = optional($product->category)->id;
        $product->sub_category = optional($product->subCategory)->id;
        $product->child_category = current(optional($product->childCategory)->pluck('id'));

        $delivery_option = current(optional($product->delivery_option)->pluck('delivery_option_id'));
        $product->delivery_option = implode(' , ', $delivery_option);

        $product_gallery = current(optional($product->product_gallery)->pluck('image_id'));
        $product->product_gallery = implode('|', $product_gallery);

        $product_tags = current(optional($product->tag)->pluck('tag_name'));
        $product->tags = implode(',', $product_tags);

        $data["unit_id"] = optional($product->uom)->unit_id;
        $data["uom"] = optional($product->uom)->quantity;

        // product attributes
        $data['item_stock_count'] = count(optional($product->inventory)->inventoryDetails);
        $data['item_stock_count']= \Arr::wrap($data['item_stock_count']);
        $quantity = null;
        if ($data['item_stock_count'] > 1)
        {
            $data['item_stock_count'] = array();
            foreach (optional($product->inventory)->inventoryDetails ?? [] as $i => $details)
            {
                $data['item_color'][$i] = $details->color;
                $data['item_size'][$i] = $details->size;
                $data['item_additional_price'][$i] = $details->additional_price;
                $data['item_extra_cost'][$i] = $details->add_cost;
                $data['item_image'][$i] = $details->image;
                $data['item_stock_count'][$i] = $details->stock_count;
                $quantity += $details->stock_count;

                foreach ($details->attribute ?? [] as $j => $attribute)
                {
                    $data['item_attribute_name'][$i][$j] = $attribute->attribute_name;
                    $data['item_attribute_value'][$i][$j] = $attribute->attribute_value;
                }
            }
        }
        $data["quantity"] = !empty($quantity) ? $quantity : $inventoryQuantity;
        $data['policy_description'] = $product?->return_policy?->shipping_return_description;
        return $this->insert_product_data($data, $id, $product);
    }

    protected function destroy($id){
        return Product::find($id)->delete();
    }

    protected function trash_destroy($id){
        $product = Product::onlyTrashed()->findOrFail($id);
        ProductUom::where('product_id', $product->id)->delete();
        ProductTag::where('product_id', $product->id)->delete();
        ProductGallery::where('product_id', $product->id)->delete();
        ProductDeliveryOption::where('product_id', $product->id)->delete();
        ProductChildCategory::where('product_id', $product->id)->delete();
        ProductSubCategory::where('product_id', $product->id)->delete();
        ProductCategory::where('product_id', $product->id)->delete();
        ProductInventoryDetailAttribute::where('product_id', $product->id)->delete();
        ProductInventoryDetail::where('product_id', $product->id)->delete();
        ProductInventory::where('product_id', $product->id)->delete();
        $product->forceDelete();

        return (bool)$product;
    }

    protected function bulk_delete($ids)
    {
        $product = Product::whereIn('id' ,$ids)->delete();
        return (bool)$product;
    }

    protected function trash_bulk_delete($ids)
    {
        try {
            ProductUom::whereIn('product_id', $ids)->delete();
            ProductTag::whereIn('product_id', $ids)->delete();
            ProductGallery::whereIn('product_id', $ids)->delete();
            ProductDeliveryOption::whereIn('product_id', $ids)->delete();
            ProductChildCategory::whereIn('product_id', $ids)->delete();
            ProductSubCategory::whereIn('product_id', $ids)->delete();
            ProductCategory::whereIn('product_id', $ids)->delete();
            ProductInventoryDetailAttribute::whereIn('product_id', $ids)->delete();
            ProductInventoryDetail::whereIn('product_id', $ids)->delete();
            ProductInventory::whereIn('product_id', $ids)->delete();
            $products = Product::onlyTrashed()->whereIn('id',$ids)->forceDelete();
        } catch (\Exception $exception) { return false; }

        return (bool)$products;
    }

    public function updateInventory($data, $id){
        $product = Product::find($id);

        $product?->inventory?->updateOrCreate(["product_id" => $id],$this->prepareInventoryData($data));
        // updated by info in product created by table
        $this->createdByUpdatedBy($id, "update");
        $this->prepareProductInventoryDetailsAndInsert($data, $id, $product?->inventory?->id, "update");

        return true;
    }
}
