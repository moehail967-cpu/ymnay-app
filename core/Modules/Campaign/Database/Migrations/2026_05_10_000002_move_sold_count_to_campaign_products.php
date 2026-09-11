<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add sold_count directly on campaign_products
        Schema::table('campaign_products', function (Blueprint $table) {
            $table->unsignedInteger('sold_count')->default(0)->after('units_for_sale');
        });

        // Migrate existing sold_count data from campaign_sold_products
        if (Schema::hasTable('campaign_sold_products')) {
            DB::statement('
                UPDATE campaign_products cp
                JOIN campaign_sold_products csp
                    ON csp.product_id = cp.product_id
                   AND csp.campaign_id = cp.campaign_id
                SET cp.sold_count = csp.sold_count
            ');

            Schema::drop('campaign_sold_products');
        }
    }

    public function down(): void
    {
        Schema::create('campaign_sold_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->integer('sold_count')->default(0);
            $table->float('total_amount')->default(0);
            $table->timestamps();
        });

        Schema::table('campaign_products', function (Blueprint $table) {
            $table->dropColumn('sold_count');
        });
    }
};
