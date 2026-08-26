<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plugin_options')) {
            Schema::create('plugin_options', function (Blueprint $table) {
                $table->id();
                $table->string('plugin_id', 100);
                $table->string('option_key', 100);
                $table->text('option_value')->nullable();
                $table->timestamps();

                $table->unique(['plugin_id', 'option_key'], 'unique_plugin_option');
                $table->index('plugin_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_options');
    }
};
