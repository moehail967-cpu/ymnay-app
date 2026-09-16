<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_onboarding_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('price_plans')->nullOnDelete();
            $table->string('theme_slug')->nullable();
            $table->string('store_name')->nullable();
            $table->string('subdomain')->nullable()->index();
            $table->string('tenant_id')->nullable()->index();
            $table->string('status')->default('draft')->index();
            $table->json('plan_snapshot')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_onboarding_requests');
    }
};
