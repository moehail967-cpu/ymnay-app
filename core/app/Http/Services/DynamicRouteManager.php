<?php

namespace App\Http\Services;

use App\Enums\SlugMorphableTypeEnum;
use App\Models\Page;
use App\Models\PricePlan;
use App\Models\Slug;
use App\Models\StaticOption;
use App\Traits\SeoDataConfig;
use Artesaos\SEOTools\Traits\SEOTools;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Attributes\Entities\Category;
use Modules\Attributes\Entities\ChildCategory;
use Modules\Attributes\Entities\Color;
use Modules\Attributes\Entities\Size;
use Modules\Attributes\Entities\SubCategory;
use Modules\Blog\Entities\Blog;
use Modules\Blog\Entities\BlogCategory;
use Modules\Blog\Entities\BlogComment;
use Modules\Blog\Entities\BlogTag;
use Modules\DigitalProduct\Entities\AdditionalField;
use Modules\DigitalProduct\Entities\DigitalAuthor;
use Modules\DigitalProduct\Entities\DigitalCategories;
use Modules\DigitalProduct\Entities\DigitalChildCategories;
use Modules\DigitalProduct\Entities\DigitalLanguage;
use Modules\DigitalProduct\Entities\DigitalProduct;
use Modules\DigitalProduct\Entities\DigitalProductCategories;
use Modules\DigitalProduct\Entities\DigitalProductChildCategories;
use Modules\DigitalProduct\Entities\DigitalProductReviews;
use Modules\DigitalProduct\Entities\DigitalProductSubCategories;
use Modules\DigitalProduct\Entities\DigitalProductTags;
use Modules\DigitalProduct\Entities\DigitalSubCategories;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductCategory;
use Modules\Product\Entities\ProductCustomSpecification;
use Modules\Product\Entities\ProductTag;
use Modules\Product\Entities\ProductUom;
use Xgenious\PageBuilder\Services\PageBuilderRenderService;
use function Laravel\Prompts\alert;

class DynamicRouteManager
{
    use SeoDataConfig, SEOTools;

    public static function handle($slug)
    {
        $slugData = Slug::where('slug', $slug)->first();
//dd($slugData);
        if ($slugData) {
            $type = $slugData->morphable_type;

            if (SlugMorphableTypeEnum::PAGE->value === $type)
            {
                return self::handlePage($slug);
            }
            elseif (SlugMorphableTypeEnum::BLOG->value === $type)
            {
                return self::handleBlog($slug);
            }
            elseif (SlugMorphableTypeEnum::BLOG_CATEGORY->value === $type)
            {
                return self::handleBlogCategories($slug);
            }
            elseif (SlugMorphableTypeEnum::BLOG_TAG->value === $type)
            {
                return self::handleBlogTags($slug);
            }
            elseif (in_array(
                $type, [
                    SlugMorphableTypeEnum::PRODUCT_CATEGORY->value,
                    SlugMorphableTypeEnum::PRODUCT_SUBCATEGORY->value,
                    SlugMorphableTypeEnum::PRODUCT_CHILDCATEGORY->value
                ]
            ))
            {
                return self::handleProductCategories($slug);
            }
            elseif (SlugMorphableTypeEnum::PRODUCT->value === $type)
            {
                return self::handleProduct($slug);
            }
            elseif (in_array(
                $type, [
                    SlugMorphableTypeEnum::PRODUCT_DIGITAL_CATEGORY->value,
                    SlugMorphableTypeEnum::PRODUCT_DIGITAL_SUBCATEGORY->value,
                    SlugMorphableTypeEnum::PRODUCT_DIGITAL_CHILDCATEGORY->value
                ]
            ))
            {
                return self::handleDigitalProductCategories($slug);
            }
            elseif (SlugMorphableTypeEnum::PRODUCT_DIGITAL_AUTHOR->value === $type)
            {
                return self::handleDigitalProductAuthor($slug);
            }
            elseif (SlugMorphableTypeEnum::PRODUCT_DIGITAL_TAG->value === $type)
            {
                return self::handleDigitalProductTag($slug);
            }
            elseif (SlugMorphableTypeEnum::PRODUCT_DIGITAL_LANGUAGE->value === $type)
            {
                return self::handleDigitalProductLanguage($slug);
            }
            elseif (SlugMorphableTypeEnum::PRODUCT_DIGITAL_PRODUCT->value === $type)
            {
                return self::handleDigitalProduct($slug);
            }
        }

        abort(404);
    }

