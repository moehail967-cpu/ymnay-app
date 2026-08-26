<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugin_tenant_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('plugin_id', 100);
            $table->unsignedBigInteger('tenant_id');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->boolean('self_manage')->default(false); // landlord allows tenant to toggle this plugin
            $table->boolean('is_preview')->default(false);  // preview mode — active for this tenant only
            $table->timestamps();

            $table->unique(['plugin_id', 'tenant_id'], 'unique_plugin_tenant');
            $table->index('plugin_id');
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_tenant_overrides');
    }
};
