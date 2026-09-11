<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugin_options', function (Blueprint $table) {
            $table->id();
            $table->string('plugin_id', 100);
            $table->string('tenant_id', 255)->nullable();
            $table->string('option_key', 191);
            $table->longText('option_value')->nullable();
            $table->timestamps();

            $table->unique(['plugin_id', 'tenant_id', 'option_key'], 'unique_plugin_option');
            $table->index(['plugin_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_options');
    }
};