    public static function handlePage($slug)
    {
        if (tenant())
        {
            return self::handleTenantPage($slug);
        }
        else
        {
            return self::handleLandlordPage($slug);
        }
    }

    private static function handleLandlordPage($slug)
    {
        $page_post = Page::where('slug', $slug)->firstOrFail();



        self::staticSetMetaDataInfo($page_post);

        if ($page_post->use_page_builder) {
            $pageBuilderService = app(PageBuilderRenderService::class);
//            dd($pageBuilderService);
            $renderable_object = $pageBuilderService->renderPage($page_post,true);
            $page_post->rendered_content  = $renderable_object['html'];
            $page_post->pagebuilder_generated_styles = $renderable_object['css'] ?? '';
        }


        $price_page_slug = get_page_slug(get_static_option('pricing-plan'), 'price-plan');
        if ($slug === $price_page_slug) {
            $all_blogs = PricePlan::where(['status' => 'publish'])->paginate(10);
            return view('landlord.frontend.pages.dynamic-single')->with([
                'all_blogs' => $all_blogs,
                'page_post' => $page_post
            ]);
        }

        return view('landlord.frontend.pages.dynamic-single')->with([
            'page_post' => $page_post
        ]);
    }

    private static function handleTenantPage($slug)
    {
        $page_post = Page::where('slug', $slug)->first();

        $blog_page_slug = get_page_slug(get_static_option('blog_page'), 'blog_page');
        if ($slug === $blog_page_slug) {
            if (tenant()) {
                $sorting = blog_sorting(request());
                $order_by = $sorting['order_by'];
                $order = $sorting['order'];
                $order_type = $sorting['order_type'];

                $blogs = Blog::where('status', 1)->orderBy($order_by, $order)->paginate(get_static_option('blog_page_item_show') ?? 9);

                return view('blog::tenant.frontend.blog.blog-all')->with([
                    'page_post' => $page_post,
                    'blogs' => $blogs,
                    'order_type' => $order_type
                ]);
            }
        }

        $shop_page_slug = get_page_slug(get_static_option('shop_page'), 'shop_page');
        if ($slug === $shop_page_slug) {
            if (tenant()) {
                $product_object = Product::where('status_id', 1)->latest()->withSum('taxOptions', 'rate')->paginate(12);
                $categories = Category::whereHas('product', function ($query) {
                    $query->where('status_id', 1);
                })->select('id', 'name', 'slug')->withCount('product')->get();
                $sizes = Size::whereHas('product_sizes')->select('id', 'name', 'size_code', 'slug')->get();
                $colors = Color::select('id', 'name', 'color_code', 'slug')->get();
                $tags = ProductTag::whereHas('product')->select('tag_name')->distinct()->get();

                $create_arr = request()->all();
                $create_url = http_build_query($create_arr);

                $product_object->url(route('tenant.shop') . '?' . $create_url);
//                $product_object->url(route('tenant.shop') . '?' . $create_url);

                $links = $product_object->getUrlRange(1, $product_object->lastPage());
                $current_page = $product_object->currentPage();

                $products = $product_object->items();

                $pagination = $product_object->withQueryString();
                return themeView('shop.all-products', compact(
                    'page_post',
                    'products',
                    'links',
                    'current_page',
                    'pagination',
                    'categories',
                    'sizes',
                    'colors',
                    'tags'
                ));
            }
        }

        $digital_shop_page_slug = get_page_slug(get_static_option('digital_shop_page'), 'digital_shop_page');
        if (tenant_has_digital_product() && $slug === $digital_shop_page_slug) {
            if (tenant()) {
                $product_object = DigitalProduct::where('status_id', 1)->latest()->paginate(12);
                $categories = DigitalCategories::whereHas('product', function ($query) {
                    $query->where('status_id', 1);
                })->select('id', 'name', 'slug')->withCount('product')->get();
                $authors = DigitalAuthor::where('status', 1)->get();
                $languages = DigitalLanguage::where('status', 1)->get();
                $tags = DigitalProductTags::select('tag_name')->distinct()->get();

                $create_arr = request()->all();
                $create_url = http_build_query($create_arr);

                $product_object->url(route('tenant.digital.shop') . '?' . $create_url);
                $product_object->url(route('tenant.digital.shop') . '?' . $create_url);

                $links = $product_object->getUrlRange(1, $product_object->lastPage());
                $current_page = $product_object->currentPage();

                $products = $product_object->items();

                $pagination = $product_object->withQueryString();
                return themeView('digital-shop.all-products', compact(
                    'page_post',
                    'products',
                    'links',
                    'current_page',
                    'pagination',
                    'categories',
                    'tags',
                    'languages',
                    'authors'
                ));
            }
        }

        $track_page_slug = get_page_slug(get_static_option('track_order'), 'track_order');
        if ($slug === $track_page_slug) {
            if (tenant()) {
                return themeView('shop.track-order');
            }
        }

        self::staticSetMetaDataInfo($page_post);

        if ($page_post->use_page_builder) {
            $pageBuilderService = app(PageBuilderRenderService::class);
            $renderable_object = $pageBuilderService->renderPage($page_post, true);
            $page_post->rendered_content = $renderable_object['html'];
            $page_post->pagebuilder_generated_styles = $renderable_object['css'] ?? '';
        }

        return themeView('pages.dynamic-single')->with([
            'page_post' => $page_post
        ]);
    }

