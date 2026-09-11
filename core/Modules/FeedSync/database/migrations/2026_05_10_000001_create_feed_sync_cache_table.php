<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_sync_cache', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('feed_type', 32);     // 'google' | 'facebook'
            $table->longText('content');
            $table->timestamp('generated_at')->useCurrent();
            $table->unique(['tenant_id', 'feed_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_sync_cache');
    }
};
