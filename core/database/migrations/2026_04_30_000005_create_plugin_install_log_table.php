<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugin_install_log', function (Blueprint $table) {
            $table->id();
            $table->string('plugin_id');
            $table->string('version', 20);
            $table->string('action', 20);        // install | update | uninstall
            $table->string('source', 20);        // upload | remote
            $table->string('from_version', 20)->nullable(); // populated on update
            $table->string('download_url')->nullable();
            $table->boolean('checksum_verified')->default(false);
            $table->unsignedBigInteger('installed_by')->nullable(); // admin user id
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['plugin_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_install_log');
    }
};
