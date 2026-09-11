<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ml_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->string('model_type', 64);
            $table->unsignedBigInteger('model_id');
            $table->string('locale', 20);
            $table->string('field', 64);
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->unique(
                ['tenant_id', 'model_type', 'model_id', 'locale', 'field'],
                'ml_unique'
            );
            $table->index(['model_type', 'model_id', 'locale'], 'ml_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ml_translations');
    }
};
