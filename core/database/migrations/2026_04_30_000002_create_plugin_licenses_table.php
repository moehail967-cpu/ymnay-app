<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugin_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('plugin_id', 100)->unique();
            $table->text('license_key');
            $table->enum('status', ['active', 'expired', 'invalid', 'none'])->default('none');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_checked')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_licenses');
    }
};
