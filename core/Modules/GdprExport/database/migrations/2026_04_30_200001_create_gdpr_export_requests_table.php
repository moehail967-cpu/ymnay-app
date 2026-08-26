<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdpr_export_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id');
            $table->string('status', 20)->default('pending'); // pending | processing | ready | downloaded | expired
            $table->string('download_token', 64)->nullable()->unique();
            $table->string('file_path')->nullable();          // storage path of the generated ZIP
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('expires_at')->nullable();      // 48 h after ready
            $table->timestamps();

            $table->index(['tenant_id', 'user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_export_requests');
    }
};