    public static function handleBlog($slug)
    {
        if (tenant())
        {
            $blog_post = Blog::with(['user', 'category', 'comments'])->where(['slug' => $slug, 'status' => 1])->firstOrFail();
            $blog_comments = BlogComment::where(['blog_id' => $blog_post->id, 'parent_id' => null])->orderByDesc('created_at')->take(3)->get();
            $blog_comments_count = BlogComment::where(['blog_id' => $blog_post->id, 'parent_id' => null])->count();

            $all_category = BlogCategory::withCount('blogs')->has('blogs')->get();
            $all_tags = BlogTag::orderByDesc('created_at')->select('id','title','slug')->take(15)->get();

            self::staticSetMetaDataInfo($blog_post);

            return view('blog::tenant.frontend.blog.blog-single', compact('blog_post', 'blog_comments', 'blog_comments_count', 'all_category', 'all_tags'));
        }
        else
        {
            $blog_post = Blog::with(['user', 'category', 'comments'])->where(['slug' => $slug, 'status' => 1])->firstOrFail();
            $blog_comments = BlogComment::where(['blog_id' => $blog_post->id, 'parent_id' => null])->orderByDesc('created_at')->take(3)->get();
            $recent_blogs = Blog::where('status', 1)->where('id', '!=', $blog_post->id)->orderByDesc('id')->take(4)->get();

            self::staticSetMetaDataInfo($blog_post);

            return view('blog::landlord.frontend.new-blog.blog-single', compact('blog_post', 'blog_comments', 'recent_blogs'));
        }
    }

