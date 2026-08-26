<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sprint 19 T1: handle_variants flag on products
        if (!Schema::hasColumn('products', 'handle_variants')) {
            Schema::table('products', function (Blueprint $table) {
                $table->tinyInteger('handle_variants')->default(0)->after('quantity');
            });
        }

        // Sprint 19 T1: sku, attributes JSON, hash for upsert on inventory details
        Schema::table('product_inventory_details', function (Blueprint $table) {
            if (!Schema::hasColumn('product_inventory_details', 'sku')) {
                $table->string('sku')->nullable()->after('id');
            }
            if (!Schema::hasColumn('product_inventory_details', 'attributes')) {
                $table->json('attributes')->nullable()->after('size');
            }
            if (!Schema::hasColumn('product_inventory_details', 'hash')) {
                $table->string('hash', 32)->nullable()->index()->after('attributes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('handle_variants');
        });
        Schema::table('product_inventory_details', function (Blueprint $table) {
            $table->dropColumn(['sku', 'attributes', 'hash']);
        });
    }
};
