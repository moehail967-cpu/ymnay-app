<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_inventory_details', function (Blueprint $table) {
            if (!Schema::hasColumn('product_inventory_details', 'sku')) {
                $table->string('sku')->nullable()->after('stock_count');
            }
            if (!Schema::hasColumn('product_inventory_details', 'attributes')) {
                $table->json('attributes')->nullable()->after('sku');
            }
            if (!Schema::hasColumn('product_inventory_details', 'hash')) {
                $table->string('hash', 64)->nullable()->after('attributes');
                $table->index('hash');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_inventory_details', function (Blueprint $table) {
            $table->dropColumnIfExists('sku');
            $table->dropColumnIfExists('attributes');
            $table->dropColumnIfExists('hash');
        });
    }
};
