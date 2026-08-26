<?php

namespace Database\Seeders\Tenant;

use App\Helpers\ImageDataSeedingHelper;
use App\Helpers\SanitizeInput;
use App\Mail\TenantCredentialMail;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Language;
use App\Models\Menu;
use App\Models\Page;
use App\Models\PlanFeature;
use App\Models\PricePlan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Attributes\Entities\Category;
use Modules\Attributes\Entities\ChildCategory;
use Modules\Attributes\Entities\Color;
use Modules\Attributes\Entities\Size;
use Modules\Attributes\Entities\SubCategory;
use Modules\Campaign\Entities\Campaign;
use Modules\Product\Entities\Product;

class ProductSeed extends Seeder
{
    /**
     * @throws \Exception
     */
    public function run()
    {
        if (!Schema::hasTable('shipping_methods'))
        {
            Schema::create('shipping_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('zone_id')->nullable(); // could be zone independent, so default = null
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('shipping_method_options'))
        {
            Schema::create('shipping_method_options', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->unsignedBigInteger('shipping_method_id');
                $table->boolean('status')->default(true);
                $table->boolean('tax_status')->default(true);
                $table->string('setting_preset')->nullable();
                $table->float('cost')->default(0);
                $table->float('minimum_order_amount')->nullable();
                $table->string('coupon')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('zones'))
        {
            Schema::create('zones', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('zone_regions'))
        {
            Schema::create('zone_regions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('zone_id');
                $table->longText('country')->nullable();
                $table->longText('state')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_coupons'))
        {
            Schema::create('product_coupons', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('code')->unique();
                $table->string('discount')->nullable();
                $table->string('discount_type')->nullable();
                $table->string('discount_on')->nullable();
                $table->longText('discount_on_details')->nullable();
                $table->date('expire_date')->nullable();
                $table->string('status')->default('draft');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('refund_products'))
        {
            Schema::create('refund_products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('product_id');
                $table->boolean('status')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('refund_chat'))
        {
            Schema::create('refund_chat', function (Blueprint $table) {
                $table->id();
                $table->text('title')->nullable();
                $table->text('via')->nullable();
                $table->string('operating_system')->nullable();
                $table->string('user_agent')->nullable();
                $table->longText('description')->nullable();
                $table->text('subject')->nullable();
                $table->string('status')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('refund_messages'))
        {
            Schema::create('refund_messages', function (Blueprint $table) {
                $table->id();
                $table->longText('message')->nullable();
                $table->string('notify')->nullable();
                $table->string('attachment')->nullable();
                $table->string('type')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('refund_chat_id')->nullable();
                $table->timestamps();
            });
        }

            $this->seedCategories();
            $this->seedSubCategories();
            $this->seedChildCategories();
            $this->seedColors();
            $this->seedSize();
            $this->seedTags();
            $this->seedUnit();
            $this->seedCountries();
            $this->seedStates();
            $this->seedCities();
            $this->seedDeliveryOption();
            $this->seedBadge();

        $this->seedTaxClass();
        $this->seedProduct();
        $this->seedProductCategory();
        $this->seedProductSubCategories();
        $this->seedProductChildCategories();
        $this->seedProductTags();
        $this->seedProductGalleries();
        $this->seedProductInventories();
        $this->seedProductInventoryDetails();
        $this->seedProductUOM();
        $this->seedProductCreatedBy();
        $this->seedProductDeliveryOption();
        $this->seedProductReturnPolicies();

        if (!Schema::hasTable('campaigns'))
        {
            Schema::create('campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->longText('subtitle')->nullable();
                $table->bigInteger('image')->nullable();
                $table->timestamp('start_date')->nullable();
                $table->timestamp('end_date')->nullable();
                $table->string('status')->nullable();
                $table->unsignedInteger('admin_id')->nullable();
                $table->unsignedInteger('vendor_id')->nullable();
                $table->string('type')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('campaign_products'))
        {
            Schema::create('campaign_products', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('product_id');
                $table->bigInteger('campaign_id')->nullable();
                $table->decimal('campaign_price')->nullable();
                $table->integer('units_for_sale')->nullable();
                $table->timestamp('start_date')->nullable();
                $table->timestamp('end_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('campaign_sold_products'))
        {
            Schema::create('campaign_sold_products', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('product_id')->nullable();
                $table->integer('sold_count')->nullable();
                $table->double('total_amount')->nullable();
                $table->timestamps();
            });
        }

        $this->seedCampaign();
        $this->seedCampaignProducts();
    }

    private function seedCategories()
    {
        if (session()->get('theme') == 'casual')
        {
            Category::insert([
                [
                    'id' => 6,
                    'name' => 'Clothing',
                    'slug' => 'clothing',
                    'image_id' => 517,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:29:38',
                    'updated_at' => '2022-11-16 09:29:38',
                    'deleted_at' => null,
                ],
                [
                    'id' => 7,
                    'name' => 'Beauty',
                    'slug' => 'beauty',
                    'image_id' => 518,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:30:00',
                    'updated_at' => '2022-11-16 09:30:00',
                    'deleted_at' => null,
                ],
                [
                    'id' => 8,
                    'name' => 'Shoes',
                    'slug' => 'shoes',
                    'image_id' => 523,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:30:19',
                    'updated_at' => '2022-11-16 09:30:19',
                    'deleted_at' => null,
                ],
                [
                    'id' => 9,
                    'name' => 'Bag & Laggage',
                    'slug' => 'bag-laggage',
                    'image_id' => 521,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:30:47',
                    'updated_at' => '2022-11-16 09:30:47',
                    'deleted_at' => null,
                ],
                [
                    'id' => 10,
                    'name' => 'Man',
                    'slug' => 'man',
                    'image_id' => 521,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:31:07',
                    'updated_at' => '2022-11-16 09:31:07',
                    'deleted_at' => null,
                ],
                [
                    'id' => 11,
                    'name' => 'Woman',
                    'slug' => 'woman',
                    'image_id' => 519,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:31:18',
                    'updated_at' => '2022-11-16 09:31:18',
                    'deleted_at' => null,
                ],
                [
                    'id' => 12,
                    'name' => 'Baby',
                    'slug' => 'baby',
                    'image_id' => 520,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:34:38',
                    'updated_at' => '2022-11-16 09:34:38',
                    'deleted_at' => null,
                ]
            ]);
        } else {
            Category::insert([
                [
                    'id' => 6,
                    'name' => 'Clothing',
                    'slug' => 'clothing',
                    'image_id' => 370,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:29:38',
                    'updated_at' => '2022-11-16 09:29:38',
                    'deleted_at' => null,
                ],
                [
                    'id' => 7,
                    'name' => 'Beauty',
                    'slug' => 'beauty',
                    'image_id' => 356,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:30:00',
                    'updated_at' => '2022-11-16 09:30:00',
                    'deleted_at' => null,
                ],
                [
                    'id' => 8,
                    'name' => 'Shoes',
                    'slug' => 'shoes',
                    'image_id' => 365,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:30:19',
                    'updated_at' => '2022-11-16 09:30:19',
                    'deleted_at' => null,
                ],
                [
                    'id' => 9,
                    'name' => 'Bag & Laggage',
                    'slug' => 'bag-laggage',
                    'image_id' => 358,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:30:47',
                    'updated_at' => '2022-11-16 09:30:47',
                    'deleted_at' => null,
                ],
                [
                    'id' => 10,
                    'name' => 'Man',
                    'slug' => 'man',
                    'image_id' => 359,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:31:07',
                    'updated_at' => '2022-11-16 09:31:07',
                    'deleted_at' => null,
                ],
                [
                    'id' => 11,
                    'name' => 'Woman',
                    'slug' => 'woman',
                    'image_id' => 363,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:31:18',
                    'updated_at' => '2022-11-16 09:31:18',
                    'deleted_at' => null,
                ],
                [
                    'id' => 12,
                    'name' => 'Baby',
                    'slug' => 'baby',
                    'image_id' => 362,
                    'status_id' => 1,
                    'created_at' => '2022-11-16 09:34:38',
                    'updated_at' => '2022-11-16 09:34:38',
                    'deleted_at' => null,
                ]
            ]);
        }

        Category::all()->each(function ($category) {
            try {
                $category->slug()->create(['slug' => $category->slug]);
            } catch (\Exception $exception) {
                $category->slug()->create(['slug' => create_slug($category->slug, 'Slug')]);
            }
        });
    }

    private function seedSubCategories()
    {
        DB::statement("INSERT IGNORE INTO `sub_categories` (`id`, `category_id`, `name`, `slug`, `description`, `image_id`, `created_at`, `updated_at`, `deleted_at`, `status_id`) VALUES
        (10, 10, 'T-Shirt', 't-shirt', NULL, 359, '2022-11-16 09:31:55', '2022-11-16 09:31:55', NULL, 1),
        (11, 6, 'Jacket', 'jacket', NULL, 357, '2022-11-16 09:32:19', '2022-11-16 09:32:19', NULL, 1),
        (12, 10, 'Jersy', 'jersy', NULL, 364, '2022-11-16 09:32:42', '2022-11-16 09:32:42', NULL, 1),
        (13, 7, 'Sun Glass', 'sun-glass', NULL, 360, '2022-11-16 09:33:04', '2022-11-16 09:33:04', NULL, 1),
        (14, 11, 'Sharee', 'sharee', NULL, 363, '2022-11-16 09:33:23', '2022-11-16 09:33:23', NULL, 1),
        (15, 8, 'High Heel', 'high-heel', NULL, 356, '2022-11-16 09:33:54', '2022-11-16 09:33:54', NULL, 1),
        (16, 8, 'Baby Shoe', 'baby-shoe', NULL, 362, '2022-11-16 09:35:14', '2022-11-16 09:35:14', NULL, 1),
        (17, 10, 'Pant', 'pant', NULL, 352, '2022-11-16 09:37:12', '2022-11-16 09:37:12', NULL, 1),
        (18, 9, 'Bag', 'bag', NULL, 367, '2022-11-16 09:39:03', '2022-11-16 09:39:03', NULL, 1)");

        SubCategory::all()->each(function ($category) {
            try {
                $category->slug()->create(['slug' => $category->slug]);
            } catch (\Exception $exception) {
                $category->slug()->create(['slug' => create_slug($category->slug, 'Slug')]);
            }
        });
    }

    private function seedChildCategories()
    {
        DB::statement("INSERT IGNORE INTO `child_categories` (`id`, `category_id`, `sub_category_id`, `name`, `slug`, `description`, `image_id`, `created_at`, `updated_at`, `deleted_at`, `status_id`) VALUES
        (12, 7, 13, 'Fiber Sun Glass', 'fiber-sun-glass', NULL, 360, '2022-11-16 09:35:45', '2022-11-16 09:35:45', NULL, 1),
        (13, 10, 10, 'T-Shirt Set', 't-shirt-set', NULL, 361, '2022-11-16 09:36:11', '2022-11-16 09:36:11', NULL, 1),
        (14, 10, 17, 'Jeans', 'jeans', NULL, 352, '2022-11-16 09:37:35', '2022-11-16 09:37:35', NULL, 1),
        (15, 6, 11, 'Leather Jacket', 'leather-jacket', NULL, 368, '2022-11-16 09:38:08', '2022-11-16 09:38:08', NULL, 1),
        (16, 9, 18, 'Purse Bag', 'purse-bag', NULL, 367, '2022-11-16 09:39:24', '2022-11-16 09:39:24', NULL, 1),
        (17, 8, 16, 'Fabric Shoe', 'fabric-shoe', NULL, 362, '2022-11-16 09:42:45', '2022-11-16 09:42:45', NULL, 1),
        (18, 11, 14, 'Classic Sharee', 'classic-sharee', NULL, 363, '2022-11-16 09:43:13', '2022-11-16 09:43:13', NULL, 1),
        (19, 10, 10, 'Graphics T-Short', 'graphics-t-short', NULL, 355, '2022-11-16 09:43:43', '2022-11-16 09:43:43', NULL, 1),
        (20, 8, 15, 'Party Heel', 'part-heel', NULL, 375, '2022-11-16 10:40:39', '2022-11-16 10:42:12', NULL, 1)");

        ChildCategory::all()->each(function ($category) {
            try {
                $category->slug()->create(['slug' => $category->slug]);
            } catch (\Exception $exception) {
                $category->slug()->create(['slug' => create_slug($category->slug, 'Slug')]);
            }
        });
    }

    private function seedColors()
    {
        DB::statement("INSERT IGNORE INTO `colors` (`id`, `name`, `color_code`, `slug`, `created_at`, `updated_at`) VALUES
        (1, 'Red', '#ff3838', 'red', '2022-08-22 05:29:37', '2022-09-20 05:36:03'),
        (3, 'Black', '#000000', 'black', '2022-08-22 05:29:53', '2022-08-22 05:29:53'),
        (4, 'White', '#ffffff', 'white', '2022-08-22 05:30:01', '2022-09-20 04:38:05'),
        (5, 'Blue', '#0984e3', 'blue', '2022-08-22 05:30:12', '2022-09-20 05:31:20'),
        (6, 'Green', '#55efc4', 'green', '2022-08-22 05:30:20', '2022-09-20 05:30:30'),
        (7, 'Yellow', '#feca39', 'yellow', '2022-08-22 05:30:34', '2022-09-20 05:33:20'),
        (8, 'Magenta', '#e82fa7', 'magenta', '2022-09-15 12:16:06', '2022-09-20 05:31:58'),
        (9, 'Pink', '#e84393', 'pink', '2022-09-15 12:16:26', '2022-09-20 05:32:48'),
        (10, 'Purple', '#a600ff', 'purple', '2022-09-15 12:16:40', '2022-09-15 12:16:40'),
        (11, 'Sky Blue', '#54a0ff', 'sky-blue', '2022-09-15 12:16:57', '2022-09-20 05:34:20'),
        (12, 'Olive', '#c4e538', 'olive', '2022-09-15 12:17:14', '2022-09-20 05:37:02')");

        Color::all()->each(function ($category) {
            try {
                $category->slug()->create(['slug' => $category->slug]);
            } catch (\Exception $exception) {
                $category->slug()->create(['slug' => create_slug($category->slug, 'Slug')]);
            }
        });
    }

    private function seedSize()
    {
        DB::statement("INSERT IGNORE INTO `sizes` (`id`, `name`, `size_code`, `slug`, `created_at`, `updated_at`) VALUES
        (1, 'Large', 'L', 'large', '2022-08-22 05:31:08', '2022-08-22 05:31:08'),
        (2, 'Small', 'S', 'small', '2022-08-22 05:31:12', '2022-08-22 05:31:12'),
        (3, 'Medium', 'M', 'medium', '2022-08-22 05:31:16', '2022-08-22 05:31:16'),
        (4, 'Very Small', 'XS', 'very-small', '2022-08-22 05:32:07', '2022-08-22 05:32:07'),
        (5, 'Very Large', 'XL', 'very-large', '2022-08-22 05:32:16', '2022-09-12 12:21:36')");

        Size::all()->each(function ($category) {
            try {
                $category->slug()->create(['slug' => $category->slug]);
            } catch (\Exception $exception) {
                $category->slug()->create(['slug' => create_slug($category->slug, 'Slug')]);
            }
        });
    }

    private function seedTags()
    {
        DB::statement("INSERT IGNORE INTO `tags` (`id`, `tag_text`, `created_at`, `updated_at`, `deleted_at`) VALUES
        (5, 'abrasives', '2022-11-16 09:44:18', '2022-11-16 09:44:18', NULL),
        (6, 'baby suit', '2022-11-16 09:44:24', '2022-11-16 09:44:24', NULL),
        (7, 'ameriacan logo t shirt', '2022-11-16 09:44:29', '2022-11-16 09:44:29', NULL),
        (8, 'best jeans pant', '2022-11-16 09:44:34', '2022-11-16 09:44:34', NULL),
        (9, 'babys frock', '2022-11-16 09:44:40', '2022-11-16 09:44:40', NULL),
        (10, 'winter dress', '2022-11-16 09:44:46', '2022-11-16 09:44:46', NULL),
        (11, 'best saree for wedding', '2022-11-16 09:44:54', '2022-11-16 09:44:54', NULL),
        (12, 'best saree', '2022-11-16 09:44:58', '2022-11-16 09:44:58', NULL),
        (13, 'gifed saree', '2022-11-16 09:45:03', '2022-11-16 09:45:03', NULL),
        (14, 'color t shirt', '2022-11-16 09:45:08', '2022-11-16 09:45:08', NULL),
        (15, 'amazing t-shirt', '2022-11-16 09:45:12', '2022-11-16 09:45:12', NULL),
        (16, 'stylish hat', '2022-11-16 09:45:18', '2022-11-16 09:45:18', NULL),
        (17, 'denim shirt', '2022-11-16 09:45:27', '2022-11-16 09:45:27', NULL),
        (18, 'best dress for kid', '2022-11-16 09:45:33', '2022-11-16 09:45:33', NULL),
        (19, 'sun glasses', '2022-11-16 09:45:40', '2022-11-16 09:45:40', NULL),
        (20, 'casual t shirt', '2022-11-16 09:45:48', '2022-11-16 09:45:48', NULL),
        (21, 'kameez', '2022-11-16 09:46:06', '2022-11-16 09:46:06', NULL)");
    }

    private function seedUnit()
    {
        DB::statement("INSERT IGNORE INTO `units` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
        (1, 'Kg', '2022-08-22 05:28:38', '2022-08-22 05:28:38', NULL),
        (2, 'Lb', '2022-08-22 05:28:41', '2022-08-22 05:28:41', NULL),
        (3, 'Dozen', '2022-08-22 05:28:49', '2022-08-22 05:28:49', NULL),
        (4, 'Ltr', '2022-08-22 05:28:53', '2022-08-22 05:28:53', NULL),
        (5, 'g', '2022-08-22 05:29:02', '2022-08-22 05:29:02', NULL),
        (6, 'Piece', '2022-11-16 09:06:11', '2022-11-16 09:06:11', NULL)");
    }

    private function seedCountries()
    {
        DB::statement("INSERT IGNORE INTO `countries` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
        (1, 'Bangladesh', 'publish', '2022-08-22 06:35:32', '2022-08-22 06:35:32'),
        (2, 'USA', 'publish', '2022-08-22 06:35:38', '2022-08-22 06:35:38'),
        (3, 'Turkey', 'publish', '2022-08-22 06:35:43', '2022-08-22 06:35:43'),
        (4, 'Russia', 'publish', '2022-08-22 06:35:48', '2022-08-22 06:35:48'),
        (5, 'China', 'publish', '2022-08-22 06:35:52', '2022-08-22 06:35:52'),
        (6, 'England', 'publish', '2022-08-22 06:35:59', '2022-08-22 06:35:59'),
        (7, 'Saudi Arabia', 'publish', '2022-08-22 06:41:29', '2022-08-22 06:41:29')");
    }

//    private function seedStates()
//    {
//        DB::statement("INSERT IGNORE INTO `states` (`id`, `name`, `country_id`, `status`, `created_at`, `updated_at`) VALUES
//        (1, 'Dhaka', 1, 'publish', '2022-08-22 06:36:11', '2022-08-22 06:36:11'),
//        (2, 'Chandpur', 1, 'publish', '2022-08-22 06:36:15', '2022-08-22 06:36:15'),
//        (3, 'Noakhali', 1, 'publish', '2022-08-22 06:36:21', '2022-08-22 06:36:21'),
//        (4, 'Bhola', 1, 'publish', '2022-08-22 06:36:27', '2022-08-22 06:36:27'),
//        (5, 'Barishal', 1, 'publish', '2022-08-22 06:36:32', '2022-08-22 06:36:32'),
//        (6, 'Nework', 2, 'publish', '2022-08-22 06:36:43', '2022-08-22 06:36:43'),
//        (7, 'Chicago', 2, 'publish', '2022-08-22 06:36:54', '2022-08-22 06:36:54'),
//        (8, 'Las Vegas', 2, 'publish', '2022-08-22 06:37:05', '2022-08-22 06:37:05'),
//        (9, 'Ankara', 3, 'publish', '2022-08-22 06:37:12', '2022-08-22 06:37:12'),
//        (10, 'Istanbul', 3, 'publish', '2022-08-22 06:37:19', '2022-08-22 06:37:19'),
//        (11, 'Izmir', 3, 'publish', '2022-08-22 06:37:26', '2022-08-22 06:37:26'),
//        (12, 'Moscow', 4, 'publish', '2022-08-22 06:37:34', '2022-08-22 06:37:34'),
//        (13, 'Lalingard', 4, 'publish', '2022-08-22 06:37:44', '2022-08-22 06:37:44'),
//        (14, 'Siberia', 4, 'publish', '2022-08-22 06:37:55', '2022-08-22 06:37:55'),
//        (15, 'Shanghai', 5, 'publish', '2022-08-22 06:38:04', '2022-08-22 06:38:04'),
//        (16, 'Anuhai', 5, 'publish', '2022-08-22 06:38:13', '2022-08-22 06:38:13'),
//        (17, 'Hong Kong', 5, 'publish', '2022-08-22 06:38:29', '2022-08-22 06:38:29'),
//        (18, 'London', 6, 'publish', '2022-08-22 06:38:37', '2022-08-22 06:38:37'),
//        (19, 'Madina', 7, 'publish', '2022-08-22 06:41:44', '2022-08-22 06:41:44')");
//    }
    private function seedStates()
    {
        DB::statement("INSERT IGNORE INTO `states` (`id`, `name`, `country_id`, `status`, `created_at`, `updated_at`) VALUES
        (1, 'Andhra Pradesh', 1, 'publish', '2025-06-25 12:00:00', '2025-06-25 12:00:00'),
        (2, 'Arunachal Pradesh', 1, 'publish', '2025-06-25 12:00:01', '2025-06-25 12:00:01'),
        (3, 'Assam', 1, 'publish', '2025-06-25 12:00:02', '2025-06-25 12:00:02'),
        (4, 'Bihar', 1, 'publish', '2025-06-25 12:00:03', '2025-06-25 12:00:03'),
        (5, 'Chhattisgarh', 1, 'publish', '2025-06-25 12:00:04', '2025-06-25 12:00:04'),
        (6, 'Delhi', 1, 'publish', '2025-06-25 12:00:05', '2025-06-25 12:00:05'),
        (7, 'Goa', 1, 'publish', '2025-06-25 12:00:06', '2025-06-25 12:00:06'),
        (8, 'Gujarat', 1, 'publish', '2025-06-25 12:00:07', '2025-06-25 12:00:07'),
        (9, 'Haryana', 1, 'publish', '2025-06-25 12:00:08', '2025-06-25 12:00:08'),
        (10, 'Himachal Pradesh', 1, 'publish', '2025-06-25 12:00:09', '2025-06-25 12:00:09'),
        (11, 'Jharkhand', 1, 'publish', '2025-06-25 12:00:10', '2025-06-25 12:00:10'),
        (12, 'Karnataka', 1, 'publish', '2025-06-25 12:00:11', '2025-06-25 12:00:11'),
        (13, 'Kerala', 1, 'publish', '2025-06-25 12:00:12', '2025-06-25 12:00:12'),
        (14, 'Madhya Pradesh', 1, 'publish', '2025-06-25 12:00:13', '2025-06-25 12:00:13'),
        (15, 'Maharashtra', 1, 'publish', '2025-06-25 12:00:14', '2025-06-25 12:00:14'),
        (16, 'Manipur', 1, 'publish', '2025-06-25 12:00:15', '2025-06-25 12:00:15'),
        (17, 'Meghalaya', 1, 'publish', '2025-06-25 12:00:16', '2025-06-25 12:00:16'),
        (18, 'Mizoram', 1, 'publish', '2025-06-25 12:00:17', '2025-06-25 12:00:17'),
        (19, 'Nagaland', 1, 'publish', '2025-06-25 12:00:18', '2025-06-25 12:00:18'),
        (20, 'Odisha', 1, 'publish', '2025-06-25 12:00:19', '2025-06-25 12:00:19'),
        (21, 'Punjab', 1, 'publish', '2025-06-25 12:00:20', '2025-06-25 12:00:20'),
        (22, 'Rajasthan', 1, 'publish', '2025-06-25 12:00:21', '2025-06-25 12:00:21'),
        (23, 'Sikkim', 1, 'publish', '2025-06-25 12:00:22', '2025-06-25 12:00:22'),
        (24, 'Tamil Nadu', 1, 'publish', '2025-06-25 12:00:23', '2025-06-25 12:00:23'),
        (25, 'Telangana', 1, 'publish', '2025-06-25 12:00:24', '2025-06-25 12:00:24'),
        (26, 'Tripura', 1, 'publish', '2025-06-25 12:00:25', '2025-06-25 12:00:25'),
        (27, 'Uttar Pradesh', 1, 'publish', '2025-06-25 12:00:26', '2025-06-25 12:00:26'),
        (28, 'Uttarakhand', 1, 'publish', '2025-06-25 12:00:27', '2025-06-25 12:00:27'),
        (29, 'West Bengal', 1, 'publish', '2025-06-25 12:00:28', '2025-06-25 12:00:28')");
    }

    private function seedCities()
    {
        try {
            DB::statement("INSERT IGNORE INTO `cities` (`id`, `name`, `country_id`,`state_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Anakapalli', 1, 1, 'publish', '2025-06-25 12:00:30', '2025-06-25 12:00:30'),
(2, 'Anantapur', 1, 1, 'publish', '2025-06-25 12:00:31', '2025-06-25 12:00:31'),
(3, 'Annamayya', 1, 1, 'publish', '2025-06-25 12:00:32', '2025-06-25 12:00:32'),
(4, 'Bapatla', 1, 1, 'publish', '2025-06-25 12:00:33', '2025-06-25 12:00:33'),
(5, 'Chittoor', 1, 1, 'publish', '2025-06-25 12:00:34', '2025-06-25 12:00:34'),
(6, 'Dr. B.R. Ambedkar Konaseema', 1, 1, 'publish', '2025-06-25 12:00:35', '2025-06-25 12:00:35'),
(7, 'East Godavari', 1, 1, 'publish', '2025-06-25 12:00:36', '2025-06-25 12:00:36'),
(8, 'Eluru', 1, 1, 'publish', '2025-06-25 12:00:37', '2025-06-25 12:00:37'),
(9, 'Guntur', 1, 1, 'publish', '2025-06-25 12:00:38', '2025-06-25 12:00:38'),
(10, 'Kakinada', 1, 1, 'publish', '2025-06-25 12:00:39', '2025-06-25 12:00:39'),
(11, 'Krishna', 1, 1, 'publish', '2025-06-25 12:00:40', '2025-06-25 12:00:40'),
(12, 'Kurnool', 1, 1, 'publish', '2025-06-25 12:00:41', '2025-06-25 12:00:41'),
(13, 'Nandyal', 1, 1, 'publish', '2025-06-25 12:00:42', '2025-06-25 12:00:42'),
(14, 'Nellore (Sri Potti Sriramulu Nellore)', 1, 1, 'publish', '2025-06-25 12:00:43', '2025-06-25 12:00:43'),
(15, 'NTR', 1, 1, 'publish', '2025-06-25 12:00:44', '2025-06-25 12:00:44'),
(16, 'Palnadu', 1, 1, 'publish', '2025-06-25 12:00:45', '2025-06-25 12:00:45'),
(17, 'Parvathipuram Manyam', 1, 1, 'publish', '2025-06-25 12:00:46', '2025-06-25 12:00:46'),
(18, 'Prakasam', 1, 1, 'publish', '2025-06-25 12:00:47', '2025-06-25 12:00:47'),
(19, 'Srikakulam', 1, 1, 'publish', '2025-06-25 12:00:48', '2025-06-25 12:00:48'),
(20, 'Tirupati', 1, 1, 'publish', '2025-06-25 12:00:49', '2025-06-25 12:00:49'),
(21, 'Visakhapatnam', 1, 1, 'publish', '2025-06-25 12:00:50', '2025-06-25 12:00:50'),
(22, 'Vizianagaram', 1, 1, 'publish', '2025-06-25 12:00:51', '2025-06-25 12:00:51'),
(23, 'West Godavari', 1, 1, 'publish', '2025-06-25 12:00:52', '2025-06-25 12:00:52'),
(24, 'YSR Kadapa', 1, 1, 'publish', '2025-06-25 12:00:53', '2025-06-25 12:00:53'),
(25, 'Alluri Sitharama Raju', 1, 1, 'publish', '2025-06-25 12:00:54', '2025-06-25 12:00:54'),
(26, 'Sri Satya Sai', 1, 1, 'publish', '2025-06-25 12:00:55', '2025-06-25 12:00:55'),
(27, 'Tawang', 1, 2, 'publish', '2025-06-25 12:00:56', '2025-06-25 12:00:56'),
(28, 'West Kameng', 1, 2, 'publish', '2025-06-25 12:00:57', '2025-06-25 12:00:57'),
(29, 'East Kameng', 1, 2, 'publish', '2025-06-25 12:00:58', '2025-06-25 12:00:58'),
(30, 'Papum Pare', 1, 2, 'publish', '2025-06-25 12:00:59', '2025-06-25 12:00:59'),
(31, 'Kurung Kumey', 1, 2, 'publish', '2025-06-25 12:01:00', '2025-06-25 12:01:00'),
(32, 'Kra Daadi', 1, 2, 'publish', '2025-06-25 12:01:01', '2025-06-25 12:01:01'),
(33, 'Lower Subansiri', 1, 2, 'publish', '2025-06-25 12:01:02', '2025-06-25 12:01:02'),
(34, 'Upper Subansiri', 1, 2, 'publish', '2025-06-25 12:01:03', '2025-06-25 12:01:03'),
(35, 'West Siang', 1, 2, 'publish', '2025-06-25 12:01:04', '2025-06-25 12:01:04'),
(36, 'East Siang', 1, 2, 'publish', '2025-06-25 12:01:05', '2025-06-25 12:01:05'),
(37, 'Siang', 1, 2, 'publish', '2025-06-25 12:01:06', '2025-06-25 12:01:06'),
(38, 'Upper Siang', 1, 2, 'publish', '2025-06-25 12:01:07', '2025-06-25 12:01:07'),
(39, 'Lower Siang', 1, 2, 'publish', '2025-06-25 12:01:08', '2025-06-25 12:01:08'),
(40, 'Lower Dibang Valley', 1, 2, 'publish', '2025-06-25 12:01:09', '2025-06-25 12:01:09'),
(41, 'Dibang Valley', 1, 2, 'publish', '2025-06-25 12:01:10', '2025-06-25 12:01:10'),
(42, 'Anjaw', 1, 2, 'publish', '2025-06-25 12:01:11', '2025-06-25 12:01:11'),
(43, 'Lohit', 1, 2, 'publish', '2025-06-25 12:01:12', '2025-06-25 12:01:12'),
(44, 'Namsai', 1, 2, 'publish', '2025-06-25 12:01:13', '2025-06-25 12:01:13'),
(45, 'Changlang', 1, 2, 'publish', '2025-06-25 12:01:14', '2025-06-25 12:01:14'),
(46, 'Tirap', 1, 2, 'publish', '2025-06-25 12:01:15', '2025-06-25 12:01:15'),
(47, 'Longding', 1, 2, 'publish', '2025-06-25 12:01:16', '2025-06-25 12:01:16'),
(48, 'Pakke Kessang', 1, 2, 'publish', '2025-06-25 12:01:17', '2025-06-25 12:01:17'),
(49, 'Kamle', 1, 2, 'publish', '2025-06-25 12:01:18', '2025-06-25 12:01:18'),
(50, 'Lepa Rada', 1, 2, 'publish', '2025-06-25 12:01:19', '2025-06-25 12:01:19'),
(51, 'Shi Yomi', 1, 2, 'publish', '2025-06-25 12:01:20', '2025-06-25 12:01:20'),
(52, 'Itanagar Capital Complex', 1, 2, 'publish', '2025-06-25 12:01:21', '2025-06-25 12:01:21'),
(53, 'Baksa', 1, 3, 'publish', '2025-06-25 12:01:22', '2025-06-25 12:01:22'),
(54, 'Barpeta', 1, 3, 'publish', '2025-06-25 12:01:23', '2025-06-25 12:01:23'),
(55, 'Biswanath', 1, 3, 'publish', '2025-06-25 12:01:24', '2025-06-25 12:01:24'),
(56, 'Bongaigaon', 1, 3, 'publish', '2025-06-25 12:01:25', '2025-06-25 12:01:25'),
(57, 'Cachar', 1, 3, 'publish', '2025-06-25 12:01:26', '2025-06-25 12:01:26'),
(58, 'Charaideo', 1, 3, 'publish', '2025-06-25 12:01:27', '2025-06-25 12:01:27'),
(59, 'Chirang', 1, 3, 'publish', '2025-06-25 12:01:28', '2025-06-25 12:01:28'),
(60, 'Darrang', 1, 3, 'publish', '2025-06-25 12:01:29', '2025-06-25 12:01:29'),
(61, 'Dhemaji', 1, 3, 'publish', '2025-06-25 12:01:30', '2025-06-25 12:01:30'),
(62, 'Dhubri', 1, 3, 'publish', '2025-06-25 12:01:31', '2025-06-25 12:01:31'),
(63, 'Dibrugarh', 1, 3, 'publish', '2025-06-25 12:01:32', '2025-06-25 12:01:32'),
(64, 'Dima Hasao (North Cachar Hills)', 1, 3, 'publish', '2025-06-25 12:01:33', '2025-06-25 12:01:33'),
(65, 'Goalpara', 1, 3, 'publish', '2025-06-25 12:01:34', '2025-06-25 12:01:34'),
(66, 'Golaghat', 1, 3, 'publish', '2025-06-25 12:01:35', '2025-06-25 12:01:35'),
(67, 'Hailakandi', 1, 3, 'publish', '2025-06-25 12:01:36', '2025-06-25 12:01:36'),
(68, 'Hojai', 1, 3, 'publish', '2025-06-25 12:01:37', '2025-06-25 12:01:37'),
(69, 'Jorhat', 1, 3, 'publish', '2025-06-25 12:01:38', '2025-06-25 12:01:38'),
(70, 'Kamrup', 1, 3, 'publish', '2025-06-25 12:01:39', '2025-06-25 12:01:39'),
(71, 'Kamrup Metropolitan', 1, 3, 'publish', '2025-06-25 12:01:40', '2025-06-25 12:01:40'),
(72, 'Karbi Anglong', 1, 3, 'publish', '2025-06-25 12:01:41', '2025-06-25 12:01:41'),
(73, 'Karimganj', 1, 3, 'publish', '2025-06-25 12:01:42', '2025-06-25 12:01:42'),
(74, 'Kokrajhar', 1, 3, 'publish', '2025-06-25 12:01:43', '2025-06-25 12:01:43'),
(75, 'Lakhimpur', 1, 3, 'publish', '2025-06-25 12:01:44', '2025-06-25 12:01:44'),
(76, 'Majuli', 1, 3, 'publish', '2025-06-25 12:01:45', '2025-06-25 12:01:45'),
(77, 'Morigaon', 1, 3, 'publish', '2025-06-25 12:01:46', '2025-06-25 12:01:46'),
(78, 'Nagaon', 1, 3, 'publish', '2025-06-25 12:01:47', '2025-06-25 12:01:47'),
(79, 'Nalbari', 1, 3, 'publish', '2025-06-25 12:01:48', '2025-06-25 12:01:48'),
(80, 'Sivasagar', 1, 3, 'publish', '2025-06-25 12:01:49', '2025-06-25 12:01:49'),
(81, 'Sonitpur', 1, 3, 'publish', '2025-06-25 12:01:50', '2025-06-25 12:01:50'),
(82, 'South Salmara-Mankachar', 1, 3, 'publish', '2025-06-25 12:01:51', '2025-06-25 12:01:51'),
(83, 'Tinsukia', 1, 3, 'publish', '2025-06-25 12:01:52', '2025-06-25 12:01:52'),
(84, 'Udalguri', 1, 3, 'publish', '2025-06-25 12:01:53', '2025-06-25 12:01:53'),
(85, 'West Karbi Anglong', 1, 3, 'publish', '2025-06-25 12:01:54', '2025-06-25 12:01:54'),
(86, 'Tamulpur', 1, 3, 'publish', '2025-06-25 12:01:55', '2025-06-25 12:01:55'),
(87, 'Bajali', 1, 3, 'publish', '2025-06-25 12:01:56', '2025-06-25 12:01:56'),
(88, 'Araria', 1, 4, 'publish', '2025-06-25 12:01:57', '2025-06-25 12:01:57'),
(89, 'Arwal', 1, 4, 'publish', '2025-06-25 12:01:58', '2025-06-25 12:01:58'),
(90, 'Aurangabad', 1, 4, 'publish', '2025-06-25 12:01:59', '2025-06-25 12:01:59'),
(91, 'Banka', 1, 4, 'publish', '2025-06-25 12:02:00', '2025-06-25 12:02:00'),
(92, 'Begusarai', 1, 4, 'publish', '2025-06-25 12:02:01', '2025-06-25 12:02:01'),
(93, 'Bhagalpur', 1, 4, 'publish', '2025-06-25 12:02:02', '2025-06-25 12:02:02'),
(94, 'Bhojpur', 1, 4, 'publish', '2025-06-25 12:02:03', '2025-06-25 12:02:03'),
(95, 'Buxar', 1, 4, 'publish', '2025-06-25 12:02:04', '2025-06-25 12:02:04'),
(96, 'Darbhanga', 1, 4, 'publish', '2025-06-25 12:02:05', '2025-06-25 12:02:05'),
(97, 'East Champaran', 1, 4, 'publish', '2025-06-25 12:02:06', '2025-06-25 12:02:06'),
(98, 'Gaya', 1, 4, 'publish', '2025-06-25 12:02:07', '2025-06-25 12:02:07'),
(99, 'Gopalganj', 1, 4, 'publish', '2025-06-25 12:02:08', '2025-06-25 12:02:08'),
(100, 'Jamui', 1, 4, 'publish', '2025-06-25 12:02:09', '2025-06-25 12:02:09'),
(101, 'Jehanabad', 1, 4, 'publish', '2025-06-25 12:02:10', '2025-06-25 12:02:10'),
(102, 'Kaimur', 1, 4, 'publish', '2025-06-25 12:02:11', '2025-06-25 12:02:11'),
(103, 'Katihar', 1, 4, 'publish', '2025-06-25 12:02:12', '2025-06-25 12:02:12'),
(104, 'Khagaria', 1, 4, 'publish', '2025-06-25 12:02:13', '2025-06-25 12:02:13'),
(105, 'Kishanganj', 1, 4, 'publish', '2025-06-25 12:02:14', '2025-06-25 12:02:14'),
(106, 'Lakhisarai', 1, 4, 'publish', '2025-06-25 12:02:15', '2025-06-25 12:02:15'),
(107, 'Madhepura', 1, 4, 'publish', '2025-06-25 12:02:16', '2025-06-25 12:02:16'),
(108, 'Madhubani', 1, 4, 'publish', '2025-06-25 12:02:17', '2025-06-25 12:02:17'),
(109, 'Munger', 1, 4, 'publish', '2025-06-25 12:02:18', '2025-06-25 12:02:18'),
(110, 'Muzaffarpur', 1, 4, 'publish', '2025-06-25 12:02:19', '2025-06-25 12:02:19'),
(111, 'Nalanda', 1, 4, 'publish', '2025-06-25 12:02:20', '2025-06-25 12:02:20'),
(112, 'Nawada', 1, 4, 'publish', '2025-06-25 12:02:21', '2025-06-25 12:02:21'),
(113, 'Patna', 1, 4, 'publish', '2025-06-25 12:02:22', '2025-06-25 12:02:22'),
(114, 'Purnia', 1, 4, 'publish', '2025-06-25 12:02:23', '2025-06-25 12:02:23'),
(115, 'Rohtas', 1, 4, 'publish', '2025-06-25 12:02:24', '2025-06-25 12:02:24'),
(116, 'Saharsa', 1, 4, 'publish', '2025-06-25 12:02:25', '2025-06-25 12:02:25'),
(117, 'Samastipur', 1, 4, 'publish', '2025-06-25 12:02:26', '2025-06-25 12:02:26'),
(118, 'Saran', 1, 4, 'publish', '2025-06-25 12:02:27', '2025-06-25 12:02:27'),
(119, 'Sheikhpura', 1, 4, 'publish', '2025-06-25 12:02:28', '2025-06-25 12:02:28'),
(120, 'Sheohar', 1, 4, 'publish', '2025-06-25 12:02:29', '2025-06-25 12:02:29'),
(121, 'Sitamarhi', 1, 4, 'publish', '2025-06-25 12:02:30', '2025-06-25 12:02:30'),
(122, 'Siwan', 1, 4, 'publish', '2025-06-25 12:02:31', '2025-06-25 12:02:31'),
(123, 'Supaul', 1, 4, 'publish', '2025-06-25 12:02:32', '2025-06-25 12:02:32'),
(124, 'Vaishali', 1, 4, 'publish', '2025-06-25 12:02:33', '2025-06-25 12:02:33'),
(125, 'West Champaran', 1, 4, 'publish', '2025-06-25 12:02:34', '2025-06-25 12:02:34'),
(126, 'Balod', 1, 5, 'publish', '2025-06-25 12:02:35', '2025-06-25 12:02:35'),
(127, 'Baloda Bazar', 1, 5, 'publish', '2025-06-25 12:02:36', '2025-06-25 12:02:36'),
(128, 'Balrampur', 1, 5, 'publish', '2025-06-25 12:02:37', '2025-06-25 12:02:37'),
(129, 'Bastar', 1, 5, 'publish', '2025-06-25 12:02:38', '2025-06-25 12:02:38'),
(130, 'Bemetara', 1, 5, 'publish', '2025-06-25 12:02:39', '2025-06-25 12:02:39'),
(131, 'Bijapur', 1, 5, 'publish', '2025-06-25 12:02:40', '2025-06-25 12:02:40'),
(132, 'Bilaspur', 1, 5, 'publish', '2025-06-25 12:02:41', '2025-06-25 12:02:41'),
(133, 'Dantewada (South Bastar)', 1, 5, 'publish', '2025-06-25 12:02:42', '2025-06-25 12:02:42'),
(134, 'Dhamtari', 1, 5, 'publish', '2025-06-25 12:02:43', '2025-06-25 12:02:43'),
(135, 'Durg', 1, 5, 'publish', '2025-06-25 12:02:44', '2025-06-25 12:02:44'),
(136, 'Gariaband', 1, 5, 'publish', '2025-06-25 12:02:45', '2025-06-25 12:02:45'),
(137, 'Gaurela-Pendra-Marwahi', 1, 5, 'publish', '2025-06-25 12:02:46', '2025-06-25 12:02:46'),
(138, 'Janjgir-Champa', 1, 5, 'publish', '2025-06-25 12:02:47', '2025-06-25 12:02:47'),
(139, 'Jashpur', 1, 5, 'publish', '2025-06-25 12:02:48', '2025-06-25 12:02:48'),
(140, 'Kabirdham (Kawardha)', 1, 5, 'publish', '2025-06-25 12:02:49', '2025-06-25 12:02:49'),
(141, 'Kanker (North Bastar)', 1, 5, 'publish', '2025-06-25 12:02:50', '2025-06-25 12:02:50'),
(142, 'Kondagaon', 1, 5, 'publish', '2025-06-25 12:02:51', '2025-06-25 12:02:51'),
(143, 'Korba', 1, 5, 'publish', '2025-06-25 12:02:52', '2025-06-25 12:02:52'),
(144, 'Koriya (Korea)', 1, 5, 'publish', '2025-06-25 12:02:53', '2025-06-25 12:02:53'),
(145, 'Mahasamund', 1, 5, 'publish', '2025-06-25 12:02:54', '2025-06-25 12:02:54'),
(146, 'Manendragarh-Chirmiri-Bharatpur', 1, 5, 'publish', '2025-06-25 12:02:55', '2025-06-25 12:02:55'),
(147, 'Mohla-Manpur-Ambagarh Chowki', 1, 5, 'publish', '2025-06-25 12:02:56', '2025-06-25 12:02:56'),
(148, 'Mungeli', 1, 5, 'publish', '2025-06-25 12:02:57', '2025-06-25 12:02:57'),
(149, 'Narayanpur', 1, 5, 'publish', '2025-06-25 12:02:58', '2025-06-25 12:02:58'),
(150, 'Raigarh', 1, 5, 'publish', '2025-06-25 12:02:59', '2025-06-25 12:02:59'),
(151, 'Raipur', 1, 5, 'publish', '2025-06-25 12:03:00', '2025-06-25 12:03:00'),
(152, 'Rajnandgaon', 1, 5, 'publish', '2025-06-25 12:03:01', '2025-06-25 12:03:01'),
(153, 'Sarangarh-Bilaigarh', 1, 5, 'publish', '2025-06-25 12:03:02', '2025-06-25 12:03:02'),
(154, 'Sakti', 1, 5, 'publish', '2025-06-25 12:03:03', '2025-06-25 12:03:03'),
(155, 'Surajpur', 1, 5, 'publish', '2025-06-25 12:03:04', '2025-06-25 12:03:04'),
(156, 'Surguja', 1, 5, 'publish', '2025-06-25 12:03:05', '2025-06-25 12:03:05'),
(157, 'Khairagarh-Chhuikhadan-Gandai', 1, 5, 'publish', '2025-06-25 12:03:06', '2025-06-25 12:03:06'),
(158, 'Bilaigarh', 1, 5, 'publish', '2025-06-25 12:03:07', '2025-06-25 12:03:07'),
(159, 'Central Delhi', 1, 6, 'publish', '2025-06-25 12:03:08', '2025-06-25 12:03:08'),
(160, 'East Delhi', 1, 6, 'publish', '2025-06-25 12:03:09', '2025-06-25 12:03:09'),
(161, 'New Delhi', 1, 6, 'publish', '2025-06-25 12:03:10', '2025-06-25 12:03:10'),
(162, 'North Delhi', 1, 6, 'publish', '2025-06-25 12:03:11', '2025-06-25 12:03:11'),
(163, 'North East Delhi', 1, 6, 'publish', '2025-06-25 12:03:12', '2025-06-25 12:03:12'),
(164, 'North West Delhi', 1, 6, 'publish', '2025-06-25 12:03:13', '2025-06-25 12:03:13'),
(165, 'South Delhi', 1, 6, 'publish', '2025-06-25 12:03:14', '2025-06-25 12:03:14'),
(166, 'South West Delhi', 1, 6, 'publish', '2025-06-25 12:03:15', '2025-06-25 12:03:15'),
(167, 'West Delhi', 1, 6, 'publish', '2025-06-25 12:03:16', '2025-06-25 12:03:16'),
(168, 'North West Delhi', 1, 6, 'publish', '2025-06-25 12:03:17', '2025-06-25 12:03:17'),
(169, 'Shahdara', 1, 6, 'publish', '2025-06-25 12:03:18', '2025-06-25 12:03:18'),
(170, 'North Goa', 1, 7, 'publish', '2025-06-25 12:03:19', '2025-06-25 12:03:19'),
(171, 'South Goa', 1, 7, 'publish', '2025-06-25 12:03:20', '2025-06-25 12:03:20'),
(172, 'Ahmedabad', 1, 8, 'publish', '2025-06-25 12:03:21', '2025-06-25 12:03:21'),
(173, 'Amreli', 1, 8, 'publish', '2025-06-25 12:03:22', '2025-06-25 12:03:22'),
(174, 'Anand', 1, 8, 'publish', '2025-06-25 12:03:23', '2025-06-25 12:03:23'),
(175, 'Aravalli', 1, 8, 'publish', '2025-06-25 12:03:24', '2025-06-25 12:03:24'),
(176, 'Banaskantha', 1, 8, 'publish', '2025-06-25 12:03:25', '2025-06-25 12:03:25'),
(177, 'Bharuch', 1, 8, 'publish', '2025-06-25 12:03:26', '2025-06-25 12:03:26'),
(178, 'Bhavnagar', 1, 8, 'publish', '2025-06-25 12:03:27', '2025-06-25 12:03:27'),
(179, 'Botad', 1, 8, 'publish', '2025-06-25 12:03:28', '2025-06-25 12:03:28'),
(180, 'Chhota Udaipur', 1, 8, 'publish', '2025-06-25 12:03:29', '2025-06-25 12:03:29'),
(181, 'Dahod', 1, 8, 'publish', '2025-06-25 12:03:30', '2025-06-25 12:03:30'),
(182, 'Dang', 1, 8, 'publish', '2025-06-25 12:03:31', '2025-06-25 12:03:31'),
(183, 'Devbhoomi Dwarka', 1, 8, 'publish', '2025-06-25 12:03:32', '2025-06-25 12:03:32'),
(184, 'Gandhinagar', 1, 8, 'publish', '2025-06-25 12:03:33', '2025-06-25 12:03:33'),
(185, 'Gir Somnath', 1, 8, 'publish', '2025-06-25 12:03:34', '2025-06-25 12:03:34'),
(186, 'Jamnagar', 1, 8, 'publish', '2025-06-25 12:03:35', '2025-06-25 12:03:35'),
(187, 'Junagadh', 1, 8, 'publish', '2025-06-25 12:03:36', '2025-06-25 12:03:36'),
(188, 'Kheda', 1, 8, 'publish', '2025-06-25 12:03:37', '2025-06-25 12:03:37'),
(189, 'Kutch', 1, 8, 'publish', '2025-06-25 12:03:38', '2025-06-25 12:03:38'),
(190, 'Mahisagar', 1, 8, 'publish', '2025-06-25 12:03:39', '2025-06-25 12:03:39'),
(191, 'Mehsana', 1, 8, 'publish', '2025-06-25 12:03:40', '2025-06-25 12:03:40'),
(192, 'Morbi', 1, 8, 'publish', '2025-06-25 12:03:41', '2025-06-25 12:03:41'),
(193, 'Narmada', 1, 8, 'publish', '2025-06-25 12:03:42', '2025-06-25 12:03:42'),
(194, 'Navsari', 1, 8, 'publish', '2025-06-25 12:03:43', '2025-06-25 12:03:43'),
(195, 'Panchmahal', 1, 8, 'publish', '2025-06-25 12:03:44', '2025-06-25 12:03:44'),
(196, 'Patan', 1, 8, 'publish', '2025-06-25 12:03:45', '2025-06-25 12:03:45'),
(197, 'Porbandar', 1, 8, 'publish', '2025-06-25 12:03:46', '2025-06-25 12:03:46'),
(198, 'Rajkot', 1, 8, 'publish', '2025-06-25 12:03:47', '2025-06-25 12:03:47'),
(199, 'Sabarkantha', 1, 8, 'publish', '2025-06-25 12:03:48', '2025-06-25 12:03:48'),
(200, 'Surat', 1, 8, 'publish', '2025-06-25 12:03:49', '2025-06-25 12:03:49'),
(201, 'Surendranagar', 1, 8, 'publish', '2025-06-25 12:03:50', '2025-06-25 12:03:50'),
(202, 'Tapi', 1, 8, 'publish', '2025-06-25 12:03:51', '2025-06-25 12:03:51'),
(203, 'Vadodara', 1, 8, 'publish', '2025-06-25 12:03:52', '2025-06-25 12:03:52'),
(204, 'Valsad', 1, 8, 'publish', '2025-06-25 12:03:53', '2025-06-25 12:03:53'),
(205, 'Ambala', 1, 9, 'publish', '2025-06-25 12:03:54', '2025-06-25 12:03:54'),
(206, 'Bhiwani', 1, 9, 'publish', '2025-06-25 12:03:55', '2025-06-25 12:03:55'),
(207, 'Charkhi Dadri', 1, 9, 'publish', '2025-06-25 12:03:56', '2025-06-25 12:03:56'),
(208, 'Faridabad', 1, 9, 'publish', '2025-06-25 12:03:57', '2025-06-25 12:03:57'),
(209, 'Fatehabad', 1, 9, 'publish', '2025-06-25 12:03:58', '2025-06-25 12:03:58'),
(210, 'Gurugram', 1, 9, 'publish', '2025-06-25 12:03:59', '2025-06-25 12:03:59'),
(211, 'Hisar', 1, 9, 'publish', '2025-06-25 12:04:00', '2025-06-25 12:04:00'),
(212, 'Jhajjar', 1, 9, 'publish', '2025-06-25 12:04:01', '2025-06-25 12:04:01'),
(213, 'Jind', 1, 9, 'publish', '2025-06-25 12:04:02', '2025-06-25 12:04:02'),
(214, 'Kaithal', 1, 9, 'publish', '2025-06-25 12:04:03', '2025-06-25 12:04:03'),
(215, 'Karnal', 1, 9, 'publish', '2025-06-25 12:04:04', '2025-06-25 12:04:04'),
(216, 'Kurukshetra', 1, 9, 'publish', '2025-06-25 12:04:05', '2025-06-25 12:04:05'),
(217, 'Mahendragarh', 1, 9, 'publish', '2025-06-25 12:04:06', '2025-06-25 12:04:06'),
(218, 'Nuh', 1, 9, 'publish', '2025-06-25 12:04:07', '2025-06-25 12:04:07'),
(219, 'Palwal', 1, 9, 'publish', '2025-06-25 12:04:08', '2025-06-25 12:04:08'),
(220, 'Panchkula', 1, 9, 'publish', '2025-06-25 12:04:09', '2025-06-25 12:04:09'),
(221, 'Panipat', 1, 9, 'publish', '2025-06-25 12:04:10', '2025-06-25 12:04:10'),
(222, 'Rewari', 1, 9, 'publish', '2025-06-25 12:04:11', '2025-06-25 12:04:11'),
(223, 'Rohtak', 1, 9, 'publish', '2025-06-25 12:04:12', '2025-06-25 12:04:12'),
(224, 'Sirsa', 1, 9, 'publish', '2025-06-25 12:04:13', '2025-06-25 12:04:13'),
(225, 'Sonipat', 1, 9, 'publish', '2025-06-25 12:04:14', '2025-06-25 12:04:14'),
(226, 'Yamunanagar', 1, 9, 'publish', '2025-06-25 12:04:15', '2025-06-25 12:04:15'),
(227, 'Bilaspur', 1, 10, 'publish', '2025-06-25 12:04:16', '2025-06-25 12:04:16'),
(228, 'Chamba', 1, 10, 'publish', '2025-06-25 12:04:17', '2025-06-25 12:04:17'),
(229, 'Hamirpur', 1, 10, 'publish', '2025-06-25 12:04:18', '2025-06-25 12:04:18'),
(230, 'Kangra', 1, 10, 'publish', '2025-06-25 12:04:19', '2025-06-25 12:04:19'),
(231, 'Kinnaur', 1, 10, 'publish', '2025-06-25 12:04:20', '2025-06-25 12:04:20'),
(232, 'Kullu', 1, 10, 'publish', '2025-06-25 12:04:21', '2025-06-25 12:04:21'),
(233, 'Lahaul and Spiti', 1, 10, 'publish', '2025-06-25 12:04:22', '2025-06-25 12:04:22'),
(234, 'Mandi', 1, 10, 'publish', '2025-06-25 12:04:23', '2025-06-25 12:04:23'),
(235, 'Shimla', 1, 10, 'publish', '2025-06-25 12:04:24', '2025-06-25 12:04:24'),
(236, 'Sirmaur', 1, 10, 'publish', '2025-06-25 12:04:25', '2025-06-25 12:04:25'),
(237, 'Solan', 1, 10, 'publish', '2025-06-25 12:04:26', '2025-06-25 12:04:26'),
(238, 'Una', 1, 10, 'publish', '2025-06-25 12:04:27', '2025-06-25 12:04:27'),
(239, 'Bokaro', 1, 11, 'publish', '2025-06-25 12:04:28', '2025-06-25 12:04:28'),
(240, 'Chatra', 1, 11, 'publish', '2025-06-25 12:04:29', '2025-06-25 12:04:29'),
(241, 'Deoghar', 1, 11, 'publish', '2025-06-25 12:04:30', '2025-06-25 12:04:30'),
(242, 'Dhanbad', 1, 11, 'publish', '2025-06-25 12:04:31', '2025-06-25 12:04:31'),
(243, 'Dumka', 1, 11, 'publish', '2025-06-25 12:04:32', '2025-06-25 12:04:32'),
(244, 'East Singhbhum', 1, 11, 'publish', '2025-06-25 12:04:33', '2025-06-25 12:04:33'),
(245, 'Garhwa', 1, 11, 'publish', '2025-06-25 12:04:34', '2025-06-25 12:04:34'),
(246, 'Giridih', 1, 11, 'publish', '2025-06-25 12:04:35', '2025-06-25 12:04:35'),
(247, 'Godda', 1, 11, 'publish', '2025-06-25 12:04:36', '2025-06-25 12:04:36'),
(248, 'Gumla', 1, 11, 'publish', '2025-06-25 12:04:37', '2025-06-25 12:04:37'),
(249, 'Hazaribagh', 1, 11, 'publish', '2025-06-25 12:04:38', '2025-06-25 12:04:38'),
(250, 'Jamtara', 1, 11, 'publish', '2025-06-25 12:04:39', '2025-06-25 12:04:39'),
(251, 'Khunti', 1, 11, 'publish', '2025-06-25 12:04:40', '2025-06-25 12:04:40'),
(252, 'Koderma', 1, 11, 'publish', '2025-06-25 12:04:41', '2025-06-25 12:04:41'),
(253, 'Latehar', 1, 11, 'publish', '2025-06-25 12:04:42', '2025-06-25 12:04:42'),
(254, 'Lohardaga', 1, 11, 'publish', '2025-06-25 12:04:43', '2025-06-25 12:04:43'),
(255, 'Pakur', 1, 11, 'publish', '2025-06-25 12:04:44', '2025-06-25 12:04:44'),
(256, 'Palamu', 1, 11, 'publish', '2025-06-25 12:04:45', '2025-06-25 12:04:45'),
(257, 'Ramgarh', 1, 11, 'publish', '2025-06-25 12:04:46', '2025-06-25 12:04:46'),
(258, 'Ranchi', 1, 11, 'publish', '2025-06-25 12:04:47', '2025-06-25 12:04:47'),
(259, 'Sahebganj', 1, 11, 'publish', '2025-06-25 12:04:48', '2025-06-25 12:04:48'),
(260, 'Seraikela Kharsawan', 1, 11, 'publish', '2025-06-25 12:04:49', '2025-06-25 12:04:49'),
(261, 'Simdega', 1, 11, 'publish', '2025-06-25 12:04:50', '2025-06-25 12:04:50'),
(262, 'West Singhbhum', 1, 11, 'publish', '2025-06-25 12:04:51', '2025-06-25 12:04:51'),
(263, 'Bagalkote', 1, 12, 'publish', '2025-06-25 12:04:52', '2025-06-25 12:04:52'),
(264, 'Ballari', 1, 12, 'publish', '2025-06-25 12:04:53', '2025-06-25 12:04:53'),
(265, 'Belagavi', 1, 12, 'publish', '2025-06-25 12:04:54', '2025-06-25 12:04:54'),
(266, 'Bengaluru Rural', 1, 12, 'publish', '2025-06-25 12:04:55', '2025-06-25 12:04:55'),
(267, 'Bengaluru Urban', 1, 12, 'publish', '2025-06-25 12:04:56', '2025-06-25 12:04:56'),
(268, 'Bidar', 1, 12, 'publish', '2025-06-25 12:04:57', '2025-06-25 12:04:57'),
(269, 'Chamarajanagar', 1, 12, 'publish', '2025-06-25 12:04:58', '2025-06-25 12:04:58'),
(270, 'Chikkaballapur', 1, 12, 'publish', '2025-06-25 12:04:59', '2025-06-25 12:04:59'),
(271, 'Chikkamagaluru', 1, 12, 'publish', '2025-06-25 12:05:00', '2025-06-25 12:05:00'),
(272, 'Chitradurga', 1, 12, 'publish', '2025-06-25 12:05:01', '2025-06-25 12:05:01'),
(273, 'Dakshina Kannada', 1, 12, 'publish', '2025-06-25 12:05:02', '2025-06-25 12:05:02'),
(274, 'Davanagere', 1, 12, 'publish', '2025-06-25 12:05:03', '2025-06-25 12:05:03'),
(275, 'Dharwad', 1, 12, 'publish', '2025-06-25 12:05:04', '2025-06-25 12:05:04'),
(276, 'Gadag', 1, 12, 'publish', '2025-06-25 12:05:05', '2025-06-25 12:05:05'),
(277, 'Hassan', 1, 12, 'publish', '2025-06-25 12:05:06', '2025-06-25 12:05:06'),
(278, 'Haveri', 1, 12, 'publish', '2025-06-25 12:05:07', '2025-06-25 12:05:07'),
(279, 'Kodagu', 1, 12, 'publish', '2025-06-25 12:05:08', '2025-06-25 12:05:08'),
(280, 'Kolar', 1, 12, 'publish', '2025-06-25 12:05:09', '2025-06-25 12:05:09'),
(281, 'Koppal', 1, 12, 'publish', '2025-06-25 12:05:10', '2025-06-25 12:05:10'),
(282, 'Mandya', 1, 12, 'publish', '2025-06-25 12:05:11', '2025-06-25 12:05:11'),
(283, 'Mysuru', 1, 12, 'publish', '2025-06-25 12:05:12', '2025-06-25 12:05:12'),
(284, 'Raichur', 1, 12, 'publish', '2025-06-25 12:05:13', '2025-06-25 12:05:13'),
(285, 'Ramanagara', 1, 12, 'publish', '2025-06-25 12:05:14', '2025-06-25 12:05:14'),
(286, 'Shivamogga', 1, 12, 'publish', '2025-06-25 12:05:15', '2025-06-25 12:05:15'),
(287, 'Tumakuru', 1, 12, 'publish', '2025-06-25 12:05:16', '2025-06-25 12:05:16'),
(288, 'Udupi', 1, 12, 'publish', '2025-06-25 12:05:17', '2025-06-25 12:05:17'),
(289, 'Uttara Kannada', 1, 12, 'publish', '2025-06-25 12:05:18', '2025-06-25 12:05:18'),
(290, 'Vijayapura', 1, 12, 'publish', '2025-06-25 12:05:19', '2025-06-25 12:05:19'),
(291, 'Yadgir', 1, 12, 'publish', '2025-06-25 12:05:20', '2025-06-25 12:05:20'),
(292, 'Bengaluru Urban', 1, 12, 'publish', '2025-06-25 12:05:21', '2025-06-25 12:05:21'),
(293, 'Bengaluru Rural', 1, 12, 'publish', '2025-06-25 12:05:22', '2025-06-25 12:05:22'),
(294, 'Alappuzha', 1, 13, 'publish', '2025-06-25 12:05:23', '2025-06-25 12:05:23'),
(295, 'Ernakulam', 1, 13, 'publish', '2025-06-25 12:05:24', '2025-06-25 12:05:24'),
(296, 'Idukki', 1, 13, 'publish', '2025-06-25 12:05:25', '2025-06-25 12:05:25'),
(297, 'Kannur', 1, 13, 'publish', '2025-06-25 12:05:26', '2025-06-25 12:05:26'),
(298, 'Kasaragod', 1, 13, 'publish', '2025-06-25 12:05:27', '2025-06-25 12:05:27'),
(299, 'Kollam', 1, 13, 'publish', '2025-06-25 12:05:28', '2025-06-25 12:05:28'),
(300, 'Kottayam', 1, 13, 'publish', '2025-06-25 12:05:29', '2025-06-25 12:05:29'),
(301, 'Kozhikode', 1, 13, 'publish', '2025-06-25 12:05:30', '2025-06-25 12:05:30'),
(302, 'Malappuram', 1, 13, 'publish', '2025-06-25 12:05:31', '2025-06-25 12:05:31'),
(303, 'Palakkad', 1, 13, 'publish', '2025-06-25 12:05:32', '2025-06-25 12:05:32'),
(304, 'Pathanamthitta', 1, 13, 'publish', '2025-06-25 12:05:33', '2025-06-25 12:05:33'),
(305, 'Thiruvananthapuram', 1, 13, 'publish', '2025-06-25 12:05:34', '2025-06-25 12:05:34'),
(306, 'Thrissur', 1, 13, 'publish', '2025-06-25 12:05:35', '2025-06-25 12:05:35'),
(307, 'Wayanad', 1, 13, 'publish', '2025-06-25 12:05:36', '2025-06-25 12:05:36'),
(308, 'Agar Malwa', 1, 14, 'publish', '2025-06-25 12:05:37', '2025-06-25 12:05:37'),
(309, 'Alirajpur', 1, 14, 'publish', '2025-06-25 12:05:38', '2025-06-25 12:05:38'),
(310, 'Anuppur', 1, 14, 'publish', '2025-06-25 12:05:39', '2025-06-25 12:05:39'),
(311, 'Ashoknagar', 1, 14, 'publish', '2025-06-25 12:05:40', '2025-06-25 12:05:40'),
(312, 'Balaghat', 1, 14, 'publish', '2025-06-25 12:05:41', '2025-06-25 12:05:41'),
(313, 'Barwani', 1, 14, 'publish', '2025-06-25 12:05:42', '2025-06-25 12:05:42'),
(314, 'Betul', 1, 14, 'publish', '2025-06-25 12:05:43', '2025-06-25 12:05:43'),
(315, 'Bhind', 1, 14, 'publish', '2025-06-25 12:05:44', '2025-06-25 12:05:44'),
(316, 'Bhopal', 1, 14, 'publish', '2025-06-25 12:05:45', '2025-06-25 12:05:45'),
(317, 'Burhanpur', 1, 14, 'publish', '2025-06-25 12:05:46', '2025-06-25 12:05:46'),
(318, 'Chhatarpur', 1, 14, 'publish', '2025-06-25 12:05:47', '2025-06-25 12:05:47'),
(319, 'Chhindwara', 1, 14, 'publish', '2025-06-25 12:05:48', '2025-06-25 12:05:48'),
(320, 'Damoh', 1, 14, 'publish', '2025-06-25 12:05:49', '2025-06-25 12:05:49'),
(321, 'Datia', 1, 14, 'publish', '2025-06-25 12:05:50', '2025-06-25 12:05:50'),
(322, 'Dewas', 1, 14, 'publish', '2025-06-25 12:05:51', '2025-06-25 12:05:51'),
(323, 'Dhar', 1, 14, 'publish', '2025-06-25 12:05:52', '2025-06-25 12:05:52'),
(324, 'Dindori', 1, 14, 'publish', '2025-06-25 12:05:53', '2025-06-25 12:05:53'),
(325, 'Guna', 1, 14, 'publish', '2025-06-25 12:05:54', '2025-06-25 12:05:54'),
(326, 'Gwalior', 1, 14, 'publish', '2025-06-25 12:05:55', '2025-06-25 12:05:55'),
(327, 'Harda', 1, 14, 'publish', '2025-06-25 12:05:56', '2025-06-25 12:05:56'),
(328, 'Hoshangabad', 1, 14, 'publish', '2025-06-25 12:05:57', '2025-06-25 12:05:57'),
(329, 'Indore', 1, 14, 'publish', '2025-06-25 12:05:58', '2025-06-25 12:05:58'),
(330, 'Jabalpur', 1, 14, 'publish', '2025-06-25 12:05:59', '2025-06-25 12:05:59'),
(331, 'Jhabua', 1, 14, 'publish', '2025-06-25 12:06:00', '2025-06-25 12:06:00'),
(332, 'Katni', 1, 14, 'publish', '2025-06-25 12:06:01', '2025-06-25 12:06:01'),
(333, 'Khandwa', 1, 14, 'publish', '2025-06-25 12:06:02', '2025-06-25 12:06:02'),
(334, 'Khargone', 1, 14, 'publish', '2025-06-25 12:06:03', '2025-06-25 12:06:03'),
(335, 'Mandla', 1, 14, 'publish', '2025-06-25 12:06:04', '2025-06-25 12:06:04'),
(336, 'Mandsaur', 1, 14, 'publish', '2025-06-25 12:06:05', '2025-06-25 12:06:05'),
(337, 'Morena', 1, 14, 'publish', '2025-06-25 12:06:06', '2025-06-25 12:06:06'),
(338, 'Narsinghpur', 1, 14, 'publish', '2025-06-25 12:06:07', '2025-06-25 12:06:07'),
(339, 'Neemuch', 1, 14, 'publish', '2025-06-25 12:06:08', '2025-06-25 12:06:08'),
(340, 'Niwari', 1, 14, 'publish', '2025-06-25 12:06:09', '2025-06-25 12:06:09'),
(341, 'Pandhurna', 1, 14, 'publish', '2025-06-25 12:06:10', '2025-06-25 12:06:10'),
(342, 'Panna', 1, 14, 'publish', '2025-06-25 12:06:11', '2025-06-25 12:06:11'),
(343, 'Raisen', 1, 14, 'publish', '2025-06-25 12:06:12', '2025-06-25 12:06:12'),
(344, 'Rajgarh', 1, 14, 'publish', '2025-06-25 12:06:13', '2025-06-25 12:06:13'),
(345, 'Ratlam', 1, 14, 'publish', '2025-06-25 12:06:14', '2025-06-25 12:06:14'),
(346, 'Rewa', 1, 14, 'publish', '2025-06-25 12:06:15', '2025-06-25 12:06:15'),
(347, 'Sagar', 1, 14, 'publish', '2025-06-25 12:06:16', '2025-06-25 12:06:16'),
(348, 'Satna', 1, 14, 'publish', '2025-06-25 12:06:17', '2025-06-25 12:06:17'),
(349, 'Sehore', 1, 14, 'publish', '2025-06-25 12:06:18', '2025-06-25 12:06:18'),
(350, 'Seoni', 1, 14, 'publish', '2025-06-25 12:06:19', '2025-06-25 12:06:19'),
(351, 'Shahdol', 1, 14, 'publish', '2025-06-25 12:06:20', '2025-06-25 12:06:20'),
(352, 'Shajapur', 1, 14, 'publish', '2025-06-25 12:06:21', '2025-06-25 12:06:21'),
(353, 'Sheopur', 1, 14, 'publish', '2025-06-25 12:06:22', '2025-06-25 12:06:22'),
(354, 'Shivpuri', 1, 14, 'publish', '2025-06-25 12:06:23', '2025-06-25 12:06:23'),
(355, 'Sidhi', 1, 14, 'publish', '2025-06-25 12:06:24', '2025-06-25 12:06:24'),
(356, 'Singrauli', 1, 14, 'publish', '2025-06-25 12:06:25', '2025-06-25 12:06:25'),
(357, 'Tikamgarh', 1, 14, 'publish', '2025-06-25 12:06:26', '2025-06-25 12:06:26'),
(358, 'Ujjain', 1, 14, 'publish', '2025-06-25 12:06:27', '2025-06-25 12:06:27'),
(359, 'Umaria', 1, 14, 'publish', '2025-06-25 12:06:28', '2025-06-25 12:06:28'),
(360, 'Vidisha', 1, 14, 'publish', '2025-06-25 12:06:29', '2025-06-25 12:06:29'),
(361, 'Mauganj', 1, 14, 'publish', '2025-06-25 12:06:30', '2025-06-25 12:06:30'),
(362, 'Maihar', 1, 14, 'publish', '2025-06-25 12:06:31', '2025-06-25 12:06:31'),
(363, 'Ahmednagar', 1, 15, 'publish', '2025-06-25 12:06:32', '2025-06-25 12:06:32'),
(364, 'Akola', 1, 15, 'publish', '2025-06-25 12:06:33', '2025-06-25 12:06:33'),
(365, 'Amravati', 1, 15, 'publish', '2025-06-25 12:06:34', '2025-06-25 12:06:34'),
(366, 'Aurangabad', 1, 15, 'publish', '2025-06-25 12:06:35', '2025-06-25 12:06:35'),
(367, 'Beed', 1, 15, 'publish', '2025-06-25 12:06:36', '2025-06-25 12:06:36'),
(368, 'Bhandara', 1, 15, 'publish', '2025-06-25 12:06:37', '2025-06-25 12:06:37'),
(369, 'Buldhana', 1, 15, 'publish', '2025-06-25 12:06:38', '2025-06-25 12:06:38'),
(370, 'Chandrapur', 1, 15, 'publish', '2025-06-25 12:06:39', '2025-06-25 12:06:39'),
(371, 'Dhule', 1, 15, 'publish', '2025-06-25 12:06:40', '2025-06-25 12:06:40'),
(372, 'Gadchiroli', 1, 15, 'publish', '2025-06-25 12:06:41', '2025-06-25 12:06:41'),
(373, 'Gondia', 1, 15, 'publish', '2025-06-25 12:06:42', '2025-06-25 12:06:42'),
(374, 'Hingoli', 1, 15, 'publish', '2025-06-25 12:06:43', '2025-06-25 12:06:43'),
(375, 'Jalgaon', 1, 15, 'publish', '2025-06-25 12:06:44', '2025-06-25 12:06:44'),
(376, 'Jalna', 1, 15, 'publish', '2025-06-25 12:06:45', '2025-06-25 12:06:45'),
(377, 'Kolhapur', 1, 15, 'publish', '2025-06-25 12:06:46', '2025-06-25 12:06:46'),
(378, 'Latur', 1, 15, 'publish', '2025-06-25 12:06:47', '2025-06-25 12:06:47'),
(379, 'Mumbai City', 1, 15, 'publish', '2025-06-25 12:06:48', '2025-06-25 12:06:48'),
(380, 'Mumbai Suburban', 1, 15, 'publish', '2025-06-25 12:06:49', '2025-06-25 12:06:49'),
(381, 'Nagpur', 1, 15, 'publish', '2025-06-25 12:06:50', '2025-06-25 12:06:50'),
(382, 'Nanded', 1, 15, 'publish', '2025-06-25 12:06:51', '2025-06-25 12:06:51'),
(383, 'Nandurbar', 1, 15, 'publish', '2025-06-25 12:06:52', '2025-06-25 12:06:52'),
(384, 'Nashik', 1, 15, 'publish', '2025-06-25 12:06:53', '2025-06-25 12:06:53'),
(385, 'Osmanabad', 1, 15, 'publish', '2025-06-25 12:06:54', '2025-06-25 12:06:54'),
(386, 'Palghar', 1, 15, 'publish', '2025-06-25 12:06:55', '2025-06-25 12:06:55'),
(387, 'Parbhani', 1, 15, 'publish', '2025-06-25 12:06:56', '2025-06-25 12:06:56'),
(388, 'Pune', 1, 15, 'publish', '2025-06-25 12:06:57', '2025-06-25 12:06:57'),
(389, 'Raigad', 1, 15, 'publish', '2025-06-25 12:06:58', '2025-06-25 12:06:58'),
(390, 'Ratnagiri', 1, 15, 'publish', '2025-06-25 12:06:59', '2025-06-25 12:06:59'),
(391, 'Sangli', 1, 15, 'publish', '2025-06-25 12:07:00', '2025-06-25 12:07:00'),
(392, 'Satara', 1, 15, 'publish', '2025-06-25 12:07:01', '2025-06-25 12:07:01'),
(393, 'Sindhudurg', 1, 15, 'publish', '2025-06-25 12:07:02', '2025-06-25 12:07:02'),
(394, 'Solapur', 1, 15, 'publish', '2025-06-25 12:07:03', '2025-06-25 12:07:03'),
(395, 'Thane', 1, 15, 'publish', '2025-06-25 12:07:04', '2025-06-25 12:07:04'),
(396, 'Wardha', 1, 15, 'publish', '2025-06-25 12:07:05', '2025-06-25 12:07:05'),
(397, 'Washim', 1, 15, 'publish', '2025-06-25 12:07:06', '2025-06-25 12:07:06'),
(398, 'Yavatmal', 1, 15, 'publish', '2025-06-25 12:07:07', '2025-06-25 12:07:07'),
(399, 'Bishnupur', 1, 16, 'publish', '2025-06-25 12:07:08', '2025-06-25 12:07:08'),
(400, 'Chandel', 1, 16, 'publish', '2025-06-25 12:07:09', '2025-06-25 12:07:09'),
(401, 'Churachandpur', 1, 16, 'publish', '2025-06-25 12:07:10', '2025-06-25 12:07:10'),
(402, 'Imphal East', 1, 16, 'publish', '2025-06-25 12:07:11', '2025-06-25 12:07:11'),
(403, 'Imphal West', 1, 16, 'publish', '2025-06-25 12:07:12', '2025-06-25 12:07:12'),
(404, 'Senapati', 1, 16, 'publish', '2025-06-25 12:07:13', '2025-06-25 12:07:13'),
(405, 'Tamenglong', 1, 16, 'publish', '2025-06-25 12:07:14', '2025-06-25 12:07:14'),
(406, 'Thoubal', 1, 16, 'publish', '2025-06-25 12:07:15', '2025-06-25 12:07:15'),
(407, 'Ukhrul', 1, 16, 'publish', '2025-06-25 12:07:16', '2025-06-25 12:07:16'),
(408, 'Jiribam', 1, 16, 'publish', '2025-06-25 12:07:17', '2025-06-25 12:07:17'),
(409, 'Kangpokpi', 1, 16, 'publish', '2025-06-25 12:07:18', '2025-06-25 12:07:18'),
(410, 'Kakching', 1, 16, 'publish', '2025-06-25 12:07:19', '2025-06-25 12:07:19'),
(411, 'Tengnoupal', 1, 16, 'publish', '2025-06-25 12:07:20', '2025-06-25 12:07:20'),
(412, 'Kamjong', 1, 16, 'publish', '2025-06-25 12:07:21', '2025-06-25 12:07:21'),
(413, 'Noney', 1, 16, 'publish', '2025-06-25 12:07:22', '2025-06-25 12:07:22'),
(414, 'Pherzawl', 1, 16, 'publish', '2025-06-25 12:07:23', '2025-06-25 12:07:23'),
(415, 'East Khasi Hills', 1, 17, 'publish', '2025-06-25 12:07:24', '2025-06-25 12:07:24'),
(416, 'West Khasi Hills', 1, 17, 'publish', '2025-06-25 12:07:25', '2025-06-25 12:07:25'),
(417, 'South West Khasi Hills', 1, 17, 'publish', '2025-06-25 12:07:26', '2025-06-25 12:07:26'),
(418, 'Eastern West Khasi Hills', 1, 17, 'publish', '2025-06-25 12:07:27', '2025-06-25 12:07:27'),
(419, 'Ri-Bhoi', 1, 17, 'publish', '2025-06-25 12:07:28', '2025-06-25 12:07:28'),
(420, 'West Jaintia Hills', 1, 17, 'publish', '2025-06-25 12:07:29', '2025-06-25 12:07:29'),
(421, 'East Jaintia Hills', 1, 17, 'publish', '2025-06-25 12:07:30', '2025-06-25 12:07:30'),
(422, 'North Garo Hills', 1, 17, 'publish', '2025-06-25 12:07:31', '2025-06-25 12:07:31'),
(423, 'East Garo Hills', 1, 17, 'publish', '2025-06-25 12:07:32', '2025-06-25 12:07:32'),
(424, 'South Garo Hills', 1, 17, 'publish', '2025-06-25 12:07:33', '2025-06-25 12:07:33'),
(425, 'West Garo Hills', 1, 17, 'publish', '2025-06-25 12:07:34', '2025-06-25 12:07:34'),
(426, 'South West Garo Hills', 1, 17, 'publish', '2025-06-25 12:07:35', '2025-06-25 12:07:35'),
(427, 'Aizawl', 1, 18, 'publish', '2025-06-25 12:07:36', '2025-06-25 12:07:36'),
(428, 'Champhai', 1, 18, 'publish', '2025-06-25 12:07:37', '2025-06-25 12:07:37'),
(429, 'Hnahthial', 1, 18, 'publish', '2025-06-25 12:07:38', '2025-06-25 12:07:38'),
(430, 'Khawzawl', 1, 18, 'publish', '2025-06-25 12:07:39', '2025-06-25 12:07:39'),
(431, 'Kolasib', 1, 18, 'publish', '2025-06-25 12:07:40', '2025-06-25 12:07:40'),
(432, 'Lawngtlai', 1, 18, 'publish', '2025-06-25 12:07:41', '2025-06-25 12:07:41'),
(433, 'Lunglei', 1, 18, 'publish', '2025-06-25 12:07:42', '2025-06-25 12:07:42'),
(434, 'Mamit', 1, 18, 'publish', '2025-06-25 12:07:43', '2025-06-25 12:07:43'),
(435, 'Saiha', 1, 18, 'publish', '2025-06-25 12:07:44', '2025-06-25 12:07:44'),
(436, 'Saitual', 1, 18, 'publish', '2025-06-25 12:07:45', '2025-06-25 12:07:45'),
(437, 'Serchhip', 1, 18, 'publish', '2025-06-25 12:07:46', '2025-06-25 12:07:46'),
(438, 'Chumoukedima', 1, 19, 'publish', '2025-06-25 12:07:47', '2025-06-25 12:07:47'),
(439, 'Dimapur', 1, 19, 'publish', '2025-06-25 12:07:48', '2025-06-25 12:07:48'),
(440, 'Kiphire', 1, 19, 'publish', '2025-06-25 12:07:49', '2025-06-25 12:07:49'),
(441, 'Kohima', 1, 19, 'publish', '2025-06-25 12:07:50', '2025-06-25 12:07:50'),
(442, 'Longleng', 1, 19, 'publish', '2025-06-25 12:07:51', '2025-06-25 12:07:51'),
(443, 'Meluri', 1, 19, 'publish', '2025-06-25 12:07:52', '2025-06-25 12:07:52'),
(444, 'Mokokchung', 1, 19, 'publish', '2025-06-25 12:07:53', '2025-06-25 12:07:53'),
(445, 'Mon', 1, 19, 'publish', '2025-06-25 12:07:54', '2025-06-25 12:07:54'),
(446, 'Niuland', 1, 19, 'publish', '2025-06-25 12:07:55', '2025-06-25 12:07:55'),
(447, 'Noklak', 1, 19, 'publish', '2025-06-25 12:07:56', '2025-06-25 12:07:56'),
(448, 'Peren', 1, 19, 'publish', '2025-06-25 12:07:57', '2025-06-25 12:07:57'),
(449, 'Phek', 1, 19, 'publish', '2025-06-25 12:07:58', '2025-06-25 12:07:58'),
(450, 'Shamator', 1, 19, 'publish', '2025-06-25 12:07:59', '2025-06-25 12:07:59'),
(451, 'Tseminyu', 1, 19, 'publish', '2025-06-25 12:08:00', '2025-06-25 12:08:00'),
(452, 'Tuensang', 1, 19, 'publish', '2025-06-25 12:08:01', '2025-06-25 12:08:01'),
(453, 'Wokha', 1, 19, 'publish', '2025-06-25 12:08:02', '2025-06-25 12:08:02'),
(454, 'Zunheboto', 1, 19, 'publish', '2025-06-25 12:08:03', '2025-06-25 12:08:03'),
(455, 'Angul', 1, 20, 'publish', '2025-06-25 12:08:04', '2025-06-25 12:08:04'),
(456, 'Balangir', 1, 20, 'publish', '2025-06-25 12:08:05', '2025-06-25 12:08:05'),
(457, 'Balasore', 1, 20, 'publish', '2025-06-25 12:08:06', '2025-06-25 12:08:06'),
(458, 'Bargarh', 1, 20, 'publish', '2025-06-25 12:08:07', '2025-06-25 12:08:07'),
(459, 'Bhadrak', 1, 20, 'publish', '2025-06-25 12:08:08', '2025-06-25 12:08:08'),
(460, 'Boudh', 1, 20, 'publish', '2025-06-25 12:08:09', '2025-06-25 12:08:09'),
(461, 'Cuttack', 1, 20, 'publish', '2025-06-25 12:08:10', '2025-06-25 12:08:10'),
(462, 'Deogarh', 1, 20, 'publish', '2025-06-25 12:08:11', '2025-06-25 12:08:11'),
(463, 'Dhenkanal', 1, 20, 'publish', '2025-06-25 12:08:12', '2025-06-25 12:08:12'),
(464, 'Gajapati', 1, 20, 'publish', '2025-06-25 12:08:13', '2025-06-25 12:08:13'),
(465, 'Ganjam', 1, 20, 'publish', '2025-06-25 12:08:14', '2025-06-25 12:08:14'),
(466, 'Jagatsinghpur', 1, 20, 'publish', '2025-06-25 12:08:15', '2025-06-25 12:08:15'),
(467, 'Jajpur', 1, 20, 'publish', '2025-06-25 12:08:16', '2025-06-25 12:08:16'),
(468, 'Jharsuguda', 1, 20, 'publish', '2025-06-25 12:08:17', '2025-06-25 12:08:17'),
(469, 'Kalahandi', 1, 20, 'publish', '2025-06-25 12:08:18', '2025-06-25 12:08:18'),
(470, 'Kandhamal', 1, 20, 'publish', '2025-06-25 12:08:19', '2025-06-25 12:08:19'),
(471, 'Kendrapara', 1, 20, 'publish', '2025-06-25 12:08:20', '2025-06-25 12:08:20'),
(472, 'Kendujhar', 1, 20, 'publish', '2025-06-25 12:08:21', '2025-06-25 12:08:21'),
(473, 'Khordha', 1, 20, 'publish', '2025-06-25 12:08:22', '2025-06-25 12:08:22'),
(474, 'Koraput', 1, 20, 'publish', '2025-06-25 12:08:23', '2025-06-25 12:08:23'),
(475, 'Malkangiri', 1, 20, 'publish', '2025-06-25 12:08:24', '2025-06-25 12:08:24'),
(476, 'Mayurbhanj', 1, 20, 'publish', '2025-06-25 12:08:25', '2025-06-25 12:08:25'),
(477, 'Nabarangpur', 1, 20, 'publish', '2025-06-25 12:08:26', '2025-06-25 12:08:26'),
(478, 'Nayagarh', 1, 20, 'publish', '2025-06-25 12:08:27', '2025-06-25 12:08:27'),
(479, 'Nuapada', 1, 20, 'publish', '2025-06-25 12:08:28', '2025-06-25 12:08:28'),
(480, 'Puri', 1, 20, 'publish', '2025-06-25 12:08:29', '2025-06-25 12:08:29'),
(481, 'Rayagada', 1, 20, 'publish', '2025-06-25 12:08:30', '2025-06-25 12:08:30'),
(482, 'Sambalpur', 1, 20, 'publish', '2025-06-25 12:08:31', '2025-06-25 12:08:31'),
(483, 'Subarnapur', 1, 20, 'publish', '2025-06-25 12:08:32', '2025-06-25 12:08:32'),
(484, 'Sundargarh', 1, 20, 'publish', '2025-06-25 12:08:33', '2025-06-25 12:08:33'),
(485, 'Amritsar', 1, 21, 'publish', '2025-06-25 12:08:34', '2025-06-25 12:08:34'),
(486, 'Barnala', 1, 21, 'publish', '2025-06-25 12:08:35', '2025-06-25 12:08:35'),
(487, 'Bathinda', 1, 21, 'publish', '2025-06-25 12:08:36', '2025-06-25 12:08:36'),
(488, 'Faridkot', 1, 21, 'publish', '2025-06-25 12:08:37', '2025-06-25 12:08:37'),
(489, 'Fatehgarh Sahib', 1, 21, 'publish', '2025-06-25 12:08:38', '2025-06-25 12:08:38'),
(490, 'Fazilka', 1, 21, 'publish', '2025-06-25 12:08:39', '2025-06-25 12:08:39'),
(491, 'Firozpur', 1, 21, 'publish', '2025-06-25 12:08:40', '2025-06-25 12:08:40'),
(492, 'Gurdaspur', 1, 21, 'publish', '2025-06-25 12:08:41', '2025-06-25 12:08:41'),
(493, 'Hoshiarpur', 1, 21, 'publish', '2025-06-25 12:08:42', '2025-06-25 12:08:42'),
(494, 'Jalandhar', 1, 21, 'publish', '2025-06-25 12:08:43', '2025-06-25 12:08:43'),
(495, 'Kapurthala', 1, 21, 'publish', '2025-06-25 12:08:44', '2025-06-25 12:08:44'),
(496, 'Ludhiana', 1, 21, 'publish', '2025-06-25 12:08:45', '2025-06-25 12:08:45'),
(497, 'Malerkotla', 1, 21, 'publish', '2025-06-25 12:08:46', '2025-06-25 12:08:46'),
(498, 'Mansa', 1, 21, 'publish', '2025-06-25 12:08:47', '2025-06-25 12:08:47'),
(499, 'Moga', 1, 21, 'publish', '2025-06-25 12:08:48', '2025-06-25 12:08:48'),
(500, 'Pathankot', 1, 21, 'publish', '2025-06-25 12:08:49', '2025-06-25 12:08:49'),
(501, 'Patiala', 1, 21, 'publish', '2025-06-25 12:08:50', '2025-06-25 12:08:50'),
(502, 'Rupnagar', 1, 21, 'publish', '2025-06-25 12:08:51', '2025-06-25 12:08:51'),
(503, 'Sahibzada Ajit Singh Nagar (Mohali)', 1, 21, 'publish', '2025-06-25 12:08:52', '2025-06-25 12:08:52'),
(504, 'Sangrur', 1, 21, 'publish', '2025-06-25 12:08:53', '2025-06-25 12:08:53'),
(505, 'Shaheed Bhagat Singh Nagar (Nawanshahr)', 1, 21, 'publish', '2025-06-25 12:08:54', '2025-06-25 12:08:54'),
(506, 'Sri Muktsar Sahib', 1, 21, 'publish', '2025-06-25 12:08:55', '2025-06-25 12:08:55'),
(507, 'Tarn Taran', 1, 21, 'publish', '2025-06-25 12:08:56', '2025-06-25 12:08:56'),
(508, 'Ajmer', 1, 22, 'publish', '2025-06-25 12:08:57', '2025-06-25 12:08:57'),
(509, 'Alwar', 1, 22, 'publish', '2025-06-25 12:08:58', '2025-06-25 12:08:58'),
(510, 'Balotra', 1, 22, 'publish', '2025-06-25 12:08:59', '2025-06-25 12:08:59'),
(511, 'Banswara', 1, 22, 'publish', '2025-06-25 12:09:00', '2025-06-25 12:09:00'),
(512, 'Baran', 1, 22, 'publish', '2025-06-25 12:09:01', '2025-06-25 12:09:01'),
(513, 'Barmer', 1, 22, 'publish', '2025-06-25 12:09:02', '2025-06-25 12:09:02'),
(514, 'Beawar', 1, 22, 'publish', '2025-06-25 12:09:03', '2025-06-25 12:09:03'),
(515, 'Bharatpur', 1, 22, 'publish', '2025-06-25 12:09:04', '2025-06-25 12:09:04'),
(516, 'Bhilwara', 1, 22, 'publish', '2025-06-25 12:09:05', '2025-06-25 12:09:05'),
(517, 'Bikaner', 1, 22, 'publish', '2025-06-25 12:09:06', '2025-06-25 12:09:06'),
(518, 'Bundi', 1, 22, 'publish', '2025-06-25 12:09:07', '2025-06-25 12:09:07'),
(519, 'Chittorgarh', 1, 22, 'publish', '2025-06-25 12:09:08', '2025-06-25 12:09:08'),
(520, 'Churu', 1, 22, 'publish', '2025-06-25 12:09:09', '2025-06-25 12:09:09'),
(521, 'Dausa', 1, 22, 'publish', '2025-06-25 12:09:10', '2025-06-25 12:09:10'),
(522, 'Deeg', 1, 22, 'publish', '2025-06-25 12:09:11', '2025-06-25 12:09:11'),
(523, 'Didwana-Kuchaman', 1, 22, 'publish', '2025-06-25 12:09:12', '2025-06-25 12:09:12'),
(524, 'Dholpur', 1, 22, 'publish', '2025-06-25 12:09:13', '2025-06-25 12:09:13'),
(525, 'Dungarpur', 1, 22, 'publish', '2025-06-25 12:09:14', '2025-06-25 12:09:14'),
(526, 'Hanumangarh', 1, 22, 'publish', '2025-06-25 12:09:15', '2025-06-25 12:09:15'),
(527, 'Jaipur', 1, 22, 'publish', '2025-06-25 12:09:16', '2025-06-25 12:09:16'),
(528, 'Jaisalmer', 1, 22, 'publish', '2025-06-25 12:09:17', '2025-06-25 12:09:17'),
(529, 'Jalore', 1, 22, 'publish', '2025-06-25 12:09:18', '2025-06-25 12:09:18'),
(530, 'Jhalawar', 1, 22, 'publish', '2025-06-25 12:09:19', '2025-06-25 12:09:19'),
(531, 'Jhunjhunu', 1, 22, 'publish', '2025-06-25 12:09:20', '2025-06-25 12:09:20'),
(532, 'Jodhpur', 1, 22, 'publish', '2025-06-25 12:09:21', '2025-06-25 12:09:21'),
(533, 'Karauli', 1, 22, 'publish', '2025-06-25 12:09:22', '2025-06-25 12:09:22'),
(534, 'Khairthal-Tijara', 1, 22, 'publish', '2025-06-25 12:09:23', '2025-06-25 12:09:23'),
(535, 'Kota', 1, 22, 'publish', '2025-06-25 12:09:24', '2025-06-25 12:09:24'),
(536, 'Kotputli-Behror', 1, 22, 'publish', '2025-06-25 12:09:25', '2025-06-25 12:09:25'),
(537, 'Nagaur', 1, 22, 'publish', '2025-06-25 12:09:26', '2025-06-25 12:09:26'),
(538, 'Pali', 1, 22, 'publish', '2025-06-25 12:09:27', '2025-06-25 12:09:27'),
(539, 'Phalodi', 1, 22, 'publish', '2025-06-25 12:09:28', '2025-06-25 12:09:28'),
(540, 'Pratapgarh', 1, 22, 'publish', '2025-06-25 12:09:29', '2025-06-25 12:09:29'),
(541, 'Rajsamand', 1, 22, 'publish', '2025-06-25 12:09:30', '2025-06-25 12:09:30'),
(542, 'Salumbar', 1, 22, 'publish', '2025-06-25 12:09:31', '2025-06-25 12:09:31'),
(543, 'Sawai Madhopur', 1, 22, 'publish', '2025-06-25 12:09:32', '2025-06-25 12:09:32'),
(544, 'Sikar', 1, 22, 'publish', '2025-06-25 12:09:33', '2025-06-25 12:09:33'),
(545, 'Sirohi', 1, 22, 'publish', '2025-06-25 12:09:34', '2025-06-25 12:09:34'),
(546, 'Sri Ganganagar', 1, 22, 'publish', '2025-06-25 12:09:35', '2025-06-25 12:09:35'),
(547, 'Tonk', 1, 22, 'publish', '2025-06-25 12:09:36', '2025-06-25 12:09:36'),
(548, 'Udaipur', 1, 22, 'publish', '2025-06-25 12:09:37', '2025-06-25 12:09:37'),
(549, 'Gangtok', 1, 23, 'publish', '2025-06-25 12:09:38', '2025-06-25 12:09:38'),
(550, 'Gyalshing (Geyzing)', 1, 23, 'publish', '2025-06-25 12:09:39', '2025-06-25 12:09:39'),
(551, 'Mangan', 1, 23, 'publish', '2025-06-25 12:09:40', '2025-06-25 12:09:40'),
(552, 'Namchi', 1, 23, 'publish', '2025-06-25 12:09:41', '2025-06-25 12:09:41'),
(553, 'Pakyong', 1, 23, 'publish', '2025-06-25 12:09:42', '2025-06-25 12:09:42'),
(554, 'Soreng', 1, 23, 'publish', '2025-06-25 12:09:43', '2025-06-25 12:09:43'),
(555, 'Ariyalur', 1, 24, 'publish', '2025-06-25 12:09:44', '2025-06-25 12:09:44'),
(556, 'Chengalpattu', 1, 24, 'publish', '2025-06-25 12:09:45', '2025-06-25 12:09:45'),
(557, 'Chennai', 1, 24, 'publish', '2025-06-25 12:09:46', '2025-06-25 12:09:46'),
(558, 'Coimbatore', 1, 24, 'publish', '2025-06-25 12:09:47', '2025-06-25 12:09:47'),
(559, 'Cuddalore', 1, 24, 'publish', '2025-06-25 12:09:48', '2025-06-25 12:09:48'),
(560, 'Dharmapuri', 1, 24, 'publish', '2025-06-25 12:09:49', '2025-06-25 12:09:49'),
(561, 'Dindigul', 1, 24, 'publish', '2025-06-25 12:09:50', '2025-06-25 12:09:50'),
(562, 'Erode', 1, 24, 'publish', '2025-06-25 12:09:51', '2025-06-25 12:09:51'),
(563, 'Kallakurichi', 1, 24, 'publish', '2025-06-25 12:09:52', '2025-06-25 12:09:52'),
(564, 'Kanchipuram', 1, 24, 'publish', '2025-06-25 12:09:53', '2025-06-25 12:09:53'),
(565, 'Kanniyakumari', 1, 24, 'publish', '2025-06-25 12:09:54', '2025-06-25 12:09:54'),
(566, 'Karur', 1, 24, 'publish', '2025-06-25 12:09:55', '2025-06-25 12:09:55'),
(567, 'Krishnagiri', 1, 24, 'publish', '2025-06-25 12:09:56', '2025-06-25 12:09:56'),
(568, 'Madurai', 1, 24, 'publish', '2025-06-25 12:09:57', '2025-06-25 12:09:57'),
(569, 'Mayiladuthurai', 1, 24, 'publish', '2025-06-25 12:09:58', '2025-06-25 12:09:58'),
(570, 'Nagapattinam', 1, 24, 'publish', '2025-06-25 12:09:59', '2025-06-25 12:09:59'),
(571, 'Namakkal', 1, 24, 'publish', '2025-06-25 12:10:00', '2025-06-25 12:10:00'),
(572, 'Nilgiris', 1, 24, 'publish', '2025-06-25 12:10:01', '2025-06-25 12:10:01'),
(573, 'Perambalur', 1, 24, 'publish', '2025-06-25 12:10:02', '2025-06-25 12:10:02'),
(574, 'Pudukkottai', 1, 24, 'publish', '2025-06-25 12:10:03', '2025-06-25 12:10:03'),
(575, 'Ramanathapuram', 1, 24, 'publish', '2025-06-25 12:10:04', '2025-06-25 12:10:04'),
(576, 'Ranipet', 1, 24, 'publish', '2025-06-25 12:10:05', '2025-06-25 12:10:05'),
(577, 'Salem', 1, 24, 'publish', '2025-06-25 12:10:06', '2025-06-25 12:10:06'),
(578, 'Sivagangai', 1, 24, 'publish', '2025-06-25 12:10:07', '2025-06-25 12:10:07'),
(579, 'Tenkasi', 1, 24, 'publish', '2025-06-25 12:10:08', '2025-06-25 12:10:08'),
(580, 'Thanjavur', 1, 24, 'publish', '2025-06-25 12:10:09', '2025-06-25 12:10:09'),
(581, 'Theni', 1, 24, 'publish', '2025-06-25 12:10:10', '2025-06-25 12:10:10'),
(582, 'Thiruvallur', 1, 24, 'publish', '2025-06-25 12:10:11', '2025-06-25 12:10:11'),
(583, 'Thiruvarur', 1, 24, 'publish', '2025-06-25 12:10:12', '2025-06-25 12:10:12'),
(584, 'Thoothukudi', 1, 24, 'publish', '2025-06-25 12:10:13', '2025-06-25 12:10:13'),
(585, 'Tiruchirappalli', 1, 24, 'publish', '2025-06-25 12:10:14', '2025-06-25 12:10:14'),
(586, 'Tirunelveli', 1, 24, 'publish', '2025-06-25 12:10:15', '2025-06-25 12:10:15'),
(587, 'Tirupathur', 1, 24, 'publish', '2025-06-25 12:10:16', '2025-06-25 12:10:16'),
(588, 'Tiruppur', 1, 24, 'publish', '2025-06-25 12:10:17', '2025-06-25 12:10:17'),
(589, 'Tiruvannamalai', 1, 24, 'publish', '2025-06-25 12:10:18', '2025-06-25 12:10:18'),
(590, 'Vellore', 1, 24, 'publish', '2025-06-25 12:10:19', '2025-06-25 12:10:19'),
(591, 'Viluppuram', 1, 24, 'publish', '2025-06-25 12:10:20', '2025-06-25 12:10:20'),
(592, 'Virudhunagar', 1, 24, 'publish', '2025-06-25 12:10:21', '2025-06-25 12:10:21'),
(593, 'Adilabad', 1, 25, 'publish', '2025-06-25 12:10:22', '2025-06-25 12:10:22'),
(594, 'Bhadradri Kothagudem', 1, 25, 'publish', '2025-06-25 12:10:23', '2025-06-25 12:10:23'),
(595, 'Hanumakonda', 1, 25, 'publish', '2025-06-25 12:10:24', '2025-06-25 12:10:24'),
(596, 'Hyderabad', 1, 25, 'publish', '2025-06-25 12:10:25', '2025-06-25 12:10:25'),
(597, 'Jagitial', 1, 25, 'publish', '2025-06-25 12:10:26', '2025-06-25 12:10:26'),
(598, 'Jangaon', 1, 25, 'publish', '2025-06-25 12:10:27', '2025-06-25 12:10:27'),
(599, 'Jayashankar Bhupalapally', 1, 25, 'publish', '2025-06-25 12:10:28', '2025-06-25 12:10:28'),
(600, 'Jogulamba Gadwal', 1, 25, 'publish', '2025-06-25 12:10:29', '2025-06-25 12:10:29'),
(601, 'Kamareddy', 1, 25, 'publish', '2025-06-25 12:10:30', '2025-06-25 12:10:30'),
(602, 'Karimnagar', 1, 25, 'publish', '2025-06-25 12:10:31', '2025-06-25 12:10:31'),
(603, 'Khammam', 1, 25, 'publish', '2025-06-25 12:10:32', '2025-06-25 12:10:32'),
(604, 'Kumuram Bheem Asifabad', 1, 25, 'publish', '2025-06-25 12:10:33', '2025-06-25 12:10:33'),
(605, 'Mahabubabad', 1, 25, 'publish', '2025-06-25 12:10:34', '2025-06-25 12:10:34'),
(606, 'Mahabubnagar', 1, 25, 'publish', '2025-06-25 12:10:35', '2025-06-25 12:10:35'),
(607, 'Mancherial', 1, 25, 'publish', '2025-06-25 12:10:36', '2025-06-25 12:10:36'),
(608, 'Medak', 1, 25, 'publish', '2025-06-25 12:10:37', '2025-06-25 12:10:37'),
(609, 'Medchal Malkajgiri', 1, 25, 'publish', '2025-06-25 12:10:38', '2025-06-25 12:10:38'),
(610, 'Mulugu', 1, 25, 'publish', '2025-06-25 12:10:39', '2025-06-25 12:10:39'),
(611, 'Nagarkurnool', 1, 25, 'publish', '2025-06-25 12:10:40', '2025-06-25 12:10:40'),
(612, 'Nalgonda', 1, 25, 'publish', '2025-06-25 12:10:41', '2025-06-25 12:10:41'),
(613, 'Narayanpet', 1, 25, 'publish', '2025-06-25 12:10:42', '2025-06-25 12:10:42'),
(614, 'Nirmal', 1, 25, 'publish', '2025-06-25 12:10:43', '2025-06-25 12:10:43'),
(615, 'Nizamabad', 1, 25, 'publish', '2025-06-25 12:10:44', '2025-06-25 12:10:44'),
(616, 'Peddapalli', 1, 25, 'publish', '2025-06-25 12:10:45', '2025-06-25 12:10:45'),
(617, 'Rajanna Sircilla', 1, 25, 'publish', '2025-06-25 12:10:46', '2025-06-25 12:10:46'),
(618, 'Ranga Reddy', 1, 25, 'publish', '2025-06-25 12:10:47', '2025-06-25 12:10:47'),
(619, 'Sangareddy', 1, 25, 'publish', '2025-06-25 12:10:48', '2025-06-25 12:10:48'),
(620, 'Siddipet', 1, 25, 'publish', '2025-06-25 12:10:49', '2025-06-25 12:10:49'),
(621, 'Suryapet', 1, 25, 'publish', '2025-06-25 12:10:50', '2025-06-25 12:10:50'),
(622, 'Vikarabad', 1, 25, 'publish', '2025-06-25 12:10:51', '2025-06-25 12:10:51'),
(623, 'Wanaparthy', 1, 25, 'publish', '2025-06-25 12:10:52', '2025-06-25 12:10:52'),
(624, 'Warangal', 1, 25, 'publish', '2025-06-25 12:10:53', '2025-06-25 12:10:53'),
(625, 'Yadadri Bhuvanagiri', 1, 25, 'publish', '2025-06-25 12:10:54', '2025-06-25 12:10:54'),
(626, 'Dhalai', 1, 26, 'publish', '2025-06-25 12:10:55', '2025-06-25 12:10:55'),
(627, 'Gomati', 1, 26, 'publish', '2025-06-25 12:10:56', '2025-06-25 12:10:56'),
(628, 'Khowai', 1, 26, 'publish', '2025-06-25 12:10:57', '2025-06-25 12:10:57'),
(629, 'North Tripura', 1, 26, 'publish', '2025-06-25 12:10:58', '2025-06-25 12:10:58'),
(630, 'Sepahijala', 1, 26, 'publish', '2025-06-25 12:10:59', '2025-06-25 12:10:59'),
(631, 'South Tripura', 1, 26, 'publish', '2025-06-25 12:11:00', '2025-06-25 12:11:00'),
(632, 'Unakoti', 1, 26, 'publish', '2025-06-25 12:11:01', '2025-06-25 12:11:01'),
(633, 'West Tripura', 1, 26, 'publish', '2025-06-25 12:11:02', '2025-06-25 12:11:02'),
(634, 'Agra', 1, 27, 'publish', '2025-06-25 12:11:03', '2025-06-25 12:11:03'),
(635, 'Aligarh', 1, 27, 'publish', '2025-06-25 12:11:04', '2025-06-25 12:11:04'),
(636, 'Ambedkar Nagar', 1, 27, 'publish', '2025-06-25 12:11:05', '2025-06-25 12:11:05'),
(637, 'Amethi', 1, 27, 'publish', '2025-06-25 12:11:06', '2025-06-25 12:11:06'),
(638, 'Amroha', 1, 27, 'publish', '2025-06-25 12:11:07', '2025-06-25 12:11:07'),
(639, 'Auraiya', 1, 27, 'publish', '2025-06-25 12:11:08', '2025-06-25 12:11:08'),
(640, 'Ayodhya', 1, 27, 'publish', '2025-06-25 12:11:09', '2025-06-25 12:11:09'),
(641, 'Azamgarh', 1, 27, 'publish', '2025-06-25 12:11:10', '2025-06-25 12:11:10'),
(642, 'Baghpat', 1, 27, 'publish', '2025-06-25 12:11:11', '2025-06-25 12:11:11'),
(643, 'Bahraich', 1, 27, 'publish', '2025-06-25 12:11:12', '2025-06-25 12:11:12'),
(644, 'Ballia', 1, 27, 'publish', '2025-06-25 12:11:13', '2025-06-25 12:11:13'),
(645, 'Balrampur', 1, 27, 'publish', '2025-06-25 12:11:14', '2025-06-25 12:11:14'),
(646, 'Banda', 1, 27, 'publish', '2025-06-25 12:11:15', '2025-06-25 12:11:15'),
(647, 'Barabanki', 1, 27, 'publish', '2025-06-25 12:11:16', '2025-06-25 12:11:16'),
(648, 'Bareilly', 1, 27, 'publish', '2025-06-25 12:11:17', '2025-06-25 12:11:17'),
(649, 'Basti', 1, 27, 'publish', '2025-06-25 12:11:18', '2025-06-25 12:11:18'),
(650, 'Bhadohi', 1, 27, 'publish', '2025-06-25 12:11:19', '2025-06-25 12:11:19'),
(651, 'Bijnor', 1, 27, 'publish', '2025-06-25 12:11:20', '2025-06-25 12:11:20'),
(652, 'Budaun', 1, 27, 'publish', '2025-06-25 12:11:21', '2025-06-25 12:11:21'),
(653, 'Bulandshahr', 1, 27, 'publish', '2025-06-25 12:11:22', '2025-06-25 12:11:22'),
(654, 'Chandauli', 1, 27, 'publish', '2025-06-25 12:11:23', '2025-06-25 12:11:23'),
(655, 'Chitrakoot', 1, 27, 'publish', '2025-06-25 12:11:24', '2025-06-25 12:11:24'),
(656, 'Deoria', 1, 27, 'publish', '2025-06-25 12:11:25', '2025-06-25 12:11:25'),
(657, 'Etah', 1, 27, 'publish', '2025-06-25 12:11:26', '2025-06-25 12:11:26'),
(658, 'Etawah', 1, 27, 'publish', '2025-06-25 12:11:27', '2025-06-25 12:11:27'),
(659, 'Farrukhabad', 1, 27, 'publish', '2025-06-25 12:11:28', '2025-06-25 12:11:28'),
(660, 'Fatehpur', 1, 27, 'publish', '2025-06-25 12:11:29', '2025-06-25 12:11:29'),
(661, 'Firozabad', 1, 27, 'publish', '2025-06-25 12:11:30', '2025-06-25 12:11:30'),
(662, 'Gautam Buddha Nagar', 1, 27, 'publish', '2025-06-25 12:11:31', '2025-06-25 12:11:31'),
(663, 'Ghaziabad', 1, 27, 'publish', '2025-06-25 12:11:32', '2025-06-25 12:11:32'),
(664, 'Ghazipur', 1, 27, 'publish', '2025-06-25 12:11:33', '2025-06-25 12:11:33'),
(665, 'Gonda', 1, 27, 'publish', '2025-06-25 12:11:34', '2025-06-25 12:11:34'),
(666, 'Gorakhpur', 1, 27, 'publish', '2025-06-25 12:11:35', '2025-06-25 12:11:35'),
(667, 'Hamirpur', 1, 27, 'publish', '2025-06-25 12:11:36', '2025-06-25 12:11:36'),
(668, 'Hapur', 1, 27, 'publish', '2025-06-25 12:11:37', '2025-06-25 12:11:37'),
(669, 'Hardoi', 1, 27, 'publish', '2025-06-25 12:11:38', '2025-06-25 12:11:38'),
(670, 'Hathras', 1, 27, 'publish', '2025-06-25 12:11:39', '2025-06-25 12:11:39'),
(671, 'Jalaun', 1, 27, 'publish', '2025-06-25 12:11:40', '2025-06-25 12:11:40'),
(672, 'Jaunpur', 1, 27, 'publish', '2025-06-25 12:11:41', '2025-06-25 12:11:41'),
(673, 'Jhansi', 1, 27, 'publish', '2025-06-25 12:11:42', '2025-06-25 12:11:42'),
(674, 'Kannauj', 1, 27, 'publish', '2025-06-25 12:11:43', '2025-06-25 12:11:43'),
(675, 'Kanpur Dehat', 1, 27, 'publish', '2025-06-25 12:11:44', '2025-06-25 12:11:44'),
(676, 'Kanpur Nagar', 1, 27, 'publish', '2025-06-25 12:11:45', '2025-06-25 12:11:45'),
(677, 'Kasganj', 1, 27, 'publish', '2025-06-25 12:11:46', '2025-06-25 12:11:46'),
(678, 'Kaushambi', 1, 27, 'publish', '2025-06-25 12:11:47', '2025-06-25 12:11:47'),
(679, 'Kushinagar', 1, 27, 'publish', '2025-06-25 12:11:48', '2025-06-25 12:11:48'),
(680, 'Lakhimpur Kheri', 1, 27, 'publish', '2025-06-25 12:11:49', '2025-06-25 12:11:49'),
(681, 'Lalitpur', 1, 27, 'publish', '2025-06-25 12:11:50', '2025-06-25 12:11:50'),
(682, 'Lucknow', 1, 27, 'publish', '2025-06-25 12:11:51', '2025-06-25 12:11:51'),
(683, 'Maharajganj', 1, 27, 'publish', '2025-06-25 12:11:52', '2025-06-25 12:11:52'),
(684, 'Mahoba', 1, 27, 'publish', '2025-06-25 12:11:53', '2025-06-25 12:11:53'),
(685, 'Mainpuri', 1, 27, 'publish', '2025-06-25 12:11:54', '2025-06-25 12:11:54'),
(686, 'Mathura', 1, 27, 'publish', '2025-06-25 12:11:55', '2025-06-25 12:11:55'),
(687, 'Mau', 1, 27, 'publish', '2025-06-25 12:11:56', '2025-06-25 12:11:56'),
(688, 'Meerut', 1, 27, 'publish', '2025-06-25 12:11:57', '2025-06-25 12:11:57'),
(689, 'Mirzapur', 1, 27, 'publish', '2025-06-25 12:11:58', '2025-06-25 12:11:58'),
(690, 'Moradabad', 1, 27, 'publish', '2025-06-25 12:11:59', '2025-06-25 12:11:59'),
(691, 'Muzaffarnagar', 1, 27, 'publish', '2025-06-25 12:12:00', '2025-06-25 12:12:00'),
(692, 'Pilibhit', 1, 27, 'publish', '2025-06-25 12:12:01', '2025-06-25 12:12:01'),
(693, 'Pratapgarh', 1, 27, 'publish', '2025-06-25 12:12:02', '2025-06-25 12:12:02'),
(694, 'Prayagraj', 1, 27, 'publish', '2025-06-25 12:12:03', '2025-06-25 12:12:03'),
(695, 'Raebareli', 1, 27, 'publish', '2025-06-25 12:12:04', '2025-06-25 12:12:04'),
(696, 'Rampur', 1, 27, 'publish', '2025-06-25 12:12:05', '2025-06-25 12:12:05'),
(697, 'Saharanpur', 1, 27, 'publish', '2025-06-25 12:12:06', '2025-06-25 12:12:06'),
(698, 'Sambhal', 1, 27, 'publish', '2025-06-25 12:12:07', '2025-06-25 12:12:07'),
(699, 'Sant Kabir Nagar', 1, 27, 'publish', '2025-06-25 12:12:08', '2025-06-25 12:12:08'),
(700, 'Shahjahanpur', 1, 27, 'publish', '2025-06-25 12:12:09', '2025-06-25 12:12:09'),
(701, 'Shamli', 1, 27, 'publish', '2025-06-25 12:12:10', '2025-06-25 12:12:10'),
(702, 'Shravasti', 1, 27, 'publish', '2025-06-25 12:12:11', '2025-06-25 12:12:11'),
(703, 'Siddharthnagar', 1, 27, 'publish', '2025-06-25 12:12:12', '2025-06-25 12:12:12'),
(704, 'Sitapur', 1, 27, 'publish', '2025-06-25 12:12:13', '2025-06-25 12:12:13'),
(705, 'Sonbhadra', 1, 27, 'publish', '2025-06-25 12:12:14', '2025-06-25 12:12:14'),
(706, 'Sultanpur', 1, 27, 'publish', '2025-06-25 12:12:15', '2025-06-25 12:12:15'),
(707, 'Unnao', 1, 27, 'publish', '2025-06-25 12:12:16', '2025-06-25 12:12:16'),
(708, 'Varanasi', 1, 27, 'publish', '2025-06-25 12:12:17', '2025-06-25 12:12:17'),
(709, 'Almora', 1, 28, 'publish', '2025-06-25 12:12:18', '2025-06-25 12:12:18'),
(710, 'Bageshwar', 1, 28, 'publish', '2025-06-25 12:12:19', '2025-06-25 12:12:19'),
(711, 'Chamoli', 1, 28, 'publish', '2025-06-25 12:12:20', '2025-06-25 12:12:20'),
(712, 'Champawat', 1, 28, 'publish', '2025-06-25 12:12:21', '2025-06-25 12:12:21'),
(713, 'Dehradun', 1, 28, 'publish', '2025-06-25 12:12:22', '2025-06-25 12:12:22'),
(714, 'Haridwar', 1, 28, 'publish', '2025-06-25 12:12:23', '2025-06-25 12:12:23'),
(715, 'Nainital', 1, 28, 'publish', '2025-06-25 12:12:24', '2025-06-25 12:12:24'),
(716, 'Pauri Garhwal', 1, 28, 'publish', '2025-06-25 12:12:25', '2025-06-25 12:12:25'),
(717, 'Pithoragarh', 1, 28, 'publish', '2025-06-25 12:12:26', '2025-06-25 12:12:26'),
(718, 'Rudraprayag', 1, 28, 'publish', '2025-06-25 12:12:27', '2025-06-25 12:12:27'),
(719, 'Tehri Garhwal', 1, 28, 'publish', '2025-06-25 12:12:28', '2025-06-25 12:12:28'),
(720, 'Udham Singh Nagar', 1, 28, 'publish', '2025-06-25 12:12:29', '2025-06-25 12:12:29'),
(721, 'Uttarkashi', 1, 28, 'publish', '2025-06-25 12:12:30', '2025-06-25 12:12:30'),
(722, 'Alipurduar', 1, 29, 'publish', '2025-06-25 12:12:31', '2025-06-25 12:12:31'),
(723, 'Bankura', 1, 29, 'publish', '2025-06-25 12:12:32', '2025-06-25 12:12:32'),
(724, 'Paschim Bardhaman', 1, 29, 'publish', '2025-06-25 12:12:33', '2025-06-25 12:12:33'),
(725, 'Purba Bardhaman', 1, 29, 'publish', '2025-06-25 12:12:34', '2025-06-25 12:12:34'),
(726, 'Birbhum', 1, 29, 'publish', '2025-06-25 12:12:35', '2025-06-25 12:12:35'),
(727, 'Cooch Behar', 1, 29, 'publish', '2025-06-25 12:12:36', '2025-06-25 12:12:36'),
(728, 'Dakshin Dinajpur', 1, 29, 'publish', '2025-06-25 12:12:37', '2025-06-25 12:12:37'),
(729, 'Darjeeling', 1, 29, 'publish', '2025-06-25 12:12:38', '2025-06-25 12:12:38'),
(730, 'Hooghly', 1, 29, 'publish', '2025-06-25 12:12:39', '2025-06-25 12:12:39'),
(731, 'Howrah', 1, 29, 'publish', '2025-06-25 12:12:40', '2025-06-25 12:12:40'),
(732, 'Jalpaiguri', 1, 29, 'publish', '2025-06-25 12:12:41', '2025-06-25 12:12:41'),
(733, 'Jhargram', 1, 29, 'publish', '2025-06-25 12:12:42', '2025-06-25 12:12:42'),
(734, 'Kalimpong', 1, 29, 'publish', '2025-06-25 12:12:43', '2025-06-25 12:12:43'),
(735, 'Kolkata', 1, 29, 'publish', '2025-06-25 12:12:44', '2025-06-25 12:12:44'),
(736, 'Malda', 1, 29, 'publish', '2025-06-25 12:12:45', '2025-06-25 12:12:45'),
(737, 'Murshidabad', 1, 29, 'publish', '2025-06-25 12:12:46', '2025-06-25 12:12:46'),
(738, 'Nadia', 1, 29, 'publish', '2025-06-25 12:12:47', '2025-06-25 12:12:47'),
(739, 'North 24 Parganas', 1, 29, 'publish', '2025-06-25 12:12:48', '2025-06-25 12:12:48'),
(740, 'Paschim Medinipur', 1, 29, 'publish', '2025-06-25 12:12:49', '2025-06-25 12:12:49'),
(741, 'Purba Medinipur', 1, 29, 'publish', '2025-06-25 12:12:50', '2025-06-25 12:12:50'),
(742, 'Purulia', 1, 29, 'publish', '2025-06-25 12:12:51', '2025-06-25 12:12:51'),
(743, 'South 24 Parganas', 1, 29, 'publish', '2025-06-25 12:12:52', '2025-06-25 12:12:52'),
(744, 'Uttar Dinajpur', 1, 29, 'publish', '2025-06-25 12:12:53', '2025-06-25 12:12:53'),
(745, 'Sundarban', 1, 29, 'publish', '2025-06-25 12:12:54', '2025-06-25 12:12:54'),
(746, 'Ichhamati', 1, 29, 'publish', '2025-06-25 12:12:55', '2025-06-25 12:12:55'),
(747, 'Basirhat', 1, 29, 'publish', '2025-06-25 12:12:56', '2025-06-25 12:12:56'),
(748, 'Ranaghat', 1, 29, 'publish', '2025-06-25 12:12:57', '2025-06-25 12:12:57'),
(749, 'Bishnupur', 1, 29, 'publish', '2025-06-25 12:12:58', '2025-06-25 12:12:58'),
(750, 'Baharampur', 1, 29, 'publish', '2025-06-25 12:12:59', '2025-06-25 12:12:59'),
(751, 'Jangipur', 1, 29, 'publish', '2025-06-25 12:13:00', '2025-06-25 12:13:00')");
        }catch (\Exception $e){
            Log::error('Error seeding cities: ' . $e->getMessage());
            throw $e;
        }
    }

    private function seedDeliveryOption()
    {
        DB::statement("INSERT IGNORE INTO `delivery_options` (`id`, `icon`, `title`, `sub_title`, `created_at`, `updated_at`, `deleted_at`) VALUES
        (1, 'las la-gift', 'Estimated Delivery', 'With 4 Days', '2022-08-25 04:04:31', '2022-08-25 04:04:31', NULL),
        (2, 'las la-gift', 'Free Shipping', 'Order over 100$', '2022-08-25 04:04:52', '2022-08-25 04:04:52', NULL),
        (3, 'las la-gift', '7 Days Return', 'Without any damage', '2022-08-25 04:05:17', '2022-08-25 04:05:17', NULL)");
    }

    private function seedBadge()
    {
        DB::statement("INSERT IGNORE INTO `badges` (`id`, `name`, `image`, `for`, `sale_count`, `type`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
        (2, '100 Sales', 255, NULL, NULL, NULL, 'active', '2022-08-22 06:31:46', '2022-08-22 06:31:46', NULL),
        (3, 'New Arival', 251, NULL, NULL, NULL, 'active', '2022-08-25 04:41:24', '2022-08-25 04:41:24', NULL)");
    }

    private function seedProduct()
    {
        if (session()->get('theme') == 'casual')
        {
            DB::statement("INSERT IGNORE INTO `products` (`id`, `name`, `slug`, `summary`, `description`, `image_id`, `price`, `sale_price`, `cost`, `badge_id`, `brand_id`, `status_id`, `product_type`, `sold_count`, `min_purchase`, `max_purchase`, `is_refundable`, `is_in_house`, `is_inventory_warn_able`, `created_at`, `updated_at`, `deleted_at`) VALUES
            (190,'High Heel Wedding Shoes','high-heel-wedding-shoes','No Import Fees Deposit and $25.56 Shipping to Bangladesh','Heel Height approximately 3.0\" Platform measures approximately 0.25\"| True size to fit.\r\n\r\nAll your friends will be asking your advice when they see you with these sexy sandals! The open toe and strappy with sparkling rhinestone design front is eye-catching and alluring and will have envious stares on you all night long.\r\n\r\nThis pair is perfectly designed for steady steps, as it features a single, slim sole that ideally balances the heel height with the rest of the sleek shoe design.\r\n\r\nThis stunning pair of heels is ideal for weddings, parties and every other special occasion that calls for dressy, upscale shoes!\r\n\r\nFeaturing a slim straps that hugs your ankle for custom support and provides a comfort throughout wear. Your feet will not slip, turn or move out of place while wearing these gorgeous sandals!\r\n\r\n apples and other desserts.','529',250,240,250,2,2,1,1,NULL,1,10,0,1,1,'2022-11-16 10:29:36','2023-06-24 18:25:10',NULL),
            (191,'Mans Silver Ridge Lite Long Sleeve Shirt','mans-silver-ridge-lite-long-sleeve-shirt-1','No Import Fees Deposit and $25.56 Shipping to Bangladesh','Neck StyleCollared NeckAbout this Item. Omni-wick - the ultimate moisture management technology for the outdoors. Omni-wick quickly moves moisture from the skin into the fabric where it spreads across the surface to quickly evaporate—keeping you cool and your clothing dry.','532',774,533,774,NULL,2,1,1,NULL,1,10,0,1,1,'2022-11-16 10:30:14','2023-06-24 18:24:13',NULL),
            (192,'Buck  Long Sleeve Button Down Shirt','buck-long-sleeve-button-down-shirt-1','No Import Fees Deposit and $25.56 Shipping to Bangladesh','Fabric Type64% Cotton, 34% Polyester, 2% Spandex. Care InstructionsMachine Wash','531',452,321,452,NULL,2,1,1,NULL,1,10,0,1,1,'2022-11-16 10:32:18','2023-06-24 18:23:52',NULL),
            (193,'Mens Regular Fit Long Sleeve Poplin Jacket','mens-regular-fit-long-sleeve-poplin-jacket-1','No Import Fees Deposit and $25.56 Shipping to Bangladesh','Fabric Type64% Cotton, 34% Polyester, 2% Spandex','530',800,1000,800,3,2,1,1,NULL,1,10,0,1,1,'2022-11-16 10:37:51','2023-06-24 18:23:33',NULL),
            (195,'Baby shoes','baby-shoes','100% Textile\r\nSynthetic sole\r\nBoys sneaker-style boots with hook and loop closure\r\nHigh-top styling\r\nHook and loop closure for easy on-and-off','100% TextileSynthetic soleBoy’s sneaker-style boots with hook and loop closureHigh-top stylingHook and loop closure for easy on-and-off','537',223,200,223,NULL,2,1,1,NULL,1,10,0,1,1,'2022-11-16 10:51:12','2023-06-24 18:23:07',NULL),
            (196,'Stylish color  Jersey','stylish-color-jersey','The Blackout Jersey will match with any dirt bike pant, because what doesnt match with black? It has a moisture-wicking main body construction to keep you comfortable while youre putting down laps on the track or miles on the local trail. Plus, it has a perforated mesh fabric, so there is plenty of airflow through this motocross jersey.','100% PolyesterImportedPull On closureMachine WashBreathable crewneck jersey made for soccerRegular fit is wider at the body, with a straight silhouetteCrewneck provides full coverageThis product is made with recycled content as part of our ambition to end plastic waste','538',250,190,250,NULL,7,1,1,NULL,2,10,0,1,1,'2022-11-16 10:54:10','2023-06-24 18:22:37',NULL),
            (197,'High Heel Wedding Shoes','high-heel-wedding-shoes-1','No Import Fees Deposit and $25.56 Shipping to Bangladesh','Product details','375',250,240,250,2,2,1,1,NULL,1,10,0,1,1,'2022-11-16 11:24:22','2022-11-16 11:25:58','2022-11-16 11:25:58')");
        }
        elseif(session()->get('theme') == 'electro')
        {
            DB::statement("
INSERT INTO `products` (`id`, `name`, `slug`, `summary`, `description`, `image_id`, `price`, `sale_price`, `cost`, `badge_id`, `brand_id`, `status_id`, `product_type`, `sold_count`, `min_purchase`, `max_purchase`, `is_refundable`, `is_in_house`, `is_inventory_warn_able`, `created_at`, `updated_at`, `deleted_at`, `is_taxable`, `tax_class_id`) VALUES
(190, 'QuantumCore Pro Gaming Motherboard', 'quantumcore-pro-gaming-motherboard', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', 'Heel Height approximately 3.0\" Platform measures approximately 0.25\"| True size to fit.\r\n\r\nAll your friends will be asking your advice when they see you with these sexy sandals! The open toe and strappy with sparkling rhinestone design front is eye-catching and alluring and will have envious stares on you all night long.\r\n\r\nThis pair is perfectly designed for steady steps, as it features a single, slim sole that ideally balances the heel height with the rest of the sleek shoe design.\r\n\r\nThis stunning pair of heels is ideal for weddings, parties and every other special occasion that calls for dressy, upscale shoes!\r\n\r\nFeaturing a slim straps that hugs your ankle for custom support and provides a comfort throughout wear. Your feet will not slip, turn or move out of place while wearing these gorgeous sandals!\r\n\r\n apples and other desserts.', '972', 250, 240, 250, 2, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2022-11-16 04:29:36', '2023-08-17 13:32:28', NULL, 0, NULL),
(191, 'TitanPro Gaming Laptop', 'titanpro-gaming-laptop', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', 'Neck StyleCollared NeckAbout this Item. Omni-wick - the ultimate moisture management technology for the outdoors. Omni-wick quickly moves moisture from the skin into the fabric where it spreads across the surface to quickly evaporate—keeping you cool and your clothing dry.', '963', 774, 533, 774, NULL, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2022-11-16 04:30:14', '2023-11-17 18:01:10', NULL, 1, 1),
(192, 'GuardianEye HD Surveillance Camera', 'guardianeye-hd-surveillance-camera', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', 'Fabric Type64% Cotton, 34% Polyester, 2% Spandex. Care InstructionsMachine Wash', '975', 452, 321, 452, NULL, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2022-11-16 04:32:18', '2023-08-17 13:31:50', NULL, 0, NULL),
(193, 'SwiftGlide Precision Mouse', 'swiftglide-precision-mouse', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', 'Fabric Type64% Cotton, 34% Polyester, 2% Spandex', '974', 800, 700, 800, 3, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2022-11-16 04:37:51', '2023-11-17 18:01:03', NULL, 1, 1),
(196, 'Intle Gaming Laptop', 'intle-gaming-laptop', 'The Blackout Jersey will match with any dirt bike pant, because what doesnt match with black? It has a moisture-wicking main body construction to keep you comfortable while youre putting down laps on the track or miles on the local trail. Plus, it has a perforated mesh fabric, so there is plenty of airflow through this motocross jersey.', '100% PolyesterImportedPull On closureMachine WashBreathable crewneck jersey made for soccerRegular fit is wider at the body, with a straight silhouetteCrewneck provides full coverageThis product is made with recycled content as part of our ambition to end plastic waste', '973', 250, 190, 250, NULL, 7, 1, 1, NULL, 2, 10, 0, 1, 1, '2022-11-16 04:54:10', '2023-12-06 09:57:39', NULL, 1, 1),
(197, 'PhoenixTech Motherboard X7', 'phoenixtech-motherboard-x7', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', 'Heel Height approximately 3.0\" Platform measures approximately 0.25\"| True size to fit.\r\n\r\nAll your friends will be asking your advice when they see you with these sexy sandals! The open toe and strappy with sparkling rhinestone design front is eye-catching and alluring and will have envious stares on you all night long.\r\n\r\nThis pair is perfectly designed for steady steps, as it features a single, slim sole that ideally balances the heel height with the rest of the sleek shoe design.\r\n\r\nThis stunning pair of heels is ideal for weddings, parties and every other special occasion that calls for dressy, upscale shoes!\r\n\r\nFeaturing a slim straps that hugs your ankle for custom support and provides a comfort throughout wear. Your feet will not slip, turn or move out of place while wearing these gorgeous sandals!\r\n\r\n apples and other desserts.', '972', 250, 240, 250, 2, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2023-08-17 13:26:12', '2023-11-15 08:27:16', NULL, 1, 1),
(198, 'PhoenixTech Motherboard X7', 'phoenixtech-motherboard-x7', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', 'Heel Height approximately 3.0\" Platform measures approximately 0.25\"| True size to fit.\r\n\r\nAll your friends will be asking your advice when they see you with these sexy sandals! The open toe and strappy with sparkling rhinestone design front is eye-catching and alluring and will have envious stares on you all night long.\r\n\r\nThis pair is perfectly designed for steady steps, as it features a single, slim sole that ideally balances the heel height with the rest of the sleek shoe design.\r\n\r\nThis stunning pair of heels is ideal for weddings, parties and every other special occasion that calls for dressy, upscale shoes!\r\n\r\nFeaturing a slim straps that hugs your ankle for custom support and provides a comfort throughout wear. Your feet will not slip, turn or move out of place while wearing these gorgeous sandals!\r\n\r\n apples and other desserts.', '972', 250, 240, 250, 2, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2023-08-17 13:26:12', '2023-11-15 08:27:16', NULL, 1, 1),
(195, 'UltraVision 4K Monitor', 'ultravision-4k-monitor', 'The Blackout Jersey will match with any dirt bike pant, because what doesnt match with black? It has a moisture-wicking main body construction to keep you comfortable while youre putting down laps on the track or miles on the local trail. Plus, it has a perforated mesh fabric, so there is plenty of airflow through this motocross jersey.', '100% PolyesterImportedPull On closureMachine WashBreathable crewneck jersey made for soccerRegular fit is wider at the body, with a straight silhouetteCrewneck provides full coverageThis product is made with recycled content as part of our ambition to end plastic waste', '971', 250, 190, 250, NULL, 7, 1, 1, NULL, 2, 10, 0, 1, 1, '2023-08-17 13:26:20', '2023-11-17 18:00:45', NULL, 1, 1),
(199, 'UltraVision 4K Monitor', 'ultravision-4k-monitor', 'The Blackout Jersey will match with any dirt bike pant, because what doesnt match with black? It has a moisture-wicking main body construction to keep you comfortable while youre putting down laps on the track or miles on the local trail. Plus, it has a perforated mesh fabric, so there is plenty of airflow through this motocross jersey.', '100% PolyesterImportedPull On closureMachine WashBreathable crewneck jersey made for soccerRegular fit is wider at the body, with a straight silhouetteCrewneck provides full coverageThis product is made with recycled content as part of our ambition to end plastic waste', '971', 250, 190, 250, NULL, 7, 1, 1, NULL, 2, 10, 0, 1, 1, '2023-08-17 13:26:20', '2023-11-17 18:00:45', NULL, 1, 1),
(200, 'GuardianEye HD Surveillance Camera', 'guardianeye-hd-surveillance-camera-1', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', 'Fabric Type64% Cotton, 34% Polyester, 2% Spandex. Care InstructionsMachine Wash', '975', 452, 321, 452, NULL, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2023-11-17 18:03:39', '2023-11-17 18:04:00', NULL, 0, NULL),
(201, 'GuardianEye HD Surveillance Camera', 'guardianeye-hd-surveillance-camera-2', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', 'Fabric Type64% Cotton, 34% Polyester, 2% Spandex. Care InstructionsMachine Wash', '975', 452, 321, 452, NULL, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2023-11-19 14:35:48', '2023-11-27 11:03:53', NULL, 0, NULL),
(202, 'TitanPro Gaming Laptop', 'titanpro-gaming-laptop-1', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', 'Neck StyleCollared NeckAbout this Item. Omni-wick - the ultimate moisture management technology for the outdoors. Omni-wick quickly moves moisture from the skin into the fabric where it spreads across the surface to quickly evaporate—keeping you cool and your clothing dry.', '963', 774, 533, 774, NULL, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2023-11-27 11:03:49', '2023-11-27 11:04:01', NULL, 0, NULL),
(203, 'QuantumCore Pro Gaming Motherboard', 'quantumcore-pro-gaming-motherboard-1', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', 'Heel Height approximately 3.0\" Platform measures approximately 0.25\"| True size to fit.\r\n\r\nAll your friends will be asking your advice when they see you with these sexy sandals! The open toe and strappy with sparkling rhinestone design front is eye-catching and alluring and will have envious stares on you all night long.\r\n\r\nThis pair is perfectly designed for steady steps, as it features a single, slim sole that ideally balances the heel height with the rest of the sleek shoe design.\r\n\r\nThis stunning pair of heels is ideal for weddings, parties and every other special occasion that calls for dressy, upscale shoes!\r\n\r\nFeaturing a slim straps that hugs your ankle for custom support and provides a comfort throughout wear. Your feet will not slip, turn or move out of place while wearing these gorgeous sandals!\r\n\r\n apples and other desserts.', '972', 250, 240, 250, 2, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2023-11-27 11:04:06', '2023-11-27 11:04:16', NULL, 0, NULL);
");
        }
        else
        {
            DB::statement("INSERT IGNORE INTO `products` (`id`, `name`, `slug`, `summary`, `description`, `image_id`, `price`, `sale_price`, `cost`, `badge_id`, `brand_id`, `status_id`, `product_type`, `sold_count`, `min_purchase`, `max_purchase`, `is_refundable`, `is_in_house`, `is_inventory_warn_able`, `created_at`, `updated_at`, `deleted_at`) VALUES
            (190, 'High Heel Wedding Shoes', 'high-heel-wedding-shoes', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', '<h3 class=\"product-facts-title\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 4px 0px 14px; font-family: \" amazon=\"\" ember\",=\"\" arial,=\"\" sans-serif;=\"\" font-weight:=\"\" 700;=\"\" line-height:=\"\" 20px;=\"\" color:=\"\" rgb(15,=\"\" 17,=\"\" 17);=\"\" font-size:=\"\" 16px;=\"\" text-rendering:=\"\" optimizelegibility;\"=\"\"><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">Heel Height approximately 3.0\" Platform measures approximately 0.25\"| True size to fit.</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">All your friends will be asking your advice when they see you with these sexy sandals! The open toe and strappy with sparkling rhinestone design front is eye-catching and alluring and will have envious stares on you all night long.</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">This pair is perfectly designed for steady steps, as it features a single, slim sole that ideally balances the heel height with the rest of the sleek shoe design.</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">This stunning pair of heels is ideal for weddings, parties and every other special occasion that calls for dressy, upscale shoes!</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">Featuring a slim straps that hugs your ankle for custom support and provides a comfort throughout wear. Your feet will not slip, turn or move out of place while wearing these gorgeous sandals!</span></li></ul><span style=\"margin: 0px; padding: 0px; font-size: 14px; color: rgb(51, 51, 51); font-family: &quot;segoe ui&quot;, Helvetica, &quot;droid sans&quot;, Arial, &quot;lucida grande&quot;, tahoma, verdana, arial, sans-serif;\">&nbsp;apples and other desserts.</span><br></h3><h3 class=\"product-facts-title\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 4px 0px 14px; font-family: \" amazon=\"\" ember\",=\"\" arial,=\"\" sans-serif;=\"\" font-weight:=\"\" 700;=\"\" line-height:=\"\" 20px;=\"\" color:=\"\" rgb(15,=\"\" 17,=\"\" 17);=\"\" font-size:=\"\" 16px;=\"\" text-rendering:=\"\" optimizelegibility;\"=\"\"><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: \" amazon=\"\" ember\",=\"\" arial,=\"\" sans-serif;\"=\"\"></div></h3>', '375', 250, 240, 250, 2, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2022-11-16 10:29:36', '2022-11-16 10:42:27', NULL),
            (191, 'Mans Silver Ridge Lite Long Sleeve Shirt', 'mans-silver-ridge-lite-long-sleeve-shirt-1', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', '<h3 class=\"product-facts-title\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 4px 0px 14px; font-family: \"Amazon Ember\", Arial, sans-serif; font-weight: 700; line-height: 20px; color: rgb(15, 17, 17); font-size: 16px; text-rendering: optimizelegibility;\">Product Details</h3><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Fabric Type</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">100% Polyester</span></div></div></div><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Origin</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">Imported</span></div></div></div><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Closure Type</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">Button</span></div></div></div><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Neck Style</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">Collared Neck</span></div></div></div><hr aria-hidden=\"true\" class=\"a-spacing-base a-spacing-top-base a-divider-normal\" style=\"padding: 0px; overflow: visible; border-top: 1px solid rgb(231, 231, 231); font-size: 14px; line-height: 19px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif; margin-top: 12px !important; margin-bottom: 12px !important;\"><h3 class=\"product-facts-title\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 4px 0px 14px; font-family: \"Amazon Ember\", Arial, sans-serif; font-weight: 700; line-height: 20px; color: rgb(15, 17, 17); font-size: 16px; text-rendering: optimizelegibility;\">About this Item</h3><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">Omni-wick - the ultimate moisture management technology for the outdoors. Omni-wick quickly moves moisture from the skin into the fabric where it spreads across the surface to quickly evaporate—keeping you cool and your clothing dry.</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">Handy features: it features two chest pockets to keep your small items secure.</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">Adjustable features: front button closures and button-down cuffs add adjustable comfort.</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">Casual fit: with 100% cotton fabric, this women\'s flannel features a casual fit perfect for everyday wear.</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">Advanced technology: Columbia women\'s silver ridge lite long sleeve shirt features signature wicking fabric that pulls moisture away from the body so sweat can evaporate quickly and UPF 40 sun protection.</span></li></ul>', '359', 774, 533, 774, NULL, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2022-11-16 10:30:14', '2022-11-16 10:30:22', NULL),
            (192, 'Buck  Long Sleeve Button Down Shirt', 'buck-long-sleeve-button-down-shirt-1', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', '<h3 class=\"product-facts-title\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 4px 0px 14px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-weight: 700; line-height: 20px; color: rgb(15, 17, 17); font-size: 16px; text-rendering: optimizelegibility;\">Product Details</h3><h3 class=\"product-facts-title\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 4px 0px 14px; font-family: \" amazon=\"\" ember\",=\"\" arial,=\"\" sans-serif;=\"\" font-weight:=\"\" 700;=\"\" line-height:=\"\" 20px;=\"\" color:=\"\" rgb(15,=\"\" 17,=\"\" 17);=\"\" font-size:=\"\" 16px;=\"\" text-rendering:=\"\" optimizelegibility;\"=\"\"><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Fabric Type</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">64% Cotton, 34% Polyester, 2% Spandex</span></div></div></div><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Care Instructions</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">Machine Wash</span></div></div></div><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Origin</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">Imported</span></div></div></div><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Closure Type</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">Button</span></div></div></div><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Country of Origin</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">China</span></div></div></div></h3>', '391', 452, 321, 452, NULL, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2022-11-16 10:32:18', '2022-11-16 10:32:18', NULL),
            (193, 'Mens Regular-Fit Long-Sleeve Poplin Jacket', 'mens-regular-fit-long-sleeve-poplin-jacket-1', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', '<h3 class=\"product-facts-title\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 4px 0px 14px; font-family: \"Amazon Ember\", Arial, sans-serif; font-weight: 700; line-height: 20px; color: rgb(15, 17, 17); font-size: 16px; text-rendering: optimizelegibility;\">Product Details</h3><h3 class=\"product-facts-title\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 4px 0px 14px; font-family: \" amazon=\"\" ember\",=\"\" arial,=\"\" sans-serif;=\"\" font-weight:=\"\" 700;=\"\" line-height:=\"\" 20px;=\"\" color:=\"\" rgb(15,=\"\" 17,=\"\" 17);=\"\" font-size:=\"\" 16px;=\"\" text-rendering:=\"\" optimizelegibility;\"=\"\"><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Fabric Type</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">64% Cotton, 34% Polyester, 2% Spandex</span></div></div></div><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Care Instructions</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">Machine Wash</span></div></div></div><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Origin</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">Imported</span></div></div></div><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Closure Type</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">Button</span></div></div></div><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: \"Amazon Ember\", Arial, sans-serif;\"><div class=\"a-fixed-left-grid-inner\" style=\"margin: 0px; padding: 0px 0px 0px 140px; position: relative;\"><div class=\"a-fixed-left-grid-col a-col-left\" style=\"margin: 0px 0px 0px -140px; padding: 0px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 140px; float: left;\"><span class=\"a-color-base a-text-bold\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; font-weight: 700 !important;\">Country of Origin</span></div><div class=\"a-fixed-left-grid-col a-col-right\" style=\"margin: 0px; padding: 0px 0px 0px 32.0739px; position: relative; overflow: visible; zoom: 1; min-height: 1px; width: 534.588px; float: left;\"><span class=\"a-color-secondary\" style=\"margin: 0px; padding: 0px; word-break: break-word; hyphens: auto; color: rgb(86, 89, 89) !important;\">China</span></div></div></div></h3>', '357', 800, 1000, 800, 3, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2022-11-16 10:37:51', '2022-11-16 10:37:51', NULL),
            (195, 'Baby shoes', 'baby-shoes', '100% Textile\r\nSynthetic sole\r\nBoys sneaker-style boots with hook and loop closure\r\nHigh-top styling\r\nHook and loop closure for easy on-and-off', '<ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">100% Textile</span></li><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">Synthetic sole</span></li><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">Boy’s sneaker-style boots with hook and loop closure</span></li><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">High-top styling</span></li><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">Hook and loop closure for easy on-and-off</span></li></ul>', '374', 223, 200, 223, NULL, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2022-11-16 10:51:12', '2022-11-16 10:51:19', NULL),
            (196, 'Stylish color  Jersey', 'stylish-color-jersey', 'The Blackout Jersey will match with any dirt bike pant, because what doesnt match with black? It has a moisture-wicking main body construction to keep you comfortable while youre putting down laps on the track or miles on the local trail. Plus, it has a perforated mesh fabric, so there is plenty of airflow through this motocross jersey.', '<ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">100% Polyester</span></li><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">Imported</span></li><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">Pull On closure</span></li><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">Machine Wash</span></li><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">Breathable crewneck jersey made for soccer</span></li><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">Regular fit is wider at the body, with a straight silhouette</span></li><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">Crewneck provides full coverage</span></li><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item\" style=\"margin: 0px; padding: 0px; overflow-wrap: break-word; display: block;\">This product is made with recycled content as part of our ambition to end plastic waste</span></li></ul>', '377', 250, 190, 250, NULL, 7, 1, 1, NULL, 2, 10, 0, 1, 2, '2022-11-16 10:54:10', '2022-11-16 10:54:26', NULL),
            (197, 'High Heel Wedding Shoes', 'high-heel-wedding-shoes-1', 'No Import Fees Deposit and $25.56 Shipping to Bangladesh', '<h3 class=\"product-facts-title\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 4px 0px 14px; font-family: \" amazon=\"\" ember\",=\"\" arial,=\"\" sans-serif;=\"\" font-weight:=\"\" 700;=\"\" line-height:=\"\" 20px;=\"\" color:=\"\" rgb(15,=\"\" 17,=\"\" 17);=\"\" font-size:=\"\" 16px;=\"\" text-rendering:=\"\" optimizelegibility;\"=\"\"><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">Heel Height approximately 3.0\" Platform measures approximately 0.25\"| True size to fit.</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">All your friends will be asking your advice when they see you with these sexy sandals! The open toe and strappy with sparkling rhinestone design front is eye-catching and alluring and will have envious stares on you all night long.</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">This pair is perfectly designed for steady steps, as it features a single, slim sole that ideally balances the heel height with the rest of the sleek shoe design.</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">This stunning pair of heels is ideal for weddings, parties and every other special occasion that calls for dressy, upscale shoes!</span></li></ul><ul class=\"a-unordered-list a-vertical a-spacing-small\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; list-style-type: none; font-size: 14px; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif;\"><li style=\"margin: 0px; padding: 0px; list-style: disc; overflow-wrap: break-word;\"><span class=\"a-list-item a-size-base a-color-secondary\" style=\"margin: 0px; padding: 0px; line-height: 20px !important;\">Featuring a slim straps that hugs your ankle for custom support and provides a comfort throughout wear. Your feet will not slip, turn or move out of place while wearing these gorgeous sandals!</span></li></ul><span style=\"margin: 0px; padding: 0px; font-size: 14px; color: rgb(51, 51, 51); font-family: &quot;segoe ui&quot;, Helvetica, &quot;droid sans&quot;, Arial, &quot;lucida grande&quot;, tahoma, verdana, arial, sans-serif;\">&nbsp;apples and other desserts.</span><br></h3><h3 class=\"product-facts-title\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 4px 0px 14px; font-family: \" amazon=\"\" ember\",=\"\" arial,=\"\" sans-serif;=\"\" font-weight:=\"\" 700;=\"\" line-height:=\"\" 20px;=\"\" color:=\"\" rgb(15,=\"\" 17,=\"\" 17);=\"\" font-size:=\"\" 16px;=\"\" text-rendering:=\"\" optimizelegibility;\"=\"\"><div class=\"a-fixed-left-grid product-facts-detail\" style=\"margin: 0px 0px 18px; padding: 0px; font-size: 14px; position: relative; line-height: 16px; color: rgb(15, 17, 17); font-family: \" amazon=\"\" ember\",=\"\" arial,=\"\" sans-serif;\"=\"\"></div></h3>', '375', 250, 240, 250, 2, 2, 1, 1, NULL, 1, 10, 0, 1, 1, '2022-11-16 11:24:22', '2022-11-16 11:25:58', '2022-11-16 11:25:58')");
        }

        Product::all()->each(function ($product) {
            try {
                $product->slug()->create(['slug' => $product->slug]);
            } catch (\Exception $exception) {
                $product->slug()->create(['slug' => create_slug($product->slug, 'Slug')]);
            }
        });
    }

    private function seedProductCategory()
    {
        DB::statement("INSERT IGNORE INTO `product_categories` (`id`, `product_id`, `category_id`) VALUES
        (151, 190, 8),
        (152, 191, 10),
        (153, 192, 10),
        (154, 193, 6),
        (155, 195, 8),
        (156, 196, 10),
        (157, 197, 8)");
    }

    private function seedProductSubCategories()
    {
        DB::statement("INSERT IGNORE INTO `product_sub_categories` (`id`, `product_id`, `sub_category_id`) VALUES
        (150, 190, 15),
        (151, 191, 10),
        (152, 192, 10),
        (153, 193, 11),
        (154, 195, 16),
        (155, 196, 10),
        (156, 197, 15)");
    }

    private function seedProductChildCategories()
    {
        DB::statement("INSERT IGNORE INTO `product_child_categories` (`id`, `product_id`, `child_category_id`) VALUES
        (550, 191, 13),
        (551, 191, 19),
        (554, 192, 13),
        (555, 192, 19),
        (557, 193, 15),
        (558, 190, 20),
        (560, 195, 17),
        (561, 196, 13),
        (562, 197, 20)");
    }

    private function seedProductTags()
    {
        DB::statement("INSERT IGNORE INTO `product_tags` (`id`, `tag_name`, `product_id`) VALUES
        (738, 'tshirt', 191),
        (740, 'tshirt', 192),
        (742, 'jacket', 193),
        (743, 'jacket', 190),
        (745, 'baby shoe', 195),
        (746, 'jersy', 196),
        (747, 'jacket', 197)");
    }

    private function seedProductGalleries()
    {
        DB::statement("INSERT IGNORE INTO `product_galleries` (`id`, `product_id`, `image_id`) VALUES
        (147, 191, 380),
        (148, 191, 379),
        (149, 191, 377),
        (153, 192, 382),
        (154, 192, 379),
        (155, 192, 372),
        (159, 193, 377),
        (160, 193, 368),
        (161, 193, 357),
        (162, 195, 362),
        (163, 196, 361)");
    }

    private function seedProductInventories()
    {
        DB::statement("INSERT IGNORE INTO `product_inventories` (`id`, `product_id`, `sku`, `stock_count`, `sold_count`) VALUES
        (211, 190, 'phh4', 20, NULL),
        (212, 191, 'swr234-1', 100, NULL),
        (213, 192, 'srw12-1', 100, NULL),
        (214, 193, 'jck12-1', 50, NULL),
        (216, 195, 'bbs15', 20, NULL),
        (217, 196, 'jrs45', 45, NULL),
        (218, 197, 'phh4-1', 20, NULL)");
    }

    private function seedProductInventoryDetails()
    {
        DB::statement("INSERT IGNORE INTO `product_inventory_details` (`id`, `product_inventory_id`, `product_id`, `color`, `size`, `hash`, `additional_price`, `add_cost`, `image`, `stock_count`, `sold_count`) VALUES
        (379, 216, 195, '1', '2', '', 2.00, 2.00, 362, 10, 0),
        (380, 216, 195, '5', '2', '', 3.00, 3.00, 354, 10, 0)");
    }

    private function seedProductUOM()
    {
        DB::statement("INSERT IGNORE INTO `product_uom` (`id`, `product_id`, `unit_id`, `quantity`) VALUES
        (123, 190, 6, 1.00),
        (124, 191, 6, 1.00),
        (125, 192, 6, 1.00),
        (126, 193, 6, 1.00),
        (127, 195, 6, 1.00),
        (128, 196, 6, 1.00),
        (129, 197, 6, 1.00)");
    }

    private function seedProductCreatedBy()
    {
        DB::statement("INSERT IGNORE INTO `product_created_by` (`id`, `product_id`, `created_by_id`, `guard_name`, `updated_by`, `updated_by_guard`, `deleted_by`, `deleted_by_guard`) VALUES
        (181, 190, 1, 'admin', 1, 'admin', NULL, NULL),
        (182, 191, 1, 'admin', 1, 'admin', NULL, NULL),
        (183, 192, 1, 'admin', NULL, NULL, NULL, NULL),
        (184, 193, 1, 'admin', NULL, NULL, NULL, NULL),
        (186, 195, 1, 'admin', 1, 'admin', NULL, NULL),
        (187, 196, 1, 'admin', 1, 'admin', NULL, NULL),
        (188, 197, 1, 'admin', NULL, NULL, NULL, NULL)");
    }

    private function seedProductDeliveryOption()
    {
        DB::statement("INSERT IGNORE INTO `product_delivery_options` (`id`, `product_id`, `delivery_option_id`) VALUES
        (754, 191, 1),
        (755, 191, 2),
        (756, 191, 3),
        (760, 192, 1),
        (761, 192, 2),
        (762, 192, 3),
        (766, 193, 1),
        (767, 193, 2),
        (768, 193, 3),
        (769, 190, 1),
        (770, 190, 3),
        (774, 195, 1),
        (775, 195, 2),
        (776, 195, 3),
        (777, 197, 1),
        (778, 197, 3)");
    }

    private function seedProductReturnPolicies()
    {
        DB::statement("INSERT IGNORE INTO `product_shipping_return_policies` (`id`, `product_id`, `shipping_return_description`, `created_at`, `updated_at`) VALUES
        (31, 190, '<p>Return in 7 Days is acceptable</p>', '2022-11-16 10:29:36', '2022-11-16 10:29:36'),
        (32, 191, '<p>Return in 7 Days is acceptable</p>', '2022-11-16 10:30:14', '2022-11-16 10:30:14'),
        (33, 192, '<p>Return in 7 Days is acceptable</p>', '2022-11-16 10:32:18', '2022-11-16 10:32:18'),
        (34, 193, '<p>Return in 7 Days is acceptable</p>', '2022-11-16 10:37:52', '2022-11-16 10:37:52'),
        (36, 195, NULL, '2022-11-16 10:51:12', '2022-11-16 10:51:12'),
        (37, 196, NULL, '2022-11-16 10:54:10', '2022-11-16 10:54:10'),
        (38, 197, '<p>Return in 7 Days is acceptable</p>', '2022-11-16 11:24:22', '2022-11-16 11:24:22')");
    }

    private function seedCampaign()
    {
        if (session()->get('theme') == 'electro')
        {
            Campaign::insert([
                [
                    'id' => 14,
                    'title' => 'Winter Products',
                    'subtitle' => 'Coming Soon',
                    'image' => 565,
                    'start_date' => '2023-04-01 00:00:00',
                    'end_date' => '2024-01-01 00:00:00',
                    'status' => 'publish',
                    'created_at' => '2022-11-16 11:01:00',
                    'updated_at' => '2022-11-16 11:12:03',
                    'admin_id' => 1,
                    'type' => 'admin'
                ],
                [
                    'id' => 15,
                    'title' => 'Summer Sale',
                    'subtitle' => 'Buy 1 Get 1 Free',
                    'image' => 552,
                    'start_date' => '2023-04-01 00:00:00',
                    'end_date' => '2024-01-01 00:00:00',
                    'status' => 'publish',
                    'created_at' => '2022-11-16 11:01:00',
                    'updated_at' => '2022-11-16 11:12:03',
                    'admin_id' => 1,
                    'type' => 'admin'
                ]
            ]);
        } else {
            Campaign::create([
                'id' => 14,
                'title' => 'Summer Collection',
                'subtitle' => 'Summer',
                'image' => 540,
                'start_date' => '2023-04-01 00:00:00',
                'end_date' => '2024-01-01 00:00:00',
                'status' => 'publish',
                'created_at' => '2022-11-16 11:01:00',
                'updated_at' => '2022-11-16 11:12:03',
                'admin_id' => 1,
                'type' => 'admin'
            ]);
        }
    }

    private function seedCampaignProducts()
    {
        DB::statement("INSERT IGNORE INTO `campaign_products` (`id`, `product_id`, `campaign_id`, `campaign_price`, `units_for_sale`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
        (118, 191, 14, '479.70', 5, '2022-11-19 18:00:00', '2023-01-30 18:00:00', NULL, NULL),
        (119, 192, 14, '288.90', 5, '2022-11-19 18:00:00', '2023-01-30 18:00:00', NULL, NULL),
        (120, 196, 14, '171.00', 5, '2022-11-19 18:00:00', '2023-01-30 18:00:00', NULL, NULL),
        (121, 193, 14, '900.00', 5, '2022-11-19 18:00:00', '2023-01-30 18:00:00', NULL, NULL)");
    }

    private function seedTaxClass()
    {
        DB::statement("INSERT IGNORE INTO `tax_classes` (`id`, `name`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'VAT', NULL, '2023-10-02 11:24:55', '2023-11-17 17:59:56')");
    }

    private function seedTaxClassOption()
    {
        DB::statement("INSERT IGNORE INTO `tax_class_options` (`id`, `class_id`, `tax_name`, `country_id`, `state_id`, `city_id`, `postal_code`, `priority`, `is_compound`, `is_shipping`, `rate`, `created_at`, `updated_at`) VALUES
(5, 1, 'Total VAT', 1, 1, 1, '1832', 1, 0, 1, 3.00, NULL, NULL)");
    }
}
