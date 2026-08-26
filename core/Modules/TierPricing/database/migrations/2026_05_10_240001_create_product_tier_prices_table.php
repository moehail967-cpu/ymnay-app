<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_tier_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedInteger('min_qty');
            $table->enum('discount_type', ['percentage', 'flat', 'fixed_price'])->default('percentage');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->string('label')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'product_id', 'min_qty']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_tier_prices');
    }
};
