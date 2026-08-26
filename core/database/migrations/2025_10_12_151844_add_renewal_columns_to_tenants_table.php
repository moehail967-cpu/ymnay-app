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
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('renewal_at')->nullable();
            $table->boolean('renewal_after_expire')->default(false);
            $table->unsignedBigInteger('price_plan_id')->nullable();
            $table->unsignedBigInteger('renewal_payment_log_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['renewal_at', 'renewal_after_expire', 'price_plan_id', 'renewal_payment_log_id']);
        });
    }
};