    public static function handleBlogCategories($slug)
    {
        $sorting = blog_sorting(request());
        $order_by = $sorting['order_by'];
        $order = $sorting['order'];
        $order_type = $sorting['order_type'];

        $category = BlogCategory::where('slug', $slug)->firstOrFail();
        $blogs = Blog::where(['category_id' => $category->id, 'status' => 1])->orderBy($order_by, $order)->paginate(get_static_option('category_page_item_show') ?? 9);
        $category_name = $category->title;

        if (tenant()) {
            return view('blog::tenant.frontend.blog.blog-category')->with([
                'blogs' => $blogs,
                'category_name' => $category_name,
                'order_type' => $order_type,
            ]);
        }

        return view('blog::landlord.frontend.blog.blog-category')->with([
            'all_blogs' => $blogs,
            'category_name' => $category_name,
        ]);
    }

    public static function handleBlogTags($slug)
    {
        $sorting = blog_sorting(request());
        $order_by = $sorting['order_by'];
        $order = $sorting['order'];
        $order_type = $sorting['order_type'];

        $all_blogs = Blog::Where('tags', 'LIKE', '%' . $slug . '%')
            ->orderBy($order_by, $order)->paginate(get_static_option('blog_tag_item_show') ?? 9);

        if (tenant()) {
            return view('blog::tenant.frontend.blog.blog-category')->with([
                'blogs' => $all_blogs,
                'category_name' => ucfirst($slug),
                'order_type' => $order_type
            ]);
        }

        return view('blog::landlord.frontend.blog.blog-tags')->with([
            'all_blogs' => $all_blogs,
            'tag_name' => $slug,
        ]);
    }

    public static function handleProductCategories($slug)
    {
//        $categoryQuery = Category::select('id', 'slug', 'name', DB::raw("'Category' as type"))
//            ->where('slug', $slug);
//
//        $subcategoryQuery = SubCategory::select('id', 'slug', 'name', DB::raw("'SubCategory' as type"))
//            ->where('slug', $slug)
//            ->union($categoryQuery);
//
//        $childCategoryQuery = ChildCategory::select('id', 'slug', 'name', DB::raw("'ChildCategory' as type"))
//            ->where('slug', $slug)
//            ->union($subcategoryQuery);
//
//        $queryResult = $childCategoryQuery->first() ?? abort(404);
//dd($slug);
//        $type = $queryResult->type;

        $category = Category::select('id', 'slug', 'name', DB::raw("'Category' as type"))
            ->where('slug', $slug)
            ->first();

        if ($category) {
            $queryResult = $category;
        } else {
            // Try to find the record in the SubCategory model
            $subCategory = SubCategory::select('id', 'slug', 'name', DB::raw("'SubCategory' as type"))
                ->where('slug', $slug)
                ->first();

            if ($subCategory) {
                $queryResult = $subCategory;
            } else {
                // Try to find the record in the ChildCategory model
                $childCategory = ChildCategory::select('id', 'slug', 'name', DB::raw("'ChildCategory' as type"))
                    ->where('slug', $slug)
                    ->first();

                if ($childCategory) {
                    $queryResult = $childCategory;
                } else {
                    // If no record was found in any of the models, abort with a 404 error
                    abort(404);
                }
            }
        }

        $type = $queryResult->type;

        $model_name = "Product" . ucfirst(Str::camel($type));
        $model_name_space = "Modules\Product\Entities\\$model_name";
        $resolved_model = resolve($model_name_space);

        $target_column = match (strtolower($type)) {
            'category' => 'category_id',
            'subcategory' => 'sub_category_id',
            'childcategory' => 'child_category_id',
        };

        $products_id = $resolved_model::where($target_column, $queryResult->id)->select('product_id')->pluck('product_id');
        $product_object = Product::whereIn('id', $products_id)->paginate(10);

        abort_if(empty($product_object), 403);

        $categories = Category::whereHas('product', function ($query) {
            $query->where('status_id', 1);
        })->select('id', 'name', 'slug')->withCount('product')->get();
        $sizes = Size::whereHas('product_sizes')->select('id', 'name', 'size_code', 'slug')->get();
        $colors = Color::select('id', 'name', 'color_code', 'slug')->get();
        $tags = ProductTag::whereHas('product')->select('tag_name')->distinct()->get();

        $create_arr = request()->all();
        $create_url = http_build_query($create_arr);

        $product_object->url(route('tenant.shop') . '?' . $create_url);

        $links = $product_object->getUrlRange(1, $product_object->lastPage());
        $current_page = $product_object->currentPage();
        $pagination = $product_object->withQueryString();

        return themeView('shop.single_pages.category', [
            'category' => $queryResult, 
            'products' => $product_object, 
            'links' => $links, 
            'current_page' => $current_page,
            'pagination' => $pagination,
            'categories' => $categories,
            'sizes' => $sizes,
            'colors' => $colors,
            'tags' => $tags
        ]);
    }

