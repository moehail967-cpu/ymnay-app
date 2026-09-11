<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('product_coupons', 'minimum_quantity')) {
                $table->unsignedInteger('minimum_quantity')->nullable();
            }
            if (!Schema::hasColumn('product_coupons', 'minimum_spend')) {
                $table->decimal('minimum_spend', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('product_coupons', 'maximum_spend')) {
                $table->decimal('maximum_spend', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('product_coupons', 'usage_limit_per_coupon')) {
                $table->unsignedInteger('usage_limit_per_coupon')->nullable();
            }
            if(!Schema::hasColumn('product_coupons', 'usage_limit_per_user')){
                $table->unsignedInteger('usage_limit_per_user')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_coupons', function (Blueprint $table) {
            if (Schema::hasColumn('product_coupons', 'minimum_quantity')) {
                $table->dropColumn('minimum_quantity');
            }
            if (Schema::hasColumn('product_coupons', 'minimum_spend')) {
                $table->dropColumn('minimum_spend');
            }
            if (Schema::hasColumn('product_coupons', 'maximum_spend')) {
                $table->dropColumn('maximum_spend');
            }
            if (Schema::hasColumn('product_coupons', 'usage_limit_per_coupon')) {
                $table->dropColumn('usage_limit_per_coupon');
            }
            if (Schema::hasColumn('product_coupons', 'usage_limit_per_user')) {
                $table->dropColumn('usage_limit_per_user');
            }
        });
    }
};
