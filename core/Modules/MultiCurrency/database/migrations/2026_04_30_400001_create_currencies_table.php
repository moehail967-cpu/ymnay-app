<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Both tables run on the TENANT database via TenantConnection models.
// Each tenant gets their own isolated copy of currencies and rate settings.
return new class extends Migration
{
    public function up(): void
    {
        // Currency registry — seeded with common currencies on plugin activation,
        // rates refreshed daily via cron when auto_refresh is enabled.
        Schema::create('mc_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 60);
            $table->string('symbol', 10);
            $table->decimal('rate', 16, 6)->default(1); // relative to base currency (default USD)
            $table->string('base_code', 10)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamp('rate_updated_at')->nullable();
            $table->timestamps();
        });

        // Currencies the tenant has enabled in their store.
        // No tenant_id needed — table lives on the tenant's own DB.
        Schema::create('mc_tenant_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 10)->unique();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mc_tenant_currencies');
        Schema::dropIfExists('mc_currencies');
    }
};
