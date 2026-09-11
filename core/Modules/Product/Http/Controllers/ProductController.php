<?php

namespace Modules\Product\Http\Controllers;

use App\Enums\SlugMorphableTypeEnum;
use App\Helpers\FlashMsg;
use App\Http\Services\DynamicCustomSlugValidation;
use App\Mail\ProductOrderEmail;
use App\Mail\StockOutEmail;
use App\Models\ProductReviews;
use App\Models\Status;
use App\Services\AdminTheme\MetaDataHelpers;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Attributes\Entities\Brand;
use Modules\Attributes\Entities\Category;
use Modules\Attributes\Entities\ChildCategory;
use Modules\Attributes\Entities\Color;
use Modules\Attributes\Entities\DeliveryOption;
use Modules\Attributes\Entities\Size;
use Modules\Attributes\Entities\SubCategory;
use Modules\Attributes\Entities\Tag;
use Modules\Attributes\Entities\Unit;
use Modules\Badge\Entities\Badge;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttribute;
use Modules\Product\Entities\ProductCategory;
use Modules\Product\Entities\ProductChildCategory;
use Modules\Product\Entities\ProductCustomSpecification;
use Modules\Product\Entities\ProductDeliveryOption;
use Modules\Product\Entities\ProductGallery;
use Modules\Product\Entities\ProductInventory;
use Modules\Product\Entities\ProductInventoryDetail;
use Modules\Product\Entities\ProductInventoryDetailAttribute;
use Modules\Product\Entities\ProductSize;
use Modules\Product\Entities\ProductSubCategory;
use Modules\Product\Entities\ProductTag;
use Modules\Product\Entities\ProductUom;
use Modules\Product\Http\Requests\ProductStoreRequest;
use Modules\Product\Http\Services\Admin\AdminProductServices;
use Modules\TaxModule\Entities\TaxClass;
use Stripe\Service\ProductService;


class ProductController extends Controller
{
    CONST BASE_PATH = '';

