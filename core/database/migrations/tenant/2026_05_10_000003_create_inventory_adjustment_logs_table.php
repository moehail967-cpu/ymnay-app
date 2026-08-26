<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_adjustment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->integer('qty_before');
            $table->integer('qty_change');  // positive = add, negative = remove
            $table->integer('qty_after');
            $table->enum('reason', ['purchase', 'return', 'manual_add', 'damaged', 'theft', 'correction', 'campaign_sale']);
            $table->string('note')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustment_logs');
    }
};
