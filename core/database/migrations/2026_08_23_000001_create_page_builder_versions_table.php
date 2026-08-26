<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_builder_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->json('content')->nullable();       // snapshot of page_builder_content.content
            $table->json('widgets_data')->nullable();  // snapshot of all page_builder_widgets rows
            $table->string('version_label')->default('Auto-save');
            $table->boolean('is_pinned')->default(false); // true for the very first "Original" snapshot
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
            $table->index(['page_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_builder_versions');
    }
};