    public static function handleProduct($slug)
    {
        extract(self::productVariant($slug));

        // related products
        $product_category = $product?->category?->id;
        $product_id = $product->id;
        $related_products = Product::where('status_id', 1)
            ->whereIn('id', function ($query) use ($product_id, $product_category) {
                $query->select('product_categories.product_id')
                    ->from(with(new ProductCategory())->getTable())
                    ->where('product_id', '!=', $product_id)
                    ->where('category_id', '=', $product_category)
                    ->get();
            })
            ->withSum('taxOptions', 'rate')
            ->inRandomOrder()
            ->take(5)
            ->get();

        // sidebar data
        $all_category = ProductCategory::all();
        $all_units = ProductUom::all();
        $maximum_available_price = Product::query()->with('category')->max('price');
        $min_price = request()->pr_min ? request()->pr_min : Product::query()->min('price');
        $max_price = request()->pr_max ? request()->pr_max : $maximum_available_price;
        $all_tags = ProductTag::all();
        $custom_specifications = ProductCustomSpecification::where('product_id', $product->id)->get();
        // todo:: now check product inventory set
//dd($custom_specifications);
        return themeView('shop.product_details.product-details', compact(
            'product',
            'related_products',
            'available_attributes',
            'product_inventory_set',
            'additional_info_store',
            'all_category',
            'all_units',
            'maximum_available_price',
            'min_price',
            'max_price',
            'all_tags',
            'productColors',
            'productSizes',
            'setting_text',
            'custom_specifications'
        ));
    }

    public static function productVariant($slug)
    {
        // Delegate to TenantFrontendController which has the full Sprint-19 variant logic
        return \App\Http\Controllers\Tenant\Frontend\TenantFrontendController::productVariant($slug);
    }

    public static function handleDigitalProductCategories($slug)
    {
//        $categoryQuery = DigitalCategories::select('id', 'slug', 'name', DB::raw("'DigitalProductCategories' as type"))
//            ->where('slug', $slug);
//
//        $subcategoryQuery = DigitalSubCategories::select('id', 'slug', 'name', DB::raw("'DigitalProductSubCategories' as type"))
//            ->where('slug', $slug)
//            ->union($categoryQuery);
//
//        $childCategoryQuery = DigitalChildCategories::select('id', 'slug', 'name', DB::raw("'DigitalProductChildCategories' as type"))
//            ->where('slug', $slug)
//            ->union($subcategoryQuery);
//
//        $queryResult = $childCategoryQuery->first() ?? abort(404);

        $category = DigitalCategories::select('id', 'slug', 'name', DB::raw("'DigitalProductCategories' as type"))
            ->where('slug', $slug)
            ->first();

        if ($category) {
            $queryResult = $category;
        } else {
            // Try to find the record in the DigitalSubCategories model
            $subCategory = DigitalSubCategories::select('id', 'slug', 'name', DB::raw("'DigitalProductSubCategories' as type"))
                ->where('slug', $slug)
                ->first();

            if ($subCategory) {
                $queryResult = $subCategory;
            } else {
                // Try to find the record in the DigitalChildCategories model
                $childCategory = DigitalChildCategories::select('id', 'slug', 'name', DB::raw("'DigitalProductChildCategories' as type"))
                    ->where('slug', $slug)
                    ->first();

                if ($childCategory) {
                    $queryResult = $childCategory;
                } else {
                    // If no record was found in any of the models, abort with a 404 error
                    abort(404);
                }
            }
        }

        $type = $queryResult->type;

        $model_name = ucfirst(Str::camel($type));
        $model_name_space = "Modules\DigitalProduct\Entities\\$model_name";
        $resolved_model = resolve($model_name_space);

        $target_column = match (strtolower($type)) {
            'digitalproductcategories' => 'category_id',
            'digitalproductsubcategories' => 'sub_category_id',
            'digitalproductchildcategories' => 'child_category_id',
        };

        $products_id = $resolved_model::where($target_column, $queryResult->id)->select('product_id')->pluck('product_id');
        $products = DigitalProduct::whereIn('id', $products_id)->paginate(12);

        abort_if(empty($products), 403);

        return themeView('digital-shop.single_pages.category', [
                'category' => $queryResult,
                'products' => $products,
                'type' => trim(str_replace(['_id', '_'], ' ', $target_column))
            ]
        );
    }

