<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_pixel_event_log', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('channel', 32);       // 'ga4' | 'meta' | 'tiktok'
            $table->string('event_type', 64);    // 'Purchase' | 'AddToCart' | 'test'
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->text('response_snippet')->nullable();
            $table->string('event_id', 128)->nullable()->index(); // deduplication
            $table->timestamp('fired_at')->useCurrent();

            $table->index(['tenant_id', 'fired_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_pixel_event_log');
    }
};
