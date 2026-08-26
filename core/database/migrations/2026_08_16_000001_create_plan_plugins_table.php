<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plan_plugins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->string('plugin_id', 100);
            $table->timestamps();
            $table->unique(['plan_id', 'plugin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_plugins');
    }
};