    public static function handleDigitalProductAuthor($slug)
    {
        $category = DigitalAuthor::where('slug', $slug)->select('id', 'name')->first();
        $products_id = AdditionalField::where('author_id', $category->id)->select('product_id')->pluck('product_id');

        $products = DigitalProduct::whereIn('id', $products_id)->paginate(12);

        abort_if(empty($products), 403);

        return themeView('digital-shop.single_pages.category', ['category' => $category, 'products' => $products, 'type' => 'Author']);
    }

    public static function handleDigitalProductTag($slug)
    {
        $products_id = DigitalProductTags::where('tag_name', $slug)->select('product_id')->pluck('product_id');
        $products = DigitalProduct::whereIn('id', $products_id)->paginate(12);

        abort_if(empty($products), 403);

        return themeView('digital-shop.single_pages.category', ['category' => (object) ['name' => $slug] ,'products' => $products, 'type' => 'Tags']);
    }

    public static function handleDigitalProductLanguage($slug)
    {
        $category = DigitalLanguage::where('slug', $slug)->select('id', 'name')->firstOrFail();
        $products_id = AdditionalField::where('language', $category->id)->select('product_id')->pluck('product_id');

        $products = DigitalProduct::whereIn('id', $products_id)->paginate(12);
        abort_if(empty($products), 403);

        return themeView('digital-shop.single_pages.category', ['category' => $category ,'products' => $products, 'type' => 'Languages']);
    }

    public static function handleDigitalProduct($slug)
    {
        if (! tenant_has_digital_product()) {
            abort(404);
        }

        $product = DigitalProduct::with('category', 'tag', 'tax', 'additionalFields', 'additionalCustomFields', 'gallery_images', 'refund_policy', 'downloads')
            ->withCount('downloads')
            ->where('slug', $slug)
            ->where('status_id', 1)
            ->firstOrFail();

        // related products
        $product_category = $product?->category?->id;
        $product_id = $product->id;
        $related_products = DigitalProduct::where('status_id', 1)
            ->whereIn('id', function ($query) use ($product_id, $product_category) {
                $query->select('digital_product_categories.product_id')
                    ->from(with(new DigitalProductCategories())->getTable())
                    ->where('product_id', '!=', $product_id)
                    ->where('category_id', '=', $product_category)
                    ->get();
            })
            ->inRandomOrder()
            ->take(3)
            ->get();

        $reviews = DigitalProductReviews::where('product_id', $product->id)->orderBy('id', 'desc')->take(5)->get();

        return themeView('digital-shop.product_details.product-details', compact(
            'product',
            'related_products',
            'reviews'
        ));
    }
}