    public function __construct(){
        $this->middleware("auth:admin");
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request): Renderable
    {
        $theme_meta_instance = MetaDataHelpers::Init();
        $theme_info = $theme_meta_instance->getThemesInfo();
        $theme_name = get_static_option('tenant_admin_dashboard_theme') ?? '';
        $render_view_file = $theme_meta_instance->getThemeOverrideViews($theme_name,'all_products','product::index');

        $products = AdminProductServices::productSearch($request);
        $trash = Product::onlyTrashed()->count();
        $statuses = Status::all();
        return view($render_view_file, compact("products","statuses", "trash"));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $data = [
            "brands" => Brand::select("id", "name")->get(),
            "badges" => Badge::where("status","active")->get(),
            "units" => Unit::select("id", "name")->get(),
            "tags" => Tag::select("id", "tag_text as name")->get(),
            "categories" => Category::select("id", "name")->get(),
            "deliveryOptions" => DeliveryOption::select("id", "title", "sub_title", "icon")->get(),
            "all_attribute" => ProductAttribute::all()->groupBy('title')->map(fn($query) => $query[0]),
            "product_colors" => Color::all(),
            "product_sizes" => Size::all(),
            "tax_classes" => TaxClass::all()
        ];

        $theme_meta_instance = MetaDataHelpers::Init();
        $theme_info = $theme_meta_instance->getThemesInfo();
        $theme_name = get_static_option('tenant_admin_dashboard_theme') ?? '';
        $render_view_file = $theme_meta_instance->getThemeOverrideViews($theme_name,'create_product','product::create');

        return view($render_view_file, compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     * @param ProductStoreRequest $request
     * @return JsonResponse
     */

    public function store(ProductStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        DynamicCustomSlugValidation::validate(
            slug: $data['slug'] ?? Str::slug($data['name'], '-', null)
        );
            $product = (new AdminProductServices)->store($data);
//            dd($data); //get quantity
            return response()->json($product ? ["success" => true] : ["success" => false]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('product::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id, $aria_name = null)
    {
        $data = [
            "brands" => Brand::select("id", "name")->get(),
            "badges" => Badge::where("status","active")->get(),
            "units" => Unit::select("id", "name")->get(),
            "tags" => Tag::select("id", "tag_text as name")->get(),
            "categories" => Category::select("id", "name")->get(),
            "deliveryOptions" => DeliveryOption::select("id", "title", "sub_title", "icon")->get(),
            "all_attribute" => ProductAttribute::all()->groupBy('title')->map(fn($query) => $query[0]),
            "product_colors" => Color::all(),
            "product_sizes" => Size::all(),
            "tax_classes" => TaxClass::all(),
            'aria_name' => $aria_name
        ];

        $product = (new AdminProductServices)->get_edit_product($id);
        $subCat = $product?->subCategory?->id ?? null;
        $cat = $product?->category?->id ?? null;

        $sub_categories = SubCategory::select("id", "name")->where("category_id", $cat)->where("status_id", 1)->get();
        $child_categories = ChildCategory::select("id", "name")->where("sub_category_id", $subCat)->where("status_id", 1)->get();

        $theme_meta_instance = MetaDataHelpers::Init();
        $theme_info = $theme_meta_instance->getThemesInfo();
        $theme_name = get_static_option('tenant_admin_dashboard_theme') ?? '';
        $render_view_file = $theme_meta_instance->getThemeOverrideViews($theme_name,'edit_product','product::edit');
//dd($data,$product);
        return view($render_view_file, compact("data", "product", "sub_categories", "child_categories"));
    }

    /**
     * Update the specified resource in storage.
     * @param ProductStoreRequest $request
     * @param int $id
     * @return JsonResponse
     */
//    public function update(ProductStoreRequest $request, int $id)
//    {
//
//        $data = $request->validated();
//        DynamicCustomSlugValidation::validate(
//            slug: $data['slug'] ?? Str::slug($data['name'], '-', null),
//            id: $id,
//            type: SlugMorphableTypeEnum::PRODUCT
//        );
//
//        return response()->json((new AdminProductServices)->update($data, $id) ? ["success" => true] : ["success" => false]);
//    }

    public function update(ProductStoreRequest $request, int $id)
    {

        try {
            $data = $request->validated();

            DynamicCustomSlugValidation::validate(
                slug: $data['slug'] ?? Str::slug($data['name'], '-', null),
                id: $id,
                type: SlugMorphableTypeEnum::PRODUCT
            );
            $updated = (new AdminProductServices)->update($data, $id);

            return response()->json([
                'success' => $updated,
                'message' => $updated ? 'Product updated successfully.' : 'Product update failed.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        } catch (\Throwable $e) {
            \Log::error('[product-update] '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    // for ajax handling previous version:
//    public function update(ProductStoreRequest $request, int $id)
//    {
//        $data = $request->validated();
//
//        DynamicCustomSlugValidation::validate(
//            slug: $data['slug'] ?? Str::slug($data['name'], '-', null),
//            id: $id,
//            type: SlugMorphableTypeEnum::PRODUCT
//        );
//
//        try {
//            $success = (new AdminProductServices)->update($data, $id);
//
//            if ($request->ajax()) {
//                //  If request is AJAX → return JSON (your JS can handle it)
//                return response()->json(['success' => (bool) $success]);
//            }
//
//            //  Otherwise redirect back with flash message
//            if ($success) {
//                return redirect()->back()->with('success', 'Product updated successfully!');
//            } else {
//                return redirect()->back()->with('error', 'Failed to update product.');
//            }
//
//        } catch (\Exception $e) {
//            \Log::error('Product update failed: '.$e->getMessage());
//
//            if ($request->ajax()) {
//                return response()->json(['success' => false, 'message' => 'Something went wrong.']);
//            }
//
//            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
//        }
//    }


    private function validateUpdateStatus($req): array
    {
        return Validator::make($req,[
            "id" => "required",
            "status_id" => "required"
        ])->validated();
    }

    public function update_status(Request $request)
    {
        $data = $this->validateUpdateStatus($request->all());

        return (new AdminProductServices)->updateStatus($data["id"],$data["status_id"]);
    }

    public function clone($id)
    {
        return (new AdminProductServices)->clone($id) ? back()->with(FlashMsg::clone_succeed('Product')) : back()->with(FlashMsg::clone_failed('Product'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return response()->json((new AdminProductServices)->delete($id) ? ["success" => true, "msg" => "Product deleted successfully"] : ["success" => false]);
    }

    public function bulk_destroy(Request $request): JsonResponse
    {
        return response()->json((new AdminProductServices)->bulk_delete_action($request->ids) ? ["success" => true] : ["success" => false]);
    }

    public function trash(): Renderable
    {
        $products = Product::with('category','subCategory', 'childCategory')->onlyTrashed()->get();
        return view('product::trash',compact("products"));
    }

    public function restore($id)
    {
        $restore = Product::onlyTrashed()->findOrFail($id)->restore();
        return back()->with($restore ? FlashMsg::restore_succeed('Trashed Product') : FlashMsg::restore_failed('Trashed Product'));
    }

    public function trash_delete($id)
    {
        return (new AdminProductServices)->trash_delete($id) ? back()->with(FlashMsg::delete_succeed('Trashed Product')) : back()->with(FlashMsg::delete_failed('Trashed Product'));
    }

    public function trash_bulk_destroy(Request $request)
    {
        return response()->json((new AdminProductServices)->trash_bulk_delete_action($request->ids) ? ["success" => true] : ["success" => false]);
    }

    public function trash_empty(Request $request)
    {
        $ids = explode('|', $request->ids);
        return response()->json((new AdminProductServices)->trash_bulk_delete_action($ids) ? ["success" => true] : ["success" => false]);
    }

    public function productSearch(Request $request): string
    {
        $products = AdminProductServices::productSearch($request);
        $statuses = Status::all();

        return view('product::search',compact("products","statuses"))->render();
    }

    public function productReview()
    {
        $review_list = ProductReviews::paginate(10);
        return view('product::review', compact('review_list'));
    }

    public function settings()
    {
        return view('product::settings');
    }

    public function settings_update(Request $request)
    {
        $validated = $request->validate([
            'product_title_length' => 'nullable|integer',
            'product_description_length' => 'nullable|integer',
            'phone_screen_products_card' => 'nullable|integer|min:1|max:3'
        ]);

        foreach ($validated as $index => $value)
        {
            update_static_option($index, $value);
        }

        return back()->with(FlashMsg::update_succeed('Product global settings'));
    }


    public function exportProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $data = [$this->productToCSVRow($product)];
            $filename = 'product_' . Str::slug($product->name) . '_' . date('Y-m-d') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            return Response::stream(function () use ($data) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel
                fwrite($file, implode(',', array_keys($data[0])) . "\n"); // Headers
                fwrite($file, implode(',', array_map(fn($value) => str_replace(',', ' ', $value), array_values($data[0]))) . "\n"); // Data
                fclose($file);
            }, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Product Export Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export product.');
        }
    }
    private function productToCSVRow($product)
    {
        return [
            'name' => $product->name ?? '',
            'slug' => $product->slug ?? '',
            'summery' => $product->summary ?? '',
            'description' => $product->description ?? '',
            // 'brand' => SKIPPED - User selects from existing brands
            // 'category_id' => SKIPPED - User selects from existing categories
            'cost' => $product->cost ?? '',
            'price' => $product->price ?? '',
            'sale_price' => $product->sale_price ?? '',
            'sku' => $product->sku ?? '',
            'quantity' => $product->quantity ?? '',
            'unit_id' => $product->unit_id ?? '',
            'uom' => $product->uom ?? '',
            'image_id' => $product->image_id ?? '',
            'product_gallery' => is_array($product->product_gallery) ? implode('|', $product->product_gallery) : ($product->product_gallery ?? ''),
            'tags' => is_array($product->tags) ? implode(',', $product->tags) : ($product->tags ?? ''),
            'badge_id' => $product->badge_id ?? '',
            'item_size' => $product->item_size ?? '',
            'item_color' => $product->item_color ?? '',
            'item_image' => $product->item_image ?? '',
            'item_additional_price' => $product->item_additional_price ?? '',
            'item_extra_price' => $product->item_extra_price ?? '',
            'item_stock_count' => $product->item_stock_count ?? '',
            'item_extra_cost' => $product->item_extra_cost ?? '',
            'item_attribute_id' => $product->item_attribute_id ?? '',
            'item_attribute_name' => $product->item_attribute_name ?? '',
            'item_attribute_value' => $product->item_attribute_value ?? '',
            'sub_category' => $product->sub_category ?? '',
            'child_category' => $product->child_category ?? '',
            'delivery_option' => $product->delivery_option ?? '',
            'general_title' => $product->general_title ?? '',
            'general_description' => $product->general_description ?? '',
            'general_image' => $product->general_image ?? '',
            'facebook_title' => $product->facebook_title ?? '',
            'facebook_description' => $product->facebook_description ?? '',
            'facebook_image' => $product->facebook_image ?? '',
            'twitter_title' => $product->twitter_title ?? '',
            'twitter_description' => $product->twitter_description ?? '',
            'twitter_image' => $product->twitter_image ?? '',
            'min_purchase' => $product->min_purchase ?? '',
            'max_purchase' => $product->max_purchase ?? '',
            'is_refundable' => $product->is_refundable ?? '',
            'is_taxable' => $product->is_taxable ?? '',
            'tax_class' => $product->tax_class ?? '',
            'is_inventory_warn_able' => $product->is_inventory_warn_able ?? '',
            'policy_description' => $product->policy_description ?? '',
        ];
    }

}
