<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable();
            $table->json('cart_items');          // serialised cart contents
            $table->decimal('cart_total', 12, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('status', 20)->default('abandoned'); // abandoned | reminded | converted | expired
            $table->timestamp('reminded_at')->nullable();       // when reminder email was last sent
            $table->unsignedTinyInteger('reminder_count')->default(0);
            $table->timestamp('converted_at')->nullable();      // set when order_completed fires
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'created_at']);
            $table->index(['email', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};
