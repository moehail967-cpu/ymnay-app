<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugin_hook_log', function (Blueprint $table) {
            $table->id();
            $table->string('hook', 191);
            $table->string('plugin_id', 100);
            $table->float('duration_ms');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['hook', 'plugin_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_hook_log');
    }
};
