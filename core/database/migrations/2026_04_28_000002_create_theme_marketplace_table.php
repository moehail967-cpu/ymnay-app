<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('theme_marketplace')) {
            return;
        }

        Schema::create('theme_marketplace', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('niche')->nullable();
            $table->string('version')->default('1.0.0');
            $table->string('preview_url')->nullable();
            $table->string('download_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_free')->default(true);
            $table->boolean('is_installed')->default(false);
            $table->string('installed_version')->nullable();
            $table->string('latest_version')->nullable();
            $table->boolean('update_available')->default(false);
            $table->json('screenshots')->nullable();
            $table->json('tags')->nullable();
            $table->string('author')->nullable();
            $table->string('license_type')->default('free'); // free, single, unlimited
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_marketplace');
    }
};
