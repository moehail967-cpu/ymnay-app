<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Sprint19InventoryVariantRewrite extends Migration
{
    public function up()
    {
        // T1a: add handle_variants toggle to products
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'handle_variants')) {
            Schema::table('products', function (Blueprint $table) {
                $table->tinyInteger('handle_variants')->default(0)->after('is_taxable');
            });
        }

        // T1b: add per-variant SKU and JSON attributes to product_inventory_details
        // (replaces the product_inventory_detail_attributes third table)
        if (Schema::hasTable('product_inventory_details')) {
            Schema::table('product_inventory_details', function (Blueprint $table) {
                if (!Schema::hasColumn('product_inventory_details', 'sku')) {
                    $table->string('sku')->nullable()->after('id');
                }
                if (!Schema::hasColumn('product_inventory_details', 'attributes')) {
                    $table->json('attributes')->nullable()->after('image');
                }
            });
        }

        // T1c: add variant_id to campaign_products for per-variant campaign pricing
        if (Schema::hasTable('campaign_products') && !Schema::hasColumn('campaign_products', 'variant_id')) {
            Schema::table('campaign_products', function (Blueprint $table) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            });
        }
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('handle_variants');
        });

        Schema::table('product_inventory_details', function (Blueprint $table) {
            $table->dropColumn(['sku', 'attributes']);
        });

        Schema::table('campaign_products', function (Blueprint $table) {
            $table->dropColumn('variant_id');
        });
    }
}
